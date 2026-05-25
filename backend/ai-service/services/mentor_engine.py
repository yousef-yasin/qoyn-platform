from __future__ import annotations

import os
import re
import json
import requests
from typing import Any, Dict, List, Optional, Set

try:
    import pymysql
    from pymysql.cursors import DictCursor
except Exception:
    pymysql = None
    DictCursor = None

try:
    from sentence_transformers import SentenceTransformer
    import numpy as np
except Exception:
    SentenceTransformer = None
    np = None


STOPWORDS = {
    "and", "or", "the", "a", "an", "of", "to", "for", "in", "on", "with",
    "using", "use", "is", "are", "be", "this", "that", "these", "those",
    "student", "project", "task", "team", "role", "skills", "learning",
    "path", "playlist", "playlists"
}

_EMBED_MODEL = None
OLLAMA_URL = os.getenv("OLLAMA_URL", "http://127.0.0.1:11434/api/generate")
OLLAMA_MODEL = os.getenv("OLLAMA_MODEL", "llama3.2:3b")


def normalize_text(s: Any) -> str:
    s = str(s or "").strip()
    s = s.replace("\r", "\n")
    s = re.sub(r"\n{3,}", "\n\n", s)
    return s.strip()


def normalize_key(s: Any) -> str:
    s = str(s or "").strip().lower()
    s = s.replace("،", ",")
    s = s.replace(";", ",")
    s = s.replace("|", ",")
    s = s.replace("/", " ")
    s = s.replace("-", " ")
    s = s.replace("_", " ")
    s = re.sub(r"\s+", " ", s)
    return s.strip()


def canonical_skill_name(s: Any) -> str:
    s = normalize_key(s)
    s = re.sub(r"[^a-z0-9+#.\s]", " ", s)
    s = re.sub(r"\s+", " ", s).strip()

    aliases = {
        "ml": "machine learning",
        "ai": "artificial intelligence",
        "js": "javascript",
        "ts": "typescript",
        "py": "python",
        "cv": "computer vision",
        "nlp": "natural language processing",
        "dl": "deep learning",
        "nn": "neural networks",
        "neural network": "neural networks",
        "rest api": "api",
        "restful api": "api",
    }
    return aliases.get(s, s)


def split_skills(value: Any) -> List[str]:
    if value is None:
        return []

    if isinstance(value, list):
        raw = value
    else:
        txt = str(value)
        txt = txt.replace("\n", ",").replace("\r", ",")
        txt = txt.replace("،", ",").replace(";", ",").replace("|", ",")
        raw = txt.split(",")

    out: List[str] = []
    seen: Set[str] = set()

    for item in raw:
        skill = canonical_skill_name(item)
        if not skill or len(skill) <= 1:
            continue
        if skill not in seen:
            seen.add(skill)
            out.append(skill)

    return out


def tokenize(text: str) -> Set[str]:
    text = normalize_key(text)
    tokens = re.split(r"\s+", text)
    return {t for t in tokens if t and t not in STOPWORDS and len(t) > 1}


def keyword_overlap_score(a: str, b: str) -> float:
    ta = tokenize(a)
    tb = tokenize(b)
    if not ta or not tb:
        return 0.0
    inter = len(ta & tb)
    union = len(ta | tb)
    if union == 0:
        return 0.0
    return inter / union


def detect_lang(text: str) -> str:
    for ch in text:
        if '\u0600' <= ch <= '\u06FF':
            return "ar"
    return "en"


def get_db_conn():
    if pymysql is None:
        raise RuntimeError("pymysql is not installed. Run: pip install pymysql")

    host = os.getenv("MYSQL_HOST", "127.0.0.1")
    port = int(os.getenv("MYSQL_PORT", "3306"))
    user = os.getenv("MYSQL_USER", "root")
    password = os.getenv("MYSQL_PASSWORD", "")
    database = os.getenv("MYSQL_DB", "utbn_db")

    return pymysql.connect(
        host=host,
        port=port,
        user=user,
        password=password,
        database=database,
        charset="utf8mb4",
        cursorclass=DictCursor,
        autocommit=True,
    )


def get_embed_model():
    global _EMBED_MODEL
    if _EMBED_MODEL is not None:
        return _EMBED_MODEL
    if SentenceTransformer is None:
        return None
    try:
        _EMBED_MODEL = SentenceTransformer("all-MiniLM-L6-v2")
        return _EMBED_MODEL
    except Exception:
        return None


def semantic_rank(query: str, docs: List[Dict[str, Any]], top_k: int = 5) -> List[Dict[str, Any]]:
    model = get_embed_model()
    if model is None or np is None or not docs:
        ranked = []
        for d in docs:
            s = keyword_overlap_score(query, d.get("content", ""))
            ranked.append((s, d))
        ranked.sort(key=lambda x: x[0], reverse=True)
        return [x[1] for x in ranked[:top_k] if x[0] > 0]

    try:
        texts = [query] + [d.get("content", "") for d in docs]
        vecs = model.encode(texts)
        qv = vecs[0]
        dvs = vecs[1:]

        scored = []
        qn = float(np.linalg.norm(qv)) or 1.0
        for i, dv in enumerate(dvs):
            dn = float(np.linalg.norm(dv)) or 1.0
            sim = float(np.dot(qv, dv) / (qn * dn))
            doc = dict(docs[i])
            doc["_score"] = sim
            scored.append(doc)

        scored.sort(key=lambda x: x.get("_score", 0), reverse=True)
        return scored[:top_k]
    except Exception:
        ranked = []
        for d in docs:
            s = keyword_overlap_score(query, d.get("content", ""))
            ranked.append((s, d))
        ranked.sort(key=lambda x: x[0], reverse=True)
        return [x[1] for x in ranked[:top_k] if x[0] > 0]


def call_ollama_text(prompt: str) -> str:
    try:
        r = requests.post(
            OLLAMA_URL,
            json={
                "model": OLLAMA_MODEL,
                "prompt": prompt,
                "stream": False,
                "options": {
                    "temperature": 0.3,
                    "top_p": 0.9
                }
            },
            timeout=180,
        )
    except Exception as e:
        raise RuntimeError(f"Ollama connection failed: {e}")

    if r.status_code != 200:
        raise RuntimeError(f"Ollama error {r.status_code}: {r.text[:300]}")

    data = r.json()
    return (data.get("response") or "").strip()


def fetch_student_profile(conn, user_id: int) -> Dict[str, Any]:
    profile: Dict[str, Any] = {
        "user_id": user_id,
        "full_name": "",
        "major_text": "",
        "selected_role_key": "",
        "selected_path_id": None,
        "selected_path_title": "",
        "skills": [],
        "coins": 0,
    }

    try:
        sql = """
            SELECT id, full_name, major_text, coins
            FROM users
            WHERE id = %s
            LIMIT 1
        """
        with conn.cursor() as cur:
            cur.execute(sql, (user_id,))
            row = cur.fetchone()
        if row:
            profile["full_name"] = row.get("full_name") or ""
            profile["major_text"] = row.get("major_text") or ""
            profile["coins"] = int(row.get("coins") or 0)
    except Exception:
        pass

    try:
        sql = """
            SELECT role_key
            FROM user_selected_role
            WHERE user_id = %s
            LIMIT 1
        """
        with conn.cursor() as cur:
            cur.execute(sql, (user_id,))
            row = cur.fetchone()
        if row:
            profile["selected_role_key"] = row.get("role_key") or ""
    except Exception:
        pass

    try:
        sql = """
            SELECT usp.path_id, lp.title
            FROM user_selected_path usp
            LEFT JOIN learning_paths lp ON lp.id = usp.path_id
            WHERE usp.user_id = %s
            LIMIT 1
        """
        with conn.cursor() as cur:
            cur.execute(sql, (user_id,))
            row = cur.fetchone()
        if row:
            profile["selected_path_id"] = row.get("path_id")
            profile["selected_path_title"] = row.get("title") or ""
    except Exception:
        pass

    try:
        sql = """
            SELECT s.skill_name
            FROM user_skills us
            INNER JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = %s
            ORDER BY s.skill_name ASC
        """
        with conn.cursor() as cur:
            cur.execute(sql, (user_id,))
            rows = cur.fetchall() or []
        profile["skills"] = [canonical_skill_name(r["skill_name"]) for r in rows if r.get("skill_name")]
    except Exception:
        pass

    return profile


def fetch_project_info(conn, project_id: Optional[int]) -> Dict[str, Any]:
    if not project_id:
        return {}

    try:
        sql = """
            SELECT id, project_title, project_description, status
            FROM partner_phase3_projects
            WHERE id = %s
            LIMIT 1
        """
        with conn.cursor() as cur:
            cur.execute(sql, (project_id,))
            row = cur.fetchone()
        return row or {}
    except Exception:
        return {}


def fetch_project_tasks(conn, project_id: Optional[int], team_id: Optional[int], user_id: int) -> List[Dict[str, Any]]:
    if not project_id:
        return []

    try:
        sql = """
            SELECT
                t.id,
                t.task_code,
                t.role_key,
                t.role_name,
                t.description AS task_description,
                'ASSIGNED' AS status,
                a.student_id,
                a.match_score
            FROM phase3_tasks t
            INNER JOIN phase3_task_assignments a
              ON a.task_id = t.id AND a.project_id = t.project_id
            WHERE t.project_id = %s
              AND a.student_id = %s
            ORDER BY t.task_order ASC, t.id ASC
        """
        with conn.cursor() as cur:
            cur.execute(sql, (project_id, user_id))
            rows = cur.fetchall() or []
        return rows
    except Exception:
        return []


def fetch_team_chat_messages(conn, team_id: Optional[int], project_id: Optional[int], limit: int = 10) -> List[Dict[str, Any]]:
    if not team_id and not project_id:
        return []

    try:
        sql = """
            SELECT cm.message, cm.sender_role, cm.created_at
            FROM chat_messages cm
            INNER JOIN chat_threads ct ON ct.id = cm.thread_id
            WHERE (%s IS NULL OR ct.team_id = %s)
              AND (%s IS NULL OR ct.phase3_project_id = %s)
            ORDER BY cm.created_at DESC
            LIMIT %s
        """
        with conn.cursor() as cur:
            cur.execute(sql, (team_id, team_id, project_id, project_id, int(limit)))
            rows = cur.fetchall() or []
        rows.reverse()
        return rows
    except Exception:
        return []


def fetch_skill_gap(conn, user_id: int, target_role_key: str) -> Dict[str, Any]:
    try:
        from services.skill_gap_engine import analyze_skill_gap
        return analyze_skill_gap(
            user_id=user_id,
            target_role_key=target_role_key,
            student_skills=[],
        )
    except Exception as e:
        return {"ok": False, "error": "SKILL_GAP_FETCH_FAILED", "details": str(e)}


def fetch_recommendations(conn, user_id: int, role_key: str, path_id: Optional[int], missing_skills: List[str]) -> Dict[str, Any]:
    try:
        from services.recommender_engine import recommend_playlists
        return recommend_playlists(
            user_id=user_id,
            role_key=role_key,
            path_id=path_id,
            skills=[],
            missing_skills=missing_skills,
        )
    except Exception as e:
        return {"ok": False, "error": "RECOMMEND_FETCH_FAILED", "details": str(e)}


def build_context_docs(
    profile: Dict[str, Any],
    project: Dict[str, Any],
    tasks: List[Dict[str, Any]],
    gap: Dict[str, Any],
    recs: Dict[str, Any],
    team_messages: List[Dict[str, Any]],
    chat_context: Optional[List[Dict[str, Any]]] = None,
) -> List[Dict[str, Any]]:
    docs: List[Dict[str, Any]] = []

    docs.append({
        "source": "student_profile",
        "content": (
            f"Student profile\n"
            f"Name: {profile.get('full_name','')}\n"
            f"Major: {profile.get('major_text','')}\n"
            f"Selected role: {profile.get('selected_role_key','')}\n"
            f"Selected path: {profile.get('selected_path_title','')}\n"
            f"Skills: {', '.join(profile.get('skills', []))}\n"
            f"Coins: {profile.get('coins', 0)}"
        )
    })

    if project:
        docs.append({
            "source": "project",
            "content": (
                f"Project information\n"
                f"Title: {project.get('project_title','')}\n"
                f"Description: {project.get('project_description','')}\n"
                f"Status: {project.get('status','')}"
            )
        })

    for i, t in enumerate(tasks[:12], start=1):
        docs.append({
            "source": f"task_{i}",
            "content": (
                f"Task {i}\n"
                f"Task code: {t.get('task_code','')}\n"
                f"Role key: {t.get('role_key','')}\n"
                f"Role name: {t.get('role_name','')}\n"
                f"Description: {t.get('task_description','')}\n"
                f"Status: {t.get('status','')}\n"
                f"Match score: {t.get('match_score','')}"
            )
        })

    if gap.get("ok"):
        docs.append({
            "source": "skill_gap",
            "content": (
                f"Skill gap analysis\n"
                f"Target role: {gap.get('target_role_key','')}\n"
                f"Coverage score: {gap.get('coverage_score','')}\n"
                f"Gap score: {gap.get('gap_score','')}\n"
                f"Matched skills: {', '.join([x.get('skill_name','') for x in gap.get('matched_skills', [])[:10]])}\n"
                f"Missing skills: {', '.join([x.get('skill_name','') for x in gap.get('missing_skills', [])[:10]])}"
            )
        })

    if recs.get("ok"):
        top_recs = recs.get("recommendations", [])[:5]
        rec_lines = []
        for r in top_recs:
            rec_lines.append(
                f"- {r.get('title','')} | reason: {r.get('reason','')} | score: {r.get('score','')}"
            )
        docs.append({
            "source": "recommendations",
            "content": "Recommended playlists\n" + "\n".join(rec_lines)
        })

    if team_messages:
        msg_lines = []
        for m in team_messages[:10]:
            msg_lines.append(
                f"[{m.get('sender_role','unknown')}] {normalize_text(m.get('message',''))}"
            )
        docs.append({
            "source": "team_chat",
            "content": "Recent team chat\n" + "\n".join(msg_lines)
        })

    if chat_context:
        ext_lines = []
        for m in chat_context[:10]:
            role = m.get("role", "user")
            text = normalize_text(m.get("content", ""))
            if text:
                ext_lines.append(f"[{role}] {text}")
        if ext_lines:
            docs.append({
                "source": "request_chat_context",
                "content": "Current conversation context\n" + "\n".join(ext_lines)
            })

    return docs


def build_answer(
    question: str,
    ranked_docs: List[Dict[str, Any]],
    profile: Dict[str, Any],
    gap: Dict[str, Any],
    recs: Dict[str, Any]
) -> str:
    lang = detect_lang(question)

    missing_skills = []
    if gap.get("ok"):
        missing_skills = [
            x.get("skill_name", "")
            for x in gap.get("missing_skills", [])[:8]
            if x.get("skill_name")
        ]

    top_recommendations = []
    if recs.get("ok"):
        for r in recs.get("recommendations", [])[:5]:
            top_recommendations.append({
                "title": r.get("title", ""),
                "reason": r.get("reason", ""),
                "score": r.get("score", 0),
            })

    ranked_context = []
    for d in ranked_docs[:5]:
        ranked_context.append({
            "source": d.get("source", ""),
            "content": d.get("content", "")[:1200]
        })

    prompt = f"""
You are an AI mentor inside a student-company capstone platform.

IMPORTANT:
- If the question is Arabic, answer ONLY in Arabic.
- If the question is English, answer ONLY in English.
- Do NOT switch languages.
- Use the provided context only.
- Do not invent facts.
- Keep the answer practical, short, and personalized.
- If the student greets you, greet them back briefly.
- If the student asks what to study, prioritize missing skills and recommended playlists.
- If the student asks about the current task, explain how to start based on the task context.

Detected language: {lang}

Student question:
{question}

Student profile:
{json.dumps(profile, ensure_ascii=False)}

Skill gap:
{json.dumps({
    "ok": gap.get("ok"),
    "target_role_key": gap.get("target_role_key"),
    "coverage_score": gap.get("coverage_score"),
    "gap_score": gap.get("gap_score"),
    "missing_skills": missing_skills
}, ensure_ascii=False)}

Top recommendations:
{json.dumps(top_recommendations, ensure_ascii=False)}

Relevant context docs:
{json.dumps(ranked_context, ensure_ascii=False)}

Return plain text only. No JSON. No markdown list unless necessary.
""".strip()

    try:
        answer = call_ollama_text(prompt)
        if answer:
            return answer
    except Exception:
        pass

    if lang == "ar":
        parts = ["بناءً على بياناتك الحالية، الأفضل تبدأ بالمحتوى الأقرب لمهمتك الحالية."]
        if missing_skills:
            parts.append("المهارات الناقصة الأهم عندك هي: " + "، ".join(missing_skills[:5]) + ".")
        if top_recommendations:
            parts.append("وأفضل محتوى مقترح الآن: " + " | ".join([x["title"] for x in top_recommendations[:3]]) + ".")
        parts.append("ابدأ بخطوة صغيرة عملية داخل التاسك ثم طوّرها تدريجيًا.")
        return " ".join(parts)

    parts = ["Based on your current data, start with the learning content closest to your current task."]
    if missing_skills:
        parts.append("Your most important missing skills are: " + ", ".join(missing_skills[:5]) + ".")
    if top_recommendations:
        parts.append("Best content to start with now: " + " | ".join([x["title"] for x in top_recommendations[:3]]) + ".")
    parts.append("Begin with one small practical step inside the task, then improve it gradually.")
    return " ".join(parts)


def mentor_chat(
    user_id: int,
    project_id: Optional[int] = None,
    team_id: Optional[int] = None,
    question: str = "",
    chat_context: Optional[List[Dict[str, Any]]] = None,
) -> Dict[str, Any]:
    conn = None
    try:
        conn = get_db_conn()

        profile = fetch_student_profile(conn, int(user_id))
        role_key = (profile.get("selected_role_key") or "").strip().lower()

        project = fetch_project_info(conn, project_id)
        tasks = fetch_project_tasks(conn, project_id, team_id, int(user_id))
        team_messages = fetch_team_chat_messages(conn, team_id, project_id, limit=10)

        gap = fetch_skill_gap(conn, int(user_id), role_key) if role_key else {"ok": False}
        missing_skills = [x.get("skill_name", "") for x in gap.get("missing_skills", [])[:10]] if gap.get("ok") else []

        recs = fetch_recommendations(
            conn,
            int(user_id),
            role_key=role_key,
            path_id=profile.get("selected_path_id"),
            missing_skills=missing_skills,
        ) if role_key else {"ok": False}

        docs = build_context_docs(
            profile=profile,
            project=project,
            tasks=tasks,
            gap=gap,
            recs=recs,
            team_messages=team_messages,
            chat_context=chat_context or [],
        )

        ranked = semantic_rank(question, docs, top_k=5)
        answer = build_answer(question, ranked, profile, gap, recs)

        sources = []
        for d in ranked:
            sources.append({
                "source": d.get("source"),
                "snippet": normalize_text(d.get("content", ""))[:280]
            })

        return {
            "ok": True,
            "user_id": user_id,
            "project_id": project_id,
            "team_id": team_id,
            "answer": answer,
            "sources": sources,
            "context_summary": {
                "role_key": role_key,
                "path_title": profile.get("selected_path_title", ""),
                "skills_count": len(profile.get("skills", [])),
                "tasks_count": len(tasks),
                "gap_ok": bool(gap.get("ok")),
                "recommendations_ok": bool(recs.get("ok")),
            }
        }

    except Exception as e:
        return {
            "ok": False,
            "error": "MENTOR_CHAT_FAILED",
            "details": str(e),
            "user_id": user_id,
            "project_id": project_id,
            "team_id": team_id,
        }
    finally:
        try:
            if conn is not None:
                conn.close()
        except Exception:
            pass
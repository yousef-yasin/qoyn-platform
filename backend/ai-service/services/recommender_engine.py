from __future__ import annotations

import os
import re
from typing import Any, Dict, List, Optional, Set

try:
    import pymysql
    from pymysql.cursors import DictCursor
except Exception:
    pymysql = None
    DictCursor = None


STOPWORDS = {
    "and", "or", "the", "a", "an", "of", "to", "for", "in", "on", "with",
    "using", "use", "course", "courses", "playlist", "playlists", "video", "videos",
    "introduction", "intro", "basics", "basic", "advanced", "learn", "learning"
}


def normalize_text(s: Any) -> str:
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
    s = normalize_text(s)
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
        if not skill:
            continue
        if skill in STOPWORDS:
            continue
        if len(skill) <= 1:
            continue
        if skill not in seen:
            seen.add(skill)
            out.append(skill)

    return out


def tokenize_text(value: Any) -> Set[str]:
    text = canonical_skill_name(value)
    if not text:
        return set()
    tokens = [t for t in re.split(r"\s+", text) if t and t not in STOPWORDS]
    return set(tokens)


def text_contains_skill(text: str, skill: str) -> bool:
    text_n = canonical_skill_name(text)
    skill_n = canonical_skill_name(skill)

    if not text_n or not skill_n:
        return False

    if skill_n in text_n:
        return True

    tt = tokenize_text(text_n)
    st = tokenize_text(skill_n)
    if not tt or not st:
        return False

    inter = len(tt & st)
    union = len(tt | st)
    if union == 0:
        return False

    return (inter / union) >= 0.5


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


def fetch_user_role_from_db(conn, user_id: int) -> Optional[str]:
    sql = """
        SELECT role_key
        FROM user_selected_role
        WHERE user_id = %s
        LIMIT 1
    """
    with conn.cursor() as cur:
        cur.execute(sql, (user_id,))
        row = cur.fetchone()
    if not row:
        return None
    return normalize_text(row.get("role_key")) or None


def fetch_user_path_from_db(conn, user_id: int) -> Optional[int]:
    sql = """
        SELECT path_id
        FROM user_selected_path
        WHERE user_id = %s
        LIMIT 1
    """
    with conn.cursor() as cur:
        cur.execute(sql, (user_id,))
        row = cur.fetchone()
    if not row:
        return None
    try:
        return int(row.get("path_id"))
    except Exception:
        return None


def fetch_user_skills_from_db(conn, user_id: int) -> List[str]:
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
    return [canonical_skill_name(r["skill_name"]) for r in rows if r.get("skill_name")]


def fetch_playlists(conn) -> List[Dict[str, Any]]:
    sql = """
        SELECT
            pp.id AS playlist_id,
            pp.name AS playlist_title,
            pp.description AS playlist_description,
            pp.path_id AS path_id,
            '' AS role_key,
            'partner_playlists' AS source_table
        FROM partner_playlists pp
        WHERE pp.is_published = 1
        ORDER BY pp.id ASC
    """

    with conn.cursor() as cur:
        cur.execute(sql)
        rows = cur.fetchall() or []

    merged: Dict[int, Dict[str, Any]] = {}

    for r in rows:
        try:
            pid = int(r.get("playlist_id"))
        except Exception:
            continue

        if pid <= 0:
            continue

        title = str(r.get("playlist_title") or "").strip()
        desc = str(r.get("playlist_description") or "").strip()
        path_id = r.get("path_id")
        role_key = str(r.get("role_key") or "").strip().lower()
        source_table = str(r.get("source_table") or "")

        if pid not in merged:
            merged[pid] = {
                "playlist_id": pid,
                "title": title,
                "description": desc,
                "path_ids": set(),
                "role_keys": set(),
                "sources": set(),
            }

        if path_id is not None:
            try:
                merged[pid]["path_ids"].add(int(path_id))
            except Exception:
                pass

        if role_key:
            merged[pid]["role_keys"].add(role_key)

        if source_table:
            merged[pid]["sources"].add(source_table)

    out = []
    for pid, item in merged.items():
        out.append({
            "playlist_id": pid,
            "title": item["title"],
            "description": item["description"],
            "path_ids": sorted(item["path_ids"]),
            "role_keys": sorted(item["role_keys"]),
            "sources": sorted(item["sources"]),
        })

    return out


def score_playlist(
    playlist: Dict[str, Any],
    role_key: str,
    path_id: Optional[int],
    skills: List[str],
    missing_skills: List[str],
) -> Dict[str, Any]:
    score = 0.0
    reasons: List[str] = []

    title = str(playlist.get("title") or "")
    desc = str(playlist.get("description") or "")
    text_blob = f"{title}\n{desc}"
    playlist_paths = set(playlist.get("path_ids") or [])
    playlist_roles = {normalize_text(x) for x in (playlist.get("role_keys") or [])}

    if path_id is not None and path_id in playlist_paths:
        score += 0.35
        reasons.append("Matches selected learning path")

    if role_key and role_key in playlist_roles:
        score += 0.25
        reasons.append("Matches selected role")

    covered_missing = []
    for ms in missing_skills:
        if text_contains_skill(text_blob, ms):
            covered_missing.append(ms)

    if covered_missing:
        bonus = min(0.30, 0.10 * len(covered_missing))
        score += bonus
        reasons.append("Covers missing skills: " + ", ".join(covered_missing[:3]))

    covered_existing = []
    for sk in skills:
        if text_contains_skill(text_blob, sk):
            covered_existing.append(sk)

    if covered_existing:
        bonus = min(0.10, 0.03 * len(covered_existing))
        score += bonus
        reasons.append("Builds on existing student skills")

    if not reasons and text_blob.strip():
        score += 0.02
        reasons.append("General relevant learning content")

    score = min(score, 1.0)

    return {
        "playlist_id": int(playlist.get("playlist_id")),
        "score": round(score, 4),
        "reason": " | ".join(reasons) if reasons else "General recommendation",
        "title": title,
        "description": desc,
        "path_ids": playlist.get("path_ids") or [],
        "role_keys": playlist.get("role_keys") or [],
        "sources": playlist.get("sources") or [],
    }


def recommend_playlists(
    user_id: Optional[int] = None,
    role_key: str = "",
    path_id: Optional[int] = None,
    skills: Optional[List[str]] = None,
    missing_skills: Optional[List[str]] = None,
) -> Dict[str, Any]:
    conn = None
    try:
        conn = get_db_conn()

        if user_id and not role_key:
            role_key = fetch_user_role_from_db(conn, int(user_id)) or ""

        if user_id and path_id is None:
            path_id = fetch_user_path_from_db(conn, int(user_id))

        if user_id and not skills:
            skills = fetch_user_skills_from_db(conn, int(user_id))

        role_key = normalize_text(role_key)
        skills = split_skills(skills or [])
        missing_skills = split_skills(missing_skills or [])

        playlists = fetch_playlists(conn)
        if not playlists:
            return {
                "ok": False,
                "error": "NO_PLAYLISTS_FOUND",
                "user_id": user_id,
                "role_key": role_key,
                "path_id": path_id,
            }

        scored = [
            score_playlist(
                playlist=p,
                role_key=role_key,
                path_id=path_id,
                skills=skills,
                missing_skills=missing_skills,
            )
            for p in playlists
        ]

        scored.sort(key=lambda x: x["score"], reverse=True)

        recommendations = []
        for item in scored[:10]:
            recommendations.append({
                "playlist_id": item["playlist_id"],
                "score": item["score"],
                "reason": item["reason"],
                "title": item["title"],
                "description": item["description"],
                "path_ids": item["path_ids"],
                "role_keys": item["role_keys"],
                "sources": item["sources"],
            })

        return {
            "ok": True,
            "user_id": user_id,
            "role_key": role_key,
            "path_id": path_id,
            "skills": skills,
            "missing_skills": missing_skills,
            "recommendations": recommendations,
        }

    except Exception as e:
        return {
            "ok": False,
            "error": "RECOMMENDATION_FAILED",
            "details": str(e),
            "user_id": user_id,
            "role_key": normalize_text(role_key),
            "path_id": path_id,
        }
    finally:
        try:
            if conn is not None:
                conn.close()
        except Exception:
            pass
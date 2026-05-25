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


# ---------------------------
# text normalization helpers
# ---------------------------

STOPWORDS = {
    "and", "or", "the", "a", "an", "of", "to", "for", "in", "on", "with",
    "using", "use", "basic", "advanced", "intro", "introduction",
    "skill", "skills", "knowledge", "competence", "competencies"
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
        "sql db": "sql",
        "mysql database": "mysql",
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


def skill_token_set(s: str) -> Set[str]:
    s = canonical_skill_name(s)
    tokens = [t for t in re.split(r"\s+", s) if t and t not in STOPWORDS]
    return set(tokens)


def skills_similar(a: str, b: str) -> bool:
    a = canonical_skill_name(a)
    b = canonical_skill_name(b)

    if not a or not b:
        return False

    if a == b:
        return True

    if a in b or b in a:
        return True

    ta = skill_token_set(a)
    tb = skill_token_set(b)

    if not ta or not tb:
        return False

    inter = len(ta & tb)
    union = len(ta | tb)
    if union == 0:
        return False

    jaccard = inter / union
    return jaccard >= 0.5


# ---------------------------
# db helpers
# ---------------------------

def get_db_conn():
    if pymysql is None:
        raise RuntimeError("pymysql is not installed. Run: pip install pymysql")

    host = os.getenv("MYSQL_HOST", "127.0.0.1")
    port = int(os.getenv("MYSQL_PORT", "3306"))
    user = os.getenv("MYSQL_USER", "root")
    password = os.getenv("MYSQL_PASSWORD", "")
    database = os.getenv("MYSQL_DB", "utbn_db")
    print("DB NAME:", database)
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


def fetch_student_skills_from_db(conn, user_id: int) -> List[str]:
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


def fetch_target_role_from_db(conn, user_id: int) -> Optional[str]:
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

    return (row.get("role_key") or "").strip().lower() or None


def fetch_role_required_skills(conn, role_key: str) -> List[Dict[str, Any]]:
    role_key = (role_key or "").strip().lower()

    sql_manual = """
        SELECT
            role_key,
            role_key AS role_name,
            NULL AS skill_id,
            skill_name,
            1 AS weight,
            'MANUAL' AS source
        FROM role_required_skills
        WHERE LOWER(role_key) = %s
        ORDER BY skill_name ASC
    """

    with conn.cursor() as cur:
        cur.execute(sql_manual, (role_key,))
        rows = cur.fetchall() or []

    return rows


# ---------------------------
# main analyzer
# ---------------------------

def analyze_skill_gap(
    user_id: Optional[int] = None,
    target_role_key: Optional[str] = None,
    student_skills: Optional[List[str]] = None,
) -> Dict[str, Any]:
    conn = None
    try:
        conn = get_db_conn()

        if not target_role_key and user_id:
            target_role_key = fetch_target_role_from_db(conn, user_id)

        target_role_key = (target_role_key or "").strip().lower()
        if not target_role_key:
            return {
                "ok": False,
                "error": "target_role_key is required"
            }

        if not student_skills and user_id:
            student_skills = fetch_student_skills_from_db(conn, int(user_id))

        normalized_student_skills = split_skills(student_skills or [])
        required_rows = fetch_role_required_skills(conn, target_role_key)

        if not required_rows:
            return {
                "ok": False,
                "error": "NO_ROLE_SKILLS_FOUND",
                "target_role_key": target_role_key,
            }

        required_skills: List[Dict[str, Any]] = []
        for r in required_rows:
            skill_name = canonical_skill_name(r.get("skill_name"))
            weight = float(r.get("weight", 1) or 1)
            source = str(r.get("source", "MANUAL") or "MANUAL")

            if not skill_name:
                continue

            required_skills.append({
                "skill_name": skill_name,
                "weight": weight,
                "source": source,
            })

        matched_skills: List[Dict[str, Any]] = []
        missing_skills: List[Dict[str, Any]] = []
        covered_weight = 0.0
        total_weight = 0.0

        for req in required_skills:
            req_skill = req["skill_name"]
            weight = float(req["weight"])
            source = req["source"]
            total_weight += weight

            matched_by = None
            for st in normalized_student_skills:
                if skills_similar(req_skill, st):
                    matched_by = st
                    break

            item = {
                "skill_name": req_skill,
                "weight": round(weight, 4),
                "source": source,
            }

            if matched_by:
                covered_weight += weight
                item["matched_by"] = matched_by
                matched_skills.append(item)
            else:
                missing_skills.append(item)

        coverage_score = 0.0
        gap_score = 0.0
        if total_weight > 0:
            coverage_score = covered_weight / total_weight
            gap_score = 1.0 - coverage_score

        missing_skills.sort(key=lambda x: x["weight"], reverse=True)
        matched_skills.sort(key=lambda x: x["weight"], reverse=True)

        return {
            "ok": True,
            "user_id": user_id,
            "target_role_key": target_role_key,
            "student_skills": normalized_student_skills,
            "required_skills_count": len(required_skills),
            "matched_skills": matched_skills,
            "missing_skills": missing_skills,
            "coverage_score": round(coverage_score, 4),
            "gap_score": round(gap_score, 4),
            "top_missing_skills": [x["skill_name"] for x in missing_skills[:10]],
        }

    except Exception as e:
        return {
            "ok": False,
            "error": "SKILL_GAP_ANALYSIS_FAILED",
            "details": str(e),
            "target_role_key": (target_role_key or "").strip().lower(),
            "user_id": user_id,
        }

    finally:
        try:
            if conn is not None:
                conn.close()
        except Exception:
            pass
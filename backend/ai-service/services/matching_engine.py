from typing import List, Dict, Any, Set
import math


def normalize_text(s: str) -> str:
    return (s or "").strip().lower()


def tokenize_skills(text: str) -> Set[str]:
    raw = (text or "").replace("،", ",").replace(";", ",")
    parts = [x.strip().lower() for x in raw.split(",")]
    return {p for p in parts if p}


def normalize_task_skills(task: Dict[str, Any]) -> Set[str]:
    task_skills = task.get("skills", [])
    if isinstance(task_skills, str):
        task_skills = tokenize_skills(task_skills)
    elif isinstance(task_skills, list):
        task_skills = {normalize_text(str(x)) for x in task_skills if str(x).strip()}
    else:
        task_skills = set()
    return task_skills


def role_match_score(student: Dict[str, Any], task: Dict[str, Any]) -> float:
    task_role = normalize_text(task.get("role_key", ""))
    sr = normalize_text(student.get("selected_role_key", ""))
    pr = normalize_text(student.get("selected_path_role_key", ""))

    score = 0.0
    if sr == task_role:
        score += 0.65
    if pr == task_role:
        score += 0.25

    return min(score, 1.0)


def skill_overlap_score(student: Dict[str, Any], task: Dict[str, Any]) -> float:
    student_skills = tokenize_skills(student.get("skills_text", ""))
    task_skills = normalize_task_skills(task)

    if not student_skills or not task_skills:
        return 0.0

    inter = len(student_skills & task_skills)
    union = len(task_skills)
    return inter / max(union, 1)


def performance_score(student: Dict[str, Any]) -> float:
    role_score = float(student.get("role_score", 0) or 0)
    return max(0.0, min(role_score / 100.0, 1.0))


def coin_score(student: Dict[str, Any]) -> float:
    coins = int(student.get("coins", 0) or 0)
    return max(0.0, min(coins / 50000.0, 1.0))


def success_history_score(student: Dict[str, Any]) -> float:
    # جاهزة للمستقبل
    history = float(student.get("success_history", 0) or 0)
    return max(0.0, min(history, 1.0))


def final_student_task_score(student: Dict[str, Any], task: Dict[str, Any]) -> float:
    r = role_match_score(student, task)
    s = skill_overlap_score(student, task)
    p = performance_score(student)
    c = coin_score(student)
    h = success_history_score(student)

    score = (
        0.40 * r +
        0.30 * s +
        0.20 * p +
        0.05 * c +
        0.05 * h
    )
    return round(score, 4)


def assign_students_to_tasks(students: List[Dict[str, Any]], tasks: List[Dict[str, Any]]) -> Dict[str, Any]:
    assignments = []
    used_students = set()

    for task in tasks:
        ranked = []

        for student in students:
            sid = int(student.get("id", 0) or 0)
            if sid <= 0:
                continue
            if sid in used_students:
                continue

            score = final_student_task_score(student, task)
            ranked.append({
                "student_id": sid,
                "full_name": student.get("full_name", ""),
                "score": score,
                "role_match_score": round(role_match_score(student, task), 4),
                "skill_overlap_score": round(skill_overlap_score(student, task), 4),
                "performance_score": round(performance_score(student), 4),
                "coin_score": round(coin_score(student), 4),
                "success_history_score": round(success_history_score(student), 4),
            })

        ranked.sort(key=lambda x: x["score"], reverse=True)
        best = ranked[0] if ranked else None

        assignments.append({
            "task_id": int(task.get("id", 0) or 0),
            "task_code": task.get("task_code"),
            "role_key": task.get("role_key"),
            "top_candidates": ranked[:5],
            "assigned_student_id": best["student_id"] if best else None,
            "assigned_score": best["score"] if best else 0.0
        })

        if best:
            used_students.add(best["student_id"])

    return {
        "ok": True,
        "assignments": assignments
    }
from pathlib import Path
from typing import Dict, Any, Optional, List
from services.cv_engine import extract_text_from_file, score_cv
from services.project_engine import score_project
from services.readiness_engine import compute_readiness
from services.roadmap_engine import generate_roadmap
import joblib
import json
import numpy as np
import requests
import uvicorn

from fastapi import FastAPI, Query, HTTPException
from pydantic import BaseModel, Field

from vector_store import upsert_document, query_similar
from agents import context_agent, retrieval_agent, challenge_generator_agent, evaluation_agent
from grader_v2 import build_evidence
from services.semantic_matching_engine import semantic_skill_match
from services.matching_engine import assign_students_to_tasks
from services.skill_gap_engine import analyze_skill_gap
from services.recommender_engine import recommend_playlists
from services.mentor_engine import mentor_chat


ROOT = Path(__file__).resolve().parent
MODEL_PATH = ROOT / "model.joblib"

app = FastAPI(title="UTBN AI Service")


# =========================
# Existing ML (predict_level)
# =========================

class PredictReq(BaseModel):
    user_id: Optional[int] = None
    features: Dict[str, Any]



def _to_float(x, default=0.0) -> float:
    try:
        if x is None:
            return float(default)
        return float(x)
    except Exception:
        return float(default)


def _clamp(x: float, lo: float, hi: float) -> float:
    if x < lo:
        return lo
    if x > hi:
        return hi
    return x


def normalize_avg_watch(v: float) -> float:
    # handle old scale 0..100
    if v > 1.0:
        v = v / 100.0
    return _clamp(v, 0.0, 1.0)


_MODEL_PACK: Optional[dict] = None


def load_pack():
    global _MODEL_PACK
    if _MODEL_PACK is not None:
        return _MODEL_PACK

    if MODEL_PATH.exists():
        pack = joblib.load(MODEL_PATH)
        _MODEL_PACK = pack if isinstance(pack, dict) else {"model": pack}
        return _MODEL_PACK

    _MODEL_PACK = {}
    return _MODEL_PACK


@app.get("/health")
def health():
    pack = load_pack()
    ok = bool(pack.get("model") is not None)
    return {
        "ok": True,
        "model_loaded": ok,
        "model_version": str(pack.get("version", "ml-v1" if ok else "missing")),
        "feature_names": pack.get("feature_names"),
    }



@app.get("/predict")
def predict(
    user_id: Optional[int] = Query(default=None),
    n: int = Query(default=20),
    avg_score: float = Query(default=0.0),
    avg_time: float = Query(default=0.0),
    avg_watch: float = Query(default=0.0),
    avg_difficulty: float = Query(default=3.0),
    hard_avg_score: float = Query(default=0.0),
):
    
    req = PredictReq(
        user_id=user_id,
        features={
            "n": n,
            "avg_score": avg_score,
            "avg_time": avg_time,
            "avg_watch": avg_watch,
            "avg_difficulty": avg_difficulty,
            "hard_avg_score": hard_avg_score,
        },
    )
    out = predict_level(req)



    return {
        "ok": True,
        "features": req.features,
        "level": out.get("level"),
        "phase_ready": out.get("phase_ready"),
        "phase2_ready": out.get("phase2_ready"),
        "phase3_ready": out.get("phase3_ready"),
        "model_version": out.get("model_version"),
    }


@app.post("/predict_level")
def predict_level(req: PredictReq):
    pack = load_pack()
    model = pack.get("model")
    labels_map = pack.get("labels") or {0: "beginner", 1: "intermediate", 2: "advanced"}
    feature_names = pack.get("feature_names")
    version = str(pack.get("version", "ml-v1")) if model is not None else "rule-v1"

    f = req.features or {}

    # accept aliases
    if "avg_watched" in f and "avg_watch" not in f:
        f["avg_watch"] = f["avg_watched"]

    if "avg_time_spent" in f and "avg_time" not in f:
        f["avg_time"] = f["avg_time_spent"]







    if "attempts" in f and "num_attempts" not in f:
        f["num_attempts"] = f["attempts"]

    # Build feature vector
    if feature_names and isinstance(feature_names, list) and len(feature_names) > 0:
        vec = []
        for name in feature_names:
            if name == "avg_watch":
                vec.append(normalize_avg_watch(_to_float(f.get(name, 0.0))))
            else:
                vec.append(_to_float(f.get(name, 0.0)))
        x = np.array(vec, dtype=np.float32).reshape(1, -1)
    else:
        avg_score = _clamp(_to_float(f.get("avg_score", 0.0)), 0.0, 100.0)
        avg_time = _clamp(_to_float(f.get("avg_time", 0.0)), 0.0, 7200.0)
        avg_watch = normalize_avg_watch(_to_float(f.get("avg_watch", 0.0)))
        avg_diff = _clamp(_to_float(f.get("avg_difficulty", 3.0)), 1.0, 5.0)
        hard_avg = _to_float(f.get("hard_avg_score", 0.0))
        x = np.array([avg_score, avg_time, avg_watch, avg_diff, hard_avg], dtype=np.float32).reshape(1, -1)

    # If model not trained/available -> rule fallback
    if model is None:
        avg = _to_float(f.get("avg_score", 0.0))
        avg_watch = normalize_avg_watch(_to_float(f.get("avg_watch", 0.0)))

        if avg >= 85:
            level = "advanced"
        elif avg >= 65:
            level = "intermediate"
        else:
            level = "beginner"

        phase2_ready = bool(avg >= 75 and avg_watch >= 0.85)
        phase3_ready = bool(avg >= 85 and avg_watch >= 0.90)
        phase_ready = phase2_ready

        return {
            "level": level,
            "phase_ready": phase_ready,
            "phase2_ready": phase2_ready,
            "phase3_ready": phase3_ready,
            "model_version": "rule-v1",
        }

    pred = model.predict(x)[0]
    level = labels_map.get(int(pred), str(pred))

    avg = _to_float(f.get("avg_score", 0.0))
    avg_watch = normalize_avg_watch(_to_float(f.get("avg_watch", 0.0)))
    phase2_ready = bool(avg >= 75 and avg_watch >= 0.85)
    phase3_ready = bool(avg >= 85 and avg_watch >= 0.90)
    phase_ready = phase2_ready

    return {
        "level": level,
        "phase_ready": phase_ready,
        "phase2_ready": phase2_ready,
        "phase3_ready": phase3_ready,
        "model_version": version,
    }


# =========================
# NEW: Phase2 LLM Endpoints (Ollama)
# =========================

OLLAMA_URL = "http://127.0.0.1:11434/api/generate"
OLLAMA_MODEL = "llama3.2:3b"


def call_ollama_json(prompt: str) -> dict:
    
    try:
        r = requests.post(
            OLLAMA_URL,
            json={
                "model": OLLAMA_MODEL,
                "prompt": prompt,
                "stream": False,
                "format": "json",
                "options": {"temperature": 0, "top_p": 0.9},
            },
            timeout=300,
        )
    except Exception as e:
        raise HTTPException(500, f"Ollama connection failed: {e}")

    if r.status_code != 200:
        raise HTTPException(500, f"Ollama error {r.status_code}: {r.text[:400]}")

    data = r.json()
    text = (data.get("response") or "").strip()

    try:
        return json.loads(text)
    except Exception as e:
        raise HTTPException(500, f"Invalid JSON text from model: {e}. Raw: {text[:400]}")


class Phase2GenerateReq(BaseModel):
    role_key: str
    path_title: str
    playlists: List[Dict[str, Any]]
    base_coins: int = 2000


@app.post("/phase2/generate")
def phase2_generate(req: Phase2GenerateReq):
    playlists_txt = ""
    for p in req.playlists[:12]:
        name = p.get("name", "")
        desc = p.get("description", "")
        vids = p.get("video_titles", [])
        if isinstance(vids, list) and len(vids) > 0:
            vids = vids[:10]
            playlists_txt += f"\n- {name}: {desc}\n  videos: {vids}\n"
        else:
            playlists_txt += f"\n- {name}: {desc}\n"


    prompt = f"""
You are a senior mentor. Create a REAL Phase-2 capstone project for role_key="{req.role_key}".
The student finished learning path: "{req.path_title}" and these playlists:

{playlists_txt}

You MUST output STRICT JSON ONLY with schema:
{{
  "title": "...",
  "description": "...",
  "stack": ["...","..."],
  "scope": {{
    "must_have": ["...","...","..."],
    "nice_to_have": ["...","..."]
  }},
  "deliverables": [
    "Git repo link (GitHub/GitLab) OR ZIP upload",
    "README with setup/run instructions",
    "Architecture diagram (image or markdown)",
    "Demo video link (3-7 minutes)",
    "Short report (decisions, tradeoffs, what you learned)"
  ],
  "milestones": [
    {{
      "id": 1,
      "title": "....",
      "deliverable": "....",
      "acceptance": ["...","...","..."],
      "coins": 1500
    }}
  ],
  "rubric": [
    {{"criterion":"Architecture & code quality","weight":30,"how_to_check":"Review structure, readability, patterns, error handling"}},
    {{"criterion":"Correctness & evaluation","weight":30,"how_to_check":"Reproduce results, tests/metrics, sanity checks"}},
    {{"criterion":"Deployment & usability","weight":25,"how_to_check":"App/API runs, docs, demo shows working system"}},
    {{"criterion":"Documentation & communication","weight":15,"how_to_check":"README, report, clear explanations"}}
  ],
  "pass_score": 70
}}

Hard rules:
- Exactly 8 milestones (NOT quizzes, NOT MCQ).
- Each milestone MUST require a concrete submission (repo link, zip, screenshot, endpoint URL, notebook).
- Each milestone acceptance MUST be a checklist.
- Sum of milestones[].coins MUST equal base_coins={req.base_coins}.
- Align the project tightly to the playlists.
Output JSON only. No markdown. No commentary.
"""
    project = call_ollama_json(prompt)


    try:
        coins_sum = sum(int(m.get("coins", 0)) for m in project.get("milestones", []))
        if coins_sum != int(req.base_coins):
            project["_warning"] = f"coins_sum={coins_sum} != base_coins={req.base_coins}"
    except Exception:
        pass

    return {"ok": True, "project": project, "model": OLLAMA_MODEL}



class Phase2GradeReq(BaseModel):
    project: Dict[str, Any]
    answers: Dict[str, Any]


@app.post("/phase2/grade")
def phase2_grade(req: Phase2GradeReq):
    prompt = f"""
You are a strict reviewer. Grade the student's answers for a Phase2 project (legacy mode).
Return STRICT JSON ONLY with schema:
{{
  "score": 0-100,
  "decision": "PASS" | "NEEDS_FIX" | "FAIL",
  "feedback": "...",
  "fixes": ["...","...","..."]
}}

Project:
{json.dumps(req.project, ensure_ascii=False)}

Answers:
{json.dumps(req.answers, ensure_ascii=False)}

Rules:
- If evidence is weak or answers are missing -> FAIL.
- If partial / needs improvements -> NEEDS_FIX.
- If solid and meets rubric -> PASS.
Output JSON only.
"""
    grade = call_ollama_json(prompt)
    return {"ok": True, "grade": grade, "model": OLLAMA_MODEL}



class Phase2GradeProjectReq(BaseModel):
    project: Dict[str, Any]
    submission: Dict[str, Any]


@app.post("/phase2/grade_project")
def phase2_grade_project(req: Phase2GradeProjectReq):
    prompt = f"""
You are a strict technical reviewer. Grade the student's Phase2 capstone submission.
Return STRICT JSON ONLY with schema:
{{
  "score": 0-100,
  "decision": "PASS" | "NEEDS_FIX" | "FAIL",
  "feedback": "...",
  "fixes": ["...","...","..."]
}}

Project:
{json.dumps(req.project, ensure_ascii=False)}

Submission (repo/zip summary):
{json.dumps(req.submission, ensure_ascii=False)}

Rules:
- Base your judgment on rubric + milestone acceptance.
- If README is missing or project cannot be run from instructions -> at most NEEDS_FIX.
- If evidence is too weak (no code/manifest, empty readme) -> FAIL.
Output JSON only.
- You MUST grade based on the project's role_key/path_id. The submission must match the required evidence for that path.
- You MUST check milestone acceptance. If there is no evidence/mapping to milestones in README/snippets/manifest, return FAIL with score <= 30.
- If submission looks like dataset/documents only (no entrypoints, no requirements, no app hints), return FAIL with score <= 20.
"""
    
    out = call_ollama_json(prompt)
    return {"ok": True, "grade": out, "model": OLLAMA_MODEL}


class Phase2GradeProjectV2Req(BaseModel):
    project: Dict[str, Any]
    submission: Dict[str, Any]


@app.post("/phase2/grade_project_v2")
def phase2_grade_project_v2(req: Phase2GradeProjectV2Req):
    evidence = build_evidence(req.project, req.submission)


    def fallback_grade():
        if not evidence.get("ok"):
            return {
                "score": 10,
                "decision": "FAIL",
                "feedback": f"Evidence build failed: {evidence.get('error')}",
                "issues": [{"severity": "error", "file": "", "line": 0, "title": "Evidence failed", "details": str(evidence)}],
                "fixes": ["Re-submit with a valid ZIP that unzips correctly and contains the project files."],
            }

        static_issues = evidence.get("static_issues") or []
        has_error = any(i.get("severity") == "error" for i in static_issues)

        ocr_findings = ((evidence.get("vision") or {}).get("ocr_findings") or [])
        ocr_bad = any(
            any(x in (f.get("hits") or []) for x in ["fatal", "error", "warning"])
            for f in ocr_findings if isinstance(f, dict)
        )

        runtime_checks = ((evidence.get("runtime") or {}).get("checks") or [])
        http_500 = any((c.get("status") == 500) for c in runtime_checks if isinstance(c, dict))

        if has_error or ocr_bad or http_500:
            return {
                "score": 40,
                "decision": "NEEDS_FIX",
                "feedback": "Evidence shows runtime/static issues (lint/runtime/OCR). Fix errors and re-submit.",
                "issues": static_issues[:20],
                "fixes": ["Fix PHP lint/runtime errors.", "Ensure homepage loads without warnings.", "Re-submit after confirming it runs locally."],
            }

        return {"score": 70, "decision": "PASS", "feedback": "Evidence looks healthy.", "issues": [], "fixes": []}


    try:
        prompt = f"""
You are a strict technical reviewer. Grade the student's Phase2 capstone submission using evidence.
Return STRICT JSON ONLY with schema:
{{
  "score": 0-100,
  "decision": "PASS" | "NEEDS_FIX" | "FAIL",
  "feedback": "...",
  "issues": [
    {{"severity":"error|warn|info","file":"...","line":0,"title":"...","details":"..."}}
  ],
  "fixes": ["...","...","..."]
}}

Project:
{json.dumps(req.project, ensure_ascii=False)}

Submission summary (excluding artifact_dir_abs):
{json.dumps({k: v for k, v in (req.submission or {}).items() if k != "artifact_dir_abs"}, ensure_ascii=False)}

Evidence:
{json.dumps(evidence, ensure_ascii=False)}
"""
        grade = call_ollama_json(prompt)
        return {"ok": True, "grade": grade, "evidence": evidence, "model": OLLAMA_MODEL}
    except Exception as e:
        return {
            "ok": True,
            "grade": fallback_grade(),
            "evidence": evidence,
            "model": "fallback-no-ollama",
            "warning": str(e)[:300],
        }


class Phase2ReviewReq(BaseModel):
    base_coins: int = 2000
    pass_score: int = 70
    repo_url: str = ""
    notes: str = ""
    checks: Dict[str, Any] = Field(default_factory=dict)


@app.post("/phase2/review")
def phase2_review(req: Phase2ReviewReq):
    checks = req.checks or {}
    prompt = f"""
You are a strict senior code reviewer for a student capstone (Phase 2).
The student submits either a Repo URL and/or a ZIP.

We already scanned the ZIP and got these checks as JSON:
{json.dumps(checks, ensure_ascii=False)}

Repo URL: {req.repo_url}
Student notes:
{req.notes}

Your job:
- Produce a realistic review as if you will accept/reject the submission.
- Score from 0 to 100 based on completeness, evidence of real implementation, and deliverables.
- If ZIP/Repo missing key items, score must drop.
- Prefer "real-world" criteria: runnable project, README, setup, demo, evidence of model/training/evaluation, deployment.

Return STRICT JSON ONLY with schema:
{{
  "score": 0-100,
  "feedback": "short but actionable summary",
  "fixes": ["bullet1","bullet2","bullet3","... up to 10"]
}}

Rules:
- If no README => at most 60.
- If neither repo_url nor zip_uploaded => 0.
- If zip_uploaded but zip_unzipped false => at most 40.
- If has_api_or_app is false and no entrypoints => at most 55.
- If files_count < 5 => at most 35.
Only report issues that are explicitly present in Evidence.static_issues, Evidence.runtime, or Evidence.vision. Do NOT invent issues.
Output JSON only. No markdown.
"""
    review = call_ollama_json(prompt)

    try:
        s = int(review.get("score", 0))
        review["score"] = max(0, min(100, s))
        if not isinstance(review.get("fixes"), list):
            review["fixes"] = []
        review["feedback"] = str(review.get("feedback", "")).strip()
    except Exception:
        review = {
            "score": 0,
            "feedback": "Invalid AI review output.",
            "fixes": ["Re-submit with proper README and runnable project."],
        }

    return {"ok": True, "review": review, "model": OLLAMA_MODEL}


# =========================
# Phase 3 Orchestrator
# =========================




class Phase3ArchitectReq(BaseModel):
    title: str
    description: str
    constraints: Dict[str, Any] = Field(default_factory=dict)


@app.post("/phase3/architect")
def phase3_architect(req: Phase3ArchitectReq):
    prompt = f"""
You are an AI Project Architect.
Analyze the capstone description and produce:
1) architect meta: roles_needed, complexity_level, estimated_timeline_weeks, dependency_graph
2) tasks list with fields:
   task_code, role_key, role_name, description, skills (list), acceptance (list), dependencies (list of task_codes)

Return STRICT JSON ONLY with schema:
{{
  "architect": {{
    "roles_needed":[{{"role_key":"...","role_name":"...","why":"..."}}],
    "complexity_level":"beginner|intermediate|advanced",
    "estimated_timeline_weeks": 1-24,
    "dependency_graph":[["TASK_A","TASK_B"]]
  }},
  "tasks":[
    {{
      "task_code":"BE_API_01",
      "role_key":"backend_developer",
      "role_name":"Backend Developer",
      "description":"...",
      "skills":["php","mysql","rest"],
      "acceptance":["...","..."],
      "dependencies":["..."]
    }}
  ]
}}

Constraints:
{json.dumps(req.constraints, ensure_ascii=False)}

Title:
{req.title}

Description:
{req.description}
"""
    out = call_ollama_json(prompt)
    architect = out.get("architect") if isinstance(out, dict) else None
    tasks = out.get("tasks") if isinstance(out, dict) else None
    if not isinstance(architect, dict) or not isinstance(tasks, list):
        return {"ok": False, "error": "BAD_ARCHITECT_OUTPUT", "raw": out}
    return {"ok": True, "architect": architect, "tasks": tasks, "model": OLLAMA_MODEL}


class Phase3MatchReq(BaseModel):

    students: List[Dict[str, Any]]
    tasks: List[Dict[str, Any]]


@app.post("/phase3/match")
def phase3_match(req: Phase3MatchReq):
    return assign_students_to_tasks(req.students, req.tasks)


class SkillGapAnalyzeReq(BaseModel):
    user_id: Optional[int] = None
    target_role_key: Optional[str] = None
    student_skills: List[str] = Field(default_factory=list)


@app.post("/skill-gap/analyze")
def skill_gap_analyze(req: SkillGapAnalyzeReq):
    return analyze_skill_gap(
        user_id=req.user_id,
        target_role_key=req.target_role_key,
        student_skills=req.student_skills,
    )


class RecommendPlaylistsReq(BaseModel):
    user_id: Optional[int] = None
    role_key: str = ""
    path_id: Optional[int] = None
    skills: List[str] = Field(default_factory=list)
    missing_skills: List[str] = Field(default_factory=list)


@app.post("/recommend/playlists")
def recommend_playlists_endpoint(req: RecommendPlaylistsReq):
    return recommend_playlists(
        user_id=req.user_id,
        role_key=req.role_key,
        path_id=req.path_id,
        skills=req.skills,
        missing_skills=req.missing_skills,
    )


class MentorChatReq(BaseModel):
    user_id: int
    project_id: Optional[int] = None
    team_id: Optional[int] = None
    question: str
    chat_context: List[Dict[str, Any]] = Field(default_factory=list)


@app.post("/mentor/chat")
def mentor_chat_endpoint(req: MentorChatReq):
    return mentor_chat(
        user_id=req.user_id,
        project_id=req.project_id,
        team_id=req.team_id,
        question=req.question,
        chat_context=req.chat_context,
    )


class Phase3GradeTaskReq(BaseModel):
    project: Dict[str, Any]
    task: Dict[str, Any]
    submission: Dict[str, Any]



def phase3_gates(task: Dict[str, Any], evidence: Dict[str, Any]) -> Dict[str, Any] | None:
    """
    إذا رجعت dict => يعني FAIL/NEEDS_FIX فوري بدون LLM.
    إذا رجعت None => كمل تقييم عادي.
    """
    if not evidence.get("ok"):
        return {
            "score": 10,
            "decision": "FAIL",
            "feedback": f"Evidence build failed: {evidence.get('error')}",
            "issues": [{"severity": "error", "file": "submission.json", "line": 0, "title": "Evidence failed", "details": str(evidence)}],
            "fixes": ["Re-submit with a valid ZIP that unzips correctly and contains the required files."],
        }

    role_key = (task.get("role_key") or "").lower()
    role_name = (task.get("role_name") or "").lower()
    task_code = (task.get("task_code") or "").lower()

    root = Path(evidence.get("artifact_dir_abs", ""))
    app_root = Path(evidence.get("app_root", ""))
    stack = (evidence.get("stack") or "").lower()


    is_front = ("front" in role_key) or ("front" in role_name) or task_code.startswith("fe_")
    if is_front:

        if stack == "php":
            return {
                "score": 20,
                "decision": "FAIL",
                "feedback": "Wrong submission type: this is a PHP backend, but the task is Frontend.",
                "issues": [{"severity": "error", "file": str(app_root), "line": 0, "title": "Wrong stack", "details": "Frontend task requires frontend app (package.json / frontend files)."}],
                "fixes": ["Submit the frontend project (React/Vue/plain JS) with package.json or index.html."],
            }
        if not ((app_root / "package.json").exists() or (app_root / "index.html").exists() or list(root.rglob("package.json"))):
            return {
                "score": 30,
                "decision": "FAIL",
                "feedback": "Missing frontend artifacts (package.json/index.html).",
                "issues": [{"severity": "error", "file": str(root), "line": 0, "title": "Missing frontend files", "details": "No package.json or index.html found."}],
                "fixes": ["Include frontend source with runnable entry (package.json or index.html)."],
            }


    is_back = ("back" in role_key) or ("back" in role_name) or task_code.startswith("be_")
    if is_back:
        if stack != "php":
            return {
                "score": 30,
                "decision": "FAIL",
                "feedback": "Wrong submission type: backend task expects PHP project.",
                "issues": [{"severity": "error", "file": str(app_root), "line": 0, "title": "Wrong stack", "details": "Expected PHP backend (index.php)."}],
                "fixes": ["Submit the PHP backend project with index.php and required endpoints."],
            }

    return None


@app.post("/phase3/grade_task")
def phase3_grade_task(req: Phase3GradeTaskReq):
    evidence = build_evidence(req.project, req.submission)


    gate = phase3_gates(req.task, evidence)
    if gate is not None:
        return {"ok": True, "grade": gate, "evidence": evidence, "model": OLLAMA_MODEL}


    def fallback_grade() -> Dict[str, Any]:
        if not evidence.get("ok"):
            return {
                "score": 10,
                "decision": "FAIL",
                "feedback": f"Evidence build failed: {evidence.get('error')}",
                "issues": [{"severity": "error", "file": "submission.json", "line": 0, "title": "Evidence failed", "details": str(evidence)}],
                "fixes": ["Re-submit with a valid ZIP that unzips correctly and contains the required files."],
            }

        static_issues = evidence.get("static_issues") or []
        has_error = any((i.get("severity") == "error") for i in static_issues if isinstance(i, dict))

        runtime_checks = ((evidence.get("runtime") or {}).get("checks") or [])
        http_500 = any((c.get("status") == 500) for c in runtime_checks if isinstance(c, dict))

        if has_error or http_500:
            return {
                "score": 45,
                "decision": "NEEDS_FIX",
                "feedback": "Evidence shows runtime/static issues. Fix errors and re-submit.",
                "issues": static_issues[:20],
                "fixes": ["Fix PHP lint/runtime errors.", "Ensure required page/API works without warnings."],
            }


        return {
            "score": 55,
            "decision": "NEEDS_FIX",
            "feedback": "Runtime seems OK, but this does NOT prove the submission matches the required task. Missing acceptance verification.",
            "issues": [{
                "severity": "warn",
                "file": "submission.json",
                "line": 0,
                "title": "No acceptance proof",
                "details": "Submission can run, but may not match task requirements.",
            }],
            "fixes": ["Submit the correct project for this task and include required artifacts per acceptance criteria."],
        }


    try:
        prompt = f"""
You are a strict task reviewer.
Grade ONLY this specific task submission using evidence. Compare against acceptance criteria.
Return STRICT JSON ONLY with schema:
{{
  "score": 0-100,
  "decision": "PASS" | "NEEDS_FIX" | "FAIL",
  "feedback": "...",
  "issues": [
    {{"severity":"error|warn|info","file":"...","line":0,"title":"...","details":"..."}}
  ],
  "fixes": ["...","..."]
}}

Project:
{json.dumps(req.project, ensure_ascii=False)}

Task:
{json.dumps(req.task, ensure_ascii=False)}

Acceptance criteria:
{json.dumps(req.task.get("acceptance", []), ensure_ascii=False)}

Submission (excluding artifact_dir_abs):
{json.dumps({k: v for k, v in (req.submission or {}).items() if k != "artifact_dir_abs"}, ensure_ascii=False)}

Evidence:
{json.dumps(evidence, ensure_ascii=False)}
"""
        grade = call_ollama_json(prompt)


        if not isinstance(grade, dict) or "score" not in grade or "decision" not in grade:
            grade = fallback_grade()

        return {"ok": True, "grade": grade, "evidence": evidence, "model": OLLAMA_MODEL}

    except Exception as e:
        return {"ok": True, "grade": fallback_grade(), "evidence": evidence, "model": OLLAMA_MODEL, "note": str(e)[:300]}


class Phase3FinalReportReq(BaseModel):
    project: Dict[str, Any]
    tasks: List[Dict[str, Any]]
    submissions: List[Dict[str, Any]]


@app.post("/phase3/final_report")
def phase3_final_report(req: Phase3FinalReportReq):
    prompt = f"""
You are a company-facing AI program manager and technical lead.
Produce a FINAL REPORT for the capstone project based on tasks, assignments, and submission grades.
Return STRICT JSON ONLY with schema:
{{
  "executive_summary":"...",
  "overall_readiness":"READY|NEEDS_FIX|NOT_READY",
  "highlights":["..."],
  "risks":["..."],
  "missing_parts":["..."],
  "task_breakdown":[
    {{
      "task_code":"...",
      "role_name":"...",
      "status":"PASS|NEEDS_FIX|FAIL|NO_SUBMISSION",
      "score":0-100,
      "key_issues":["..."],
      "recommended_actions":["..."]
    }}
  ],
  "next_steps":["..."]
}}

Project:
{json.dumps(req.project, ensure_ascii=False)}

Tasks:
{json.dumps(req.tasks, ensure_ascii=False)}

Submissions:
{json.dumps(req.submissions, ensure_ascii=False)}
"""
    out = call_ollama_json(prompt)
    if not isinstance(out, dict) or "executive_summary" not in out:
        out = {
            "executive_summary": "Final report generation failed or returned invalid JSON.",
            "overall_readiness": "NOT_READY",
            "highlights": [],
            "risks": ["AI report invalid output"],
            "missing_parts": [],
            "task_breakdown": [],
            "next_steps": ["Re-run finalize after ensuring AI model is reachable."],
        }
    return {"ok": True, "report": out, "model": OLLAMA_MODEL}


class Level2GenerateReq(BaseModel):
    project_id: int
    student_id: int
    task_id: int = 0
    role_key: str = "general"
    project_title: str = ""
    project_description: str = ""


@app.post("/phase3/level2/generate")
def phase3_level2_generate(req: Level2GenerateReq):
    ctx = context_agent(req.project_title, req.project_description, req.role_key)

    retrieved_text = retrieval_agent(
        f"{req.project_title}\n{req.project_description}\n{req.role_key}\npost delivery issue",
        n_results=3
    )

    gen_ctx = challenge_generator_agent(ctx, retrieved_text)

    prompt = f"""
You are generating a realistic post-delivery simulation challenge for a student.

Project title:
{gen_ctx["base_context"]["project_title"]}

Project description:
{gen_ctx["base_context"]["project_description"]}

Student role:
{gen_ctx["base_context"]["role_key"]}

Retrieved context:
{gen_ctx["retrieved_text"]}

Return STRICT JSON ONLY with this schema:
{{
  "challenge_type": "bug|performance_issue|deployment_problem|security_alert|client_change_request",
  "title": "string",
  "scenario_text": "string",
  "required_actions": ["string", "string", "string"],
  "deliverables": ["string", "string", "string"],
  "rubric": {{
    "analysis": 30,
    "technical_fix": 40,
    "validation": 20,
    "communication": 10
  }},
  "difficulty": "easy|medium|hard"
}}

Rules:
- The challenge must feel like a real post-delivery issue.
- Keep it grounded in the project description and retrieved context.
- Do not invent unrelated systems.
- Output JSON only.
""".strip()

    out = call_ollama_json(prompt)

    if not isinstance(out, dict):
        out = {
            "challenge_type": "bug",
            "title": "Post-Delivery Bug Fix Challenge",
            "scenario_text": "A new issue appeared after delivery and needs investigation.",
            "required_actions": [
                "Analyze the root cause",
                "Propose a fix",
                "Explain validation steps",
            ],
            "deliverables": [
                "Technical explanation",
                "Fix plan",
                "Validation steps",
            ],
            "rubric": {
                "analysis": 30,
                "technical_fix": 40,
                "validation": 20,
                "communication": 10,
            },
            "difficulty": "medium",
        }

    return {"ok": True, "challenge": out, "model": OLLAMA_MODEL}


class Level2EvaluateReq(BaseModel):
    challenge: Dict[str, Any]
    submission: Dict[str, Any]
    project: Dict[str, Any]


@app.post("/phase3/level2/evaluate")
def phase3_level2_evaluate(req: Level2EvaluateReq):
    retrieved_text = retrieval_agent(
        f"{req.project.get('title','')} {req.project.get('description','')} {req.submission.get('submission_text','')}",
        n_results=3
    )

    eval_ctx = evaluation_agent(req.project, req.challenge, req.submission, retrieved_text)

    prompt = f"""
You are a strict evaluator for a post-delivery simulation challenge.

Project:
{json.dumps(eval_ctx["project"], ensure_ascii=False)}

Challenge:
{json.dumps(eval_ctx["challenge"], ensure_ascii=False)}

Student submission:
{json.dumps(eval_ctx["submission"], ensure_ascii=False)}

Retrieved context:
{eval_ctx["retrieved_text"]}

Return STRICT JSON ONLY with this schema:
{{
  "score": 0-100,
  "decision": "PASS|NEEDS_FIX|FAIL",
  "feedback_text": "string",
  "rubric_scores": {{
    "analysis": 0,
    "technical_fix": 0,
    "validation": 0,
    "communication": 0
  }},
  "readiness": {{
    "incident_response": "weak|medium|good",
    "technical_ownership": "weak|medium|good"
  }}
}}

Rules:
- Be fair and practical.
- Use the retrieved context in your judgment.
- If the student provides a reasonable technical explanation, do not return zero.
- Use zero only when the submission is actually empty or completely unrelated.
- Output JSON only.
""".strip()

    out = call_ollama_json(prompt)

    if not isinstance(out, dict) or "score" not in out:
        out = {
            "score": 55,
            "decision": "NEEDS_FIX",
            "feedback_text": "The submission exists, but the evaluation output was incomplete. Please improve the solution and re-submit.",
            "rubric_scores": {
                "analysis": 15,
                "technical_fix": 20,
                "validation": 10,
                "communication": 10,
            },
            "readiness": {
                "incident_response": "medium",
                "technical_ownership": "medium",
            },
        }

    try:
        sub_text = (req.submission.get("submission_text") or "").strip()
        if sub_text and isinstance(out, dict):
            if float(out.get("score", 0)) == 0:
                out["score"] = 45
                out["decision"] = "NEEDS_FIX"
                out["feedback_text"] = "The submission contains an attempt, but it needs more technical detail, clearer fix steps, and stronger validation evidence."
                out["rubric_scores"] = {
                    "analysis": 15,
                    "technical_fix": 15,
                    "validation": 5,
                    "communication": 10,
                }
                out["readiness"] = {
                    "incident_response": "medium",
                    "technical_ownership": "medium",
                }
    except Exception:
        pass

    return {"ok": True, "evaluation": out, "model": OLLAMA_MODEL}


class Level2IndexReq(BaseModel):
    project_id: int
    student_id: int
    task_id: int = 0
    role_key: str = "general"
    project_title: str = ""
    project_description: str = ""
    submission_text: str = ""


@app.post("/phase3/level2/index")
def phase3_level2_index(req: Level2IndexReq):
    docs = []

    if req.project_title or req.project_description:
        docs.append({
            "id": f"project-{req.project_id}",
            "text": f"Project Title: {req.project_title}\nProject Description: {req.project_description}",
            "meta": {
                "type": "project",
                "project_id": req.project_id,
                "student_id": req.student_id,
                "task_id": req.task_id,
                "role_key": req.role_key,
            },
        })

    if req.submission_text.strip():
        docs.append({
            "id": f"submission-{req.project_id}-{req.student_id}",
            "text": req.submission_text,
            "meta": {
                "type": "submission",
                "project_id": req.project_id,
                "student_id": req.student_id,
                "task_id": req.task_id,
                "role_key": req.role_key,
            },
        })

    for d in docs:
        upsert_document(d["id"], d["text"], d["meta"])

    return {"ok": True, "indexed": len(docs)}

# =========================
# Job Simulator
# =========================

class JobSimReq(BaseModel):
    cv_path: str
    role_key: str
    project_url: str = ""
    progress_score: float = 50.0


@app.post("/job_simulator/analyze")
def job_simulator_analyze(req: JobSimReq):
    text = extract_text_from_file(req.cv_path)
    cv_data = score_cv(text, req.role_key)
    project_data = score_project(req.project_url, req.role_key)

    match_data = semantic_skill_match(req.role_key, cv_data["skills"])
    skill_match_score = match_data["score"]
    progress_score = req.progress_score

    found = cv_data["skills"]
    missing_skills = match_data["missing"]
    missing_skills_count = len(missing_skills)

    certificates_count = 0
    courses_completed = 0

    readiness = compute_readiness(
        cv_data["cv_score"],
        project_data["project_score"],
        skill_match_score,
        progress_score,
        missing_skills_count=missing_skills_count,
        certificates_count=certificates_count,
        courses_completed=courses_completed
    )

    roadmap = generate_roadmap(req.role_key, missing_skills)

    return {
        "ok": True,
        "cv_score": cv_data["cv_score"],
        "project_score": project_data["project_score"],
        "matched_skills": match_data["matched"],
        "skill_match_score": skill_match_score,
        "progress_score": progress_score,
        "final_score": readiness["final_score"],
        "verdict": readiness["verdict"],
        "cv_strengths": cv_data["strengths"],
        "cv_weaknesses": cv_data["weaknesses"],
        "project_strengths": project_data["strengths"],
        "project_weaknesses": project_data["weaknesses"],
        "skills": found,
        "missing_skills": missing_skills,
        "roadmap": roadmap
    }




if __name__ == "__main__":

    uvicorn.run(app, host="127.0.0.1", port=5006)
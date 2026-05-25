from pathlib import Path
import joblib
import numpy as np

ROOT = Path(__file__).resolve().parent.parent
READINESS_MODEL_PATH = ROOT / "readiness_model.joblib"

_model = None

def load_readiness_model():
    global _model
    if _model is not None:
        return _model

    if READINESS_MODEL_PATH.exists():
        _model = joblib.load(READINESS_MODEL_PATH)

    return _model

def compute_readiness(
    cv_score,
    project_score,
    skill_match_score,
    progress_score,
    missing_skills_count=0,
    certificates_count=0,
    courses_completed=0
):
    model = load_readiness_model()

    if model is not None:
        X = np.array([[
            cv_score,
            project_score,
            skill_match_score,
            progress_score,
            missing_skills_count,
            certificates_count,
            courses_completed
        ]], dtype=float)

        pred = float(model.predict(X)[0])
        final_score = round(max(0, min(pred, 100)), 2)
    else:
        final_score = round(
            cv_score * 0.20 +
            project_score * 0.25 +
            skill_match_score * 0.30 +
            progress_score * 0.20 +
            certificates_count * 0.8 +
            courses_completed * 0.5 -
            missing_skills_count * 2.5,
            2
        )
        final_score = max(0, min(100, final_score))

    if final_score < 50:
        verdict = "Not Ready"
    elif final_score < 70:
        verdict = "Needs Improvement"
    elif final_score < 85:
        verdict = "Internship Ready"
    else:
        verdict = "Junior Ready"

    return {
        "final_score": final_score,
        "verdict": verdict
    }
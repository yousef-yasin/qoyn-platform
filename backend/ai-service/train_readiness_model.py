import pandas as pd
from sklearn.ensemble import RandomForestRegressor
import joblib

df = pd.read_csv("readiness_dataset.csv")

X = df[
    [
        "cv_score",
        "project_score",
        "skill_match_score",
        "progress_score",
        "missing_skills_count",
        "certificates_count",
        "courses_completed"
    ]
]

y = df["target_score"]

model = RandomForestRegressor(
    n_estimators=200,
    random_state=42
)

model.fit(X, y)

joblib.dump(model, "readiness_model.joblib")
print("saved readiness_model.joblib")
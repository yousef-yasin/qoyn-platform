import random
import pandas as pd

rows = []

for _ in range(5000):
    cv_score = random.randint(20, 95)
    project_score = random.randint(10, 95)
    skill_match_score = random.randint(15, 95)
    progress_score = random.randint(10, 100)

    missing_skills_count = random.randint(0, 6)
    certificates_count = random.randint(0, 8)
    courses_completed = random.randint(0, 12)

    target_score = (
        cv_score * 0.20 +
        project_score * 0.25 +
        skill_match_score * 0.30 +
        progress_score * 0.20 +
        certificates_count * 0.8 +
        courses_completed * 0.5 -
        missing_skills_count * 2.5
    )

    target_score = max(0, min(100, round(target_score, 2)))

    rows.append({
        "cv_score": cv_score,
        "project_score": project_score,
        "skill_match_score": skill_match_score,
        "progress_score": progress_score,
        "missing_skills_count": missing_skills_count,
        "certificates_count": certificates_count,
        "courses_completed": courses_completed,
        "target_score": target_score
    })

df = pd.DataFrame(rows)
df.to_csv("readiness_dataset.csv", index=False)
print("saved readiness_dataset.csv with", len(df), "rows")
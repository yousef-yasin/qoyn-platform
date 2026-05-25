from sentence_transformers import SentenceTransformer, util

model = SentenceTransformer("all-MiniLM-L6-v2")

ROLE_REQUIREMENTS = {
    "ml_engineer": ["python", "sql", "machine learning", "pandas", "numpy"],
    "fullstack": ["html", "css", "javascript", "php", "mysql"],
    "pentester": ["networking", "security", "sql", "linux"],
    "algorithm_engineer": ["python", "numpy", "machine learning"]
}

def semantic_skill_match(role_key: str, student_skills: list[str]):
    required = ROLE_REQUIREMENTS.get(role_key, [])
    if not required or not student_skills:
        return {
            "score": 0.0,
            "matched": [],
            "missing": required
        }

    req_emb = model.encode(required, convert_to_tensor=True)
    stu_emb = model.encode(student_skills, convert_to_tensor=True)

    matched = []
    missing = []

    for i, req in enumerate(required):
        sims = util.cos_sim(req_emb[i], stu_emb)[0]
        best = float(sims.max())
        if best >= 0.60:
            matched.append(req)
        else:
            missing.append(req)

    score = round((len(matched) / max(len(required), 1)) * 100, 2)

    return {
        "score": score,
        "matched": matched,
        "missing": missing
    }
from pathlib import Path
import re
import fitz
from docx import Document
import spacy

nlp = spacy.load("en_core_web_sm")

KNOWN_SKILLS = [
    "python", "sql", "machine learning", "deep learning",
    "javascript", "html", "css", "php", "mysql",
    "tensorflow", "scikit-learn", "pandas", "numpy",
    "networking", "security", "rest api", "fastapi",
    "flask", "docker", "git", "github", "linux"
]

ROLE_REQUIREMENTS = {
    "ml_engineer": ["python", "sql", "machine learning", "pandas", "numpy"],
    "fullstack": ["html", "css", "javascript", "php", "mysql"],
    "pentester": ["networking", "security", "sql", "linux"],
    "algorithm_engineer": ["python", "numpy", "machine learning"]
}

def extract_text_from_file(path: str) -> str:
    p = Path(path)
    if not p.exists():
        return ""

    ext = p.suffix.lower()

    if ext == ".pdf":
        text = ""
        doc = fitz.open(path)
        for page in doc:
            text += page.get_text()
        return text.strip()

    if ext == ".docx":
        doc = Document(path)
        return "\n".join([para.text for para in doc.paragraphs]).strip()

    if ext == ".doc":
        return ""

    return ""

def extract_skills_spacy(text: str):
    t = text.lower()
    found = set()

    for skill in KNOWN_SKILLS:
        if skill in t:
            found.add(skill)

    doc = nlp(text)
    for ent in doc.ents:
        val = ent.text.strip().lower()
        if val in KNOWN_SKILLS:
            found.add(val)

    return sorted(found)

def extract_sections(text: str):
    lower = text.lower()
    return {
        "has_education": "education" in lower,
        "has_projects": "project" in lower or "projects" in lower,
        "has_experience": "experience" in lower,
        "has_github": "github" in lower,
        "has_linkedin": "linkedin" in lower,
    }

def score_cv(text: str, role_key: str):
    skills = extract_skills_spacy(text)
    sections = extract_sections(text)

    required = ROLE_REQUIREMENTS.get(role_key, [])
    matched_required = [s for s in required if s in skills]

    skill_score = (len(matched_required) / max(len(required), 1)) * 70
    section_score = 0

    if sections["has_projects"]:
        section_score += 10
    if sections["has_experience"]:
        section_score += 10
    if sections["has_github"]:
        section_score += 5
    if sections["has_linkedin"]:
        section_score += 5

    final_cv_score = round(min(skill_score + section_score, 100), 2)

    strengths = []
    weaknesses = []

    if matched_required:
        strengths.append(f"Matched required skills: {', '.join(matched_required)}")

    missing_required = [s for s in required if s not in skills]
    if missing_required:
        weaknesses.append(f"Missing role-related skills: {', '.join(missing_required)}")

    if not sections["has_projects"]:
        weaknesses.append("CV does not clearly include a projects section")
    if not sections["has_github"]:
        weaknesses.append("No GitHub mentioned in CV")

    return {
        "extracted_text": text,
        "skills": skills,
        "cv_score": final_cv_score,
        "strengths": strengths,
        "weaknesses": weaknesses,
        "missing_required_skills": missing_required
    }
import os
import pandas as pd
import pymysql
from dotenv import load_dotenv

load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

ESCO_REL = os.path.join(os.path.dirname(__file__), "../data/esco/occupationSkillRelations_en.csv")

# ربط Roles داخل مشروعك بمهن ESCO الرسمية (بالاسم)
ROLE_MAP = {
    "ml_engineer": {
        "role_name": "🤖 Machine Learning Engineer",
        "occupations": ["artificial intelligence engineer", "data scientist"]
    },
    "algorithm_engineer": {
        "role_name": "🧠 Algorithm Engineer",
        "occupations": ["computer scientist"]
    },
    "fullstack": {
        "role_name": "🌐 Full Stack Developer",
        "occupations": ["software developer", "web developer"]
    },
    "pentester": {
        "role_name": "🔐 Penetration Tester / Cybersecurity",
        "occupations": ["ICT security technician", "ICT security administrator"]
    }
}

def weight_from_relation(relation_type: str) -> int:
    # ESCO غالباً يعطي essential/optional
    if not relation_type:
        return 1
    r = relation_type.lower().strip()
    if "essential" in r:
        return 5
    if "optional" in r:
        return 3
    return 2

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor
)
cur = conn.cursor()

print("Reading ESCO occupation-skill relations...")
df = pd.read_csv(ESCO_REL)

# نخلي labels lowercase عشان المطابقة
df["occupationLabel_lc"] = df["occupationLabel"].astype(str).str.lower()

# --- 1) إدخال roles داخل career_roles ---
print("Inserting roles into career_roles...")
for role_key, info in ROLE_MAP.items():
    cur.execute("""
        INSERT IGNORE INTO career_roles (role_key, role_name, role_desc)
        VALUES (%s, %s, %s)
    """, (role_key, info["role_name"], "ESCO-based role mapping"))
conn.commit()

# --- 2) لكل role: نجمع skills من occupations المرتبطة ---
print("Building role_skills from ESCO...")
for role_key, info in ROLE_MAP.items():
    # جلب role_id
    cur.execute("SELECT id FROM career_roles WHERE role_key=%s", (role_key,))
    role_id = cur.fetchone()["id"]

    occ_list = [o.lower() for o in info["occupations"]]

    rel = df[df["occupationLabel_lc"].isin(occ_list)].copy()

    # إذا ما لقى علاقات، نطبع تحذير
    if rel.empty:
        print(f"⚠️ No relations found for {role_key}: {info['occupations']}")
        continue

    # كل skillLabel (مع relationType)
    for _, row in rel.iterrows():
        skill_label = row.get("skillLabel")
        relation_type = row.get("relationType")

        if pd.isna(skill_label):
            continue

        skill_label = str(skill_label).strip()
        if not skill_label:
            continue

        # نطابق skill_name مباشرة (لأن skills table فيها skill_name من ESCO)
        cur.execute("SELECT id FROM skills WHERE skill_name=%s LIMIT 1", (skill_label,))
        s = cur.fetchone()
        if not s:
            # لو ما انوجد بالاسم (نادر) نتجاهله
            continue

        skill_id = s["id"]
        w = weight_from_relation(str(relation_type) if not pd.isna(relation_type) else "")

        cur.execute("""
            INSERT IGNORE INTO role_skills (role_id, skill_id, weight, source)
            VALUES (%s, %s, %s, %s)
        """, (role_id, skill_id, w, "ESCO"))

    conn.commit()
    print(f"✅ role_skills built for {role_key} ({info['role_name']})")

conn.close()
print("✅ Done: career_roles + role_skills from ESCO.")
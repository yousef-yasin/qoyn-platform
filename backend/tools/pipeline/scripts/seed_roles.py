import os
import json
import pymysql
from dotenv import load_dotenv

# تحميل بيانات .env
load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")
# الاتصال بقاعدة البيانات
conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset='utf8mb4',
    cursorclass=pymysql.cursors.DictCursor
)

cursor = conn.cursor()

# قراءة JSON
data_path = os.path.join(os.path.dirname(__file__), "../data/roles_seed.json")

with open(data_path, "r", encoding="utf-8") as f:
    seed = json.load(f)

# إدخال skills
print("Inserting skills...")
for skill_key, skill_name, category in seed["skills"]:
    cursor.execute("""
        INSERT IGNORE INTO skills (skill_key, skill_name, category)
        VALUES (%s, %s, %s)
    """, (skill_key, skill_name, category))

conn.commit()

# إدخال roles
print("Inserting roles...")
for role in seed["roles"]:
    cursor.execute("""
        INSERT IGNORE INTO career_roles (role_key, role_name, role_desc)
        VALUES (%s, %s, %s)
    """, (role["role_key"], role["role_name"], role["role_name"]))

conn.commit()

# إدخال role_skills + path + references
print("Linking skills and inserting paths...")
for role in seed["roles"]:

    # جلب role_id
    cursor.execute("SELECT id FROM career_roles WHERE role_key=%s", (role["role_key"],))
    role_id = cursor.fetchone()["id"]

    # إدخال role_skills
    for skill_key, weight in role["skills"]:
        cursor.execute("SELECT id FROM skills WHERE skill_key=%s", (skill_key,))
        skill_id = cursor.fetchone()["id"]

        cursor.execute("""
            INSERT IGNORE INTO role_skills (role_id, skill_id, weight)
            VALUES (%s, %s, %s)
        """, (role_id, skill_id, weight))

    # إدخال path steps
    step_order = 1
    for title, desc in role["path_steps"]:
        cursor.execute("""
            INSERT INTO role_path_steps (role_id, step_order, step_title, step_desc)
            VALUES (%s, %s, %s, %s)
        """, (role_id, step_order, title, desc))
        step_order += 1

    # إدخال references
    for ref in role["references"]:
        cursor.execute("""
            INSERT INTO role_references (role_id, source_name, source_note)
            VALUES (%s, %s, %s)
        """, (role_id, ref["source"], ref["note"]))

conn.commit()
conn.close()

print("✅ Done seeding roles, skills, and paths successfully.")
import os
import pandas as pd
import pymysql
from dotenv import load_dotenv

# تحميل .env
load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

# اتصال DB
conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor
)
cursor = conn.cursor()

# مسار ملف ESCO skills
skills_path = os.path.join(
    os.path.dirname(__file__),
    "../data/esco/skills_en.csv"
)

print("Reading ESCO skills...")

df = pd.read_csv(skills_path)
print("Inserting skills into DB...")

for _, row in df.iterrows():
    preferred_label = row.get("preferredLabel")
    skill_type = row.get("skillType")

    # تجاهل الصفوف بدون اسم
    if pd.isna(preferred_label):
        continue

    preferred_label = str(preferred_label).strip()

    if not preferred_label:
        continue

    # تنظيف skill_type
    if pd.isna(skill_type):
        skill_type = None
    else:
        skill_type = str(skill_type)

    skill_key = preferred_label.lower().replace(" ", "_")

    cursor.execute("""
        INSERT IGNORE INTO skills (skill_key, skill_name, skill_type, source)
        VALUES (%s, %s, %s, %s)
    """, (skill_key, preferred_label, skill_type, "ESCO"))

conn.commit()
conn.close()

print("✅ Skills imported successfully from ESCO.")
import os
import pymysql
from dotenv import load_dotenv

load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor
)

cur = conn.cursor()

print("Loading role ↔ SOC mappings...")
cur.execute("""
    SELECT r.role_id, o.id as occupation_id
    FROM role_onet_occupations r
    JOIN onet_occupations o ON r.soc_code = o.soc_code
""")
role_occ = cur.fetchall()

inserted = 0

for row in role_occ:
    role_id = row["role_id"]
    occupation_id = row["occupation_id"]

    # جيب أهم items المرتبطة بالoccupation
    cur.execute("""
        SELECT item_id, importance, level
        FROM onet_occupation_items
        WHERE occupation_id = %s
    """, (occupation_id,))
    items = cur.fetchall()

    for it in items:
        item_id = it["item_id"]

        importance = float(it["importance"] or 0)
        level = float(it["level"] or 0)

        score = (importance * 0.7) + (level * 0.3)

        if score >= 4:
            weight = 5
        elif score >= 3:
            weight = 4
        elif score >= 2:
            weight = 3
        elif score >= 1:
            weight = 2
        else:
            weight = 1
        # حول ONET item → ESCO skill
        cur.execute("""
        SELECT id
        FROM skills
        WHERE source='ONET' AND skill_key = CONCAT('onet_item_', %s)
        LIMIT 1
        """, (item_id,))
        s = cur.fetchone()

        if not s:
         continue

        skill_id = s["id"]

        cur.execute("""
            INSERT INTO role_skills (role_id, skill_id, weight, source)
            VALUES (%s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE
            weight = GREATEST(weight, VALUES(weight))
        """, (role_id, skill_id, weight, "ONET"))

        inserted += 1

conn.commit()
conn.close()

print(f"✅ Inserted/Updated {inserted} role_skills from ONET.")
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
skipped_no_skill = 0

for row in role_occ:
    role_id = int(row["role_id"])
    occupation_id = int(row["occupation_id"])

    # get occupation items
    cur.execute("""
        SELECT item_id, importance, level
        FROM onet_occupation_items
        WHERE occupation_id = %s
    """, (occupation_id,))
    items = cur.fetchall()

    for it in items:
        item_id = int(it["item_id"])
        importance = float(it["importance"] or 0)
        level = float(it["level"] or 0)

        score = (importance * 0.7) + (level * 0.3)

        # convert score to discrete weight 1..5
        if score >= 4.0:
            weight = 5
        elif score >= 3.0:
            weight = 4
        elif score >= 2.0:
            weight = 3
        elif score >= 1.0:
            weight = 2
        else:
            weight = 1

        # DIRECT: ONET item -> skills table (you already inserted 120)
        cur.execute("""
            SELECT id
            FROM skills
            WHERE source='ONET' AND skill_key = CONCAT('onet_item_', %s)
            LIMIT 1
        """, (item_id,))
        s = cur.fetchone()

        if not s:
            skipped_no_skill += 1
            continue

        skill_id = int(s["id"])

        cur.execute("""
            INSERT INTO role_skills (role_id, skill_id, weight, source)
            VALUES (%s, %s, %s, 'ONET')
            ON DUPLICATE KEY UPDATE
                weight = GREATEST(weight, VALUES(weight))
        """, (role_id, skill_id, weight))

        inserted += 1

conn.commit()
conn.close()

print(f"✅ Inserted/Updated {inserted} role_skills from ONET (direct).")
print(f"⚠️ Skipped items with no ONET-skill row: {skipped_no_skill}")
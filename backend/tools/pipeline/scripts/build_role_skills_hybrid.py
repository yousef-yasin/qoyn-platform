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

print("1) Clearing role_skills ONET/CURATED sources (optional clean)...")
cur.execute("DELETE FROM role_skills WHERE source IN ('ONET','CURATED')")

print("2) Insert CURATED role skills...")
cur.execute("""
    SELECT role_id, skill_id, weight
    FROM role_skills_curated
""")
curated = cur.fetchall()
ins_cur = 0

for r in curated:
    cur.execute("""
        INSERT INTO role_skills (role_id, skill_id, weight, source)
        VALUES (%s, %s, %s, 'CURATED')
        ON DUPLICATE KEY UPDATE
          weight = GREATEST(weight, VALUES(weight)),
          source = IF(weight < VALUES(weight), 'CURATED', source)
    """, (r["role_id"], r["skill_id"], int(r["weight"])))
    ins_cur += 1

print(f"✅ CURATED inserted/updated: {ins_cur}")

print("3) Insert ONET role skills (Skills+Knowledge only)...")

# role ↔ SOC
cur.execute("""
    SELECT r.role_id, o.id AS occupation_id
    FROM role_onet_occupations r
    JOIN onet_occupations o ON r.soc_code = o.soc_code
""")
role_occ = cur.fetchall()

# IMPORTANT: ensure you have skills created from ONET items already:
# - your build_onet_items_as_skills.py inserted skills with skill_key 'onet_item_X'
# so we map item_id -> skill by skill_key

ins_onet = 0

for row in role_occ:
    role_id = row["role_id"]
    occupation_id = row["occupation_id"]

    cur.execute("""
        SELECT item_id, importance, level
        FROM onet_occupation_items
        WHERE occupation_id = %s
    """, (occupation_id,))
    items = cur.fetchall()

    for it in items:
        item_id = it["item_id"]
        importance = float(it["importance"] or 0.0)
        level = float(it["level"] or 0.0)

        # score 0..5 تقريباً
        score = (importance * 0.7) + (level * 0.3)

        # حوله لوزن 1..5
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

        # map item_id -> skill_id via skills table (ONET items already in skills)
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
            VALUES (%s, %s, %s, 'ONET')
            ON DUPLICATE KEY UPDATE
              weight = GREATEST(weight, VALUES(weight))
        """, (role_id, skill_id, weight))
        ins_onet += 1

conn.commit()
conn.close()

print(f"✅ ONET inserted/updated rows: {ins_onet}")
print("✅ Done: role_skills is now Hybrid (CURATED + ONET).")
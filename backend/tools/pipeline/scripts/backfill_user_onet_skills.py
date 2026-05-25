import os
import pymysql
from dotenv import load_dotenv

load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

USER_ID = 2  # غيّرها إذا بدك

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor,
)
cur = conn.cursor()

print(f"Loading ESCO skills for user_id={USER_ID} ...")
cur.execute("SELECT skill_id FROM user_skills WHERE user_id=%s", (USER_ID,))
user_skill_ids = [r["skill_id"] for r in cur.fetchall()]

if not user_skill_ids:
    print("No user skills found. Stop.")
    conn.close()
    raise SystemExit(0)

inserted = 0

for esco_skill_id in user_skill_ids:
    # جيب ONET items اللي mapped على هاد الـ ESCO skill
    cur.execute("""
        SELECT source_item_id
        FROM skill_mappings
        WHERE source='ONET' AND skill_id=%s
    """, (esco_skill_id,))
    onet_item_ids = [r["source_item_id"] for r in cur.fetchall()]

    if not onet_item_ids:
        continue

    for onet_item_id in onet_item_ids:
        # حول onet_item_id -> skills.id (skill_key = onet_item_<id>)
        cur.execute("""
            SELECT id
            FROM skills
            WHERE source='ONET' AND skill_key = CONCAT('onet_item_', %s)
            LIMIT 1
        """, (onet_item_id,))
        sk = cur.fetchone()
        if not sk:
            continue

        onet_skill_id = sk["id"]

        # ضيفه للمستخدم (IGNORE لتجنب التكرار)
        cur.execute("""
            INSERT IGNORE INTO user_skills (user_id, skill_id, source)
            VALUES (%s, %s, %s)
        """, (USER_ID, onet_skill_id, "mapped_onet"))

        inserted += cur.rowcount

conn.commit()
conn.close()

print(f"✅ Backfill done. Inserted {inserted} ONET-mapped skills into user_skills.")
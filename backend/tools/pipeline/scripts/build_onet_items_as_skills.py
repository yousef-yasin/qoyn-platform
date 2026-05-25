import os
import re
import pymysql
from dotenv import load_dotenv

load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

def make_key(item_id: int) -> str:
    return f"onet_item_{item_id}"

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor
)
cur = conn.cursor()

# اقرأ كل ONET items
cur.execute("SELECT id, item_name, category FROM onet_items")
items = cur.fetchall()

inserted = 0
skipped = 0

for it in items:
    item_id = int(it["id"])
    name = (it["item_name"] or "").strip()
    cat = (it["category"] or "").strip()  # skill / knowledge / ability

    if not name:
        skipped += 1
        continue

    skill_key = make_key(item_id)

    # أدخلها كـ skill جديدة (بدون تكرار)
    cur.execute("""
        INSERT IGNORE INTO skills
        (skill_key, skill_name, category, skill_type, source, norm_name)
        VALUES (%s, %s, %s, %s, %s, NULL)
    """, (
        skill_key,
        name,
        cat if cat else None,
        "onet_item",
        "ONET"
    ))

    if cur.rowcount > 0:
        inserted += 1
    else:
        skipped += 1

conn.commit()
conn.close()

print(f"✅ Inserted {inserted} ONET items into skills (skipped/exists {skipped}).")
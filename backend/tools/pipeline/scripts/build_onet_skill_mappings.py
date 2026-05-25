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

print("Loading ONET items...")
cur.execute("SELECT id, item_name FROM onet_items")
onet_items = cur.fetchall()

print("Loading ESCO skills...")
cur.execute("SELECT id, skill_name FROM skills")
esco_skills = cur.fetchall()

# نحول ESCO skills إلى dict للبحث السريع
esco_map = {}
for s in esco_skills:
    key = s["skill_name"].strip().lower()
    esco_map[key] = s["id"]

inserted = 0

for item in onet_items:
    onet_name = item["item_name"].strip().lower()

    if onet_name in esco_map:
        cur.execute("""
            INSERT IGNORE INTO skill_mappings
            (source, source_item_id, skill_id, match_type, confidence)
            VALUES (%s, %s, %s, %s, %s)
        """, (
            "ONET",
            item["id"],
            esco_map[onet_name],
            "exact",
            1.0
        ))
        inserted += 1

conn.commit()
conn.close()

print(f"✅ Inserted {inserted} exact ONET→ESCO mappings.")
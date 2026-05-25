import os
import pandas as pd
import pymysql
from dotenv import load_dotenv

load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

BASE = os.path.join(os.path.dirname(__file__), "../data/onet")
RATINGS_FILE = os.path.join(BASE, "Task Ratings.xlsx")

print("Reading Task Ratings...")
df = pd.read_excel(RATINGS_FILE)

conn = pymysql.connect(
    host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME,
    charset="utf8mb4", cursorclass=pymysql.cursors.DictCursor
)
cur = conn.cursor()

def occ_id(soc):
    cur.execute("SELECT id FROM onet_occupations WHERE soc_code=%s", (soc,))
    r = cur.fetchone()
    return r["id"] if r else None

def task_db_id(task_id):
    cur.execute("SELECT id FROM onet_tasks WHERE task_id=%s", (task_id,))
    r = cur.fetchone()
    return r["id"] if r else None

processed = 0
inserted = 0

# غالباً الأعمدة: O*NET-SOC Code, Task ID, Scale ID, Data Value
for _, row in df.iterrows():
    soc = str(row.get("O*NET-SOC Code")).strip()
    tid = row.get("Task ID")
    scale = str(row.get("Scale ID")).strip()
    val = row.get("Data Value")

    if not soc or pd.isna(tid) or pd.isna(val):
        continue

    tid = str(tid).strip()
    if not tid:
        continue

    # نأخذ بس importance (عادة IM)
    if scale != "IM":
        continue

    o = occ_id(soc)
    t = task_db_id(tid)
    if not o or not t:
        continue

    cur.execute("""
        INSERT INTO onet_occupation_tasks (occupation_id, task_db_id, importance)
        VALUES (%s, %s, %s)
        ON DUPLICATE KEY UPDATE
        importance = VALUES(importance)
    """, (o, t, float(val)))
    inserted += 1
    processed += 1

conn.commit()
conn.close()

print(f"✅ Done. Inserted {inserted} occupation-task rows (processed {processed}).")
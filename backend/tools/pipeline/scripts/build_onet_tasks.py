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
TASKS_FILE = os.path.join(BASE, "Task Statements.xlsx")

print("Reading Task Statements...")
df = pd.read_excel(TASKS_FILE)

conn = pymysql.connect(
    host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME,
    charset="utf8mb4", cursorclass=pymysql.cursors.DictCursor
)
cur = conn.cursor()

inserted = 0

# غالباً الأعمدة: Task ID, Task
for _, row in df.iterrows():
    task_id = row.get("Task ID")
    task_text = row.get("Task")

    if pd.isna(task_id) or pd.isna(task_text):
        continue

    task_id = str(task_id).strip()
    task_text = str(task_text).strip()

    if not task_id or not task_text:
        continue

    cur.execute("""
        INSERT IGNORE INTO onet_tasks (task_id, task_text)
        VALUES (%s, %s)
    """, (task_id, task_text))
    inserted += 1

conn.commit()
conn.close()

print(f"✅ Imported {inserted} tasks into onet_tasks.")
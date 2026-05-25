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

FILES = [
    ("Skills.xlsx", "skill"),
    ("Knowledge.xlsx", "knowledge"),
    ("Abilities.xlsx", "ability"),
]

def import_items(cursor, filepath, category):
    print(f"Reading {os.path.basename(filepath)} ...")
    df = pd.read_excel(filepath)

    # نطابق أعمدة O*NET المعروفة
    # غالباً فيها: 'Element ID' و 'Element Name'
    col_id = None
    col_name = None

    for c in df.columns:
        lc = str(c).strip().lower()
        if lc in ["element id", "element_id", "id"]:
            col_id = c
        if lc in ["element name", "element_name", "name", "description"]:
            col_name = c

    if col_id is None or col_name is None:
        # fallback: جرّب أول عمودين
        col_id = df.columns[0]
        col_name = df.columns[1]
        print(f"⚠️ Columns not detected clearly. Using: {col_id} + {col_name}")

    inserted = 0
    for _, row in df.iterrows():
        item_id = row.get(col_id)
        item_name = row.get(col_name)

        if pd.isna(item_id) or pd.isna(item_name):
            continue

        item_id = str(item_id).strip()
        item_name = str(item_name).strip()

        if not item_id or not item_name:
            continue

        cursor.execute("""
            INSERT IGNORE INTO onet_items (onet_item_id, item_name, category)
            VALUES (%s, %s, %s)
        """, (item_id, item_name, category))
        inserted += 1

    print(f"✅ Inserted {inserted} rows for {category}")

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor
)
cur = conn.cursor()

for fname, cat in FILES:
    path = os.path.join(BASE, fname)
    if not os.path.exists(path):
        print(f"❌ Missing file: {path}")
        continue
    import_items(cur, path, cat)
    conn.commit()

conn.close()
print("✅ Done importing onet_items.")
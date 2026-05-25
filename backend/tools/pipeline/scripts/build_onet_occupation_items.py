import os
import pandas as pd
import pymysql
from dotenv import load_dotenv

# Load env
load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

BASE = os.path.join(os.path.dirname(__file__), "../data/onet")

FILES = [
    ("Skills.xlsx", "skill"),
    ("Knowledge.xlsx", "knowledge"),
]

def pick_col(df, options):
    """Return the first existing column name from options, else None."""
    for c in options:
        if c in df.columns:
            return c
    return None

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor
)
cur = conn.cursor()

def get_occ_id(soc):
    cur.execute("SELECT id FROM onet_occupations WHERE soc_code=%s", (soc,))
    r = cur.fetchone()
    return r["id"] if r else None

def get_item_id(element_id):
    cur.execute("SELECT id FROM onet_items WHERE onet_item_id=%s", (element_id,))
    r = cur.fetchone()
    return r["id"] if r else None

print("✅ Starting build_onet_occupation_items.py")
print("BASE:", os.path.abspath(BASE))

rows_seen = 0
db_ops = 0
missing_occ = 0
missing_item = 0

for fname, cat in FILES:
    path = os.path.join(BASE, fname)
    if not os.path.exists(path):
        print(f"⚠️ File not found: {path}")
        continue

    print(f"\nReading {fname} ...")
    df = pd.read_excel(path)

    # columns (O*NET files sometimes vary)
    col_soc   = pick_col(df, ["O*NET-SOC Code", "O*NET SOC Code", "ONET-SOC Code"])
    col_elem  = pick_col(df, ["Element ID", "ElementID"])
    col_scale = pick_col(df, ["Scale ID", "ScaleID"])
    col_value = pick_col(df, ["Data Value", "DataValue", "Value"])

    if not all([col_soc, col_elem, col_scale, col_value]):
        print("❌ Missing required columns in", fname)
        print("Found columns:", list(df.columns))
        continue

    file_ops = 0

    for _, row in df.iterrows():
        rows_seen += 1

        soc = str(row.get(col_soc, "")).strip()
        element_id = str(row.get(col_elem, "")).strip()
        scale_id = str(row.get(col_scale, "")).strip()
        value = row.get(col_value)

        if not soc or not element_id or pd.isna(value):
            continue

        occ_id = get_occ_id(soc)
        if not occ_id:
            missing_occ += 1
            continue

        item_id = get_item_id(element_id)
        if not item_id:
            missing_item += 1
            continue

        importance = None
        level = None

        # O*NET convention
        if scale_id == "IM":
            importance = float(value)
        elif scale_id == "LV":
            level = float(value)
        else:
            # ignore other scales for now
            continue

        cur.execute("""
            INSERT INTO onet_occupation_items
                (occupation_id, item_id, importance, level, scale)
            VALUES
                (%s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE
                importance = COALESCE(VALUES(importance), importance),
                level      = COALESCE(VALUES(level), level),
                scale      = VALUES(scale)
        """, (occ_id, item_id, importance, level, scale_id))

        file_ops += 1
        db_ops += 1

    conn.commit()
    print(f"✅ Done {fname}: processed insert/update ops = {file_ops}")

conn.close()

print("\n✅ Finished.")
print("Rows seen:", rows_seen)
print("DB ops (insert/update):", db_ops)
print("Missing occ_id lookups:", missing_occ)
print("Missing item_id lookups:", missing_item)
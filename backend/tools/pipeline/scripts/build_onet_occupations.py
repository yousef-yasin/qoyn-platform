import os
import pandas as pd
import pymysql
from dotenv import load_dotenv

# تحميل .env
load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")

# مسار الملف
file_path = os.path.join(
    os.path.dirname(__file__),
    "../data/onet/Occupation Data.xlsx"
)

print("Reading Occupation Data...")
df = pd.read_excel(file_path)

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor
)

cursor = conn.cursor()

count = 0

for _, row in df.iterrows():
    soc_code = str(row.get("O*NET-SOC Code")).strip()
    title = str(row.get("Title")).strip()
    desc = row.get("Description")

    if not soc_code or not title:
        continue

    cursor.execute("""
        INSERT IGNORE INTO onet_occupations (soc_code, title, description)
        VALUES (%s, %s, %s)
    """, (soc_code, title, desc))

    count += 1

conn.commit()
conn.close()

print(f"✅ Imported {count} occupations.")
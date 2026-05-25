import os
import math
import pandas as pd
import pymysql
from dotenv import load_dotenv

# =======================
# CONFIG (A - Conservative)
# =======================
KEEP_DOMAINS = {"skill", "knowledge"}   # exclude ability
MIN_IMPORTANCE = 3.0                   # filter weak rows
MIN_LEVEL = 2.5
MIN_SCORE = 3.4                        # final score filter
TOP_N_PER_OCC = 70                     # keep only top items per occupation
SOURCE_TAG = "ONET"

# =======================
# ENV / DB
# =======================
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

conn = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor,
    autocommit=False
)
cur = conn.cursor()

def ensure_item_domain_table():
    cur.execute("""
        CREATE TABLE IF NOT EXISTS onet_item_domains (
            onet_item_id VARCHAR(64) NOT NULL,
            domain ENUM('skill','knowledge','ability') NOT NULL,
            PRIMARY KEY (onet_item_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    """)

def upsert_domains_from_excels():
    print("🔎 Building ONET item domains (skill/knowledge/ability) from Excel files...")
    domain_map = {}  # element_id -> domain
    for fname, domain in FILES:
        path = os.path.join(BASE, fname)
        if not os.path.exists(path):
            print(f"⚠️ Missing file: {path} (skip)")
            continue
        df = pd.read_excel(path)
        # Element ID is the stable key
        for _, row in df.iterrows():
            element_id = str(row.get("Element ID") or "").strip()
            if not element_id:
                continue
            domain_map[element_id] = domain

    # upsert
    rows = [(k, v) for k, v in domain_map.items()]
    if rows:
        cur.executemany("""
            INSERT INTO onet_item_domains (onet_item_id, domain)
            VALUES (%s, %s)
            ON DUPLICATE KEY UPDATE domain = VALUES(domain)
        """, rows)
    print(f"✅ Domains upserted: {len(rows)}")

def get_role_occ():
    cur.execute("""
        SELECT r.role_id, o.id AS occupation_id
        FROM role_onet_occupations r
        JOIN onet_occupations o ON r.soc_code = o.soc_code
    """)
    return cur.fetchall()

def get_occ_items(occupation_id: int):
    # NOTE:
    # onet_occupation_items.item_id points to onet_items.id (internal)
    # we join to onet_items.onet_item_id (Element ID) to know domain
    cur.execute("""
        SELECT
            oi.item_id,
            oi.importance,
            oi.level,
            it.onet_item_id
        FROM onet_occupation_items oi
        JOIN onet_items it ON it.id = oi.item_id
        WHERE oi.occupation_id = %s
    """, (occupation_id,))
    return cur.fetchall()

def get_item_domain(onet_item_id: str):
    cur.execute("SELECT domain FROM onet_item_domains WHERE onet_item_id=%s", (onet_item_id,))
    r = cur.fetchone()
    return r["domain"] if r else None

def get_onet_skill_row_id(item_id_internal: int):
    # direct ONET skills inserted into skills table:
    # skill_key = 'onet_item_<item_id_internal>'
    cur.execute("""
        SELECT id FROM skills
        WHERE source='ONET' AND skill_key = CONCAT('onet_item_', %s)
        LIMIT 1
    """, (item_id_internal,))
    r = cur.fetchone()
    return r["id"] if r else None

def compute_score(importance, level):
    imp = float(importance or 0.0)
    lv = float(level or 0.0)
    return (imp * 0.7) + (lv * 0.3)

def rebuild_role_skills_onet():
    print("🧹 Removing old ONET role_skills ...")
    cur.execute("DELETE FROM role_skills WHERE source=%s", (SOURCE_TAG,))

    role_occ = get_role_occ()
    print(f"📌 role↔occupation pairs: {len(role_occ)}")

    inserted = 0
    skipped_no_skill = 0
    skipped_domain = 0
    skipped_low = 0

    for ro in role_occ:
        role_id = int(ro["role_id"])
        occ_id = int(ro["occupation_id"])

        items = get_occ_items(occ_id)

        scored = []
        for it in items:
            item_id_internal = int(it["item_id"])
            onet_item_id = (it["onet_item_id"] or "").strip()
            domain = get_item_domain(onet_item_id)

            if domain is None:
                # if not found in domains table, treat as ability and skip (conservative)
                skipped_domain += 1
                continue

            if domain not in KEEP_DOMAINS:
                skipped_domain += 1
                continue

            score = compute_score(it["importance"], it["level"])
            imp = float(it["importance"] or 0.0)
            lv = float(it["level"] or 0.0)

            # hard filters
            if imp < MIN_IMPORTANCE or lv < MIN_LEVEL or score < MIN_SCORE:
                skipped_low += 1
                continue

            scored.append((score, item_id_internal))

        # keep only top N per occupation
        scored.sort(key=lambda x: x[0], reverse=True)
        scored = scored[:TOP_N_PER_OCC]

        for score, item_id_internal in scored:
            skill_id = get_onet_skill_row_id(item_id_internal)
            if not skill_id:
                skipped_no_skill += 1
                continue

            # weight: map score(0..5) -> 1..5
            # conservative: push high scores to higher weight
            if score >= 4.2:
                weight = 5
            elif score >= 3.8:
                weight = 4
            elif score >= 3.4:
                weight = 3
            elif score >= 3.0:
                weight = 2
            else:
                weight = 1

            cur.execute("""
                INSERT INTO role_skills (role_id, skill_id, weight, source)
                VALUES (%s, %s, %s, %s)
                ON DUPLICATE KEY UPDATE
                    weight = GREATEST(weight, VALUES(weight)),
                    source = VALUES(source)
            """, (role_id, int(skill_id), int(weight), SOURCE_TAG))

            inserted += 1

    print("✅ rebuild_role_skills_onet finished")
    print(f"Inserted/Updated: {inserted}")
    print(f"Skipped (domain): {skipped_domain}")
    print(f"Skipped (low score): {skipped_low}")
    print(f"Skipped (no skills row): {skipped_no_skill}")

def main():
    print("▶ Rebuild ONET role_skills (A - conservative)")
    ensure_item_domain_table()
    upsert_domains_from_excels()
    rebuild_role_skills_onet()
    conn.commit()
    conn.close()
    print("🎉 Done.")

if __name__ == "__main__":
    main()
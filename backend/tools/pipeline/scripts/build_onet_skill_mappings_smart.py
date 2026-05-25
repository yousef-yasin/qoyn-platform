import os
import re
import pymysql
from dotenv import load_dotenv

load_dotenv(os.path.join(os.path.dirname(__file__), "../../../.env"))

DB_HOST = os.getenv("DB_HOST")
DB_NAME = os.getenv("DB_NAME")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASSWORD")
STOPWORDS = {
    "skill","skills","ability","abilities","knowledge",
    "system","systems","analysis","information","data",
    "process","processes","use","using","apply","applied",
    "method","methods","technique","techniques",
    "tools","tool","work","working"
}
def norm(s: str) -> str:
    s = (s or "").lower().strip()
    s = re.sub(r"[^a-z0-9\s]+", " ", s)   # remove punctuation
    s = re.sub(r"\s+", " ", s).strip()
    return s

def tokens(s: str):
    t = [x for x in norm(s).split(" ") if len(x) >= 3 and x not in STOPWORDS]
    return set(t)

def jaccard(a:set, b:set) -> float:
    if not a or not b:
        return 0.0
    inter = len(a & b)
    union = len(a | b)
    return inter / union if union else 0.0

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
onet = cur.fetchall()

print("Loading ESCO skills...")
cur.execute("SELECT id, skill_name FROM skills")
esco = cur.fetchall()

# existing mappings to skip
cur.execute("SELECT source_item_id FROM skill_mappings WHERE source='ONET'")
mapped_onet = set([r["source_item_id"] for r in cur.fetchall()])

# prepare ESCO normalized structures
esco_norm = []
for s in esco:
    sn = norm(s["skill_name"])
    esco_norm.append({
        "id": s["id"],
        "name": s["skill_name"],
        "norm": sn,
        "tok": tokens(sn)
    })

inserted = 0

for it in onet:
    onet_id = it["id"]
    if onet_id in mapped_onet:
        continue

    on = it["item_name"]
    on_n = norm(on)
    on_t = tokens(on_n)

    if not on_n or len(on_t) == 0:
        continue

    best = None
    best_score = 0.0
    best_type = None

    # search best ESCO candidate
    for s in esco_norm:
        sn = s["norm"]

        # contains check
        if on_n and (on_n in sn or sn in on_n):
            score = 0.85
            if score > best_score:
                best = s
                best_score = score
                best_type = "contains"
            continue

        # token overlap
        jac = jaccard(on_t, s["tok"])
    if jac >= 0.45:
                        # scale confidence between 0.65 and 0.90
            score = min(0.90, 0.55 + jac)
            if score > best_score:
                best = s
                best_score = score
                best_type = "token_overlap"

    if best and best_score >= 0.60:   
         cur.execute("""
            INSERT IGNORE INTO skill_mappings
            (source, source_item_id, skill_id, match_type, confidence)
            VALUES (%s, %s, %s, %s, %s)
        """, ("ONET", onet_id, best["id"], best_type, float(best_score)))
    inserted += 1

conn.commit()
conn.close()

print(f"✅ Inserted {inserted} smart ONET→ESCO mappings.")
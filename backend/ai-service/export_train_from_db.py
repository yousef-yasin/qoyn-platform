import json
import os
from pathlib import Path
from collections import defaultdict, deque

import mysql.connector

ROOT = Path(__file__).resolve().parent
OUT = ROOT / "data" / "train.jsonl"
OUT.parent.mkdir(parents=True, exist_ok=True)

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_USER = os.getenv("DB_USER", "root")
DB_PASS = os.getenv("DB_PASS", "")
DB_NAME = os.getenv("DB_NAME", "utbn_db")

N = int(os.getenv("TRAIN_N", "20"))


def normalize_watch(w) -> float:
    try:
        w = float(w)
    except Exception:
        return 0.0
    if w > 1.0:
        w = w / 100.0
    if w < 0:
        w = 0.0
    if w > 1.0:
        w = 1.0
    return w


def label_from_features(avg_score: float, avg_watch: float) -> str:
    if avg_score >= 85 and avg_watch >= 0.70:
        return "advanced"
    if avg_score >= 65 and avg_watch >= 0.45:
        return "intermediate"
    return "beginner"


def main():
    conn = mysql.connector.connect(
        host=DB_HOST, port=DB_PORT, user=DB_USER, password=DB_PASS, database=DB_NAME
    )
    cur = conn.cursor(dictionary=True)

    cur.execute(
        """
        SELECT user_id, score_percent, time_spent_seconds, watched_percent, difficulty, created_at
        FROM student_performance
        ORDER BY user_id ASC, created_at DESC
        """
    )

    buckets = defaultdict(lambda: deque(maxlen=N))
    for r in cur:
        uid = int(r["user_id"])
        buckets[uid].append(r)

    cur.close()
    conn.close()

    written = 0
    with OUT.open("w", encoding="utf-8") as f:
        for uid, rows in buckets.items():
            if len(rows) < 5:
                continue

            cnt = len(rows)
            sum_score = 0.0
            sum_time = 0.0
            sum_watch = 0.0
            sum_diff = 0.0
            hard_sum = 0.0
            hard_cnt = 0

            for r in rows:
                s = float(r.get("score_percent") or 0)
                t = float(r.get("time_spent_seconds") or 0)
                w = normalize_watch(r.get("watched_percent") or 0)
                d = float(r.get("difficulty") or 3)

                sum_score += s
                sum_time += t
                sum_watch += w
                sum_diff += d

                if d >= 4:
                    hard_sum += s
                    hard_cnt += 1

            avg_score = sum_score / max(1, cnt)
            avg_time = sum_time / max(1, cnt)
            avg_watch = sum_watch / max(1, cnt)
            avg_diff = sum_diff / max(1, cnt)
            hard_avg = (hard_sum / hard_cnt) if hard_cnt > 0 else None

            features = {
                "n": cnt,
                "avg_score": avg_score,
                "avg_time": avg_time,
                "avg_watch": avg_watch,
                "avg_difficulty": avg_diff,
                "hard_avg_score": hard_avg,
            }

            label = label_from_features(avg_score, avg_watch)

            f.write(json.dumps({"user_id": uid, "features": features, "label": label}, ensure_ascii=False) + "\n")
            written += 1

    print(f"Exported {written} training rows to: {OUT}")


if __name__ == "__main__":
    main()

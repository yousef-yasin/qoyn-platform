import pandas as pd
import numpy as np

np.random.seed(42)

rows = []

def make_block(label, n, score_range, watch_range, time_range, diff_range):
    for _ in range(n):
        avg_score = np.random.uniform(*score_range)
        avg_watch = np.random.uniform(*watch_range)
        avg_time = np.random.uniform(*time_range)
        avg_diff = np.random.uniform(*diff_range)
        hard_avg = avg_score - np.random.uniform(0, 10)
        speed = avg_score / max(avg_time, 1)

        rows.append({
            "avg_score": avg_score,
            "avg_time": avg_time,
            "avg_watch": avg_watch,
            "avg_difficulty": avg_diff,
            "hard_avg_score": hard_avg,
            "speed": speed,
            "label": label
        })

# Beginner
make_block(
    label="beginner",
    n=400,
    score_range=(40, 60),
    watch_range=(0.3, 0.7),
    time_range=(60, 400),
    diff_range=(1, 3)
)

# Intermediate
make_block(
    label="intermediate",
    n=400,
    score_range=(60, 80),
    watch_range=(0.6, 0.9),
    time_range=(40, 200),
    diff_range=(2, 4)
)

# Advanced
make_block(
    label="advanced",
    n=400,
    score_range=(80, 100),
    watch_range=(0.8, 1.0),
    time_range=(20, 120),
    diff_range=(3, 5)
)

df = pd.DataFrame(rows)
df.to_csv("data/synthetic_dataset.csv", index=False)

print("✅ Dataset created: data/synthetic_dataset.csv")
print("Rows:", len(df))

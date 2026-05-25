import json
from pathlib import Path

import joblib
import numpy as np
from sklearn.ensemble import RandomForestClassifier

DATA = Path(__file__).parent / "data" / "train.jsonl"
CSV_DATA = Path(__file__).parent / "data" / "synthetic_dataset.csv"
MODEL = Path(__file__).parent / "model.joblib"

LABELS = {"beginner": 0, "intermediate": 1, "advanced": 2}
INV = {v: k for k, v in LABELS.items()}

FEATURE_NAMES = [
    "avg_score",
    "avg_time",
    "avg_watch",
    "avg_difficulty",
    "hard_avg_score",
    "speed",  # approx score per minute (higher = faster)
]


def _to_float(x, default=0.0) -> float:
    try:
        if x is None:
            return float(default)
        return float(x)
    except Exception:
        return float(default)


def _clamp(x: float, lo: float, hi: float) -> float:
    if x < lo:
        return lo
    if x > hi:
        return hi
    return x


def normalize_features(feat: dict) -> list[float]:
    """
    Ensure consistent scale:
      - avg_score: 0..100
      - avg_time: 0..7200 (2 hours cap)
      - avg_watch: 0..1  (auto-normalize if old 0..100)
      - avg_difficulty: 1..5
      - hard_avg_score: 0..100 (optional)
      - speed: 0..200 (optional cap)
    """
    avg_score = _clamp(_to_float(feat.get("avg_score", 0.0)), 0.0, 100.0)
    avg_time = _clamp(_to_float(feat.get("avg_time", 0.0)), 0.0, 7200.0)

    avg_watch = _to_float(feat.get("avg_watch", 0.0))
    # ✅ handle old scale 0..100
    if avg_watch > 1.0:
        avg_watch = avg_watch / 100.0
    avg_watch = _clamp(avg_watch, 0.0, 1.0)

    avg_diff = _to_float(feat.get("avg_difficulty", 3.0))
    avg_diff = _clamp(avg_diff, 1.0, 5.0)

    hard_avg_score = _to_float(feat.get("hard_avg_score", 0.0))
    hard_avg_score = _clamp(hard_avg_score, 0.0, 100.0)

    # speed: score per minute (avoid divide by zero)
    if avg_time > 0:
        speed = (avg_score / max(1.0, avg_time / 60.0))
    else:
        speed = 0.0
    speed = _clamp(speed, 0.0, 200.0)

    return [avg_score, avg_time, avg_watch, avg_diff, hard_avg_score, speed]


def load_training_data():
    """Return (X, y). X is float32 matrix. y is int labels."""
        # ✅ If CSV exists (synthetic_dataset.csv), use it first
    if CSV_DATA.exists():
        print("✅ Training from CSV dataset:", CSV_DATA)
        import pandas as pd

        df = pd.read_csv(CSV_DATA)

        # لازم الأعمدة تكون موجودة
        needed = ["avg_score","avg_time","avg_watch","avg_difficulty","hard_avg_score","speed","label"]
        for c in needed:
            if c not in df.columns:
                raise ValueError(f"Missing column in CSV: {c}")

        X = []
        y = []

        for _, row in df.iterrows():
            label = str(row["label"]).strip()
            if label not in LABELS:
                continue

            feat = {
                "avg_score": row["avg_score"],
                "avg_time": row["avg_time"],
                "avg_watch": row["avg_watch"],
                "avg_difficulty": row["avg_difficulty"],
                "hard_avg_score": row["hard_avg_score"],
                "speed": row["speed"],
            }
            X.append(normalize_features(feat))
            y.append(LABELS[label])

        if X:
            return np.array(X, dtype=np.float32), np.array(y, dtype=np.int64)

    if DATA.exists():
        X = []
        y = []
        with DATA.open("r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if not line:
                    continue
                row = json.loads(line)
                feat = row.get("features") or {}
                label = row.get("label")
                if label not in LABELS:
                    continue

                X.append(normalize_features(feat))
                y.append(LABELS[label])

        if X:
            return np.array(X, dtype=np.float32), np.array(y, dtype=np.int64)

    # Synthetic starter data (so the service runs on day 1).
    rng = np.random.default_rng(42)
    n = 2000

    avg_score = rng.uniform(0, 100, size=n)
    avg_time = rng.uniform(60, 1800, size=n)  # seconds
    avg_watch = rng.uniform(0.2, 1.0, size=n)
    avg_diff = rng.uniform(1, 5, size=n)

    # hard_avg_score correlates with avg_score but noisier
    hard_avg_score = np.clip(avg_score + rng.normal(0, 12, size=n), 0, 100)

    # speed ~ score per minute (noisy)
    speed = np.clip((avg_score / np.maximum(1, avg_time / 60.0)) + rng.normal(0, 2.0, size=n), 0, 200)

    # Labels: mostly driven by score, nudged by watch & difficulty
    y = np.zeros(n, dtype=np.int64)
    # intermediate
    y[(avg_score >= 65) & (avg_watch >= 0.45)] = 1
    # advanced
    y[(avg_score >= 85) & (avg_watch >= 0.70)] = 2

    X = np.vstack([avg_score, avg_time, avg_watch, avg_diff, hard_avg_score, speed]).T.astype(np.float32)
    return X, y


def main():
    X, y = load_training_data()

    model = RandomForestClassifier(
        n_estimators=300,
        random_state=7,
        class_weight="balanced",
        n_jobs=-1,
    )
    model.fit(X, y)

    joblib.dump(
        {"model": model, "labels": INV, "feature_names": FEATURE_NAMES},
        MODEL
    )
    print(f"Saved model to: {MODEL}")


if __name__ == "__main__":
    main()

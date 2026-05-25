UTBN AI SERVICE (local)

1) Create venv and install:
   python -m venv .venv
   .venv\Scripts\activate   (Windows)
   source .venv/bin/activate (Linux/Mac)
   pip install -r requirements.txt

2) Train model (optional, but recommended):
   python train_model.py

3) Run API:
   uvicorn app:app --host 127.0.0.1 --port 8001

4) In Apache/PHP environment set env:
   AI_SERVICE_URL=http://127.0.0.1:8001/predict_level

Then call:
   GET /utbn-backend/api/ai_predict_level.php

import chromadb
import requests
from typing import List, Dict, Any

CHROMA_PATH = "./chroma_db"
EMBED_URL = "http://127.0.0.1:11434/api/embeddings"
EMBED_MODEL = "nomic-embed-text"

client = chromadb.PersistentClient(path=CHROMA_PATH)
collection = client.get_or_create_collection(name="phase3_level2")


def get_embedding(text: str) -> List[float]:
    r = requests.post(
        EMBED_URL,
        json={
            "model": EMBED_MODEL,
            "prompt": text
        },
        timeout=120
    )
    r.raise_for_status()
    data = r.json()
    return data["embedding"]


def upsert_document(doc_id: str, text: str, metadata: Dict[str, Any]):
    emb = get_embedding(text)
    collection.upsert(
        ids=[doc_id],
        documents=[text],
        embeddings=[emb],
        metadatas=[metadata]
    )


def query_similar(text: str, n_results: int = 3):
    emb = get_embedding(text)
    return collection.query(
        query_embeddings=[emb],
        n_results=n_results
    )
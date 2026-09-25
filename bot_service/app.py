from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from rapidfuzz import fuzz, process
import re
import os
import requests
from knowledge_base import KNOWLEDGE_BASE

app = FastAPI(title="Lab TKJ Chatbot AI Engine")

# Izinkan CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Konfigurasi 9Router
NINEROUTER_BASE_URL = os.getenv("NINEROUTER_BASE_URL", "http://localhost:8000/v1")
NINEROUTER_API_KEY = os.getenv("NINEROUTER_API_KEY", "sk-5194cd09ab459bfb-rsn0sz-ce29ee51")
NINEROUTER_MODEL = os.getenv("NINEROUTER_MODEL", "gemini/gemini-3.5-flash-lite")

class QueryRequest(BaseModel):
    message: str


def query_9router(user_message: str) -> str:
    """Mengirim pertanyaan ke 9Router jika tidak ada di knowledge base lokal."""
    url = f"{NINEROUTER_BASE_URL.rstrip('/')}/chat/completions"
    headers = {
        "Authorization": f"Bearer {NINEROUTER_API_KEY}",
        "Content-Type": "application/json"
    }
    
    # Ringkasan topik lab sebagai panduan konteks bagi AI
    system_instruction = (
        "Kamu adalah asisten AI interaktif untuk Laboratorium Komputer & Jaringan (TKJ) Winshark Community. "
        "Bantu pengguna menjawab pertanyaan umum seputar jaringan, lab komputer, atau panduan teknis dengan sopan dan ringkas. "
        "Gunakan format teks rapi dengan HTML ringan seperti <b>bold</b> atau <ul><li> jika membuat daftar. "
        "Jika pertanyaan terkait peminjaman barang atau aturan spesifik lab yang tidak kamu ketahui, sarankan pengguna untuk "
        "menghubungi laboran atau admin lab TKJ secara langsung."
    )
    
    payload = {
        "model": NINEROUTER_MODEL,
        "messages": [
            {"role": "system", "content": system_instruction},
            {"role": "user", "content": user_message}
        ],
        "temperature": 0.7
    }
    
    try:
        response = requests.post(url, json=payload, headers=headers, timeout=25)
        if response.status_code == 200:
            result = response.json()
            return result["choices"][0]["message"]["content"]
        else:
            return "Maaf, asisten AI sedang mengalami kendala jaringan. Silakan coba sesaat lagi."
    except Exception as e:
        return "Maaf, tidak dapat terhubung ke AI gateway saat ini."


def get_answer(query_text: str):
    query = query_text.lower().strip()
    if not query:
        return "Silakan ketik pertanyaan Anda seputar laboratorium."

    best_match = None
    highest_score = 0

    # 1. Pencocokan Kata Kunci Utuh (Regex Word Boundary)
    for item in KNOWLEDGE_BASE:
        for kw in item["keywords"]:
            pattern = r'\b' + re.escape(kw.lower()) + r'\b'
            if re.search(pattern, query):
                if item["intent"] == "greeting" and len(query.split()) > 2:
                    continue
                return item["response"]

    # 2. Fuzzy Matching Cerdas untuk Kalimat Panjang & Toleransi Typo
    for item in KNOWLEDGE_BASE:
        target_list = item.get("questions", []) + item.get("keywords", [])
        match, score, _ = process.extractOne(
            query,
            target_list,
            scorer=fuzz.token_set_ratio
        )
        if score > highest_score:
            highest_score = score
            best_match = item["response"]

    # Ambang batas kemiripan (threshold 60%)
    if highest_score >= 60 and best_match:
        return best_match

    # 3. Fallback: Alihkan ke 9Router jika tidak ada di Knowledge Base lokal
    return query_9router(query_text)


@app.post("/api/chat")
def chat(req: QueryRequest):
    return {"reply": get_answer(req.message)}


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=5000)
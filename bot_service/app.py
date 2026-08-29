# app.py
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from rapidfuzz import fuzz, process
import re
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

class QueryRequest(BaseModel):
    message: str

def get_answer(query_text: str):
    query = query_text.lower().strip()
    if not query:
        return "Silakan ketik pertanyaan Anda seputar laboratorium."

    best_match = None
    highest_score = 0

    # 1. Pencocokan Kata Kunci Utuh (Regex Word Boundary)
    # Menghindari kata "apa" atau "piket" salah terdeteksi karena ada huruf "p"
    for item in KNOWLEDGE_BASE:
        for kw in item["keywords"]:
            # Cek kecocokan kata/frasa utuh
            pattern = r'\b' + re.escape(kw.lower()) + r'\b'
            if re.search(pattern, query):
                # Khusus keyword greeting singkat (p, tes, ping, halo), 
                # jangan langsung return jika kalimat user panjang (ada pertanyaan spesifik)
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

    # Respon fallback jika tidak ditemukan
    return (
        "Maaf, saya belum memahami pertanyaan tersebut. 🤔<br><br>"
        "Coba tanyakan seputar: <em>tata tertib lab, cara pinjam alat, jadwal piket, "
        "katalog mikrotik / fiber optic, jam operasional,</em> atau ketik <strong>instagram</strong>."
    )

@app.post("/api/chat")
def chat(req: QueryRequest):
    return {"reply": get_answer(req.message)}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=5000)
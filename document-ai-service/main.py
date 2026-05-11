from fastapi import FastAPI, UploadFile, File, HTTPException
from google.cloud import documentai_v1 as documentai
from google.oauth2 import service_account
from pydantic import BaseModel
import json

app = FastAPI(title="RSUK Document AI Extractor")

# ==========================================
# KONFIGURASI GOOGLE CLOUD DOCUMENT AI
# ==========================================
PROJECT_ID = "YOUR_PROJECT_ID"
LOCATION = "us" # 'us' atau 'eu'
PROCESSOR_ID = "YOUR_PROCESSOR_ID"
CREDENTIALS_FILE = "google-credentials.json"

class ExtractionResponse(BaseModel):
    status: str
    data: dict

@app.post("/api/extract-form", response_model=ExtractionResponse)
async def extract_form(file: UploadFile = File(...)):
    # 1. Validasi tipe file
    allowed_types = ["image/jpeg", "image/png", "application/pdf"]
    if file.content_type not in allowed_types:
        raise HTTPException(status_code=400, detail="Tipe file tidak didukung. Gunakan JPEG, PNG, atau PDF.")

    file_content = await file.read()

    try:
        # 2. Setup Kredensial dan Client
        credentials = service_account.Credentials.from_service_account_file(CREDENTIALS_FILE)
        client_options = {"api_endpoint": f"{LOCATION}-documentai.googleapis.com"}
        client = documentai.DocumentProcessorServiceClient(credentials=credentials, client_options=client_options)

        # 3. Definisikan Processor Name
        name = client.processor_path(PROJECT_ID, LOCATION, PROCESSOR_ID)

        # 4. Siapkan Dokumen dan Kirim Request (Sinkron)
        raw_document = documentai.RawDocument(content=file_content, mime_type=file.content_type)
        request = documentai.ProcessRequest(name=name, raw_document=raw_document)
        
        result = client.process_document(request=request)
        document = result.document

        # 5. Struktur Target JSON (Flattened)
        extracted_data = {
            "nama_pasien": "",
            "nomor_rekam_medis": "",
            "tanggal_lahir": "",
            "pembayar": "",
            "hak_kelas_perawatan": ""
        }

        # Map jenis entitas dari Form Parser ke struktur JSON kita
        # Note: 'type_' ini bergantung pada skema Form Parser Google atau Custom Extractor Anda
        key_mapping = {
            "nama_pasien": ["nama", "nama_pasien", "patient_name", "nama pasien"],
            "nomor_rekam_medis": ["no_rm", "nomor_rm", "rekam_medis"],
            "tanggal_lahir": ["tgl_lahir", "tanggal_lahir", "dob"],
            "pembayar": ["pembayar", "cara_bayar", "penjamin"],
            "hak_kelas_perawatan": ["kelas", "hak_kelas", "kelas_perawatan"]
        }

        confidence_sum = 0
        entity_count = 0

        # 6. Parse Entities (Key-Value Pairs)
        for entity in document.entities:
            conf = entity.confidence
            type_name = str(entity.type_).lower()
            mention_text = str(entity.mention_text).replace('\n', ' ').strip()

            entity_count += 1
            confidence_sum += conf

            # Jika confidence di bawah 50%, kosongkan nilai agar diketik manual
            if conf < 0.5:
                mention_text = ""

            # Cocokkan tipe entitas dari Google dengan kolom kita
            for target_field, possible_keys in key_mapping.items():
                if any(k in type_name for k in possible_keys) and not extracted_data[target_field]:
                    extracted_data[target_field] = mention_text
                    break

        # Hitung rata-rata akurasi keseluruhan
        avg_confidence = round(confidence_sum / entity_count, 2) if entity_count > 0 else 0.0

        # 7. Kembalikan JSON terstruktur
        return {
            "status": "success",
            "data": {
                **extracted_data,
                "tingkat_akurasi": avg_confidence
            }
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)

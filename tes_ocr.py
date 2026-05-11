import base64
import requests
import json
import os

# 1. Pengaturan
API_URL = "http://localhost:4000/v1/chat/completions"
IMAGE_PATH = "dokumen_rs.jpg"  # <-- Ganti dengan nama file fotomu (harus di folder yang sama)

def encode_image(image_path):
    with open(image_path, "rb") as image_file:
        return base64.b64encode(image_file.read()).decode('utf-8')

# 2. Siapkan Data
base64_image = encode_image(IMAGE_PATH)

payload = {
    "model": "gemini/gemini-1.5-flash",
    "messages": [
        {
            "role": "user",
            "content": [
                {
                    "type": "text",
                    "text": "Tolong baca teks di gambar rekam medis ini dan buatkan dalam format JSON yang rapi (Nama, No RM, Diagnosa, Tanggal)."
                },
                {
                    "type": "image_url",
                    "image_url": {
                        "url": f"data:image/jpeg;base64,{base64_image}"
                    }
                }
            ]
        }
    ]
}

headers = {
    "Content-Type": "application/json",
    "Authorization": "Bearer sk-anything"
}

# 3. Kirim dan Cetak Hasil
print(f"Sedang memproses gambar: {IMAGE_PATH} ...")
response = requests.post(API_URL, headers=headers, data=json.dumps(payload))

if response.status_code == 200:
    result = response.json()
    print("\n--- HASIL OCR ---")
    print(result['choices'][0]['message']['content'])
else:
    print(f"Error {response.status_code}: {response.text}")

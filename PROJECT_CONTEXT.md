# Project Context

## Domain

Sistem manajemen rekam medis rumah sakit

## Flow Utama

1. Upload dokumen PDF
2. Convert ke image
3. OCR (LiteLLM)
4. AI parsing → JSON
5. Validasi user
6. Simpan ke database
7. Hitung retensi otomatis
8. Pemusnahan

## Entitas

* pasien
* kunjungan
* dokumen_rekam_medis
* retensi
* daftar_pemusnahan

## Aturan Penting

* Data tidak boleh redundan
* Semua relasi pakai foreign key
* OCR tidak langsung simpan ke pasien (harus validasi)

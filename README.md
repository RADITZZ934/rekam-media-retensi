# Sistem Retensi & Alih Media Rekam Medis

## Overview

Sistem untuk:

* Digitalisasi dokumen (OCR)
* Validasi data pasien
* Manajemen retensi rekam medis
* Pemusnahan arsip

## Tech Stack

* Laravel 11 (Backend)
* Vue 3 + Vite (Frontend)
* MySQL (Database)
* OCR: LiteLLM (Mistral OCR)

## Core Flow

Upload PDF → OCR → AI Parsing → Validasi → Simpan → Retensi → Pemusnahan

## Struktur Utama

* app/Services → Logic (OCR, AI, Retensi)
* app/Models → Database
* resources/js → Vue frontend
* routes/api.php → API

## Cara Menjalankan

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan serve
npm run dev
```

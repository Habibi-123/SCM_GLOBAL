# 🌍 Globalis — Global Supply Chain Risk Intelligence Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Leaflet-1.9-199900?style=for-the-badge&logo=leaflet&logoColor=white" alt="Leaflet.js">
  <img src="https://img.shields.io/badge/Chart.js-4.4-FF6384?style=for-the-badge&logo=chart.js&logoColor=white" alt="Chart.js">
</p>

---

# 📌 Project Overview

**Globalis (Global Supply Chain Risk Intelligence Platform)** adalah aplikasi web untuk membantu memantau risiko rantai pasok (supply chain) secara global. Platform ini mengintegrasikan 6 sumber data eksternal (cuaca, ekonomi, kurs mata uang, berita internasional, dan lokasi pelabuhan dunia) ke dalam satu dashboard, lengkap dengan mesin analisis sentimen berita (lexicon-based) dan mesin penilaian risiko (weighted risk scoring) buatan sendiri.

---

# 🚀 Main Features

### 🌍 Dashboard Monitoring
Halaman utama: pilih 1 negara dari dropdown, sistem otomatis menampilkan seluruh info negara tersebut dalam satu halaman — Risk Score & breakdown-nya, grafik tren risiko, cuaca terkini, GDP & Inflasi (+ grafik tren 2019-2025), kurs mata uang, mini peta pelabuhan, dan berita terbaru. Bersifat read-only (murni pemantauan, bukan untuk mengubah data).

### 📊 Global Country Dashboard
Daftar 250 negara (card + bendera + pencarian nama) yang bisa diklik untuk melihat halaman detail masing-masing negara.

### ⚖️ Country Comparison Engine
Membandingkan 2 negara berdampingan: GDP, Inflasi, Risk Score, Cuaca, dan Kurs Mata Uang.

### 🌦 Global Weather Monitoring
Peta dunia (Leaflet.js) menampilkan kondisi cuaca tiap negara dengan marker berwarna sesuai level risiko badai (rendah/sedang/tinggi). Tersedia dropdown untuk memilih 1 negara — peta otomatis zoom ke lokasinya beserta panel detail suhu, curah hujan, dan kecepatan angin. Data cuaca ter-refresh otomatis tiap 6 jam saat halaman diakses.

### 💱 Currency Impact Dashboard
Pilih negara, sistem menampilkan kurs mata uangnya terhadap USD beserta grafik tren perubahan (Chart.js) dan indikator naik/turun.

### 📰 News Intelligence
Berita internasional dari GNews API, bisa difilter per kategori (logistics/trade/shipping/economy/geopolitics) atau dicari berdasarkan nama negara. Setiap berita dianalisis sentimennya (Positive/Neutral/Negative) menggunakan **Lexicon Based Sentiment Analysis** buatan sendiri (kamus kata di tabel `positive_words` dan `negative_words`, tanpa AI berbayar).

### 🚢 Port Location Dashboard
Peta dunia (Leaflet.js + OpenStreetMap) menampilkan 3600+ lokasi pelabuhan dari dataset World Port Index (NGA), dengan fitur pencarian nama pelabuhan dan filter per negara.

### 📈 Data Visualization Dashboard
Grafik historis per negara: GDP Trend, Inflation Trend, Ekspor vs Impor (2019-2025), dan Risk Score Trend.

### ⭐ Favorite Monitoring List (Watchlist)
Pengguna dapat menyimpan negara favorit untuk dipantau dari halaman Watchlist.

### 👨‍💼 Admin Panel
Khusus akun dengan role **Admin** (menggunakan Spatie Laravel Permission), dapat mengelola:
- Data User (tambah/edit/hapus, atur role Admin/User)
- Dataset Pelabuhan (CRUD)
- Artikel Analisis (tulis/edit/hapus)

---

# 🧮 Risk Scoring Formula

Globalis menggunakan metode **Weighted Risk Scoring** (algoritma buatan sendiri, bukan AI berbayar) untuk menentukan tingkat risiko supply chain tiap negara.

```
Total Risk Score =
  (0.30 × Weather Score)
+ (0.20 × Inflation Score)
+ (0.40 × News Sentiment Score)
+ (0.10 × Currency Exchange Score)
```

**Cara tiap komponen dihitung (skala 0-100):**
| Komponen | Sumber | Logika |
|---|---|---|
| Weather Score | `weather_data.storm_risk` terkini | low=10, medium=50, high=90 |
| Inflation Score | `economic_indicators.inflation` tahun terbaru | `abs(inflasi) / 20 × 100`, dibatasi maks 100 |
| News Score | Sentiment berita negara tsb (fallback ke rata-rata global bila belum ada) | `(positif×20 + netral×50 + negatif×80) / total` |
| Currency Score | Volatilitas kurs 14 hari (`currency_rates`) | Coefficient of variation × 20, dibatasi maks 100 |

### Risk Level
| Total Score | Level |
|---|---|
| 0 – 34 | 🟢 Low Risk |
| 35 – 59 | 🟡 Medium Risk |
| ≥ 60 | 🔴 High Risk |

> **Catatan jujur:** data histori kurs 14 hari yang dipakai untuk Currency Score adalah **data simulasi** (variasi acak per mata uang), karena API kurs gratis yang dipakai (`open.er-api.com`) tidak menyediakan data historis asli — hanya snapshot terkini.

---

# 🔌 External APIs

| API | Fungsi | Catatan |
|---|---|---|
| REST Countries API **v5** | Profil dasar negara (nama, kode, ibu kota, populasi, koordinat) | **Wajib API Key** (gratis, daftar di restcountries.com) — versi lama v3.1 sudah deprecated |
| Open-Meteo API | Cuaca real-time (suhu, curah hujan, angin) | Tanpa API Key |
| World Bank API | GDP & Inflasi historis | Tanpa API Key |
| ExchangeRate API (open.er-api.com) | Kurs mata uang terhadap USD | Tanpa API Key |
| GNews API | Berita internasional | **Wajib API Key**, free tier 100 request/hari, delay 12 jam untuk berita real-time |
| World Port Index (NGA) | Dataset 3600+ pelabuhan dunia | Diunduh manual (CSV), diimpor via Artisan Command |
| OpenStreetMap + Leaflet.js | Peta interaktif | Tanpa API Key |

---

# 🗄 Database

MySQL, berisi 12 tabel utama:

- `countries` — data dasar 250 negara (tabel pusat, direferensikan tabel lain via `country_id`)
- `economic_indicators` — histori GDP/Inflasi/Ekspor/Impor per tahun
- `weather_data` — histori cuaca per negara
- `currency_rates` — histori kurs mata uang
- `news_articles` — berita + hasil analisis sentimen
- `positive_words`, `negative_words` — kamus lexicon sentiment analysis
- `ports` — data pelabuhan dunia
- `risk_scores` — histori hasil perhitungan risk score
- `watchlists` — pivot user ↔ negara favorit
- `articles` — artikel analisis buatan Admin
- Tabel Spatie Permission: `roles`, `permissions`, `model_has_roles`, dll — untuk role Admin/User

Ditambah tabel bawaan Laravel: `users`, `migrations`, `cache`, `sessions`, `jobs`, `password_reset_tokens`, `personal_access_tokens` (Sanctum).

---

# 🌐 Internal REST API

```http
GET /api/countries              # Daftar semua negara (paginated)
GET /api/countries/{code}       # Detail 1 negara (contoh: /api/countries/IDN)
GET /api/risk?level=high        # Daftar negara + risk score (bisa difilter level)
GET /api/ports?search=&country= # Daftar pelabuhan (bisa dicari/difilter negara)
GET /api/news?category=&sentiment= # Daftar berita (bisa difilter kategori/sentiment)
GET /api/currency?base=&target= # Daftar kurs mata uang
```

Semua endpoint mengembalikan JSON via Laravel API Resource, tidak memerlukan autentikasi.

---

# 💻 Installation

## Requirements
- PHP 8.2+
- Composer
- MySQL
- Node.js & NPM (untuk build Bootstrap 5 via Vite)

## Clone & Install

```bash
git clone https://github.com/Habibi-123/SCM_GLOBAL
cd globalis

composer install
npm install
```

## Environment

```bash
cp .env.example .env
php artisan key:generate
```

Isi `.env` dengan konfigurasi database dan **2 API Key wajib**:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_globalis
DB_USERNAME=root
DB_PASSWORD=

RESTCOUNTRIES_API_KEY=your_key_here   # daftar gratis di restcountries.com/sign-up
GNEWS_API_KEY=your_key_here           # daftar gratis di gnews.io/register
```

## Migration & Seeder

```bash
php artisan migrate
php artisan db:seed   # mengisi kamus positive_words & negative_words
```

## Build Asset & Jalankan

```bash
npm run build
php artisan serve
```

Akses aplikasi di `http://127.0.0.1:8000`.

## Isi Data Awal (WAJIB dijalankan berurutan)

```bash
php artisan countries:sync                          # sync 250 negara (REST Countries)
php artisan economic:sync-range --start=2019 --end=2023  # data ekonomi historis
php artisan weather:sync                             # cuaca semua negara
php artisan currency:sync                            # kurs mata uang
php artisan ports:import                             # import CSV World Port Index
php artisan news:sync                                # berita per kategori
php artisan news:analyze                             # analisis sentiment berita
php artisan db:seed --class=CurrencyHistorySeeder    # simulasi histori kurs (untuk Currency Score)
php artisan risk:calculate                           # hitung risk score semua negara
```

> Dataset World Port Index (`world_port_index.csv`) perlu diunduh manual dan ditaruh di `storage/app/datasets/` sebelum menjalankan `ports:import`.

---

# ⚙ Artisan Commands (Lengkap)

```bash
php artisan countries:sync                    # sync data dasar negara
php artisan economic:sync --year=2023         # sync data ekonomi 1 tahun
php artisan economic:sync-range --start=2019 --end=2023  # sync banyak tahun sekaligus
php artisan weather:sync                      # sync cuaca semua negara
php artisan currency:sync --base=USD          # sync snapshot kurs mata uang
php artisan ports:import world_port_index.csv # import dataset pelabuhan
php artisan news:sync                         # sync berita per kategori
php artisan news:analyze                      # analisis sentiment berita tersimpan
php artisan risk:calculate                    # hitung ulang risk score semua negara
```

---

# 🛠 Technology Stack

- Laravel 12 (Blade, Eloquent, Spatie Permission)
- PHP 8.2
- MySQL
- Bootstrap 5 + Sass (via Vite)
- Chart.js — grafik tren (risk, GDP, inflasi, kurs)
- Leaflet.js + OpenStreetMap — peta interaktif (pelabuhan & cuaca)
- REST Countries API v5, Open-Meteo, World Bank API, ExchangeRate API, GNews API

---

# ⚠️ Batasan & Catatan Jujur

- **Data GDP/Inflasi tidak tersedia untuk ~47 negara/wilayah** (mis. Korea Utara, Taiwan, Kosovo, sejumlah teritori kecil) karena memang tidak dilaporkan ke World Bank — bukan kesalahan sistem.
- **Data histori kurs mata uang bersifat simulasi**, dibuat untuk mendemonstrasikan perhitungan volatilitas pada Risk Scoring Engine.
- **Berita per-negara** hanya benar-benar spesifik untuk negara yang sudah pernah dibuka halamannya (mekanisme *on-demand fetch* dengan cache 24 jam, untuk menghemat kuota GNews API 100 request/hari). Negara yang belum pernah diakses akan memakai skor sentiment rata-rata global sebagai fallback.
- **GNews API free tier** memiliki delay hingga 12 jam untuk berita real-time.

---

# 👨‍💻 Developer

**Globalis**
Developed as a Final Project for the Information Systems Study Program.

---

# 📄 License

Copyright © 2026 Globalis
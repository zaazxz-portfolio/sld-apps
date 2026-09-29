# EduStyle AI

**Aplikasi Pemetaan & Analisis Gaya Belajar Siswa (VAK) berbasis AI**

---

## 🎯 Tentang Project

EduStyle AI membantu guru dan siswa memahami **cara belajar terbaik** bagi setiap individu melalui:

1. **Data Siswa** — identitas + jenjang (SMP/SMK)
2. **Kuesioner Adaptif** — 12 soal VAK berbeda per jenjang
3. **Analisis AI** — profil karakteristik, hambatan, rekomendasi personal
4. **Laporan Siap Pakai** — PDF dengan tanda tangan, disimpan otomatis di database

Output laporan: persentase VAK, analisis karakteristik, strategi belajar mandiri, rekomendasi sekolah/bengkel, media digital, panduan guru berdiferensiasi.

---

## 🧱 Stack

| Layer | Tech |
|-------|------|
| Backend | PHP 8.3 Native (no framework) |
| Database | SQLite (file `database.db`) |
| Frontend | Tailwind CSS (CDN), jQuery, Lucide Icons |
| AI | 9Router API (OpenAI-compatible `/v1/chat/completions`) |
| Infra | Docker + docker compose |
| Routing | Filesystem-based (`src/pages/**`) |

---

## 🚀 Instalasi Lengkap

### Prerequisites
- **Docker** (compose plugin built-in)
- Port 8080 (atau sesuaikan `APP_PORT`)

### Langkah-langkah

```bash
# 1. Clone repository
git clone <repo-url> sld-apps
cd sld-apps

# 2. Salin .env dari contoh
cp .env.example .env

# 3. (Opsional) Salin database dari contoh
#    database.example.db sudah dilengkapi schema (settings + analyses)
cp database.example.db database.db

# 4. Build & start container
docker compose up -d --build

# 5. Buka browser
http://localhost:8080
```

### First Run — Konfigurasi API

1. Buka `http://localhost:8080`
2. Klik **Pengaturan API 9Router**
3. Jawab soal kode (`typeof null` → `object`) untuk membuka akses
4. Isi **API URL**, **API Key**, pilih **Model** dari dropdown
5. Klik **Simpan Konfigurasi**

> Jika dropdown model kosong/gagal, klik **Muat Ulang Model** atau pastikan URL & Key benar.

### Perintah Berguna

```bash
docker compose up -d --build     # start (rebuild jika Dockerfile berubah)
docker compose down              # stop & hapus container
docker compose logs -f app       # lihat log aplikasi
docker compose restart app       # restart container
```

---

## ⚙️ Konfigurasi

Edit `.env`:

```env
APP_PORT=8080
DB_DRIVER=sqlite
DB_SQLITE_PATH=/var/www/html/database.db
```

Lalu buka `http://localhost:8080` → klik **Pengaturan API 9Router** → isi:
- **API URL**: `https://api.9router.com` (atau endpoint custom)
- **API Key**: `NINEROUTER_KEY` anda
- **Model**: pilih dari dropdown (auto-fetch dari `/v1/models`)

---

## 📁 Struktur Folder

```
sld-apps/
├── compose.yaml          # Docker compose (app only)
├── Dockerfile            # PHP 8.3 + Apache + SQLite + Xdebug
├── .env                  # Konfigurasi runtime
├── public/
│   └── index.php         # Entry point + filesystem router
├── src/
│   ├── components/
│   │   └── template.php  # Layout utama (header, footer, print CSS)
│   ├── includes/
│   │   └── db.php        # PDO SQLite + auto-create tables
│   └── pages/
│       ├── index.php     # Home: form + kuesioner + submit AI
│       ├── analisis/
│       │   └── index.php # Laporan detail (GET /analisis?id=X)
│       ├── history/
│       │   └── index.php # Riwayat datatable + search
│       ├── about/
│       │   └── index.php # Tentang project
│       └── api/
│           ├── analyze.php  # POST /api/analyze  (simpan + AI)
│           ├── analyses.php # GET  /api/analyses (list/detail)
│           └── models.php   # GET  /api/models   (proxy model list)
├── database.db           # SQLite file (auto-create)
└── docker/
    ├── apache/vhost.conf
    └── php/
        ├── php-dev.ini
        └── xdebug.ini
```

---

## 🔐 Akses Pengaturan API

> **Easter egg:** Sebelum bisa ubah konfigurasi API, user harus menjawab soal coding sederhana:
> ```js
> console.log(typeof null); // jawaban: "object"
> ```
> Jawaban benar → tersimpan di `localStorage` → akses terbuka permanen (sampai clear storage).

---

## 🖨️ Print-Optimized

Laporan (`/analisis?id=X`) sudah di-tailor untuk cetak PDF:
- `@page { margin: 0 }` — hapus header/footer browser
- `#reportDocument { padding: 1.5cm 1cm }` — margin aman
- `break-inside: avoid` pada kartu rekomendasi — anti-terpotong saat ganti halaman
- Grid → block saat print — layout rapi

---

## 🗄️ Database Schema (Auto-Create)

```sql
settings (
  id INTEGER PRIMARY KEY CHECK (id = 1),
  ai_api_key TEXT,
  ai_api_url TEXT,
  ai_model TEXT DEFAULT 'coding',
  updated_at TEXT DEFAULT datetime('now','localtime')
)

analyses (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nama, nisn, kelas, sekolah, jenjang, jurusan TEXT,
  answers_json TEXT,       -- [{id, answer, type}]
  scores_json TEXT,        -- {visual, auditori, kinestetik}
  percentages_json TEXT,   -- {visual, auditori, kinestetik}
  ai_result_json TEXT,     -- parsed AI JSON
  created_at TEXT DEFAULT datetime('now','localtime')
)
```

---

## 🛠️ Vibe Coding

> **Ini proyek *vibe coding*** — dibuat cepat, fungsional, tapi **masih sangat memungkinkan untuk diperbaiki dan dimodifikasi**.

Hal-hal yang bisa ditingkatkan:
- Validasi input lebih ketat (CSRF, sanitasi XSS)
- Unit test / integration test
- Migrasi ke DB abstraction / query builder
- Pagination & filter lanjutan di `/history`
- Export Word / Excel
- Multi-user / auth (saat ini single-tenant)
- UI/UX polish (dark mode, accessibility, mobile)
- Caching model list agar tidak fetch berulang
- Error handling AI lebih detail (retry, fallback model)

PR & issue welcome! 🎉

---

## 📄 Lisensi

MIT — bebas dipakai, dimodifikasi, dikomersialkan.

---

**Dibuat oleh [TemannGoding](https://temanngoding.id/)** — platform belajar pemrograman untuk menemukan cara belajar paling efektif.
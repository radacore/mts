# 🔬 EIPA — Sistem Informasi Laboratorium Sekolah (MTS)

EIPA adalah aplikasi web untuk pengelolaan **laboratorium sekolah** secara digital: mulai dari inventaris alat & bahan, peminjaman lab/alat/kegiatan, kelas digital guru-siswa, materi ajar (modul & LKPD), tugas, hingga absensi.

Arsitekturnya terdiri dari **3 service terpisah** yang berkomunikasi via REST API:

| Service     | Tech                      | Port   | Audiens                      |
| ----------- | ------------------------- | ------ | ---------------------------- |
| `backend/`  | Laravel 9 + Passport      | `8000` | API untuk semua client       |
| `frontend/` | Vue 3 + Quasar 2 + Vuex 4 | `8081` | Dashboard admin/laboran/guru |
| `siswa/`    | Vue 3 + Quasar 2 + Vuex 4 | `8082` | Portal terpisah untuk siswa  |

---

## 📋 Daftar Isi

- [Arsitektur Sistem](#-arsitektur-sistem)
- [Tech Stack](#-tech-stack)
- [Struktur Folder](#-struktur-folder)
- [Role & Hak Akses](#-role--hak-akses)
- [Fitur Utama](#-fitur-utama)
- [Modul Backend](#-modul-backend)
- [Endpoint API Penting](#-endpoint-api-penting)
- [Skema Database](#-skema-database)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Build Produksi](#-build-produksi)
- [Validasi Bisnis](#-validasi-bisnis-yang-sudah-aktif)
- [Import Data Siswa](#-import-data-siswa)
- [Troubleshooting](#-troubleshooting)
- [Roadmap](#-roadmap)
- [Lisensi](#-lisensi)

---

## 🏗 Arsitektur Sistem

```
┌──────────────────────┐     ┌──────────────────────┐
│  Dashboard           │     │  Portal Siswa        │
│  Vue 3 + Quasar 2    │     │  Vue 3 + Quasar 2    │
│  http://:8081        │     │  http://:8082        │
└──────────┬───────────┘     └──────────┬───────────┘
           │ axios + Bearer token                  │
           └───────────────┬───────────────────────┘
                           ▼
              ┌─────────────────────────┐
              │  Backend REST API       │
              │  Laravel 9 + Passport   │
              │  http://:8000/api       │
              └────────────┬────────────┘
                           ▼
              ┌─────────────────────────┐
              │  MySQL 5.7+ / MariaDB   │
              │  Database: mts_db       │
              └─────────────────────────┘
```

**SSO antar aplikasi**: ketika user dengan `role_id=4` (siswa) login lewat dashboard `frontend/`, token Passport otomatis di-passing ke `siswa/` via redirect `http://localhost:8082/auto-login?token=...` sehingga siswa tidak perlu login dua kali.

---

## 🛠 Tech Stack

### Backend (`backend/`)

- **PHP** ^8.0.2
- **Laravel Framework** ^9.19
- **Laravel Passport** ^11.3 — OAuth2 access token
- **Laravel Sanctum** ^3.0 — fallback API token
- **maatwebsite/excel** ^3.1 — import siswa dari `.xlsx`/`.csv`
- **Guzzle HTTP** ^7.2
- **MySQL / MariaDB**

### Frontend & Siswa (`frontend/`, `siswa/`)

- **Vue** ^3.2.13
- **Quasar Framework** ^2.0.0 (via `vue-cli-plugin-quasar`)
- **Vuex** ^4.0.0
- **Vue Router** ^4.0.3
- **Axios** ^1.1.3
- **Moment.js** ^2.29.4 (locale `id`)
- **@meforma/vue-toaster** — notifikasi toast
- **pdfvuer** + **vue3-html2pdf** — pratinjau & cetak PDF
- **v-viewer** — galeri foto inventaris

---

## 📁 Struktur Folder

```
mts/
├── backend/                          # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/api/     # 14 controller utama
│   │   ├── Models/                   # 27 model Eloquent
│   │   └── Imports/                  # importSiswa.php
│   ├── database/
│   │   ├── migrations/               # 50+ migration
│   │   └── seeders/
│   ├── routes/
│   │   ├── api.php                   # Route API utama
│   │   └── web.php
│   ├── storage/app/public/           # Upload foto, modul, lkpd, slide, profil
│   ├── config/                       # auth.php, passport, cors, dll
│   └── composer.json
│
├── frontend/                         # Dashboard admin/laboran/guru
│   ├── src/
│   │   ├── views/                    # 22 halaman (PinjamLab, Inventaris, dst)
│   │   ├── components/               # Komponen reusable (JadwalLab, BuktiAlat, dst)
│   │   ├── router/index.js
│   │   ├── store/                    # Vuex modules (auth, kontrol)
│   │   └── main.js                   # axios.defaults.baseURL
│   └── package.json
│
├── siswa/                            # Portal siswa
│   ├── src/
│   │   ├── views/                    # Login, Kelas, Tugas, Absen, dll
│   │   ├── components/
│   │   ├── router/index.js
│   │   └── main.js
│   └── package.json
│
├── plans/                            # Catatan rencana fitur
├── start-all.sh                      # Spawn 3 service sekaligus (lokal, di-ignore)
├── mts_db.sql                        # Dump database (lokal, di-ignore)
├── FORMAT_IMPORT_SISWA.md            # Format Excel import siswa (lokal)
├── PANDUAN_MIGRATE_VPS.md            # Panduan deploy ke VPS
└── README.md
```

---

## 👥 Role & Hak Akses

Sistem mengenal **4 role** yang disimpan di tabel `roles` dan dipakai di kolom `users.role_id`.

| `role_id` | Role            | Login Lewat | Hak Akses Ringkas                                                                                 |
| --------: | --------------- | ----------- | ------------------------------------------------------------------------------------------------- |
|       `1` | **Super Admin** | `frontend/` | Full akses: kelola user, role, rombel, site settings, semua peminjaman & approval                 |
|       `2` | **Laboran**     | `frontend/` | Inventaris (CRUD + mutasi stok), katalog praktikum, **approve/tolak peminjaman**, ruang praktikum |
|       `3` | **Guru**        | `frontend/` | Pengajuan peminjaman lab/alat/lain, kelas digital, materi ajar, tugas, modul/LKPD                 |
|       `4` | **Siswa**       | `siswa/`    | Lihat kelas, kumpul tugas, absensi, akses modul/LKPD                                              |

Saat siswa salah login di `frontend/`, sistem mendeteksi `role_id=4` dan otomatis melempar token ke portal siswa.

---

## ✨ Fitur Utama

### 👨‍💼 Super Admin

- CRUD user (super user, guru, siswa)
- Master role
- Manajemen **rombel/kelas** (rombongan belajar)
- **Site Settings** (judul aplikasi, info sekolah, dll)
- **Slide** landing page
- **Informasi Terkini** (banner pengumuman dengan jadwal `mulai_at`–`selesai_at`)
- Akses semua data lintas modul

### 👨‍🔬 Laboran

- **Inventaris** alat & bahan (CRUD + foto, kondisi, jumlah, lokasi)
- **Inventaris Mutations** — log perubahan stok untuk barang _consumable_ (bahan habis pakai)
- **Katalog Praktikum** — pengelompokan inventaris per topik
- **Approval peminjaman**: Lab, Alat, dan Kegiatan Lain
  - Status: `diajukan` → `disetujui` / `ditolak` (wajib alasan) / `dikembalikan` (khusus alat)
- **Ruang Praktikum** — daftar lokasi lab fisik
- Mengontrol **penutupan lab sementara** via Informasi Terkini bertipe `penutupan_lab`

### 👨‍🏫 Guru

- **Bio Guru** (profil & foto)
- **Classroom** — buat kelas digital, kelola anggota
- **Materi Ajar** — upload materi (file/link)
- **Penugasan** — buat tugas dengan tipe submission: teks, file, atau link
- **Modul & LKPD** — arsip modul praktikum (link ke peminjaman lab)
- **Pengajuan peminjaman**:
  - Lab (jadwal + topik + modul/LKPD opsional)
  - Alat (terkait katalog + jumlah yang dibutuhkan)
  - Kegiatan Lain (kegiatan non-praktikum)

### 👨‍🎓 Siswa (Portal Terpisah)

- Login lewat `siswa/` (port 8082) atau lewat SSO dari `frontend/`
- Join classroom guru
- Lihat **Materi Ajar** & **Modul/LKPD**
- Kumpul **tugas** sesuai tipe submission
- **Absensi** kelas (`jam_buka` ↔ `jam_tutup` ditentukan guru)

---

## 🧩 Modul Backend

Controller berada di `backend/app/Http/Controllers/api/`:

| Controller                    | Tanggung Jawab                                                                   |
| ----------------------------- | -------------------------------------------------------------------------------- |
| `authController`              | Login admin/guru/laboran, login siswa, info user, logout (revoke Passport token) |
| `userController`              | CRUD user lintas role, **import siswa via Excel**, kelola foto profil            |
| `bioguruController`           | Bio guru                                                                         |
| `roleController` (via routes) | Role management                                                                  |
| `rombelController`            | Rombongan belajar / kelas                                                        |
| `invController`               | Inventaris alat & bahan + mutasi stok                                            |
| `katalogController`           | Katalog topik praktikum + relasi ke inventaris                                   |
| `peminjamanController`        | **Pinjam Lab, Alat, Lain** + approval + validasi bentrok jadwal                  |
| `classroomController`         | Kelas digital, enroll/leave siswa, materi, tugas, absen                          |
| `labsiswaController`          | Endpoint khusus portal siswa (kelas-siswa, kumpul tugas)                         |
| `ModulLkpdController`         | Modul & LKPD (CRUD + relasi ke peminjaman lab)                                   |
| `informasiController`         | Informasi terkini (banner & penutupan lab)                                       |
| `notifikasiController`        | Notifikasi user (read/unread)                                                    |
| `dashboardController`         | Statistik dashboard                                                              |
| `landingController`           | Data publik untuk landing page                                                   |

Model Eloquent (27 model) berada di `backend/app/Models/`:
`User`, `role`, `bioguru`, `data_siswa`, `kelas`, `kelas_siswa`, `classroom`, `rombel` (via kelas), `inventaris`, `inventaris_mutation`, `katalog`, `data_katalog`, `pinjam_lab`, `pinjam_alat`, `pinjam_lain`, `jumlah_pinjam`, `jumlah_pinjam_alat`, `materi_ajar`, `penugasan`, `data_tugas`, `absensi`, `data_absen`, `ModulLkpd`, `silde`, `foto_profile`, `informasi_terkini`, `notifikasi_user`, `SiteSetting`.

---

## 🔌 Endpoint API Penting

Base URL: `http://127.0.0.1:8000/api` (default).

### Autentikasi

| Method | Endpoint       | Deskripsi                                       |
| ------ | -------------- | ----------------------------------------------- |
| `POST` | `/login`       | Login admin/guru/laboran → balas `access_token` |
| `POST` | `/login/siswa` | Login khusus siswa                              |
| `GET`  | `/info`        | Profil user terautentikasi + role + foto        |
| `GET`  | `/logout`      | Revoke token Passport                           |

Semua endpoint di bawah ini wajib header:

```
Authorization: Bearer <access_token>
```

### Peminjaman (`peminjamanController`)

| Method   | Endpoint                                      | Aksi                                                  |
| -------- | --------------------------------------------- | ----------------------------------------------------- |
| `GET`    | `/pinjamLab`                                  | List peminjaman lab (filter sesuai role)              |
| `POST`   | `/pinjamLab`                                  | Buat / update pengajuan lab                           |
| `GET`    | `/pinjamLab/{id}`                             | Detail / edit                                         |
| `GET`    | `/pinjamLab/copy/{id}`                        | Duplikasi                                             |
| `DELETE` | `/pinjamLab/{id}`                             | Hapus pengajuan                                       |
| `GET`    | `/pinjamAlat`                                 | List peminjaman alat                                  |
| `POST`   | `/pinjamAlat`                                 | Buat / update                                         |
| `GET`    | `/pinjamLain`                                 | List peminjaman kegiatan lain                         |
| `POST`   | `/pinjamLain`                                 | Buat / update                                         |
| `GET`    | `/peminjaman/{lab\|alat\|lain}`               | List untuk laboran                                    |
| `PUT`    | `/peminjaman/{lab\|alat\|lain}/{id}/{status}` | Ubah status (diajukan/disetujui/ditolak/dikembalikan) |

### Modul Utama Lain

- `GET|POST|PUT|DELETE /inventaris`, `/katalog`, `/rombel`
- `GET|POST|PUT|DELETE /classroom`, `/materi`, `/penugasan`, `/absensi`
- `GET|POST|PUT|DELETE /modul-lkpd`
- `GET|POST /informasi-terkini`, `/notifikasi`
- `POST /import-siswa` — upload `.xlsx`

📖 Daftar lengkap rute: lihat `backend/routes/api.php` (~150 baris).

---

## 🗄 Skema Database

Database: `mts_db` (lihat dump di `mts_db.sql`, lokal).

### Tabel Utama (dipersingkat)

```
users ─────── biogurus
  │
  ├── role_id ──→ roles
  ├── data_siswas (untuk role_id=4)
  └── foto_profiles

inventaris ──── inventaris_mutations  (log stok consumable)
data_katalogs ─── katalogs (topik praktikum)
     └── inventaris_id

kelas (rombel) ─── kelas_siswas ─── User (siswa)
classroom (kelas digital) ─── kelas_siswas
        ├── materi_ajars
        ├── penugasans ─── data_tugas (submission siswa)
        └── absensis ─── data_absens

pinjam_labs    (tgl, jam, jam_selesai, status, alasan_penolakan, modul_lkpd ⇄ M:N)
pinjam_alats   (tgl_pakai, jam_pakai, tgl_kembali, jam_kembali, status)
        └── jumlah_pinjam_alats (item × jumlah)
pinjam_lains   (tgl, mulai, selesai, kegiatan, status)

modul_lkpd ⇄ pinjam_labs (M:N)
informasi_terkini (tipe, mulai_at, selesai_at, status)
notifikasi_users (user_id, judul, pesan, tipe, tautan, meta, dibaca_at)
site_settings (key, value)
```

Status peminjaman:

- Lab & Lain: `diajukan`, `disetujui`, `ditolak`
- Alat: `diajukan`, `disetujui`, `ditolak`, `dikembalikan`

---

## 🚀 Instalasi

### Prasyarat

- PHP ^8.0.2 + ekstensi: `mbstring`, `pdo_mysql`, `gd`, `zip`, `bcmath`, `xml`, `curl`
- Composer 2.x
- Node.js 16+ & npm 8+
- MySQL 5.7+ atau MariaDB 10.3+

### 1. Clone Repository

```bash
git clone https://github.com/radacore/mts.git
cd mts
```

### 2. Setup Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Konfigurasi DB di .env (lihat bagian Konfigurasi)

# Buat database dulu:
#   mysql -u root -p -e "CREATE DATABASE mts_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Migrate + seeder (jika perlu)
php artisan migrate
# Atau restore dari dump lokal:
#   mysql -u root -p mts_db < ../mts_db.sql

# Install Passport (sekali saja)
php artisan passport:install

# Symlink storage
php artisan storage:link
```

### 3. Setup Frontend

```bash
cd ../frontend
npm install
```

### 4. Setup Portal Siswa

```bash
cd ../siswa
npm install
```

---

## ⚙ Konfigurasi

### Backend (`backend/.env`)

```env
APP_NAME=EIPA
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mts_db
DB_USERNAME=root
DB_PASSWORD=

# CORS — agar :8081 & :8082 bisa hit :8000
SANCTUM_STATEFUL_DOMAINS=localhost:8081,localhost:8082,127.0.0.1:8081,127.0.0.1:8082
```

### Frontend (`frontend/src/main.js`)

```js
axios.defaults.baseURL = "http://127.0.0.1:8000/api/";
```

### Portal Siswa (`siswa/src/main.js`)

```js
axios.defaults.baseURL = "http://127.0.0.1:8000/api/";
```

> **Catatan**: ubah base URL ini jika backend dipindah ke domain/port lain (misalnya saat deploy).

---

## ▶ Menjalankan Aplikasi

### Mode Development — 3 terminal terpisah

**Terminal 1 — Backend**

```bash
cd backend
php artisan serve
# → http://127.0.0.1:8000
```

**Terminal 2 — Dashboard**

```bash
cd frontend
npm run serve
# → http://localhost:8081
```

**Terminal 3 — Portal Siswa**

```bash
cd siswa
npm run serve
# → http://localhost:8082
```

### Skrip Lokal `start-all.sh` (opsional)

File `start-all.sh` (lokal, di-`.gitignore`) tersedia untuk menjalankan ketiga service sekaligus dengan PID tracking & log terpisah. Salin/sesuaikan jika butuh.

---

## 🏭 Build Produksi

```bash
# Dashboard
cd frontend && npm run build
# Output: frontend/dist/

# Portal Siswa
cd siswa && npm run build
# Output: siswa/dist/

# Backend (Laravel)
cd backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Detail deploy ke VPS: lihat **`PANDUAN_MIGRATE_VPS.md`** (lokal).

---

## ✅ Validasi Bisnis yang Sudah Aktif

### Peminjaman

- **Cek bentrok jadwal** (Lab & Lain) — pengajuan ditolak (HTTP 409) jika overlap dengan peminjaman lain yang berstatus `diajukan` / `disetujui`.
  - Algoritma overlap: `start_baru < selesai_lama` **AND** `selesai_baru > start_lama`
  - Status `ditolak` tidak dihitung → slot kembali bebas
  - Saat edit data sendiri, ID tersebut di-skip dari pemeriksaan
- **Jam selesai > jam mulai** (HTTP 422)
- **Penutupan Lab Sementara** — jika ada `informasi_terkini` aktif bertipe `penutupan_lab` dalam rentang `mulai_at`–`selesai_at`, pengajuan lab ditolak (HTTP 422).
- **Hak akses guru** — guru hanya bisa mengajukan untuk kelas yang dia ampu (cek via tabel `classrooms`).
- **Transisi status** — divalidasi (tidak boleh `disetujui` → `diajukan`, dst).
- **Alasan penolakan** — wajib diisi saat status diubah ke `ditolak`.

### Form (Frontend)

- Tanggal otomatis terisi hari ini saat tombol **Input** ditekan
- Field wajib ditandai `*` pada label
- Picker jam pakai format **24-jam** (`format24h`)
- Tampilan jam di tabel ringkas `HH:mm` (tanpa detik)
- Toast error menampilkan pesan dari backend (`error.response.data.message`) sehingga pesan bentrok jadwal terbaca jelas

---

## 📥 Import Data Siswa

Format file: **Excel (`.xlsx`)** atau **CSV**.

Template: `template_import_siswa.xlsx` / `template_import_siswa.csv` (lokal).
Panduan kolom & contoh: lihat **`FORMAT_IMPORT_SISWA.md`** (lokal).

Alur:

1. Super Admin buka halaman **Data Siswa** → tombol **Import**
2. Pilih file `.xlsx` / `.csv`
3. Backend memvalidasi duplikat berdasarkan NIS/NISN
4. Hasil: berapa baris berhasil + daftar baris yang ditolak beserta alasan

Implementasi: `backend/app/Imports/importSiswa.php` (pakai `maatwebsite/excel`).

---

## 🩺 Troubleshooting

### CORS error saat login dari `:8081` / `:8082`

- Pastikan `SANCTUM_STATEFUL_DOMAINS` di `.env` mencakup origin frontend
- Cek `backend/config/cors.php` → `allowed_origins`

### `419 Page Expired` saat POST

- Token tidak terkirim. Pastikan axios header `Authorization: Bearer <token>` terpasang (lihat `frontend/src/store/subcriber.js`).

### Foto inventaris/profil tidak muncul

- Jalankan `php artisan storage:link` di `backend/`

### `npm run serve` port bentrok

- Frontend pakai 8081, siswa pakai 8082. Pastikan tidak ada service lain yang pakai port tersebut.

### Database kosong setelah `migrate`

- Restore dari dump: `mysql -u root -p mts_db < mts_db.sql`

---

## 🗺 Roadmap

Yang sudah selesai (✅) dan yang masih direncanakan (⏳):

- ✅ Validasi bentrok jadwal **Pinjam Lab**
- ✅ Validasi bentrok jadwal **Pinjam Lain**
- ⏳ Validasi bentrok **Pinjam Alat** (perlu rule bisnis: bentrok per katalog atau per stok?)
- ⏳ Validasi `tgl_kembali ≥ tgl_pakai` untuk pinjam alat
- ⏳ Multi-day booking untuk lab (saat ini hanya 1 tanggal)
- ⏳ Notifikasi real-time (WebSocket / Pusher)
- ⏳ Export laporan peminjaman ke Excel/PDF
- ⏳ Mode gelap (dark mode) di Quasar

---

## 📜 Lisensi

Proyek internal sekolah. Hak cipta kontributor masing-masing. Tidak untuk distribusi publik tanpa izin.

---

## 👤 Kontributor

Lihat history Git untuk daftar kontributor:

```bash
git shortlog -sn --all
```

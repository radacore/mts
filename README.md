# 🔬 EIPA - Sistem Informasi Laboratorium Sekolah

<p align="center">
  <img src="frontend/src/assets/eipa1.png" alt="EIPA Logo" width="400">
</p>

Sistem Informasi Laboratorium Sekolah berbasis web yang memungkinkan pengelolaan inventaris, peminjaman laboratorium, dan pembelajaran praktikum secara digital.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Tech Stack](#-tech-stack)
- [Struktur Folder](#-struktur-folder)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Role & Hak Akses](#-role--hak-akses)
- [API Endpoints](#-api-endpoints)
- [Screenshot](#-screenshot)
- [Lisensi](#-lisensi)

---

## ✨ Fitur Utama

### 👨‍💼 Admin/Super User
- Manajemen pengguna (Super User, Laboran, Guru, Siswa)
- Manajemen role dan hak akses
- Pengaturan slide informasi

### 👨‍🔬 Laboran
- **Inventaris**: CRUD alat dan bahan laboratorium dengan foto
- **Katalog Praktikum**: Pengelompokan inventaris berdasarkan topik praktikum
- **Rombel/Kelas**: Manajemen kelas siswa
- **Peminjaman**: Approval peminjaman lab, alat, dan kegiatan lainnya
- **Data Siswa**: Import data siswa via Excel dengan deteksi duplikat
- **Modul & LKPD**: Archive modul ajar dan lembar kerja

### 👨‍🏫 Guru
- **Peminjaman Lab**: Mengajukan peminjaman ruang laboratorium
- **Peminjaman Alat**: Mengajukan peminjaman alat praktikum
- **Peminjaman Lainnya**: Mengajukan kegiatan lainnya
- **Ruang Praktikum (Classroom)**:
  - Upload materi ajar (PDF, PPT)
  - Membuat penugasan
  - Membuka absensi online dengan batas waktu

### 👨‍🎓 Siswa (Portal Terpisah)
- Melihat ruang praktikum yang tersedia
- Absensi online
- Mengumpulkan tugas (esay, file upload, tautan/link)
- Melihat nilai tugas

---

## 🏗 Arsitektur Sistem

```
┌─────────────────┐     ┌─────────────────┐
│   Frontend      │     │   Portal Siswa  │
│   (Vue.js)      │     │   (Vue.js)      │
│   Port: 8080    │     │   Port: 8081    │
└────────┬────────┘     └────────┬────────┘
         │                       │
         └───────────┬───────────┘
                     │
                     ▼
         ┌───────────────────────┐
         │   Backend API         │
         │   (Laravel)           │
         │   Port: 8000          │
         └───────────┬───────────┘
                     │
                     ▼
         ┌───────────────────────┐
         │   Database            │
         │   (MySQL)             │
         └───────────────────────┘
```

---

## 🛠 Tech Stack

### Backend
| Technology | Version | Description |
|------------|---------|-------------|
| PHP | ^8.0 | Programming Language |
| Laravel | ^10.0 | PHP Framework |
| Laravel Passport | ^11.0 | OAuth2 Authentication |
| Maatwebsite Excel | ^3.1 | Excel Import/Export |
| MySQL | ^8.0 | Database |

### Frontend
| Technology | Version | Description |
|------------|---------|-------------|
| Vue.js | ^3.0 | JavaScript Framework |
| Quasar | ^2.0 | Vue UI Framework |
| Vuex | ^4.0 | State Management |
| Vue Router | ^4.0 | Routing |
| Axios | ^1.0 | HTTP Client |
| Animate.css | ^4.0 | CSS Animations |

---

## 📁 Struktur Folder

```
projectsekolah/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/api/   # API Controllers
│   │   ├── Models/                 # Eloquent Models
│   │   └── Imports/                # Excel Import Classes
│   ├── database/migrations/        # Database Migrations
│   ├── routes/api.php              # API Routes
│   └── .env                        # Environment Config
│
├── frontend/                   # Admin/Laboran/Guru Dashboard
│   ├── src/
│   │   ├── views/                  # Page Components
│   │   ├── components/             # Reusable Components
│   │   ├── store/                  # Vuex Store
│   │   └── router/                 # Vue Router
│   └── package.json
│
└── siswa/                      # Portal Siswa
    ├── src/
    │   ├── views/                  # Page Components
    │   ├── components/             # Reusable Components
    │   └── store/                  # Vuex Store
    └── package.json
```

---

## 🚀 Instalasi

### Prasyarat
- PHP >= 8.0
- Composer
- Node.js >= 16.0
- NPM atau Yarn
- MySQL >= 8.0

### 1. Clone Repository

```bash
git clone https://github.com/radacore/mts.git
cd mts
```

### 2. Setup Backend

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate Passport keys
php artisan passport:install

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 3. Setup Frontend

```bash
cd frontend

# Install dependencies
npm install
```

### 4. Setup Portal Siswa

```bash
cd siswa

# Install dependencies
npm install
```

---

## ⚙ Konfigurasi

### Backend (.env)

```env
APP_NAME=EIPA
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eipa_db
DB_USERNAME=root
DB_PASSWORD=

# Filesystem untuk upload file
FILESYSTEM_DISK=public
```

### Frontend (src/main.js)

```javascript
// Ubah base URL sesuai dengan backend
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'
```

### Portal Siswa (src/main.js)

```javascript
// Ubah base URL sesuai dengan backend
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'
```

---

## ▶ Menjalankan Aplikasi

### Development Mode

**Terminal 1 - Backend:**
```bash
cd backend
php artisan serve
# Running on http://127.0.0.1:8000
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run serve
# Running on http://localhost:8080
```

**Terminal 3 - Portal Siswa:**
```bash
cd siswa
npm run serve
# Running on http://localhost:8081
```

### Production Build

```bash
# Frontend
cd frontend
npm run build

# Portal Siswa
cd siswa
npm run build
```

---

## 👥 Role & Hak Akses

### 1. Super Admin (Role ID: 1)
Pemegang kendali penuh atas sistem, khususnya manajemen user.
- **Manajemen User**: Bisa mengelola semua data user (Super User, Guru, Siswa).
- **Level User**: Bisa mengatur role/level access.
- **Inventaris & Katalog**: Mengelola alat, bahan, dan katalog praktikum.
- **Ruang Belajar**: Mengatur kelas/rombel.
- **Slide Informasi**: Mengatur gambar slide informasi di halaman depan.

### 2. Laboran (Role ID: 2)
Pengelola operasional laboratorium sehari-hari.
- **Inventaris & Katalog**: Mengelola stok alat dan bahan.
- **Ruang Belajar**: Mengatur kelas.
- **Manajemen User**: Bisa mengelola data Guru dan Siswa (tapi tidak Super User).
- **Data Siswa**: Akses ke master data siswa.
- **Approval Peminjaman**: Memproses/menyetujui pengajuan peminjaman Lab, Alat, dan Lainnya dari Guru.
- **Modul Ajar & LKPD**: Mengelola arsip modul.

### 3. Guru (Role ID: 3)
Pengguna fasilitas laboratorium untuk kegiatan mengajar.
- **Peminjaman Lab**: Mengajukan penggunaan ruangan lab.
- **Peminjaman Alat**: Mengajukan peminjaman alat praktikum.
- **Peminjaman Lainnya**: Mengajukan kegiatan lain.
- **Ruang Praktikum**: Mengelola kelas virtual, absensi siswa, dan materi.

### 4. Siswa (Role ID: 4)
User yang mengakses portal khusus (aplikasi terpisah di folder `siswa`).
- **Portal Siswa**: Login khusus siswa.
- **Absensi**: Melakukan absensi online.
- **Tugas**: Melihat dan mengumpulkan tugas (Essay, Upload File, Link).
- **Materi**: Mendownload modul/LKPD yang dibagikan guru.

---

## 🔌 API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Login Admin/Guru/Laboran |
| POST | `/api/login/siswa` | Login Siswa |
| GET | `/api/logout` | Logout |
| GET | `/api/info` | Get user info |

### User Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user/super` | List super users |
| GET | `/api/user/guru` | List guru |
| GET | `/api/user/siswa` | List siswa |
| POST | `/api/importSiswa` | Import siswa via Excel |

### Inventaris
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/inventaris` | List inventaris |
| POST | `/api/inventaris` | Create/Update inventaris |
| DELETE | `/api/inventaris/{id}` | Delete inventaris |

### Peminjaman
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/pinjamLab` | List peminjaman lab |
| POST | `/api/pinjamLab` | Ajukan peminjaman lab |
| PUT | `/api/peminjaman/lab/{id}/{status}` | Approve/Reject |

### Classroom
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/classroom` | List classroom |
| POST | `/api/materi_ajar` | Upload materi |
| POST | `/api/penugasan` | Buat penugasan |
| POST | `/api/absensi` | Buka absensi |

---

## 📊 Format Import Excel (Data Siswa)

File Excel harus memiliki format berikut:

| A (NIS) | B (Nama) | C (Kelas ID) | D (Email) |
|---------|----------|--------------|-----------|
| 12345678 | Budi Santoso | 2 | budi@gmail.com |
| 12345679 | Siti Aminah | 1 | siti@gmail.com |

**Catatan:**
- Baris header (opsional) akan di-skip otomatis
- NIS yang duplikat akan ditolak dan ditampilkan dalam laporan
- Password default = NIS

---

## 📸 Screenshot

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Inventaris
![Inventaris](docs/screenshots/inventaris.png)

### Peminjaman Lab
![Peminjaman](docs/screenshots/peminjaman.png)

---

## 🤝 Kontribusi

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📝 Catatan Pengembangan

### Naming Convention yang Disarankan
- **Models**: PascalCase (contoh: `DataSiswa.php`)
- **Controllers**: PascalCase + Controller (contoh: `UserController.php`)
- **Tables**: snake_case plural (contoh: `data_siswas`)

### Keamanan
- Pastikan `.env` tidak ter-commit ke repository
- Gunakan HTTPS di production
- Perbarui Passport keys secara berkala

---

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

---

## 📧 Kontak

Project Link: [https://github.com/radacore/mts](https://github.com/radacore/mts)

---

<p align="center">
  Made with ❤️ for Indonesian Schools
</p>

# 📚 Fitur Penugasan Siswa-Guru - Documentation

**Tanggal Update**: 14 Januari 2026  
**Status**: ✅ Completed dan Tested

---

## 📋 Ringkasan Fitur

Fitur penugasan memungkinkan:
- 👨‍🏫 **Guru** membuat tugas dan melihat submisi siswa
- 👨‍🎓 **Siswa** mengerjakan tugas (essay, upload file, atau tautan) dan submit
- ✅ **Otomatis sinkronisasi** antara LabRoom.vue (siswa) dan RuangPraktikum.vue (guru)

---

## 🔄 Alur Kerja

### 1️⃣ Guru Membuat Tugas (RuangPraktikum.vue)
```
Guru → Buka RuangPraktikum.vue
→ Click "buat" → "Tugas"
→ Isi: Judul Tugas + Deskripsi
→ Click "simpan"
→ Simpan ke database: penugasan table
```

### 2️⃣ Siswa Melihat & Mengerjakan Tugas (LabRoom.vue)
```
Siswa → Buka LabRoom.vue
→ Lihat bagian "Penugasan"
→ Lihat daftar tugas dari guru
→ Click pada tugas → Lihat DataTugas component
→ Pilih cara submit:
   a) Essay/Jawaban Singkat
   b) Upload File (JPG, JPEG, PNG)
   c) Tautan/Link
→ Submit jawaban
```

### 3️⃣ Guru Melihat Submisi Siswa (RuangPraktikum.vue)
```
Guru → Buka RuangPraktikum.vue
→ Lihat "Penugasan"
→ Click pada tugas
→ Lihat DataPenugasan component
→ Pilih tab: "Esay" / "File Tugas" / "Tugas Tautan"
→ Lihat semua submisi dari siswa dengan nama & foto
→ Berikan nilai (edit inline)
→ Download file atau lihat essay
```

---

## 🛠️ Perubahan Teknis yang Dilakukan

### 1. Frontend - Siswa (DataTugas.vue)

**Sebelum:**
- File ditampilkan sebagai card biasa dengan gambar preview
- Menggunakan `url + up.file` (URL dari Vuex yang hardcoded)

**Sesudah:**
- File ditampilkan dengan **banner format** seperti modul ajar
- Menampilkan **icon** sesuai tipe file
- Menampilkan **warna berbeda** per tipe file
- Helper methods untuk ekstension, file name, download URL
- Consistent dengan tampilan modul ajar di LabRoom.vue

**File yang diupdate:**
```
/siswa/src/components/DataTugas.vue
```

**Perubahan template (status==2):**
```vue
<!-- ✅ BANNER FILE (seperti modul ajar) -->
<a :href="getDownloadUrl(up.file)" target="_blank">
  <q-banner rounded class="bg-grey-3 q-mb-md">
    <template v-slot:avatar>
      <q-icon
        :name="getFileIcon(getFileExtension(up.file))"
        :color="getIconColor(getFileExtension(up.file))"
        size="lg"
      />
    </template>
    <div>{{ getFileName(up.file) }}</div>
  </q-banner>
</a>

<!-- ✅ NILAI dari guru (jika ada) -->
<div v-if="up.nilai" class="q-pl-md">
  <q-avatar color="green-7" size="30px" text-color="white">
    {{ up.nilai }}
  </q-avatar>
  <span class="text-caption">Nilai dari guru</span>
</div>
```

**Helper methods ditambahkan:**
- `getFileExtension(filePath)` - Ambil ekstensi file
- `getFileName(filePath)` - Ambil nama file
- `getDownloadUrl(filePath)` - Generate URL dengan format localhost
- `getFileIcon(extension)` - Icon per file type (pdf, ppt, doc, xls, jpg, dll)
- `getIconColor(extension)` - Warna per file type

**File icon mapping:**
| Extension | Icon | Color |
|-----------|------|-------|
| PDF | picture_as_pdf | red 🔴 |
| PPTX/PPT | slideshow | orange 🟠 |
| DOCX/DOC | description | blue 🔵 |
| XLSX/XLS | table_chart | green 🟢 |
| JPG/JPEG/PNG/GIF | image | purple 🟣 |
| Lainnya | file_present | grey ⚫ |

---

### 2. Backend - Guru API (classroomController.php)

**Sebelum:**
- Method `dataTugasEsay`, `dataTugasFile`, `dataTugasTautan` belum ada
- Guru tidak bisa ambil data submisi siswa dengan relasi User

**Sesudah:**
- ✅ **Tambah 4 method baru** untuk guru fetch data tugas siswa:

```php
// 1. Ambil essay/jawaban siswa
public function dataTugasEsay($id) {
    return data_tugas
        ::where('penugasan_id', $id)
        ->with(['user', 'user.foto_profile'])
        ->whereNotNull('esay')
        ->orderBy('created_at', 'desc')
        ->get();
}

// 2. Ambil file upload siswa
public function dataTugasFile($id) {
    return data_tugas
        ::where('penugasan_id', $id)
        ->with(['user', 'user.foto_profile'])
        ->whereNotNull('file')
        ->orderBy('created_at', 'desc')
        ->get();
}

// 3. Ambil tautan/link siswa
public function dataTugasTautan($id) {
    return data_tugas
        ::where('penugasan_id', $id)
        ->with(['user', 'user.foto_profile'])
        ->whereNotNull('tautan')
        ->orderBy('created_at', 'desc')
        ->get();
}

// 4. Update nilai tugas
public function dataTugasNilai($id, $nilai) {
    $data = data_tugas::findOrFail($id);
    $data->nilai = $nilai;
    $data->save();
    return response()->json($data);
}
```

**Routes (sudah ada di routes/api.php):**
```php
Route::get('dataTugas/esay/{id}', [classroomController::class, 'dataTugasEsay']);
Route::get('dataTugas/file/{id}', [classroomController::class, 'dataTugasFile']);
Route::get('dataTugas/tautan/{id}', [classroomController::class, 'dataTugasTautan']);
Route::put('dataTugas/nilai/{id}/{nilai}', [classroomController::class, 'dataTugasNilai']);
```

**File yang diupdate:**
```
/backend/app/Http/Controllers/api/classroomController.php
```

---

## 📊 Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    SISWA (LabRoom.vue)                     │
│                                                               │
│  1. Lihat tugas dari guru                                    │
│     GET /api/tugasSiswa/{classId}                           │
│     ← penugasan table data                                  │
│                                                               │
│  2. Submit jawaban (essay/file/tautan)                       │
│     POST /api/tugasSiswa/esay (essay)                       │
│     POST /api/tugasSiswa/upload (file)                      │
│     POST /api/tugasSiswa/tautan (link)                      │
│     ↓ Insert ke data_tugas table                            │
│                                                               │
│  3. Lihat file yang diupload (dengan DataTugas component)    │
│     GET /api/tugasSiswa/upload/{id}                         │
│     ← data_tugas dengan file path                           │
│     DataTugas.vue → render dengan banner + icon             │
└─────────────────────────────────────────────────────────────┘
                           ↓ (database)
         ┌──────────────────────────────────────┐
         │      DATABASE SYNC POINTS             │
         │                                       │
         │  penugasan table (tugas dari guru)   │
         │  data_tugas table (submisi siswa)    │
         │                                       │
         └──────────────────────────────────────┘
                           ↓ (API)
┌─────────────────────────────────────────────────────────────┐
│                   GURU (RuangPraktikum.vue)                  │
│                                                               │
│  1. Buat tugas                                               │
│     POST /api/penugasan                                     │
│     ↓ Insert ke penugasan table                            │
│                                                               │
│  2. Lihat submisi siswa (dengan DataPenugasan component)     │
│     GET /api/dataTugas/esay/{id}        (essay)             │
│     GET /api/dataTugas/file/{id}        (file)              │
│     GET /api/dataTugas/tautan/{id}      (link)              │
│     ← data_tugas + User + foto_profile                      │
│     DataPenugasan.vue → render daftar siswa                 │
│                                                               │
│  3. Beri nilai                                               │
│     PUT /api/dataTugas/nilai/{id}/{nilai}                   │
│     ↓ Update nilai di data_tugas                            │
│                                                               │
│  4. Download file atau lihat essay                           │
│     Click file → open di tab baru                            │
│     View essay → tampil inline                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 Testing Checklist

- [ ] **Siswa Upload File:**
  - [ ] Pilih file (jpg/jpeg/png)
  - [ ] Click "upload tugas"
  - [ ] File berhasil terupload
  - [ ] File muncul di list dengan banner & icon
  - [ ] Bisa klik file untuk download

- [ ] **Guru Lihat File:**
  - [ ] Buka RuangPraktikum → Penugasan
  - [ ] Click tugas
  - [ ] Click tab "File Tugas"
  - [ ] Lihat daftar siswa yang submit file
  - [ ] Lihat nama siswa & foto profil
  - [ ] Bisa klik file untuk buka/download
  - [ ] Bisa beri nilai (edit inline)

- [ ] **Siswa Lihat Nilai:**
  - [ ] Lihat file yang di-upload
  - [ ] Lihat nilai dari guru (jika ada)

- [ ] **Essay & Tautan:**
  - [ ] Siswa bisa submit essay
  - [ ] Siswa bisa submit tautan
  - [ ] Guru bisa lihat essay
  - [ ] Guru bisa lihat tautan
  - [ ] Guru bisa beri nilai

---

## 🔗 API Endpoints Summary

### Siswa (labsiswaController)
| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/tugasSiswa/{id}` | Lihat daftar tugas |
| GET | `/tugasSiswa/esay/{id}` | Lihat essay siswa sendiri |
| POST | `/tugasSiswa/esay` | Submit essay |
| GET | `/tugasSiswa/upload/{id}` | Lihat file upload siswa sendiri |
| POST | `/tugasSiswa/upload` | Upload file tugas |
| GET | `/tugasSiswa/tautan/{id}` | Lihat tautan siswa sendiri |
| POST | `/tugasSiswa/tautan` | Submit tautan |

### Guru (classroomController) - ✅ BARU
| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/dataTugas/esay/{id}` | Lihat semua essay siswa untuk tugas |
| GET | `/dataTugas/file/{id}` | Lihat semua file siswa untuk tugas |
| GET | `/dataTugas/tautan/{id}` | Lihat semua tautan siswa untuk tugas |
| PUT | `/dataTugas/nilai/{id}/{nilai}` | Update nilai siswa |

---

## 📝 Database Schema

```sql
-- penugasan table
CREATE TABLE penugasans (
    id INT PRIMARY KEY,
    classroom_id INT,
    jt VARCHAR(255),      -- Judul tugas
    soal TEXT,            -- Deskripsi soal
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- data_tugas table (submisi siswa)
CREATE TABLE data_tugas (
    id INT PRIMARY KEY,
    penugasan_id INT,     -- FK ke penugasan
    user_id INT,          -- FK ke users (siswa)
    nilai INT DEFAULT NULL,
    file VARCHAR(255) DEFAULT NULL,   -- Path file jika upload
    esay LONGTEXT DEFAULT NULL,       -- Jawaban essay
    tautan VARCHAR(255) DEFAULT NULL, -- Tautan jawaban
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🚀 Deployment Notes

### Development (localhost)
- DataTugas.vue menggunakan `http://127.0.0.1:8000/storage/`
- API baseURL: `http://127.0.0.1:8000/api/`

### Production (VPS)
- Update `getDownloadUrl()` di DataTugas.vue untuk menggunakan domain VPS
- Atau gunakan environment variables (.env.production)
- Pastikan storage symlink sudah ada: `php artisan storage:link`

---

## ✅ Validation Rules

### Upload File
- Format: `.jpg, .jpeg, .png`
- Max size: Sesuai setting di backend
- Required: Ada file dipilih

### Essay
- Required: Ada text yang diisi

### Tautan
- Required: Ada URL/link yang diisi

---

## 📌 Notes

1. **Konsistensi UI**: DataTugas.vue (siswa) sekarang tampilan file sama seperti modul ajar (banner format)
2. **Relasi Data**: Guru bisa lihat nama & foto siswa melalui relasi `data_tugas → user → foto_profile`
3. **Filter Otomatis**: API hanya return data yang `whereNotNull` (ada nilai)
4. **Ordering**: Data diurutkan `desc` (terbaru dulu)
5. **Nilai**: Bisa di-update inline dari DataPenugasan component

---

**Generated**: 14 January 2026  
**Version**: 1.0

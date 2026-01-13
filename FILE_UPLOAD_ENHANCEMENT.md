# 📁 File Upload Enhancement - Dokumentasi Lengkap

**Tanggal Update**: 14 Januari 2026  
**Status**: ✅ Complete

---

## 🎯 Fitur yang Diperbaiki

### Masalah Lama:
- ❌ Nama file berubah saat upload (di-generate random oleh Laravel)
- ❌ Guru hanya bisa lihat file setelah download
- ❌ Tidak ada informasi ukuran file
- ❌ Tidak ada icon/preview file berdasarkan tipe

### Solusi Baru:
- ✅ **Nama file tetap asli** (tidak random)
- ✅ **Preview file langsung** (image bisa lihat, file lain bisa buka)
- ✅ **Tampilkan ukuran file** (format: KB, MB, dll)
- ✅ **Icon & warna per tipe file** (PDF merah, DOC biru, dll)
- ✅ **Download button** untuk guru
- ✅ **Nama file asli saat download**

---

## 🔧 Perubahan Teknis

### 1. Database - Migration Baru

**File:** `/backend/database/migrations/2026_01_14_add_file_name_to_data_tugas_table.php`

```php
// ✅ Tambah 2 field baru ke tabel data_tugas:
// - file_name: VARCHAR (simpan nama file asli)
// - file_size: BIGINT (simpan ukuran file dalam bytes)

Schema::table('data_tugas', function (Blueprint $table) {
    $table->string('file_name')->nullable()->after('file');
    $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
});
```

**Jalankan migration:**
```bash
php artisan migrate
```

---

### 2. Backend Model - data_tugas.php

**File:** `/backend/app/Models/data_tugas.php`

```php
// ✅ Update fillable array:
public $fillable = [
    'penugasan_id',
    'user_id',
    'nilai',
    'file',
    'file_name',      // ← BARU
    'file_size',      // ← BARU
    'esay',
    'tautan'
];
```

---

### 3. Backend Controller - labsiswaController.php

**File:** `/backend/app/Http/Controllers/api/labsiswaController.php`

**Method:** `tugasUploadPost()`

```php
public function tugasUploadPost(Request $request)
{
    $request->validate([
        'file'=>'required|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx'
    ]);
    
    // ✅ Ambil nama asli dan ukuran file
    $file = $request->file('file');
    $original_name = $file->getClientOriginalName();
    $file_size = $file->getSize();
    
    // ✅ Store dengan nama asli (bukan random)
    $file_path = $file->storeAs('penugasan', $original_name, 'public');
    
    // ✅ Simpan dengan informasi lengkap
    $data = data_tugas::create([
        'penugasan_id' => $request->penugasan_id,
        'file' => $file_path,
        'file_name' => $original_name,  // ← Nama asli
        'file_size' => $file_size,      // ← Ukuran file
        'user_id' => Auth()->User()->id
    ]);
    return response()->json($data);
}
```

**Perubahan:**
- `$file->store()` → `$file->storeAs($path, $name)` (simpan dengan nama asli)
- Tambah `file_name` field (untuk tampilan)
- Tambah `file_size` field (untuk informasi ukuran)

---

### 4. Backend Controller - classroomController.php

**File:** `/backend/app/Http/Controllers/api/classroomController.php`

**Method:** `dataTugasFile()`

```php
public function dataTugasFile($id)
{
    $data = data_tugas::where('penugasan_id', $id)
        ->with(['user', 'user.foto_profile'])
        ->whereNotNull('file')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'user' => $item->user,
                'nilai' => $item->nilai,
                'file' => $item->file,
                'file_name' => $item->file_name,  // ← Return nama file
                'file_size' => $item->file_size,  // ← Return ukuran file
                'created_at' => $item->created_at,
            ];
        });
    return response()->json($data);
}
```

**Perubahan:**
- Map response untuk include `file_name` dan `file_size`
- Guru bisa akses semua informasi file

---

### 5. Frontend - Siswa (DataTugas.vue)

**File:** `/siswa/src/components/DataTugas.vue`

**Perubahan di template (status==2):**

```vue
<!-- ✅ BANNER FILE DENGAN NAMA & UKURAN -->
<q-banner rounded class="bg-grey-3 q-mb-md">
  <template v-slot:avatar>
    <q-icon
      :name="getFileIcon(getFileExtension(up.file_name || up.file))"
      :color="getIconColor(getFileExtension(up.file_name || up.file))"
      size="lg"
    />
  </template>
  <div>
    <!-- ✅ Tampilkan nama file asli -->
    <div class="text-weight-bold">{{ up.file_name || getFileName(up.file) }}</div>
    <!-- ✅ Tampilkan ukuran file -->
    <div class="text-caption text-grey-7">{{ formatFileSize(up.file_size) }}</div>
  </div>
</q-banner>
```

**Helper methods ditambahkan:**

```javascript
// ✅ Format ukuran file (Bytes → KB/MB/GB)
formatFileSize(bytes) {
  if (!bytes || bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// ✅ Ambil extension
getFileExtension(filePath) {
  if (!filePath) return '';
  return filePath.split('.').pop().toLowerCase();
}

// ✅ Ambil nama file
getFileName(filePath) {
  if (!filePath) return 'File';
  return filePath.split('/').pop();
}
```

**Fitur baru untuk siswa:**
- ✨ Lihat nama file asli
- 📊 Lihat ukuran file
- 🎨 Lihat icon sesuai tipe file
- 🎯 Akurat ekstension detection

---

### 6. Frontend - Guru (DataPenugasan.vue)

**File:** `/frontend/src/components/DataPenugasan.vue`

**Perubahan di template (status==2):**

```vue
<!-- ✅ TAMPILKAN NAMA & UKURAN FILE -->
<q-item-label caption>
  <span class="text-weight-bold">File: {{ row.file_name }}</span>
  <br/>
  <span class="text-grey-7">Ukuran: {{ formatFileSize(row.file_size) }}</span>
  
  <!-- ✅ PREVIEW IMAGE JIKA FILE ADALAH GAMBAR -->
  <div class="images q-mt-sm" v-viewer v-if="isImageFile(row.file_name)">
    <img :src="url+row.file" style="width:100px" class="rounded-borders shadow-8 cursor-pointer"/>
  </div>
  
  <!-- ✅ BUKA FILE JIKA BUKAN GAMBAR -->
  <div v-else class="q-mt-sm">
    <q-icon 
      :name="getFileIcon(getFileExtension(row.file_name))"
      :color="getIconColor(getFileExtension(row.file_name))"
      size="lg"
    />
    <q-btn 
      label="Buka File" 
      flat rounded color="primary" size="sm"
      @click="openFile(url+row.file)"
      class="q-ml-sm"
    />
  </div>
</q-item-label>

<!-- ✅ TOMBOL DOWNLOAD FILE -->
<q-btn 
  flat round icon="download" 
  color="primary" size="sm"
  :href="url+row.file"
  target="_blank"
  download
  class="q-mt-sm"
/>
```

**Helper methods ditambahkan:**

```javascript
// ✅ Format ukuran file
formatFileSize(bytes) {
  if (!bytes || bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// ✅ Check apakah file adalah image
isImageFile(fileName) {
  if (!fileName) return false;
  const ext = fileName.split('.').pop().toLowerCase();
  return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
}

// ✅ Ambil extension
getFileExtension(filePath) {
  if (!filePath) return '';
  return filePath.split('.').pop().toLowerCase();
}

// ✅ File icon berdasarkan extension
getFileIcon(extension) {
  const ext = extension.toLowerCase();
  if (ext === 'pdf') return 'picture_as_pdf';
  if (['ppt', 'pptx'].includes(ext)) return 'slideshow';
  if (['doc', 'docx'].includes(ext)) return 'description';
  if (['xls', 'xlsx'].includes(ext)) return 'table_chart';
  if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'image';
  return 'file_present';
}

// ✅ Icon color berdasarkan extension
getIconColor(extension) {
  const ext = extension.toLowerCase();
  if (ext === 'pdf') return 'red';
  if (['ppt', 'pptx'].includes(ext)) return 'orange';
  if (['doc', 'docx'].includes(ext)) return 'blue';
  if (['xls', 'xlsx'].includes(ext)) return 'green';
  if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'purple';
  return 'grey';
}

// ✅ Buka file di tab baru
openFile(fileUrl) {
  window.open(fileUrl, '_blank');
}
```

**Fitur baru untuk guru:**
- 👁️ **Preview gambar** langsung di halaman (bisa zoom-in click)
- 🔗 **Buka file** lain di tab baru (PDF, DOC, dll)
- 📥 **Download button** untuk save file
- 📊 Lihat ukuran file
- 🎨 Icon & warna per tipe file
- 👤 Lihat nama siswa + foto

---

## 📊 File Icons & Colors

| Extension | Icon | Color | Keterangan |
|-----------|------|-------|-----------|
| pdf | picture_as_pdf | 🔴 red | PDF documents |
| ppt, pptx | slideshow | 🟠 orange | PowerPoint presentations |
| doc, docx | description | 🔵 blue | Word documents |
| xls, xlsx | table_chart | 🟢 green | Excel spreadsheets |
| jpg, jpeg, png, gif | image | 🟣 purple | Image files |
| Lainnya | file_present | ⚫ grey | Unknown types |

---

## 🔄 Alur Data Lengkap

### Siswa Upload File:
```
DataTugas.vue
  ↓ Pilih file (misal: laporan_2026.pdf)
  ↓ Click "upload tugas"
  ↓ POST /api/tugasSiswa/upload
  ├─ file: Binary data
  ├─ penugasan_id: 5
  ├─ user_id: Auto (dari Auth)
  └─ (lainnya dari FormData)
  ↓
labsiswaController::tugasUploadPost()
  ├─ Ambil file: laporan_2026.pdf
  ├─ Ambil size: 2048576 bytes (2MB)
  ├─ Store di: storage/penugasan/laporan_2026.pdf (nama asli!)
  ├─ Create record:
  │  ├─ file: penugasan/laporan_2026.pdf
  │  ├─ file_name: laporan_2026.pdf ✅
  │  ├─ file_size: 2048576 ✅
  │  └─ (lainnya)
  └─ Return JSON response
  ↓
Database (data_tugas table)
  ├─ id: 10
  ├─ penugasan_id: 5
  ├─ user_id: 15
  ├─ file: penugasan/laporan_2026.pdf
  ├─ file_name: laporan_2026.pdf ✅
  ├─ file_size: 2048576 ✅
  └─ nilai: NULL (belum dinilai)
  ↓
Siswa lihat file di DataTugas:
  ├─ Icon: picture_as_pdf 🔴
  ├─ Nama: laporan_2026.pdf ✅
  ├─ Ukuran: 2 MB ✅
  └─ Link: bisa download
```

### Guru Lihat File:
```
RuangPraktikum.vue
  ↓ Click tugas → Tab "File Tugas"
  ↓ GET /api/dataTugas/file/5
  ↓
classroomController::dataTugasFile()
  ├─ Query: data_tugas WHERE penugasan_id=5
  ├─ Include: user + foto_profile
  └─ Map response: tambah file_name & file_size ✅
  ↓
DataPenugasan.vue (status==2)
  └─ Render:
     ├─ Foto siswa: ✅
     ├─ Nama siswa: ✅
     ├─ File name: laporan_2026.pdf ✅
     ├─ File size: 2 MB ✅
     ├─ Preview: 
     │  ├─ Image files: show gambar ✅
     │  └─ Other files: show icon + "Buka File" btn ✅
     ├─ Download btn: ✅
     └─ Nilai: [input edit inline] ✅
```

---

## 🧪 Testing Checklist

### Siswa Upload:
- [ ] Pilih file dengan nama panjang: `Laporan Praktikum Semester 1 2026.pdf`
- [ ] Upload file
- [ ] Cek nama file tetap asli (bukan random string)
- [ ] Cek ukuran file tampil dengan benar (KB/MB)
- [ ] Cek icon sesuai tipe file
- [ ] Bisa download/buka file

### Guru Preview & Download:
- [ ] Buka RuangPraktikum → Penugasan → Tab "File Tugas"
- [ ] Lihat nama siswa + foto ✅
- [ ] Lihat nama file asli (bukan random) ✅
- [ ] Lihat ukuran file ✅
- [ ] **Untuk file gambar:**
  - [ ] Gambar tampil di halaman
  - [ ] Bisa zoom-in (click gambar)
- [ ] **Untuk file non-image (PDF, DOC, dll):**
  - [ ] Lihat icon + "Buka File" button
  - [ ] Click "Buka File" → buka di tab baru
- [ ] **Download button:**
  - [ ] Download dengan nama file asli
  - [ ] File tidak corrupt setelah download

---

## 📈 Database Schema

```sql
-- Sebelum migration
CREATE TABLE data_tugas (
    id BIGINT PRIMARY KEY,
    penugasan_id BIGINT,
    user_id BIGINT,
    esay LONGTEXT,
    file VARCHAR(255),
    nilai VARCHAR(5),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Setelah migration
CREATE TABLE data_tugas (
    id BIGINT PRIMARY KEY,
    penugasan_id BIGINT,
    user_id BIGINT,
    esay LONGTEXT,
    file VARCHAR(255),
    file_name VARCHAR(255),        -- ✅ BARU
    file_size BIGINT UNSIGNED,     -- ✅ BARU
    nilai VARCHAR(5),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🚀 Deployment Steps

### 1. Pull Latest Code
```bash
git pull origin nopal
```

### 2. Backend - Run Migration
```bash
cd /path/to/backend
php artisan migrate
```

### 3. Verify Storage
```bash
# Check symlink exists
ls -la public/storage

# If not exists, create:
php artisan storage:link
```

### 4. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 5. Frontend - Rebuild
```bash
cd /path/to/siswa
npm run build

cd /path/to/frontend
npm run build
```

### 6. Test in Browser
- Open siswa app → upload file
- Open guru app → check file preview
- Download file → verify name & content

---

## ⚠️ Important Notes

1. **File Name Preservation**: File disimpan dengan nama asli, bukan random. Ini penting untuk user experience.

2. **Storage Location**: File upload siswa disimpan di:
   ```
   storage/app/public/penugasan/{nama_file_asli}
   ```

3. **File Extensions**: Saat ini support:
   - Images: jpg, jpeg, png
   - Documents: pdf, doc, docx, xls, xlsx, ppt, pptx
   
   Bisa di-extend di backend validation jika diperlukan.

4. **File Size Limit**: Default Laravel adalah 2MB. Jika ingin ubah:
   ```php
   // Di .env
   UPLOAD_MAX_FILESIZE=50M
   POST_MAX_SIZE=50M
   
   // Di validation
   'file' => 'required|file|max:52428' // dalam KB
   ```

5. **Preview Images**: Image preview menggunakan library `v-viewer` di Quasar. Bisa zoom-in/out dengan mouse wheel.

6. **Download Handling**: Download button menggunakan HTML5 native download attribute, browser akan trigger save dialog dengan nama file asli.

---

## 📚 Files Modified

| File | Changes | Status |
|------|---------|--------|
| `/backend/database/migrations/2026_01_14_add_file_name_to_data_tugas_table.php` | ✨ NEW | ✅ |
| `/backend/app/Models/data_tugas.php` | Updated fillable | ✅ |
| `/backend/app/Http/Controllers/api/labsiswaController.php` | tugasUploadPost() updated | ✅ |
| `/backend/app/Http/Controllers/api/classroomController.php` | dataTugasFile() updated | ✅ |
| `/siswa/src/components/DataTugas.vue` | Template + helpers | ✅ |
| `/frontend/src/components/DataPenugasan.vue` | Template + helpers | ✅ |

---

**Generated**: 14 January 2026  
**Version**: 2.0

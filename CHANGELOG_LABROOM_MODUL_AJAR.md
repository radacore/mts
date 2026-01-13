# Changelog - Update Fitur Modul Ajar di LabRoom.vue

## Ringkasan Perubahan
Fitur "Modul Ajar" di halaman siswa `LabRoom.vue` telah diupdate untuk menampilkan file modul dan link tambahan yang diupload guru melalui `RuangPraktikum.vue`. Fitur Absensi dan Penugasan tetap berfungsi seperti sebelumnya.

---

## 📝 Detail Perubahan

### 1. **Backend - `labsiswaController.php`**

#### Method yang diubah: `modul($id)`

**Sebelumnya:**
```php
public function modul($id)
{
    $data=materi_ajar::where('classroom_id', $id)->get();
    return response()->json($data);
}
```

**Sesudahnya:**
```php
public function modul($id)
{
    $data = materi_ajar::where('classroom_id', $id)
        ->with(['modul:id,judul,file_path,file_name'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($m) {
            $extension = null;
            $file_name = null;
            $file_path = null;

            if ($m->modul) {
                $file_name = $m->modul->file_name;
                $file_path = $m->modul->file_path;
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            }

            return [
                'id' => $m->id,
                'judul' => $m->judul,
                'des' => $m->des,
                'modul_id' => $m->modul_id,
                'modul_judul' => $m->modul ? $m->modul->judul : null,
                'modul_file_name' => $file_name,
                'modul_file_path' => $file_path,
                'modul_extension' => $extension,
                'link_tambahan' => $m->link_tambahan,
            ];
        });
    return response()->json($data);
}
```

**Penjelasan Perubahan:**
- ✅ Menambahkan relasi `with('modul')` untuk mengambil data file dari `ModulLkpd`
- ✅ Mapping data modul untuk menampilkan file details (nama, path, ekstensi)
- ✅ Menambahkan field `link_tambahan` ke response
- ✅ Mengurutkan data berdasarkan `created_at` descending (modul terbaru di atas)

---

### 2. **Frontend - `siswa/src/views/LabRoom.vue`**

#### Template - Bagian Modul Ajar

**Fitur yang ditambahkan:**

a) **Banner File Modul**
   - Menampilkan icon file dengan warna yang berbeda sesuai tipe file (PDF, PPT, DOC, etc)
   - File yang dapat di-download dengan link ke storage
   - Menampilkan nama file asli

b) **Judul & Deskripsi**
   - Menampilkan judul materi ajar
   - Menampilkan deskripsi materi

c) **Link Tambahan**
   - Menampilkan link tambahan (jika ada)
   - Format: "🔗 Link Tambahan" dengan icon external link
   - Link dapat langsung di-klik untuk membuka di tab baru

d) **Conditional Rendering**
   - Modul Ajar hanya ditampilkan jika ada data (`v-if="moduls.length"`)
   - File modul hanya ditampilkan jika ada (`v-if="mod.modul_file_path"`)
   - Link tambahan hanya ditampilkan jika ada (`v-if="mod.link_tambahan"`)

#### Script - Methods & Helpers

**3 Helper Functions Ditambahkan:**

a) **`getDownloadUrl(filePath)`**
   ```javascript
   // Mengubah file path menjadi URL yang dapat di-download
   // Support baik relative path maupun full URL
   ```

b) **`getFileIcon(extension)`**
   ```javascript
   // Return icon name sesuai tipe file:
   // PDF → picture_as_pdf (merah)
   // PPT/PPTX → slideshow (orange)
   // DOC/DOCX → description (biru)
   // XLS/XLSX → table_chart (hijau)
   // Default → file_present (abu-abu)
   ```

c) **`getIconColor(extension)`**
   ```javascript
   // Return warna icon sesuai tipe file
   // Membantu visual differentiation antara tipe file
   ```

---

## 📊 Struktur Data Response API

### Endpoint: `GET /api/modulAjar/{class_id}`

**Response Format:**
```json
[
  {
    "id": 1,
    "judul": "Pengenalan Kimia Dasar",
    "des": "Materi pengenalan konsep dasar kimia",
    "modul_id": 5,
    "modul_judul": "Kimia Dasar Module",
    "modul_file_name": "kimia_dasar.pdf",
    "modul_file_path": "modul/kimia_dasar.pdf",
    "modul_extension": "pdf",
    "link_tambahan": "https://youtu.be/example123"
  },
  {
    "id": 2,
    "judul": "Latihan Soal",
    "des": "Kumpulan soal latihan",
    "modul_id": null,
    "modul_judul": null,
    "modul_file_name": null,
    "modul_file_path": null,
    "modul_extension": null,
    "link_tambahan": null
  }
]
```

---

## 🔗 Relasi Database

```
materi_ajars
├── id (primary)
├── classroom_id (FK → classrooms)
├── judul
├── des
├── file (legacy/optional)
├── modul_id (FK → modul_lkpd) ← ✅ NEW
├── link_tambahan ← ✅ NEW
└── timestamps

modul_lkpd
├── id (primary)
├── judul
├── file_path
├── file_name
├── mime_type
├── uploaded_by (FK → users)
└── timestamps
```

---

## ✅ Fitur yang Tetap Berfungsi

1. **Absensi** - Tidak ada perubahan
   - Menampilkan jam buka & tutup absensi
   - Siswa dapat absen dengan `DataAbsen` component

2. **Penugasan** - Tidak ada perubahan
   - Menampilkan judul tugas dan soal
   - Siswa dapat mengumpulkan tugas dengan `DataTugas` component

3. **API Routes** - Tetap sama
   - `/api/absenSiswa/{id}` - Ambil data absensi
   - `/api/tugasSiswa/{id}` - Ambil data penugasan
   - `/api/modulAjar/{id}` - Ambil data modul (UPDATED)

---

## 🧪 Testing Checklist

- [ ] Backend API endpoint `/api/modulAjar/{id}` mengembalikan data dengan struktur baru
- [ ] File modul bisa di-download dari link yang ditampilkan
- [ ] Link tambahan terbuka di tab baru
- [ ] Icon file menampilkan warna yang sesuai dengan tipe file
- [ ] Modul section hanya tampil jika ada data
- [ ] File modul section hanya tampil jika ada file
- [ ] Link tambahan section hanya tampil jika ada link
- [ ] Absensi tetap berfungsi
- [ ] Penugasan tetap berfungsi
- [ ] Response API sesuai dengan expected format

---

## 📱 UI/UX Improvements

1. **Consistency** - Tampilan modul ajar sekarang konsisten dengan `RuangPraktikum.vue`
2. **File Type Indication** - Icon dan warna membantu siswa mengetahui jenis file
3. **Better Organization** - Judul, deskripsi, file, dan link sekarang terstruktur dengan baik
4. **Responsive** - Template menggunakan Quasar responsive classes
5. **Visual Hierarchy** - Separator dan spacing yang jelas antara setiap modul

---

## 🔄 Backwards Compatibility

- ✅ API endpoint yang lama tetap support
- ✅ Field `file` di `materi_ajar` masih ada (legacy)
- ✅ Field `modul_id` dan `link_tambahan` adalah optional
- ✅ Tidak ada breaking changes untuk fitur lain

---

## 📌 Notes

- Migration untuk menambah kolom `modul_id` dan `link_tambahan` sudah exist:
  - File: `2025_12_28_124421_add_link_tambahan_to_materi_ajar_table.php`
- Model `materi_ajar` sudah memiliki fillable untuk kedua field baru
- Model `ModulLkpd` sudah tersedia dengan relasi ke `User`

---

## 👨‍💻 Developer Contact

Jika ada pertanyaan atau issue, mohon check:
1. Pastikan migration sudah di-run: `php artisan migrate`
2. Pastikan endpoint API accessible: `GET /api/modulAjar/{class_id}`
3. Check browser console untuk error messages
4. Verify database columns exist di `materi_ajars` table


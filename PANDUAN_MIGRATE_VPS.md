# Panduan Aman `php artisan migrate` di VPS

Panduan ini dibuat untuk kondisi ketika `php artisan migrate --force` gagal karena
tabel `inventaris_mutations` sudah ada di database, tetapi migration
`2026_04_11_180000_create_inventaris_mutations_table` belum tercatat di tabel
`migrations` Laravel.

Tujuan panduan ini adalah membuat proses migrate di VPS aman, minim risiko, dan
tidak menghapus data production.

## Ringkasan Masalah

Laravel menentukan migration yang sudah pernah dijalankan dari tabel
`migrations`. Jika tabel fisik sudah ada, tetapi nama migration belum tercatat,
Laravel akan menganggap migration itu belum jalan.

Akibatnya saat menjalankan:

```bash
php artisan migrate --force
```

Laravel akan mencoba membuat ulang tabel `inventaris_mutations`, lalu MySQL
menolak karena tabel tersebut sudah ada.

Contoh error yang mungkin muncul:

```text
SQLSTATE[42S01]: Base table or view already exists: 1050 Table
'inventaris_mutations' already exists
```

Masalah ini bukan berarti data rusak. Ini adalah mismatch antara kondisi schema
database dan catatan migration Laravel.

## Prinsip Aman

Ikuti prinsip ini di VPS production:

1. Jangan menjalankan `migrate:fresh`.
2. Jangan menjalankan `migrate:reset`.
3. Jangan drop tabel `inventaris_mutations`.
4. Jangan menghapus isi tabel `inventaris_mutations`.
5. Backup database sebelum melakukan perbaikan.
6. Perbaiki catatan migration hanya setelah struktur tabel diverifikasi.

## File Migration yang Bermasalah

Migration yang perlu dicek:

```text
2026_04_11_180000_create_inventaris_mutations_table
```

File migration terkait:

```text
backend/database/migrations/2026_04_11_180000_create_inventaris_mutations_table.php
```

Tabel yang sudah ada:

```text
inventaris_mutations
```

## Solusi Utama yang Direkomendasikan

Solusi paling aman adalah:

1. Backup database.
2. Pastikan tabel `inventaris_mutations` sudah ada dan strukturnya benar.
3. Catat migration lama sebagai sudah berjalan di tabel `migrations`.
4. Jalankan `php artisan migrate --force` kembali.

Cara ini aman karena tidak mengubah data inventory, tidak membuat ulang tabel,
dan tidak menjalankan migration lama yang sebenarnya sudah tercermin di schema.

## Langkah Lengkap di VPS

Masuk ke folder project backend di VPS terlebih dahulu. Contoh:

```bash
cd /path/ke/project/backend
```

Sesuaikan path dengan lokasi project sebenarnya di VPS.

### 1. Aktifkan Mode Maintenance

Opsional, tetapi direkomendasikan jika aplikasi sedang dipakai.

```bash
php artisan down
```

Jika aplikasi tidak boleh downtime, langkah ini bisa dilewati, tetapi pastikan
tidak ada proses deployment lain yang berjalan bersamaan.

### 2. Backup Database

Gunakan `mysqldump`. Sesuaikan nama database, user, dan lokasi file backup.

Contoh:

```bash
mysqldump -u USER_DATABASE -p NAMA_DATABASE > backup-sebelum-migrate-$(date +%F-%H%M%S).sql
```

Pastikan file backup berhasil dibuat:

```bash
ls -lh backup-sebelum-migrate-*.sql
```

Jangan lanjut jika backup gagal.

### 3. Cek Status Migration

Jalankan:

```bash
php artisan migrate:status
```

Cari baris:

```text
2026_04_11_180000_create_inventaris_mutations_table
```

Jika statusnya `Pending`, lanjutkan ke langkah berikutnya.

Jika statusnya sudah `Ran`, masalah bukan dari migration ini dan perlu dicek
error migrate terbaru.

### 4. Cek Tabel `inventaris_mutations`

Masuk ke MySQL:

```bash
mysql -u USER_DATABASE -p NAMA_DATABASE
```

Lalu cek tabel:

```sql
SHOW TABLES LIKE 'inventaris_mutations';
```

Jika hasilnya kosong, jangan insert manual ke `migrations`. Artinya tabel memang
belum ada, dan error yang dialami bukan kasus mismatch ini.

Jika tabel ada, cek struktur:

```sql
DESCRIBE inventaris_mutations;
```

Minimal kolom yang diharapkan ada:

```text
id
inventaris_id
tahun
qty
jenis
keterangan
created_by
created_at
updated_at
```

Jika kolom-kolom utama tersebut ada, lanjut ke langkah berikutnya.

### 5. Cek Apakah Migration Sudah Tercatat

Masih di MySQL, jalankan:

```sql
SELECT *
FROM migrations
WHERE migration = '2026_04_11_180000_create_inventaris_mutations_table';
```

Jika hasilnya ada, jangan insert lagi.

Jika hasilnya kosong, lanjut ke langkah berikutnya.

### 6. Catat Migration Lama sebagai Sudah Jalan

Jalankan SQL berikut:

```sql
INSERT INTO migrations (migration, batch)
SELECT
    '2026_04_11_180000_create_inventaris_mutations_table',
    COALESCE(MAX(batch), 0) + 1
FROM migrations
WHERE NOT EXISTS (
    SELECT 1
    FROM migrations
    WHERE migration = '2026_04_11_180000_create_inventaris_mutations_table'
);
```

SQL ini aman untuk dijalankan sekali karena memiliki pengaman
`WHERE NOT EXISTS`. Jika migration sudah tercatat, query tidak akan menambahkan
duplikat.

Setelah itu cek ulang:

```sql
SELECT *
FROM migrations
WHERE migration = '2026_04_11_180000_create_inventaris_mutations_table';
```

Jika sudah muncul satu baris, keluar dari MySQL:

```sql
EXIT;
```

### 7. Jalankan Migration Normal

Kembali ke folder backend, jalankan:

```bash
php artisan migrate --force
```

Jika berhasil, Laravel akan menjalankan migration yang memang belum berjalan,
termasuk migration baru untuk relasi LKPD peminjaman jika belum dijalankan.

### 8. Bersihkan Cache Laravel

Setelah migrate berhasil:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Jika production menggunakan cache config, jalankan lagi:

```bash
php artisan config:cache
```

### 9. Matikan Mode Maintenance

Jika sebelumnya menjalankan `php artisan down`, aktifkan kembali aplikasi:

```bash
php artisan up
```

### 10. Cek Fitur yang Berkaitan

Cek halaman berikut setelah deployment:

```text
/inventaris
/pinjam-lab
/pinjam-alat
```

Yang perlu dipastikan:

1. Halaman inventaris tetap terbuka.
2. Riwayat stok inventaris tetap muncul.
3. Filter pemutihan tetap berjalan.
4. Form pinjam lab bisa memilih modul LKPD.
5. Form pinjam alat bisa memilih modul LKPD.

## Alternatif Sementara Jika Belum Mau Menyentuh Tabel `migrations`

Jika ingin deploy cepat dan belum ingin memperbaiki mismatch migration lama,
jalankan migration baru secara path-specific:

```bash
php artisan migrate --path=database/migrations/2026_05_05_010000_create_peminjaman_modul_lkpd_tables.php --force
```

Ini hanya solusi sementara. Full migrate masih bisa gagal di masa depan selama
catatan migration `inventaris_mutations` belum dibereskan.

## Cara Rollback Jika Ada Masalah

Jika setelah insert manual ternyata perlu membatalkan catatan migration tersebut,
hapus hanya baris migration itu dari tabel `migrations`:

```sql
DELETE FROM migrations
WHERE migration = '2026_04_11_180000_create_inventaris_mutations_table';
```

Jangan drop tabel `inventaris_mutations` kecuali sudah benar-benar paham
dampaknya dan sudah memiliki backup valid.

Jika terjadi masalah data besar, restore dari backup:

```bash
mysql -u USER_DATABASE -p NAMA_DATABASE < backup-sebelum-migrate-YYYY-MM-DD-HHMMSS.sql
```

Ganti nama file backup sesuai file yang dibuat sebelumnya.

## Checklist Aman Sebelum Menjalankan Full Migrate

Pastikan semua item berikut terpenuhi:

- [ ] Backup database sudah dibuat dan ukurannya masuk akal.
- [ ] `php artisan migrate:status` menunjukkan migration lama masih `Pending`.
- [ ] Tabel `inventaris_mutations` memang sudah ada.
- [ ] Struktur tabel `inventaris_mutations` sudah dicek.
- [ ] Baris migration belum ada di tabel `migrations` sebelum insert.
- [ ] Baris migration sudah ada setelah insert.
- [ ] `php artisan migrate --force` dijalankan setelah catatan migration rapi.
- [ ] Halaman `/inventaris`, `/pinjam-lab`, dan `/pinjam-alat` dicek setelah migrate.

## Kesimpulan

Untuk VPS production, solusi paling minim risiko adalah menyesuaikan catatan
Laravel di tabel `migrations` dengan kondisi schema yang sudah ada. Jangan
menghapus tabel dan jangan menjalankan reset/fresh migration di production.

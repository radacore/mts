# AGENTS.md — EIPA / MTS (E-IPA MTSN1 Makassar)

Operasional workspace untuk agent OpenCode di repo `radacore/mts`.
Sistem ini adalah **3 aplikasi monorepo** (Laravel API + dua front-end Vue/Quasar) untuk pengelolaan laboratorium sekolah.

## Arsitektur & Port (sumber kebenaran)

| App         | Path        | Stack                            | Port default |
| ----------- | ----------- | -------------------------------- | ------------ |
| Backend API | `backend/`  | Laravel 9 + Passport 11          | **8000**     |
| Dashboard   | `frontend/` | Vue 3 + Quasar 2 + Vuex 4        | **8081**     |
| Portal Siswa| `siswa/`    | Vue 3 + Quasar 2 + Vuex 4        | **8082**     |

- Port aktual: backend `8000`, frontend `8081`, siswa `8082` (lihat `frontend/.env` `VUE_APP_SISWA_URL`, `start-all.sh`, dan komentar di `siswa/src/router/index.js`).
- Tidak ada monorepo tool (Lerna/Nx/Turborepo). Tiap app install & jalankan sendiri.
- `backend` adalah sumber kebenaran domain: `backend/routes/api.php` adalah peta seluruh REST surface (semua prefix `/api`).

## File yang tidak ikut ke repo (`.gitignore` root)

Agen dari fresh clone **tidak akan melihat** ini — jangan referensikan sebagai input:
- `mts_db.sql` (dump DB lokal), `mts_db/`
- `start-all.sh`, `strukturdanalurnya.md`
- `FORMAT_IMPORT_SISWA.md`, `template_import_siswa.{csv,xlsx}`
- `.agents/`, `.roo/`, `.run/`, `.playwright-mcp/`, `.sisyphus/`, `skills-lock.json`
- `/hero-mobile-after-spacing.png`, `*.png|jpg|jpeg|webp` di root
- `.pi-lens/` di tiap app
- Lihat `.gitignore` root baris 1–34.

`PANDUAN_MIGRATE_VPS.md` adalah satu-satunya catatan dev/ops yang **ter-track** — penting untuk migration `inventaris_mutations` di production.

## Backend (`backend/`)

### Setup pertama (urutan wajib)

```bash
cd backend
cp .env.example .env && php artisan key:generate
# edit .env: DB_DATABASE=mts_db, DB_USERNAME, DB_PASSWORD
mysql -u root -p -e "CREATE DATABASE mts_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate                # atau: mysql -u root -p mts_db < ../mts_db.sql
php artisan passport:install       # generate OAuth personal + secret clients
php artisan storage:link           # symlink public/storage -> storage/app/public
```

Tanpa `passport:install`, semua endpoint `auth:api` akan 500. Tanpa `storage:link`, foto inventaris/profil/modul/LKPD tidak akan muncul di URL `/storage/...`.

### Kalau buntu, baca urutan file ini

```
backend/routes/api.php → app/Http/Controllers/api/*.php → app/Models/*.php → database/migrations/
```

Route file adalah peta satu-satunya untuk seluruh REST surface. Dari situ trace ke controller → model → migration kalau ada keraguan soal schema.

### Konvensi penting

- **Auth**: Passport OAuth2, guard `api` (`backend/config/auth.php:43`). Token di-attach sebagai `Authorization: Bearer <token>` oleh subscriber Vuex di front-end.
- **Konvensi model**: nama class `lowercase_snake_case` (mis. `pinjam_lab`, `data_siswa`, `inventaris_mutation`, `kelas_siswa`). Jangan rename ke PascalCase — bisa konflik dengan tabel existing.
- **Controller-heavy**: tidak ada service layer formal kecuali `App\Services\InventoryStockService` (untuk sync stok consumable `habis_pakai` di `peminjamanController`). Logic bisnis lain langsung di controller.
- `peminjamanController.php` sangat besar (~43KB, satu file). Saat debug, pakai header `@` atau `region` IDE; logic dipecah per method (`pinjamLabPost`, `peminjamanLabProses`, dll).
- **CORS**: tambahkan origin front-end di `backend/.env` `SANCTUM_STATEFUL_DOMAINS=localhost:8081,localhost:8082,127.0.0.1:8081,127.0.0.1:8082` dan `backend/config/cors.php` `allowed_origins` jika ada masalah preflight.
- **Migration berbahaya**: `inventaris_mutations` (2026_04_11_180000) bisa bentrok di production jika tabel sudah ada di DB tapi belum tercatat di tabel `migrations`. **Jangan** jalankan `migrate:fresh`/`migrate:reset` di VPS — baca `PANDUAN_MIGRATE_VPS.md` dulu.

### Test

```bash
cd backend && vendor/bin/phpunit          # atau: php artisan test
```

- 2 unit test nyata: `tests/Unit/InventoryStockServiceTest.php` dan `tests/Unit/PeminjamanAlatFilterTest.php`. Keduanya pakai **sqlite in-memory** di `setUp()` — tidak butuh MySQL jalan untuk test.
- Test lain di `tests/Unit/ExampleTest.php` dan `tests/Feature/ExampleTest.php` adalah boilerplate Laravel, tidak ada nilai cakupan.
- `phpunit.xml` punya suite `Unit` dan `Feature`. Tidak ada browser/E2E test di repo ini.

### Endpoint cheat sheet

- Login admin/guru/laboran: `POST /api/login` (`authController::login`)
- Login siswa: `POST /api/login/siswa` (`authController::loginSiswa`, `role_id=4` di-hardcode)
- Profil sendiri: `GET /api/info`
- Logout: `GET /api/logout` (revoke token)
- Modul: `pinjamLab`, `pinjamAlat`, `pinjamLain`, `inventaris`, `katalog`, `rombel`, `classroom`, `materi_ajar`, `penugasan`, `absensi`, `modul/lkpd`, `informasi-terkini`, `notifikasi`
- Import siswa: `POST /api/importSiswa` (multipart), positional columns — lihat catatan di bawah

### Import siswa — gotcha

`backend/app/Imports/importSiswa.php` membaca kolom Excel **berdasarkan urutan posisi**, bukan nama header. Urutan wajib: `NIS, NAMA, KELAS_ID, EMAIL`. NIS dipakai sebagai `username` + `password` default. Duplikat NIS di DB atau di file ditolak per baris (tidak gagal seluruh import).

## Front-end (`frontend/`) & Portal Siswa (`siswa/`)

### Setup & jalan

```bash
cd frontend && npm install && npm run serve   # http://localhost:8081
cd siswa    && npm install && npm run serve   # http://localhost:8082
```

`npm run serve` adalah `vue-cli-service serve`. Quasar di-load via `vue-cli-plugin-quasar` (`vue.config.js` `pluginOptions.quasar`), **bukan** Vite. `frontend` dan `siswa` punya `package.json` identik kecuali `siswa` menambahkan `vue-awesome-swiper` dan tidak punya `vue3-html2pdf`.

### URL yang hardcode (perlu diubah saat deploy)

- `frontend/src/main.js:12` dan `siswa/src/main.js:12` → `axios.defaults.baseURL = 'http://127.0.0.1:8000/api/'`
- `frontend/src/store/kontrol.js:5` dan `siswa/src/store/kontrol.js:5` → storage URL `'http://127.0.0.1:8000/storage/'`
- `frontend/.env` → `VUE_APP_SISWA_URL=http://localhost:8082` (dipakai `LoginView.vue:57` untuk redirect SSO)
- `frontend/vue.config.js:7` & `siswa/vue.config.js:7` → HTML title hardcode `"e-ipa mtsn1 kota makassar"`. Tidak ada env override.

Tidak ada abstraksi `process.env` di `main.js`/`kontrol.js` — perubahan deploy harus manual edit source.

### SSO Frontend → Siswa

`frontend/src/views/LoginView.vue` setelah `auth/signIn` sukses:
- jika `user.user.role_id === 4` (siswa) → logout dari frontend (reset store), lalu `window.location.href = \`${VUE_APP_SISWA_URL}/auto-login?token=...\``
- selain itu → ke `name: 'home'`

`siswa/src/views/AutoLogin.vue` membaca `this.$route.query.token`, dispatch `auth/attempt(token)`, lalu `replace({ name: 'ruang-praktikum' })`. **Pastikan `frontend/.env` ter-set**, kalau tidak `LoginView` akan toast error dan diam di tempat.

Quirk: `LoginView.vue:1` mulai dengan backtick stray (`\`<template>`) — harmless tapi kelihatan saat inspeksi.

### Pola umum front-end

- Vuex namespace `auth/` (`state.token`, `state.user`, getter `authenticated`). `auth/attempt` memvalidasi via `GET /api/info`.
- Subscriber `store/subcriber.js` (note: nama file **typo** "subcriber", bukan "subscriber") menyetel axios `Authorization` header + `localStorage.token` setiap mutation `auth/SET_TOKEN`.
- Router: `frontend/src/router/index.js` (~400 baris) pakai `beforeEnter` per-route, cek `store.getters['auth/authenticated']` — fallback ke `name: 'login'`. `siswa/src/router/index.js` lebih kecil, guard serupa.
- `App.vue` polling notifikasi tiap **15 detik** (`setInterval` di `created`).
- Tidak ada service layer API formal — view/component panggil `axios.get/post` langsung dengan path relatif. Upload selalu `FormData`.
- `kontrol` store (`SET_TRIGER`) jadi flag refresh lintas komponen.

## Konvensi commit & branch

- Branch aktif: `main`. Remotes lain: `aufa`, `nopal`, `rada` (multi-contributor).
- Conventional Commits; **subject Bahasa Indonesia**, lowercase, imperative, max 72 char, tanpa titik. Tipe (`feat`, `fix`, `chore`, dll) tetap English.
- **Jangan** `git push --force` atau `git reset --hard` tanpa instruksi eksplisit. Pesan lengkap via skill `commit-conventions` + command `/commit`.
- Commit jangan berisi `mts_db.sql`, dump DB, `.env`, atau `*.png` di root (sudah di-ignore, tapi cek `git status`).

## Hal yang sengaja tidak ada di repo

- Tidak ada `.github/` (CI/CD). Tidak ada Husky/lefthook/pre-commit.
- Tidak ada `opencode.json` repo-local.
- Tidak ada service layer front-end (`api/` di `src/`).
- Tidak ada Storybook, Vitest, atau Cypress.
- `siswa/package.json` `name: "frontend"` (copy-paste); tidak masalah tapi jangan dibuat referensi.

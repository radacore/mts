<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\userController;
use App\Http\Controllers\api\invController;
use App\Http\Controllers\api\rombelController;
use App\Http\Controllers\api\dashboardController;
use App\Http\Controllers\api\katalogController;
use App\Http\Controllers\api\peminjamanController;
use App\Http\Controllers\api\classroomController;
use App\Http\Controllers\api\labsiswaController;
use App\Http\Controllers\api\bioguruController;

Route::post('login', [authController::class, 'login']);
Route::post('login/siswa', [authController::class, 'loginSiswa']);
Route::get('statistik', [dashboardController::class, 'statistik']);
Route::get('jadwals', [dashboardController::class, 'jadwalLab']);
Route::get('slides', [dashboardController::class, 'dataSlide']);
Route::get('gurus', [dashboardController::class, 'guru']);
Route::get('pinjamlain', [peminjamanController::class, 'pinjamLainClient']);

// AUTHENTIFIKASI
Route::middleware('auth:api')->group(function () {
    Route::get('info', [authController::class, 'info']);
    Route::get('logout', [authController::class, 'logout']);
});

//DASHBOARD
Route::middleware('auth:api')->group(function () {
    Route::get('dataSlide', [dashboardController::class, 'dataSlide']);
    Route::post('dataSlide', [dashboardController::class, 'dataSlidePost']);
    Route::get('dataSlide/{id}', [dashboardController::class, 'dataSlideEdit']);
    Route::put('dataSlide/{id}/{stat}', [dashboardController::class, 'dataSlideStatus']);
    Route::delete('dataSlide/{id}', [dashboardController::class, 'dataSlideHapus']);
});

// USER
Route::middleware('auth:api')->group(function () {
    Route::get('user/super', [userController::class, 'super']);
    Route::post('user/super', [userController::class, 'superPost']);
    Route::get('user/super/{id}', [userController::class, 'superEdit']);
    Route::delete('user/super/{id}', [userController::class, 'superHapus']);
    Route::get('user/guru', [userController::class, 'guru']);
    Route::post('user/guru', [userController::class, 'guruPost']);
    Route::get('user/guru/{id}', [userController::class, 'guruEdit']);
    Route::delete('user/guru/{id}', [userController::class, 'guruHapus']);
    Route::get('user/siswa', [userController::class, 'siswa']);
    Route::post('user/siswa', [userController::class, 'siswaPost']);
    Route::get('user/siswa/{id}', [userController::class, 'siswaEdit']);
    Route::delete('user/siswa/{id}', [userController::class, 'siswaHapus']);
    Route::get('role', [userController::class, 'role']);
    Route::post('role', [userController::class, 'rolePost']);
    Route::get('role/{id}', [userController::class, 'roleEdit']);
    Route::post('role/update', [userController::class, 'roleUpdate']);
    Route::delete('role/{id}', [userController::class, 'roleHapus']);
    Route::post('importSiswa', [userController::class, 'importSiswa']);
    Route::get('importSiswa', [userController::class, 'importSiswaGet']);
    Route::delete('importSiswa/{id}', [userController::class, 'importSiswaHapus']);
    Route::post('importSiswa/multiple-delete', [userController::class, 'importSiswaMultipleHapus']);
    Route::get('cekUser/{email}', [userController::class, 'cekUser']);
    Route::post('userSiswa', [userController::class, 'userSiswa']);
    Route::delete('userSiswa/{id}', [userController::class, 'userSiswaHapus']);
    Route::post('resetPasswordSiswa', [userController::class, 'resetPasswordSiswa']);


    Route::get('user/superole', [userController::class, 'superole']);
    //PROFILE
    Route::get('profiles', [userController::class, 'profiles']);
    Route::post('profiles', [userController::class, 'upload']);
    Route::post('profiles/update', [userController::class, 'updateProfile']);
    Route::get('biodata', [bioguruController::class, 'guruBio']);
    Route::put('biodata', [bioguruController::class, 'guruBioUpdate']);
});

// INVENTARIS
Route::middleware('auth:api')->group(function () {
    Route::get('inventaris', [invController::class, 'index']);
    Route::post('inventaris', [invController::class, 'inventarisPost']);
    Route::get('inventaris/{id}', [invController::class, 'inventarisEdit']);
    Route::delete('inventaris/{id}', [invController::class, 'inventarisHapus']);
    Route::post('inventaris/foto', [invController::class, 'inventarisFoto']);

});

// KELAS
Route::middleware('auth:api')->group(function () {
    Route::get('rombel', [rombelController::class, 'index']);
    Route::post('rombel', [rombelController::class, 'rombelPost']);
    Route::get('rombel/{id}', [rombelController::class, 'rombelEdit']);
    Route::delete('rombel/{id}', [rombelController::class, 'rombelHapus']);
});

// KATALOG
Route::middleware('auth:api')->group(function () {
    Route::get('katalog', [katalogController::class, 'index']);
    Route::post('katalog', [katalogController::class, 'katalogPost']);
    Route::get('katalog/{id}', [katalogController::class, 'katalogEdit']);
    Route::delete('katalog/{id}', [katalogController::class, 'katalogHapus']);
    Route::get('katalog/data/{id}', [katalogController::class, 'katalogData']);
    Route::delete('katalog/data/{id}', [katalogController::class, 'katalogDataHapus']);

    Route::post('inventaris/pilih/{id}/{pilihan}', [katalogController::class, 'pilihInv']);
});

// PEMINJAMAN
Route::middleware('auth:api')->group(function () {
    Route::get('pinjamLab', [peminjamanController::class, 'index']);
    Route::post('pinjamLab', [peminjamanController::class, 'pinjamLabPost']);
    Route::get('pinjamLab/{id}', [peminjamanController::class, 'pinjamLabEdit']);
    Route::get('pinjamLab/copy/{id}', [peminjamanController::class, 'pinjamLabCopy']);
    Route::delete('pinjamLab/{id}', [peminjamanController::class, 'pinjamLabHapus']);

    Route::get('pinjamAlat', [peminjamanController::class, 'pinjamAlat']);
    Route::post('pinjamAlat', [peminjamanController::class, 'pinjamAlatPost']);
    Route::get('pinjamAlat/{id}', [peminjamanController::class, 'pinjamAlatEdit']);
    Route::get('pinjamAlat/copy/{id}', [peminjamanController::class, 'pinjamAlatCopy']);
    Route::delete('pinjamAlat/{id}', [peminjamanController::class, 'pinjamAlatHapus']);

    Route::get('peminjaman/lab', [peminjamanController::class, 'peminjamanLab']);
    Route::get('peminjaman/alat', [peminjamanController::class, 'peminjamanAlat']);
    Route::get('peminjaman/lain', [peminjamanController::class, 'peminjamanLain']);
    Route::put('peminjaman/lab/{id}/{data}', [peminjamanController::class, 'peminjamanLabProses']);
    Route::put('peminjaman/alat/{id}/{data}', [peminjamanController::class, 'peminjamanAlatProses']);
    Route::put('peminjaman/lain/{id}/{data}', [peminjamanController::class, 'peminjamanLainProses']);
    Route::get('filterTopik/{id}/{plid}', [peminjamanController::class, 'filterTopik']);
    Route::get('filterTopikAlat/{id}/{paid}', [peminjamanController::class, 'filterTopikAlat']);

    Route::get('pinjamLain', [peminjamanController::class, 'pinjamLain']);
    Route::post('pinjamLain', [peminjamanController::class, 'pinjamLainPost']);
    Route::get('pinjamLain/{id}', [peminjamanController::class, 'pinjamLainEdit']);
    Route::delete('pinjamLain/{id}', [peminjamanController::class, 'pinjamLainHapus']);

    Route::post('jumlahPinjam', [peminjamanController::class, 'jumlahPinjamPost']);
    Route::post('jumlahPinjamAlat', [peminjamanController::class, 'jumlahPinjamAlatPost']);
    Route::post('jumlahPinjam2', [peminjamanController::class, 'jumlahPinjamPost2']);
    Route::post('jumlahPinjamAlat2', [peminjamanController::class, 'jumlahPinjamAlatPost2']);
    Route::post('lkpds', [peminjamanController::class, 'lkpds']);
    Route::post('lkpdalat', [peminjamanController::class, 'lkpdalat']);
});

// CLASSROOM
Route::middleware('auth:api')->group(function () {
    Route::get('classroom', [classroomController::class, 'index']);
    Route::post('classroom', [classroomController::class, 'classroomPost']);
    Route::get('classroom/{id}', [classroomController::class, 'classroomEdit']);
    Route::delete('classroom/{id}', [classroomController::class, 'classroomHapus']);
    Route::get('classroom/cek/{id}', [classroomController::class, 'classroomCek']);

    Route::get('materi_ajar/{id}', [classroomController::class, 'materi']);
    Route::post('materi_ajar', [classroomController::class, 'materiPost']);
    Route::get('materi_ajar/edit/{id}', [classroomController::class, 'materiEdit']);
    Route::delete('materi_ajar/hapus/{id}', [classroomController::class, 'materiHapus']);

    Route::get('penugasan/{id}', [classroomController::class, 'penugasan']);
    Route::post('penugasan', [classroomController::class, 'penugasanPost']);
    Route::get('penugasan/edit/{id}', [classroomController::class, 'penugasanEdit']);
    Route::delete('penugasan/hapus/{id}', [classroomController::class, 'penugasanHapus']);

    Route::get('absensi/{id}', [classroomController::class, 'absensi']);
    Route::post('absensi', [classroomController::class, 'absensiPost']);
    Route::delete('absensi/hapus/{id}', [classroomController::class, 'absensiHapus']);
    Route::put('absensi/status/{id}', [classroomController::class, 'absensiStatus']);
    Route::get('dataAbsen/{id}', [classroomController::class, 'dataAbsen']);
    Route::get('dataTugas/esay/{id}', [classroomController::class, 'dataTugasEsay']);
    Route::get('dataTugas/file/{id}', [classroomController::class, 'dataTugasFile']);
    Route::get('dataTugas/tautan/{id}', [classroomController::class, 'dataTugasTautan']);
    Route::put('dataTugas/nilai/{id}/{nilai}', [classroomController::class, 'dataTugasNilai']);

    Route::get('filemodul', [classroomController::class, 'moduls']);

    // MODUL / LKPD - laboran/guru
    Route::get('modul/lkpd', [\App\Http\Controllers\api\ModulLkpdController::class, 'index']);
    Route::post('modul/lkpd', [\App\Http\Controllers\api\ModulLkpdController::class, 'store']);
    Route::delete('modul/lkpd/{id}', [\App\Http\Controllers\api\ModulLkpdController::class, 'destroy']);
});

// SISWA
Route::middleware('auth:api')->group(function () {
    Route::get('labsiswa', [labsiswaController::class, 'index']);
    Route::get('absenSiswa/{id}', [labsiswaController::class, 'absen']);
    Route::post('absenSiswa', [labsiswaController::class, 'absenPost']);
    Route::get('absenSiswa/cek/{id}', [labsiswaController::class, 'absenCek']);

    Route::get('modulAjar/{id}', [labsiswaController::class, 'modul']);
    Route::get('tugasSiswa/{id}', [labsiswaController::class, 'tugas']);
    Route::post('tugasSiswa/esay', [labsiswaController::class, 'tugasEsayPost']);
    Route::get('tugasSiswa/esay/{id}', [labsiswaController::class, 'tugasEsay']);
    Route::get('tugasSiswa/esay/edit/{id}', [labsiswaController::class, 'tugasEsayEdit']);
    Route::delete('tugasSiswa/esay/hapus/{id}', [labsiswaController::class, 'tugasEsayHapus']);
    Route::get('tugasSiswa/upload/{id}', [labsiswaController::class, 'tugasUpload']);
    Route::post('tugasSiswa/upload', [labsiswaController::class, 'tugasUploadPost']);
    Route::delete('tugasSiswa/upload/hapus/{id}', [labsiswaController::class, 'tugasUploadHapus']);
    Route::get('tugasSiswa/tautan/{id}', [labsiswaController::class, 'tugasTautan']);
    Route::delete('tugasSiswa/tautan/hapus/{id}', [labsiswaController::class, 'tugasTautanHapus']);
    Route::post('tugasSiswa/tautan', [labsiswaController::class, 'tugasTautanPost']);
});

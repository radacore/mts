-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 30, 2025 at 07:02 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mts_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensis`
--

CREATE TABLE `absensis` (
  `id` bigint UNSIGNED NOT NULL,
  `classroom_id` bigint UNSIGNED NOT NULL,
  `tgl` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tgl_absen` date NOT NULL,
  `jam_buka` time NOT NULL,
  `jam_tutup` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absensis`
--

INSERT INTO `absensis` (`id`, `classroom_id`, `tgl`, `status`, `created_at`, `updated_at`, `tgl_absen`, `jam_buka`, `jam_tutup`) VALUES
(4, 2, '2022-11-27', 'open', '2022-11-26 04:33:40', '2022-11-29 00:22:52', '2022-11-27', '01:10:00', '08:25:00'),
(5, 2, '1311-11-11', '1', '2025-12-30 06:01:24', '2025-12-30 06:01:24', '1311-11-11', '13:33:00', '12:23:00'),
(6, 2, '1311-11-11', '1', '2025-12-30 06:01:27', '2025-12-30 06:01:27', '1311-11-11', '13:33:00', '12:23:00'),
(7, 2, '1311-11-11', '1', '2025-12-30 06:01:28', '2025-12-30 06:01:28', '1311-11-11', '13:33:00', '12:23:00'),
(8, 2, '1311-11-11', '1', '2025-12-30 06:01:30', '2025-12-30 06:01:30', '1311-11-11', '13:33:00', '12:23:00'),
(9, 2, '1311-11-11', '1', '2025-12-30 06:01:31', '2025-12-30 06:01:31', '1311-11-11', '13:33:00', '12:23:00'),
(10, 2, '1311-11-11', '1', '2025-12-30 06:01:31', '2025-12-30 06:01:31', '1311-11-11', '13:33:00', '12:23:00'),
(11, 2, '1311-11-11', '1', '2025-12-30 06:01:31', '2025-12-30 06:01:31', '1311-11-11', '13:33:00', '12:23:00'),
(12, 2, '1311-11-11', '1', '2025-12-30 06:01:31', '2025-12-30 06:01:31', '1311-11-11', '13:33:00', '12:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `biogurus`
--

CREATE TABLE `biogurus` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `biogurus`
--

INSERT INTO `biogurus` (`id`, `user_id`, `nip`, `hp`, `created_at`, `updated_at`) VALUES
(4, 6, '12860034', '085299236781', '2023-01-06 12:31:45', '2023-01-06 12:32:36'),
(5, 7, '12860034', '086299236781', '2023-01-08 13:09:04', '2023-01-08 13:09:04'),
(6, 26, '1234566678911', '085213445667', '2023-01-11 00:45:47', '2023-01-11 00:45:47'),
(7, 27, '334455668901', '08134579988', '2023-01-11 00:50:13', '2023-01-11 00:50:13'),
(8, 28, '34569999087', '08766543221', '2023-01-11 00:52:25', '2023-01-11 00:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `katalog_id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`id`, `user_id`, `katalog_id`, `kelas_id`, `created_at`, `updated_at`) VALUES
(2, 7, 4, 3, '2022-11-23 18:04:59', '2022-11-23 18:04:59');

-- --------------------------------------------------------

--
-- Table structure for table `data_absens`
--

CREATE TABLE `data_absens` (
  `id` bigint UNSIGNED NOT NULL,
  `absensi_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_absens`
--

INSERT INTO `data_absens` (`id`, `absensi_id`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 4, 17, '2022-11-26 18:05:42', '2022-11-26 18:05:42');

-- --------------------------------------------------------

--
-- Table structure for table `data_katalogs`
--

CREATE TABLE `data_katalogs` (
  `id` bigint UNSIGNED NOT NULL,
  `katalog_id` bigint UNSIGNED NOT NULL,
  `inventaris_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_katalogs`
--

INSERT INTO `data_katalogs` (`id`, `katalog_id`, `inventaris_id`, `created_at`, `updated_at`) VALUES
(1, 4, 4, '2022-11-05 08:44:39', '2022-11-05 08:44:39'),
(2, 4, 3, '2022-11-05 08:44:39', '2022-11-05 08:44:39'),
(4, 1, 4, '2022-11-05 08:50:06', '2022-11-05 08:50:06'),
(5, 1, 3, '2022-11-05 08:50:06', '2022-11-05 08:50:06'),
(7, 2, 2, '2022-11-05 09:20:20', '2022-11-05 09:20:20'),
(8, 2, 4, '2022-11-05 09:24:50', '2022-11-05 09:24:50'),
(9, 4, 2, '2022-11-12 19:57:00', '2022-11-12 19:57:00'),
(11, 5, 2, '2022-11-29 11:03:52', '2022-11-29 11:03:52'),
(12, 5, 3, '2022-11-29 11:04:32', '2022-11-29 11:04:32'),
(13, 6, 3, '2022-11-29 11:11:42', '2022-11-29 11:11:42');

-- --------------------------------------------------------

--
-- Table structure for table `data_siswas`
--

CREATE TABLE `data_siswas` (
  `id` bigint UNSIGNED NOT NULL,
  `nis` char(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `ket` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_siswas`
--

INSERT INTO `data_siswas` (`id`, `nis`, `nama`, `kelas_id`, `ket`, `created_at`, `updated_at`, `email`) VALUES
(9, '3333', 'ayatullah', 2, NULL, '2022-12-10 14:11:59', '2022-12-10 14:11:59', 'aya@gmail.com'),
(10, '222', 'navia', 3, NULL, '2022-12-10 14:11:59', '2022-12-10 14:11:59', 'nav@mts.com');

-- --------------------------------------------------------

--
-- Table structure for table `data_tugas`
--

CREATE TABLE `data_tugas` (
  `id` bigint UNSIGNED NOT NULL,
  `penugasan_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `esay` text COLLATE utf8mb4_unicode_ci,
  `file` text COLLATE utf8mb4_unicode_ci,
  `nilai` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tautan` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `foto_profiles`
--

CREATE TABLE `foto_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `foto` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `foto_profiles`
--

INSERT INTO `foto_profiles` (`id`, `user_id`, `foto`, `created_at`, `updated_at`) VALUES
(1, 2, 'foto/user.jpg', NULL, NULL),
(3, 6, 'profile/8C85g0gW93FrvXmcYgdy9cXaemgh93LxaPHTvOvM.jpg', '2022-11-04 05:00:48', '2023-01-06 12:54:55'),
(4, 7, 'profile/0nQ30JeJVbvRAtNdFcIjG879ZHWlLkxazkTLGFZl.jpg', '2022-11-04 05:23:12', '2022-11-12 16:25:35'),
(10, 17, 'foto/user.jpg', '2022-11-04 20:38:58', '2022-11-04 20:38:58'),
(12, 26, 'foto/user.jpg', '2023-01-11 00:43:53', '2023-01-11 00:43:53'),
(13, 27, 'foto/user.jpg', '2023-01-11 00:49:43', '2023-01-11 00:49:43'),
(14, 28, 'foto/user.jpg', '2023-01-11 00:52:01', '2023-01-11 00:52:01'),
(15, 25, 'foto/user.jpg', '2025-12-27 11:40:44', '2025-12-27 11:40:44'),
(16, 29, 'foto/user.jpg', '2025-12-27 11:41:07', '2025-12-27 11:41:07'),
(17, 31, 'foto/user.jpg', '2025-12-27 12:11:12', '2025-12-27 12:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `inventaris`
--

CREATE TABLE `inventaris` (
  `id` bigint UNSIGNED NOT NULL,
  `noreg` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `katalog` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nabar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spec` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vol` int NOT NULL,
  `merek` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `produsen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thn_masuk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thn_pakai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jml` int NOT NULL,
  `kondisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ket` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `konbaik` int NOT NULL,
  `konrusak` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventaris`
--

INSERT INTO `inventaris` (`id`, `noreg`, `katalog`, `nabar`, `spec`, `satuan`, `vol`, `merek`, `tipe`, `produsen`, `asal`, `thn_masuk`, `thn_pakai`, `jml`, `kondisi`, `lokasi`, `foto`, `ket`, `created_at`, `updated_at`, `konbaik`, `konrusak`) VALUES
(2, 'L.IPA/B.2/R.1/11', 'Biologi', 'Batang Pengaduk Pendek', '<div>Terbuat dari bahan keramik yang pada bagian ujungnya <br></div><div>dibuat pipih&nbsp; dan \nujung lainnya berbentuk silinderyang berfungsi sebagai pegangan</div>', 'Buah', 11, NULL, NULL, NULL, NULL, '2010', '2010', 11, 'Rusak', 'Lemari biologi 2', 'inventaris/o0hBbNzqVuOdJS4aTjxoLIJj6GSrCtkw3KoYaIKJ.jpg', NULL, '2022-11-03 16:03:34', '2022-11-05 03:37:29', 0, 0),
(3, 'L.IPA/F.2/R.3/23', 'Fisika', 'Asbes', 'Terbuat dari bahan besi dengan bentuk persegi', 'Buah', 23, NULL, NULL, NULL, NULL, '2010', '2010', 23, '23', 'Lemari Fisika 2', 'inventaris/YIIpUhKW5Pr67PFrUdG4pFoCMlTNX18b96LHR3qr.jpg', NULL, '2022-11-03 18:06:17', '2022-11-29 11:28:24', 22, 1),
(4, 'L.IPA/F.2/R.3/25', 'Kimia', 'pipa', 'cccc', 'Buah', 3, NULL, NULL, NULL, NULL, '2010', '2010', 3, '3', 'lemari', NULL, NULL, '2022-11-05 04:14:35', '2022-11-29 11:27:52', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `jumlah_pinjams`
--

CREATE TABLE `jumlah_pinjams` (
  `id` bigint UNSIGNED NOT NULL,
  `data_katalog_id` bigint UNSIGNED NOT NULL,
  `pinjam_lab_id` bigint UNSIGNED NOT NULL,
  `minta` int DEFAULT NULL,
  `diberi` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jumlah_pinjams`
--

INSERT INTO `jumlah_pinjams` (`id`, `data_katalog_id`, `pinjam_lab_id`, `minta`, `diberi`, `created_at`, `updated_at`) VALUES
(31, 1, 11, 2, 0, NULL, '2022-11-29 02:40:52'),
(32, 2, 11, 1, 0, NULL, '2022-11-29 02:40:57'),
(33, 9, 11, 3, 0, NULL, '2022-11-29 02:41:03'),
(34, 1, 12, 0, 0, NULL, NULL),
(35, 2, 12, 0, 0, NULL, NULL),
(36, 9, 12, 0, 0, NULL, NULL),
(37, 4, 13, 3, 0, NULL, '2022-12-05 00:37:47'),
(38, 5, 13, 10, 0, NULL, '2022-12-05 00:37:54'),
(39, 1, 14, 0, 0, NULL, NULL),
(40, 2, 14, 0, 10, NULL, '2022-11-30 06:15:50'),
(41, 9, 14, 2, 0, NULL, '2022-11-29 04:02:14'),
(42, 4, 15, 3, 0, NULL, '2022-11-29 04:03:47'),
(43, 5, 15, 3, 0, NULL, '2022-11-29 04:03:53'),
(44, 4, 16, 0, 2, NULL, '2023-01-08 12:37:07'),
(45, 5, 16, 0, 4, NULL, '2023-01-08 12:37:13'),
(69, 1, 25, 2, 0, NULL, NULL),
(70, 2, 25, 1, 0, NULL, NULL),
(71, 9, 25, 3, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jumlah_pinjam_alats`
--

CREATE TABLE `jumlah_pinjam_alats` (
  `id` bigint UNSIGNED NOT NULL,
  `data_katalog_id` bigint UNSIGNED NOT NULL,
  `pinjam_alat_id` bigint UNSIGNED NOT NULL,
  `minta` int DEFAULT NULL,
  `diberi` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jumlah_pinjam_alats`
--

INSERT INTO `jumlah_pinjam_alats` (`id`, `data_katalog_id`, `pinjam_alat_id`, `minta`, `diberi`, `created_at`, `updated_at`) VALUES
(1, 7, 2, 10, 2, NULL, '2022-11-26 00:01:36'),
(2, 8, 2, 2, 2, NULL, '2022-11-26 00:01:41'),
(3, 1, 3, 3, 3, NULL, '2022-11-29 11:34:26'),
(4, 2, 3, 10, 0, NULL, '2022-11-26 00:28:28'),
(5, 9, 3, 11, 0, NULL, '2022-11-26 00:28:33'),
(6, 1, 4, 2, 2, NULL, '2022-12-02 00:14:13'),
(7, 2, 4, 10, 9, NULL, '2022-12-02 00:14:19'),
(8, 9, 4, 1, 1, NULL, '2022-12-02 00:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `katalogs`
--

CREATE TABLE `katalogs` (
  `id` bigint UNSIGNED NOT NULL,
  `topik` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `katalogs`
--

INSERT INTO `katalogs` (`id`, `topik`, `created_at`, `updated_at`) VALUES
(1, 'FISIKA', '2022-11-05 05:38:44', '2022-11-05 05:38:44'),
(2, 'Tabel Perodik', '2022-11-05 05:39:02', '2022-11-05 06:57:44'),
(4, 'KIMIA 2', '2022-11-05 06:40:09', '2022-11-05 06:40:09'),
(5, 'Analisis Kimia', '2022-11-29 11:03:29', '2022-11-29 11:03:29'),
(6, 'Makanan', '2022-11-29 11:04:18', '2022-11-29 11:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` bigint UNSIGNED NOT NULL,
  `kelas` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ket` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `kelas`, `ket`, `created_at`, `updated_at`) VALUES
(2, 'X TKJ 2', NULL, '2022-11-04 19:28:36', '2022-11-04 19:28:36'),
(3, 'X TKJ 1', NULL, '2022-11-04 19:42:23', '2022-11-04 19:42:38'),
(4, 'X TKJ 3', NULL, '2025-12-27 11:37:30', '2025-12-27 11:37:30');

-- --------------------------------------------------------

--
-- Table structure for table `kelas_siswas`
--

CREATE TABLE `kelas_siswas` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kelas_siswas`
--

INSERT INTO `kelas_siswas` (`id`, `user_id`, `kelas_id`, `created_at`, `updated_at`) VALUES
(2, 17, 3, '2022-11-04 20:38:58', '2022-11-04 20:38:58'),
(4, 25, 4, '2025-12-27 11:40:44', '2025-12-27 11:40:44'),
(5, 29, 2, '2025-12-27 11:41:07', '2025-12-27 11:41:07'),
(6, 31, 4, '2025-12-27 12:11:12', '2025-12-27 12:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `materi_ajars`
--

CREATE TABLE `materi_ajars` (
  `id` bigint UNSIGNED NOT NULL,
  `classroom_id` bigint UNSIGNED NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `des` text COLLATE utf8mb4_unicode_ci,
  `file` text COLLATE utf8mb4_unicode_ci,
  `modul_id` bigint UNSIGNED DEFAULT NULL,
  `link_tambahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `materi_ajars`
--

INSERT INTO `materi_ajars` (`id`, `classroom_id`, `judul`, `des`, `file`, `modul_id`, `link_tambahan`, `created_at`, `updated_at`) VALUES
(5, 2, 'Percobaan Kimia', 'praktikum percobaan kimia', 'modul/u4rTvYF8xQJZrEm7ciUDWlsjNBdsazgPLidKwNbJ.pdf', NULL, NULL, '2022-11-26 18:27:58', '2022-11-26 18:27:58'),
(8, 2, 'sasa', 'saasa', NULL, 1, 'https://youtube.com', '2025-12-29 06:56:11', '2025-12-29 06:56:11'),
(9, 2, 'sdfghdfg', 'dfgdf', NULL, 1, NULL, '2025-12-29 07:00:26', '2025-12-29 07:00:26'),
(10, 2, 'peinjaman', 'alat', NULL, 2, NULL, '2025-12-29 07:47:17', '2025-12-29 07:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_08_19_000000_create_failed_jobs_table', 1),
(9, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(10, '2022_11_02_232829_create_foto_profiles_table', 2),
(11, '2022_11_03_115950_create_inventaris_table', 3),
(12, '2022_11_04_132818_create_kelas_table', 4),
(13, '2022_11_05_040707_create_kelas_siswas_table', 5),
(14, '2022_11_05_133537_create_katalogs_table', 6),
(15, '2022_11_05_162728_create_data_katalogs_table', 7),
(16, '2022_11_13_011227_create_pinjam_labs_table', 8),
(17, '2022_11_13_012435_create_pinjam_labs_table', 9),
(18, '2022_11_13_055008_create_jumlah_pinjams_table', 10),
(19, '2022_11_14_111703_add_field_to_pinjam_labs_table', 11),
(20, '2022_11_24_010044_create_classrooms_table', 12),
(21, '2022_11_25_103701_create_materi_ajars_table', 13),
(22, '2022_11_25_233419_create_penugasans_table', 14),
(23, '2022_11_26_021416_create_absensis_table', 15),
(24, '2022_11_26_050325_create_pinjam_alats_table', 16),
(25, '2022_11_26_053628_add_field_to_pinjam_alats_table', 17),
(26, '2022_11_26_062345_create_jumlah_pinjam_alats_table', 18),
(27, '2022_11_26_063140_create_jumlah_pinjam_alats_table', 19),
(28, '2022_11_26_122830_add_field_to_absensis_table', 20),
(29, '2022_11_27_014258_create_data_absens_table', 21),
(30, '2022_11_27_105956_create_data_tugas_table', 22),
(31, '2022_11_29_093242_add_field_to_pinjam_labs_table', 23),
(32, '2022_11_29_191728_add_kon_to_inventaris_table', 24),
(33, '2022_11_29_201649_create_pinjam_lains_table', 25),
(34, '2022_12_05_113104_add_tautan_to_data_tugas_table', 26),
(35, '2022_12_10_192934_create_data_siswas_table', 27),
(36, '2022_12_10_203121_add_field_to_data_siswas_table', 28),
(37, '2022_12_13_083730_create_sildes_table', 29),
(38, '2023_01_06_200058_create_biogurus_table', 30),
(40, '2025_12_28_120149_create_modul_lkpd_table', 31),
(41, '2025_12_28_122759_create_modul_lkpd_table', 32),
(42, '2025_12_28_124421_add_link_tambahan_to_materi_ajar_table', 33),
(43, '2025_12_28_124330_add_link_tambahan_to_materi_ajar_table', 34),
(44, '2025_12_29_125340_create_roles_table', 35);

-- --------------------------------------------------------

--
-- Table structure for table `modul_lkpd`
--

CREATE TABLE `modul_lkpd` (
  `id` bigint UNSIGNED NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modul_lkpd`
--

INSERT INTO `modul_lkpd` (`id`, `judul`, `file_path`, `file_name`, `mime_type`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'hgfds', 'modul/hgfds_1766991311.pptx', 'EiVYK29N6CXKJM8Wxcv5FDR3dSK17mH62epmaZV7 (8).pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 6, '2025-12-29 06:55:15', '2025-12-29 06:55:15'),
(2, 'ertfyguhi', 'modul/ertfyguhi_1766994218.pdf', 'Peminjaman Alat Lab (1).pdf', 'application/pdf', 6, '2025-12-29 07:43:39', '2025-12-29 07:43:39'),
(3, 'praktikum', 'modul/praktikum_1767077861.pdf', 'STRATEGI ALGORITMA DJIKSTRA.pdf', 'application/pdf', 6, '2025-12-30 06:57:42', '2025-12-30 06:57:42');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('021469fb4d6f8dc47c11c2bacee0a5d64e6cb7c6a4215ba8ef3b34c46de77486b0c80ea968d7e840', 6, 1, 'MyApp', '[]', 1, '2022-11-29 12:39:07', '2022-11-29 13:09:36', '2023-11-29 20:39:07'),
('0526feb2156d907424205ceaf1673b5145bd35a6984a185aa5e4ea73e0ba098bf160dc9ee5b28250', 2, 3, 'MyApp', '[]', 1, '2025-12-29 06:53:59', '2025-12-29 06:54:37', '2026-12-29 14:53:59'),
('059d3af4196e02c25853075a162d7f7cc25667860f08f5566ad23801f0ca26fe4bfd4e1d4665c09e', 7, 1, 'MyApp', '[]', 1, '2025-12-28 03:31:33', '2025-12-28 03:33:08', '2026-12-28 11:31:33'),
('076b4b64ef3fb1245b6ae9a7c3ae03ef8def21b9dfc74706a9218df1e3ff0f4249364248c1d0a45a', 6, 1, 'MyApp', '[]', 1, '2025-12-27 19:51:40', '2025-12-27 19:52:48', '2026-12-28 03:51:40'),
('09818ca73373fb5883472b505785e8babcf842498226be7aaaa2c7f853cd484cab72de61dc74026a', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:57:59', '2025-12-30 05:57:59', '2026-12-30 13:57:59'),
('0a1f907f594122c3b9969dfa617cf3c5cef25384a1210d2da05f12b1152975a45874ca3682d5b1bf', 7, 5, 'MyApp', '[]', 0, '2025-12-29 07:20:35', '2025-12-29 07:20:35', '2026-12-29 15:20:35'),
('0a9e95415fb1e62b53ae59c729452a018a86b409103783533d3507d45d2f2d62a4a1ccf3d3815b12', 17, 1, 'MyApp', '[]', 1, '2022-11-26 07:18:08', '2022-11-26 19:05:48', '2023-11-26 15:18:08'),
('0aadb646445559931ef385c78c6f481d92a8bd453f6b8b73a91fc0ee994f2492282afee7640613fb', 6, 1, 'MyApp', '[]', 1, '2022-11-29 11:02:58', '2022-11-29 11:35:38', '2023-11-29 19:02:58'),
('0bb4df0c9659b7b52747588a09e23b2f2f415ceea601730bb75f4d8e04931e64397110e19a6ba26d', 7, 1, 'MyApp', '[]', 1, '2022-11-14 05:30:44', '2022-11-14 05:36:28', '2023-11-14 13:30:44'),
('0c8415a6833c4ea60dafcfdc47cca14d4521bf086eda4dd8a0fa57a9b47c1eeed62318191f4f7f5f', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:19', '2025-12-30 05:58:19', '2026-12-30 13:58:19'),
('0eaeb67192ecfa7693d91672468fca73d4d703b7fc77698624974ab1f7c20de4f1743f8ec54f4003', 7, 1, 'MyApp', '[]', 1, '2025-12-28 03:38:19', '2025-12-28 09:39:18', '2026-12-28 11:38:19'),
('11478037859f676c92b08b8d81348deed0da81246e5c25a03facaca958191dcc5dc6e99ce9bddb55', 6, 1, 'MyApp', '[]', 0, '2025-12-26 16:35:52', '2025-12-26 16:35:52', '2026-12-27 00:35:52'),
('119b079260490bed34658fc0ba77d00ec59dbdf28a0286c585d34b837aa4066eba4bf6dea8525a42', 7, 1, 'MyApp', '[]', 1, '2022-11-29 11:35:46', '2022-11-29 12:39:01', '2023-11-29 19:35:46'),
('120d9204a6653c5437fe52a239e80497ab5cbad58037c4dd15e8953efa40f9cc08e847c6991d27a2', 2, 1, 'MyApp', '[]', 1, '2025-12-27 09:54:34', '2025-12-27 09:59:59', '2026-12-27 17:54:34'),
('162cc28a23d9006f6111707f21ef33ef5b097adcabfad93952e62605d8d16b30cfedcaf45999eb19', 29, 5, 'MyApp', '[]', 0, '2025-12-29 07:48:37', '2025-12-29 07:48:37', '2026-12-29 15:48:37'),
('184fd5a48f3e19c491a1196f6b451262df46d35a0db1528dc3de7dd87ca929953a2a83d0dd6fe364', 6, 1, 'MyApp', '[]', 1, '2022-11-12 19:47:18', '2022-11-12 20:19:32', '2023-11-13 03:47:18'),
('1acc956fe8f429200f9341c7381722ee5cd7e189c5027a3b2a4a3b5d3c2d590ea2d749b9452bbaa7', 7, 3, 'MyApp', '[]', 0, '2025-12-29 07:02:53', '2025-12-29 07:02:53', '2026-12-29 15:02:53'),
('1bf7f70731162c23c783944103addb7095809c692dbfb0f68c79da9dcc37a99a1b8fdc13b697bd76', 17, 1, 'MyApp', '[]', 1, '2022-11-26 07:10:06', '2022-11-26 07:10:38', '2023-11-26 15:10:06'),
('1cf671b15fbaf39fe9229926b501970006ff659e4426b9c97a900d4337e10749a3f003d010035f99', 2, 1, 'MyApp', '[]', 1, '2025-12-26 15:00:50', '2025-12-26 15:12:09', '2026-12-26 23:00:50'),
('1d05d1e4828d5ae005ab9b86946e8aeed05835feda3b88cfece5cb0fbca115b3c5d81adae85e81e4', 6, 1, 'MyApp', '[]', 1, '2025-12-26 16:48:38', '2025-12-27 02:59:36', '2026-12-27 00:48:38'),
('1d2a7241abfe47c2c6e1b1fd276826ef5895b635d58d55ba2564e0fcf8fae963b90ac56bafeee71f', 7, 1, 'MyApp', '[]', 1, '2025-12-28 02:45:56', '2025-12-28 03:31:18', '2026-12-28 10:45:56'),
('1d560dca506a78d767a4f1c353e16fba70ccb7b49a96d510dd2cacc9dbdac4139f5d849d8b7cfd91', 6, 1, 'MyApp', '[]', 0, '2025-12-26 16:35:57', '2025-12-26 16:35:57', '2026-12-27 00:35:57'),
('1eb410d93df40686ef8a0c25ec6ae55fa855eaba5804b877db20e4de6c2c27d0937c4afba6cd58b4', 2, 1, 'MyApp', '[]', 1, '2022-11-12 16:30:03', '2022-11-12 16:30:18', '2023-11-13 00:30:03'),
('2162a8064abe53e552e5705dca1e092629bd23efa45b9f71c25ddec8dd22c3f0a4eac0216b1fcfbe', 6, 1, 'MyApp', '[]', 1, '2022-12-02 00:10:28', '2022-12-02 01:36:33', '2023-12-02 08:10:28'),
('22422f36a8cbd0f06545b91c94129cce19bc700bb7e9900e3c507af2cc9576852013097c90cb4228', 6, 1, 'MyApp', '[]', 0, '2025-12-27 09:27:58', '2025-12-27 09:27:58', '2026-12-27 17:27:58'),
('22c4877046a4a41fc648d29ba657c9dc50c79efda39840205d706a9135ec74f0fc9cf9511cadbc8d', 7, 5, 'MyApp', '[]', 0, '2025-12-29 07:14:15', '2025-12-29 07:14:15', '2026-12-29 15:14:15'),
('22e2f5222ad80ab6caf6a060447f627a99545da323b784962c24ac7f114e7646edd2ddb2194b4ad1', 6, 1, 'MyApp', '[]', 1, '2025-12-26 16:35:54', '2025-12-26 16:46:21', '2026-12-27 00:35:54'),
('231234887581d6f5c589408cbed317da3f68b6065a743fc9c99a3fab5e914f207822d63b6d0d2212', 17, 1, 'MyApp', '[]', 0, '2025-12-26 15:21:40', '2025-12-26 15:21:40', '2026-12-26 23:21:40'),
('232471544ae48cec1935ca3888076adff5938e208a6652958a22d162be7aec01ed86ff026bcf7727', 6, 1, 'MyApp', '[]', 1, '2022-11-12 15:39:39', '2022-11-12 15:41:06', '2023-11-12 23:39:39'),
('250d5461ace267e232f4f36ae57c0f39f3a8ed4124f752b46951696b304004aeaf2976257a85c470', 7, 1, 'MyApp', '[]', 1, '2025-12-27 19:28:26', '2025-12-27 19:51:28', '2026-12-28 03:28:26'),
('2822fa9d208dc5f251c08d2106a30d7eb91b807a33db908922776ab474008ffbf06a34a30d9f488a', 6, 1, 'MyApp', '[]', 1, '2022-11-26 04:39:05', '2022-11-26 04:39:36', '2023-11-26 12:39:05'),
('2a5a353646473019969bfa7d2a62b3494cdf431d787490c3a70bee5d81e3c6bb38e23cb0cba7692d', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:18', '2025-12-30 05:58:18', '2026-12-30 13:58:18'),
('2d251bd453149f878e5a13ccf7c8cca6c1e681265977f22489a65a849635d0bde749f496badd9360', 17, 1, 'MyApp', '[]', 1, '2025-12-27 11:36:40', '2025-12-27 11:39:10', '2026-12-27 19:36:40'),
('2f0b94c9f8dad54b4ba47b84f674ef058ed52a8e88a751d16e60cf5d288f57256425ee5932e92ace', 2, 1, 'MyApp', '[]', 1, '2023-01-10 06:59:40', '2023-01-11 00:43:10', '2024-01-10 14:59:40'),
('30cc2671a462a02d1816e6a1178322aeeead55b1c4236e2ffeb8518ebe98115a34e2c3928a8a1425', 6, 1, 'MyApp', '[]', 1, '2025-12-28 02:00:48', '2025-12-28 02:44:19', '2026-12-28 10:00:48'),
('317c2b2919dfd58e5baccd9586c44dc92fe0c6bc3b62213803ca8415da4a9cf66ada6e4cf4de1821', 2, 1, 'MyApp', '[]', 0, '2025-12-26 16:30:33', '2025-12-26 16:30:33', '2026-12-27 00:30:33'),
('31839df6cf17bf6622c2e76702554cc2ad8c279518a44dec8afa88d04400fc7aa310d302fbf3dd25', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:00', '2025-12-30 05:58:00', '2026-12-30 13:58:00'),
('3298eb4046fcf900a7429e4ccfc09b3a9eec38bcd2ee3a4a0e6dc1cbccc116e04c7badf028524116', 7, 5, 'MyApp', '[]', 0, '2025-12-30 06:12:16', '2025-12-30 06:12:16', '2026-12-30 14:12:16'),
('33c57604ac93a46c2c823f4bf10858b7f52b8a8719a2a27c4bf9847bb7b9b00fa98908803b7ad44b', 7, 1, 'MyApp', '[]', 1, '2022-11-29 13:09:42', '2022-11-29 13:10:06', '2023-11-29 21:09:42'),
('33e7b2ed537d4e8c2dff0ccf3d26f8b7f2cbd1aafed1c6ea2aa810ef7ca68cceb2c52d5a718f0b1c', 6, 1, 'MyApp', '[]', 0, '2025-12-27 09:28:01', '2025-12-27 09:28:01', '2026-12-27 17:28:01'),
('34242a4f3fbd864935e4ae8cbd28604f3991211f67d29c6fed8c73730226ba7ed70f45cd7bd296a0', 2, 1, 'MyApp', '[]', 1, '2025-12-27 19:18:09', '2025-12-27 19:28:16', '2026-12-28 03:18:09'),
('36947bdbbbaba01b234a4c3c0fcca38147ca55c8a30b9ccad2050ab06ba973cfb1fe9c45882b6c7b', 6, 1, 'MyApp', '[]', 0, '2022-11-05 03:18:25', '2022-11-05 03:18:25', '2023-11-05 11:18:25'),
('36bec2c3ffff47975e97a5c6fe3e1911914143edd6640e999f582e6a6352ad7bde9900a6b40d9a62', 2, 1, 'MyApp', '[]', 1, '2022-11-02 21:55:32', '2022-11-03 05:30:13', '2023-11-03 05:55:32'),
('38c590d0084305b4dccd6464d9cdfe5c0889706d95e7dd537a1d0df50cbafbe31213deddf2696330', 7, 1, 'MyApp', '[]', 0, '2022-11-23 04:04:31', '2022-11-23 04:04:31', '2023-11-23 12:04:31'),
('3c398d2a3190f856caca8a4a08748bb9df31748d4c3de439d091df5b34ac305c6a967819df108dc3', 7, 1, 'MyApp', '[]', 1, '2025-12-26 16:34:26', '2025-12-26 16:34:59', '2026-12-27 00:34:26'),
('3cc5ccccce17d5d7274aa86e8ca30b2588371fd194949f3ee2d5ae0d1968f6ee99b0d4f241674eef', 7, 1, 'MyApp', '[]', 0, '2025-12-29 04:23:37', '2025-12-29 04:23:37', '2026-12-29 12:23:37'),
('3db9fd649eb08d4628e9903edee910a36425d69e80daa63f563f738faa2bd956c62925999307826f', 6, 1, 'MyApp', '[]', 0, '2025-12-29 06:07:16', '2025-12-29 06:07:16', '2026-12-29 14:07:16'),
('3e6b12ecc88cc1b4537db820b5ad4f57435a16adea7167e314cd28ccd2c381953325814c8e026437', 7, 1, 'MyApp', '[]', 1, '2022-11-14 03:53:10', '2022-11-14 03:59:02', '2023-11-14 11:53:10'),
('4051630eb0902a3a48b282b8f145f87ef45e921a0a50769b7b6b43c5b39fc1f15b26805e1a612fba', 6, 5, 'MyApp', '[]', 0, '2025-12-30 06:44:54', '2025-12-30 06:44:55', '2026-12-30 14:44:54'),
('405e354055af2ea1bfe4eab40283a99c821222ef3582c1b4a596269efa2282357e1dcf918cfffc3c', 6, 1, 'MyApp', '[]', 1, '2023-01-11 00:48:42', '2023-01-11 00:49:47', '2024-01-11 08:48:42'),
('4125626a52b81b4208f376fe0b5e17338ee99dcbff4df4912c0f77ed7f77a84a5946d822711eaff8', 6, 1, 'MyApp', '[]', 1, '2022-11-04 05:39:36', '2022-11-04 05:49:06', '2023-11-04 13:39:36'),
('4269e720aa91f86cee7cb32f50215ecc467c76d30c12698c0fb6a065bcea77d90ffb6f137f1f42f5', 7, 1, 'MyApp', '[]', 1, '2022-11-12 20:19:39', '2022-11-13 00:06:29', '2023-11-13 04:19:39'),
('42d599815a520b55994ecca0ea735a63c87a96aa293566f3c2b603a435b7db80f6a2b2a18e31656e', 17, 1, 'MyApp', '[]', 0, '2025-12-27 11:36:39', '2025-12-27 11:36:39', '2026-12-27 19:36:39'),
('4354022a9d7e6d0cd8e92306f455a175791a414d11df6b0bde1b5463f95800fdd4372ba18d06782f', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:15', '2025-12-30 05:58:15', '2026-12-30 13:58:15'),
('457209db0812573a8b62255db2f6eebc27dbe503ad64782b2497ced87ea66ca0a6bb3051a89aa3b5', 28, 1, 'MyApp', '[]', 0, '2023-01-11 00:52:11', '2023-01-11 00:52:11', '2024-01-11 08:52:11'),
('45f96647bce89f488d6e8fb4a97f98d29c320d5fca52be387823ac9b0ad164a8739e60ca5758794b', 2, 1, 'MyApp', '[]', 1, '2025-12-26 14:16:51', '2025-12-26 14:57:59', '2026-12-26 22:16:51'),
('4921195cec14a24399484c5343bb555b7003f49faee1a44f550eada8b58de668ec715f810ce895f7', 2, 1, 'MyApp', '[]', 0, '2023-01-10 06:17:09', '2023-01-10 06:17:09', '2024-01-10 14:17:09'),
('4dd0d5abb216d81934c7f4199f84a749c65dedbcd1a8b86dd22660cc349175c3fb6cea3fd3a5375e', 6, 1, 'MyApp', '[]', 1, '2025-12-26 15:12:20', '2025-12-27 05:23:57', '2026-12-26 23:12:20'),
('4f104326bc3ad5621797cc673ca7a0817caa2dc1a13731e07b180d1ce66d57d6e2d84bd2ba2a7f08', 6, 1, 'MyApp', '[]', 1, '2022-11-12 18:24:26', '2022-11-12 19:46:43', '2023-11-13 02:24:26'),
('4fc0b92976edfc99f0908e7e3c4273eb636c1a1f6ca994355d54a3940112bcd3ecec17c74107e687', 2, 1, 'MyApp', '[]', 0, '2022-11-02 18:27:00', '2022-11-02 18:27:00', '2023-11-03 02:27:00'),
('50a2bc5bfb970a956267e7fe0a612e3b0a8f0e26f5737a9500e4b6a79fb40c3945169736fed58159', 2, 1, 'MyApp', '[]', 1, '2022-11-12 15:39:20', '2022-11-12 15:39:31', '2023-11-12 23:39:20'),
('5264f26069037fd5c109f3201bceb27df13e1721f057c1478f5459465575baf1e3a57bf5d3698d68', 6, 5, 'MyApp', '[]', 0, '2025-12-30 06:58:22', '2025-12-30 06:58:22', '2026-12-30 14:58:22'),
('53229561eb99255d7f903648cd001ab7e006be24109c78346be9b14dc744da99ca9d1aba6d1acf8f', 7, 5, 'MyApp', '[]', 0, '2025-12-30 06:34:04', '2025-12-30 06:34:04', '2026-12-30 14:34:04'),
('55e07b63b4998eb48fff3691a14b093094ed379a08fb4261e8491787b927762bfef4f7ea54470ce5', 2, 1, 'MyApp', '[]', 1, '2022-11-05 21:39:38', '2022-11-05 21:39:59', '2023-11-06 05:39:38'),
('5a2c606ded1d6f9cd29ed232f3e0ea0e9bf9c0966940474586b3543e7ef436c3cf6e575ac9becfe8', 7, 1, 'MyApp', '[]', 1, '2025-12-27 19:56:55', '2025-12-27 19:57:31', '2026-12-28 03:56:55'),
('5bbbd736d872d0ec6d79aaabb658ec91fc832122e31e1fd856d5f178b85a74509f5c9d3524aaebe6', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:57:56', '2025-12-30 05:57:58', '2026-12-30 13:57:56'),
('5c132458059fd73b59eaa3cb1f81ab81562537277c434fef144fd759eead38480ac4c57a968a2955', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:26', '2025-12-30 05:58:26', '2026-12-30 13:58:26'),
('5e5010cb3d77e5667039192c24a1d5ec223ce347c2649e5a3d461e8bfbe9e1402853b60e85b6899c', 7, 1, 'MyApp', '[]', 1, '2023-01-09 03:17:01', '2023-01-09 07:01:44', '2024-01-09 11:17:01'),
('5f3071f3ae181ce80ffe353df77559d2255fb720d82838891a41a2c6937e89cf642d8e65c85f0de3', 6, 1, 'MyApp', '[]', 1, '2025-12-27 19:54:33', '2025-12-27 19:56:44', '2026-12-28 03:54:33'),
('5f522071c47f8464b9db23617c08ab36a1b67730602ed3b9ccc1ca34d8f9411f54e6269813f0a991', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:03', '2025-12-30 05:58:03', '2026-12-30 13:58:03'),
('5f9aab62195fc6062730d1f3f3e5b10203caff81b455f9734b39467161b5b311c358e1cfb33f3519', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:16', '2025-12-30 05:58:17', '2026-12-30 13:58:16'),
('60d1d4c2697dc7646c3d064caea3d2fafbc409042cceba361d1dc19f79873597b4598cebed94935b', 2, 1, 'MyApp', '[]', 1, '2022-11-02 19:22:54', '2022-11-02 21:53:24', '2023-11-03 03:22:54'),
('60d3a0d4c230c1ed1d39cf7ae835e841c2ebe22a64c3de1ee429e8adb752357611d21477a081267d', 6, 1, 'MyApp', '[]', 0, '2025-12-27 10:00:08', '2025-12-27 10:00:08', '2026-12-27 18:00:08'),
('60e2f3a16b1f6ae2586f038c40c254214a8521c9c444135d9e03ba204d7d3728300c6ae9a77172c5', 2, 1, 'MyApp', '[]', 0, '2023-01-10 06:14:39', '2023-01-10 06:14:39', '2024-01-10 14:14:39'),
('60ff9ed08f4cf2f5b2cc651721dbe1dc4e7ae96d14a1c1a9f2b19a59cc36b20792ce53254862e4f2', 6, 1, 'MyApp', '[]', 1, '2022-11-04 18:28:30', '2022-11-05 03:17:36', '2023-11-05 02:28:30'),
('6304dcf1de7ac973be8562fc185d6a1495d76626f2c8b197cebfec13499e4c485486d8a27afde90b', 2, 1, 'MyApp', '[]', 1, '2022-11-26 04:38:44', '2022-11-26 04:38:59', '2023-11-26 12:38:44'),
('644d2baa6ed2d7f39964c1d8b409f55f4710d0f8c0b65c19529f1589ea93add1607e7724f2beb88f', 27, 1, 'MyApp', '[]', 1, '2023-01-11 00:49:55', '2023-01-11 00:51:17', '2024-01-11 08:49:55'),
('659384dc0efd24f7dfe8c461c22124739e46233253fd79252160c050667db3da44d4ee5f7b18ee0f', 6, 1, 'MyApp', '[]', 0, '2023-01-08 13:09:14', '2023-01-08 13:09:14', '2024-01-08 21:09:14'),
('65b3b4433018049bd61eda27f8eb1501f47714e02f567515feabb6c7c2e1524d0a601537c4b59637', 2, 1, 'MyApp', '[]', 1, '2022-12-12 23:59:29', '2022-12-13 02:21:45', '2023-12-13 07:59:29'),
('66f139459a3da04952b4ab0b36a5d304479c613e1311061501f5313c5952b20d1f77e49354b6faad', 7, 1, 'MyApp', '[]', 1, '2022-11-12 19:46:51', '2022-11-12 19:47:12', '2023-11-13 03:46:51'),
('6838cedd0d2abf690c0ba6f76d92f275ebcfbdfc97dd9b493d6d98d2ca00d9e21e6216d42b83b11d', 7, 1, 'MyApp', '[]', 1, '2022-11-29 03:59:24', '2022-11-29 11:02:52', '2023-11-29 11:59:24'),
('6af0061064dc3f41e5dd8fca2ad43423a2fee9c53687fa6c3347014709fe6d2a39146d670d0e9ca5', 7, 1, 'MyApp', '[]', 0, '2022-11-26 18:27:22', '2022-11-26 18:27:22', '2023-11-27 02:27:22'),
('6aff2fd4061b28addb8f442ccb8ea8756ce586f80dc2ab945d84d9cc820f561850ae92cd0aca929d', 6, 1, 'MyApp', '[]', 1, '2022-11-29 02:15:30', '2022-11-29 02:16:24', '2023-11-29 10:15:30'),
('6b3c848bce8acd7aafe58e39ca4d9b59198554f338e6d29f2c10131aa13d87b943af864128ea723a', 7, 3, 'MyApp', '[]', 0, '2025-12-29 07:09:01', '2025-12-29 07:09:01', '2026-12-29 15:09:01'),
('6bebf7b553fa9ec08fcdbf38d2c5cda8bea8acdba07073c0113ab1a0bbdd1a761943fa00f916faa3', 6, 1, 'MyApp', '[]', 1, '2022-11-04 18:08:54', '2022-11-04 18:24:31', '2023-11-05 02:08:54'),
('6c5afbea895d109af3bfce4bc17cb048e70f81c00579b87cbf2e01628c85ed31f739472d3a4c0d43', 6, 5, 'MyApp', '[]', 0, '2025-12-30 06:58:32', '2025-12-30 06:58:32', '2026-12-30 14:58:32'),
('6c6dc3683e0aa5d1d500d46506af3719bb82d4d09d4b004e8ebac314ad1b2ae2d149403e690b676b', 6, 1, 'MyApp', '[]', 0, '2025-12-26 16:35:53', '2025-12-26 16:35:53', '2026-12-27 00:35:53'),
('6c7686d8a7b8bff9af750eee1650e60046f4acb37a2e6c71a56ad2b148a6c0a3d9db471f2967a9c3', 7, 1, 'MyApp', '[]', 0, '2022-11-29 02:55:06', '2022-11-29 02:55:06', '2023-11-29 10:55:06'),
('6c76bb08921bc6b2998382b36bf3d4e8d5506c55f574c45ddd1ea280bce6f76ed003cc3180c6f6d1', 7, 1, 'MyApp', '[]', 1, '2022-11-29 02:16:31', '2022-11-29 02:16:49', '2023-11-29 10:16:31'),
('6cb034ac7cc0fcfdec520e4afe7f142a608f85416e1df7db0d1405fefa0218e0b2a5f31010be17af', 6, 1, 'MyApp', '[]', 1, '2025-12-28 10:36:55', '2025-12-29 04:23:06', '2026-12-28 18:36:55'),
('6d65530a67e2d28ccf5becb3e1087379ecd595cd1f150308050088a62e6f7e817263d45d22cdea4f', 7, 1, 'MyApp', '[]', 0, '2025-12-29 04:23:28', '2025-12-29 04:23:29', '2026-12-29 12:23:28'),
('7058faaeb707b887b49ed9ff1bc13d496f8a87fa811b2c807271fa574e4c70d3949a791296ff4918', 6, 1, 'MyApp', '[]', 1, '2023-01-08 11:28:20', '2023-01-08 13:08:30', '2024-01-08 19:28:20'),
('71a568c3e0943ac89b9db90dfeba6eb1471cbbfe2942f44989f85528b39dbc3339b5cbec7eddef3f', 6, 5, 'MyApp', '[]', 0, '2025-12-30 06:58:38', '2025-12-30 06:58:38', '2026-12-30 14:58:38'),
('71b42d291d6cb7e109f3e85d0da4cacbf23195d95ab2c0c9d607d3c1e04bb5bc6f594b043d3b05da', 2, 1, 'MyApp', '[]', 1, '2025-12-26 16:47:04', '2025-12-26 16:48:24', '2026-12-27 00:47:04'),
('71b6d36ae2448b7526c9bb5d39c3999e1fb56aeb670be4361805c82ec6c9690dfaf0b653dac804ca', 6, 1, 'MyApp', '[]', 0, '2025-12-28 09:39:41', '2025-12-28 09:39:42', '2026-12-28 17:39:41'),
('73e48b1b7b178392e35078913b5fcbd0a1de612a6cb7d9b4622dc237f05b631c02e7e5c259519a3d', 17, 1, 'MyApp', '[]', 0, '2025-12-27 11:36:37', '2025-12-27 11:36:37', '2026-12-27 19:36:37'),
('73f020fea12c4763336762c837c38fb5714fd6c3b8eeed89f16974b442183bf1c0a5fe012c0380b5', 6, 1, 'MyApp', '[]', 1, '2023-01-11 00:51:24', '2023-01-11 00:52:04', '2024-01-11 08:51:24'),
('75e6738e7b877af60e3196a7ee38a78d86112bed4c55f748f2203092d9d8331926b98ad37d133952', 17, 1, 'MyApp', '[]', 0, '2025-12-27 07:39:42', '2025-12-27 07:39:42', '2026-12-27 15:39:42'),
('763ecef1a46cf6ee9dee86f79db7b21f52f7d3b61295785743c50ea785cdc9467762a34a23ebab71', 7, 1, 'MyApp', '[]', 1, '2022-12-01 23:52:37', '2022-12-02 00:10:20', '2023-12-02 07:52:37'),
('76b3dfb6ce425290292104027357f0aeb808e6ccf9ef23d3dd8873805dd25cd062802b99c1ba4ac2', 7, 1, 'MyApp', '[]', 0, '2025-12-29 04:23:24', '2025-12-29 04:23:25', '2026-12-29 12:23:24'),
('78406f477d154cee523c10d2fab61833706a61465c7edc0ed5bf313bef62c88a28f11c25ac6905d8', 6, 1, 'MyApp', '[]', 0, '2025-12-26 16:35:48', '2025-12-26 16:35:49', '2026-12-27 00:35:48'),
('78704bd507bc6ccf6c68c485ceafc6041c2098ab860b0af247201ca4318f4097080f72d75738798b', 6, 1, 'MyApp', '[]', 1, '2022-11-12 15:41:36', '2022-11-12 15:41:53', '2023-11-12 23:41:36'),
('7987754e36f6be1492fbbced422927168172f5a77b48b4b83a04b20684e374d30b9a197abc003b01', 2, 3, 'MyApp', '[]', 0, '2025-12-29 06:53:54', '2025-12-29 06:53:58', '2026-12-29 14:53:54'),
('79eee4fca48db57c3ae5fc81214292aed3a883c1bbad093ea8c6d4e15ecb6f314b66d38beee952ac', 6, 1, 'MyApp', '[]', 1, '2022-11-04 18:26:39', '2022-11-04 18:26:52', '2023-11-05 02:26:39'),
('7e003128abe3cbc68f5d4b3d11026681518b2def637b126acd2f2b98258bf71edb69293f0868ffb5', 7, 1, 'MyApp', '[]', 1, '2022-12-12 01:12:18', '2022-12-12 02:49:24', '2023-12-12 09:12:18'),
('7eec1b2f3775f0d2e3f8a8579da3f338b866813f463fa929a7b3f880672b3e665807a5418be8d591', 6, 1, 'MyApp', '[]', 1, '2022-11-25 23:17:14', '2022-11-25 23:47:35', '2023-11-26 07:17:14'),
('81abc385593426542183be067d53956da6f750d041a67804388582168b8a1a64241537d7f3be803f', 7, 1, 'MyApp', '[]', 1, '2022-11-12 15:41:59', '2022-11-12 16:29:57', '2023-11-12 23:41:59'),
('863fb73478775b346f626a906e35025f1a771bc94f51f70b3237307c2df32e44440bd9f05a470fdc', 17, 1, 'MyApp', '[]', 1, '2022-12-05 02:08:25', '2022-12-10 09:34:40', '2023-12-05 10:08:25'),
('86edcc0f1f69e823969c36b253357a8be3967f7a90f4ef231a0d9b15dbe01d5e379a897a5fa5d131', 17, 1, 'MyApp', '[]', 0, '2025-12-27 11:36:27', '2025-12-27 11:36:27', '2026-12-27 19:36:27'),
('8767e4527235ca6b32659f8336fa9055297f3e896de7296361bb762d564e1d6699089fb32a2dc916', 6, 1, 'MyApp', '[]', 1, '2025-12-28 03:33:20', '2025-12-28 03:38:07', '2026-12-28 11:33:20'),
('87a631930ea65d1882f1e27d6507c930103b6399bf8649dca88538b5256c387530812b7953f6220f', 6, 1, 'MyApp', '[]', 1, '2022-11-13 00:06:36', '2022-11-13 00:22:05', '2023-11-13 08:06:36'),
('8b79794f9c3397d5e26201f0b4b35899cf8873f948125914fbebf4f1f51117f60c065217789e2d5a', 2, 1, 'MyApp', '[]', 1, '2025-12-26 16:30:39', '2025-12-26 16:34:13', '2026-12-27 00:30:39'),
('8bdc85849d81156aaeb67bd34f8ae5c2c0444920018164adf66a1e9110f751fbdc7057653465fcdb', 6, 1, 'MyApp', '[]', 0, '2022-11-29 13:10:15', '2022-11-29 13:10:15', '2023-11-29 21:10:15'),
('8c0ed7d6b38ba1a8fe8cbdbddcf4332983bb6d9b0fae6c2e54455ef174ede7d69f96e09878720783', 6, 1, 'MyApp', '[]', 0, '2022-12-10 10:42:17', '2022-12-10 10:42:17', '2023-12-10 18:42:17'),
('8dd57802bdc1941a8a1d24015af40f38a45c16004dbcbabd48f86c5226b6ca142977fde74836ae6c', 7, 5, 'MyApp', '[]', 0, '2025-12-30 06:14:47', '2025-12-30 06:14:48', '2026-12-30 14:14:47'),
('90b0551548fb8c0e4fd4363052e35f6798316f1c0e3faa55f0d0aee10d71bcffdfcafbb535ebed92', 6, 3, 'MyApp', '[]', 1, '2025-12-29 06:54:46', '2025-12-29 06:55:22', '2026-12-29 14:54:46'),
('939c672886607e8ab2e259571ab2d707f16cffcbd9c98ab4e43dc4806576df658ca6ab4074cb8b6e', 17, 1, 'MyApp', '[]', 1, '2022-11-26 07:11:02', '2022-11-26 07:12:15', '2023-11-26 15:11:02'),
('94053aa02e97a39fff3eedbc712925736a47b9ed0733b9331f7a9749b99c6b2f6797512a9a8e038a', 7, 1, 'MyApp', '[]', 1, '2023-01-08 13:31:41', '2023-01-09 01:55:40', '2024-01-08 21:31:41'),
('946dd041b8412f9270dab300784966e52b901bef04be95c3644aba2fc66dda8ec80fa4854e950771', 7, 1, 'MyApp', '[]', 0, '2022-11-29 02:17:03', '2022-11-29 02:17:03', '2023-11-29 10:17:03'),
('94db272f9e779937a546465ccf3987d4510d6f4c17f94c112761b9bde51bfd818929b927430cb104', 6, 1, 'MyApp', '[]', 0, '2023-02-08 05:40:47', '2023-02-08 05:40:47', '2024-02-08 13:40:47'),
('952ed577e1b681d872f28841bcd9ee68cf8ce3fe24e2742d1a55d788ffa36c508a78c647605fefac', 7, 1, 'MyApp', '[]', 1, '2022-11-05 21:40:06', '2022-11-06 04:13:18', '2023-11-06 05:40:06'),
('953b15b4adfed90bee4e390eaeed2f683189d265a65c014f07d3c1cb6544455b0f1b796e9227bc27', 17, 1, 'MyApp', '[]', 1, '2022-11-26 07:12:53', '2022-11-26 07:18:00', '2023-11-26 15:12:53'),
('967746427b53d24a712195f880a86a656581cc91bc3bd6006bfe65dc3f12926a2645995e51f65859', 2, 1, 'MyApp', '[]', 1, '2022-11-04 18:26:59', '2022-11-04 18:27:06', '2023-11-05 02:26:59'),
('96ff9703524a7e7d2fd7c566d865f590b69c8e07c708ca6a2b28554f6cf84bf6b92f74285959a133', 2, 1, 'MyApp', '[]', 1, '2025-12-27 19:57:41', '2025-12-28 02:00:38', '2026-12-28 03:57:41'),
('984dce89e4bc8848d9b6700620c30bb563f856392b6eefe108e25c016c53ca729fa3220966bfbff8', 17, 1, 'MyApp', '[]', 0, '2022-12-13 02:21:54', '2022-12-13 02:21:54', '2023-12-13 10:21:54'),
('9a591cc155024cf0560d2c0608e7911ecd2f4244ff1b0105a8ad8a587b854294449d5da8b51691f2', 17, 1, 'MyApp', '[]', 0, '2025-12-27 11:36:29', '2025-12-27 11:36:29', '2026-12-27 19:36:29'),
('9b42acb23442c979fee316c7d72682ff15a83e3ac6dd4af4bfbaeb44ddf6b8659e66489c632f5caf', 2, 1, 'MyApp', '[]', 1, '2025-12-26 14:58:12', '2025-12-26 14:59:04', '2026-12-26 22:58:12'),
('9bb3f2efbe8f3dbd47eb8ab3e683aaa7463f11b04e1617e4e35555a1597f78ba58851d698dc875bf', 2, 1, 'MyApp', '[]', 0, '2023-01-10 06:13:56', '2023-01-10 06:13:56', '2024-01-10 14:13:56'),
('9c182fefc1ec6f2ec3b5b319267f6f8df3f49e24997f5cb3561c0b2343d5572f9306766d63e08995', 6, 1, 'MyApp', '[]', 0, '2025-12-26 16:35:58', '2025-12-26 16:35:58', '2026-12-27 00:35:58'),
('9ca5ffda4a54ed91df88b292d37ef549e5e6122799f3db2f1f28d9350b57d0cef7299b341e047c2e', 6, 1, 'MyApp', '[]', 0, '2025-12-27 13:40:24', '2025-12-27 13:40:24', '2026-12-27 21:40:24'),
('9da57e7e6dfadb384a74a0f7712d364e6fd832c62c570711d52fcb493638a88c0694dc3c8650c713', 29, 5, 'MyApp', '[]', 0, '2025-12-29 07:50:02', '2025-12-29 07:50:02', '2026-12-29 15:50:02'),
('9dc93c2bf0649a68ccd908516acd466beeaef7d598f11fa4ad6a94e81fb68ed185da6148a9f23c48', 2, 1, 'MyApp', '[]', 1, '2025-12-27 07:30:21', '2025-12-27 07:32:26', '2026-12-27 15:30:21'),
('a078af201e5add0de145b6260cce912f316d9944819ab8bf641413dfca4b0a018ccfebf14ae60cc1', 17, 1, 'MyApp', '[]', 1, '2022-11-27 02:40:57', '2022-11-27 11:57:06', '2023-11-27 10:40:57'),
('a1017571e1d473db8cf28e36159608d5a6f1461c6539d4ad14b4451a9fc0f4036524038365bcb5ad', 6, 5, 'MyApp', '[]', 0, '2025-12-30 06:57:00', '2025-12-30 06:57:01', '2026-12-30 14:57:00'),
('a13a9e502bb97ec08ff6bc32270281d8703d2d529dd35cabb99da48ef1fd3acb027c3d6d7678ffe4', 17, 1, 'MyApp', '[]', 1, '2022-12-05 02:06:31', '2022-12-05 02:06:39', '2023-12-05 10:06:31'),
('a22c2684d9116640da8145f48f3bbd0b7108d4c16aafafd73c061f03864542711d788f6e3023452f', 2, 1, 'MyApp', '[]', 0, '2023-01-10 06:14:10', '2023-01-10 06:14:10', '2024-01-10 14:14:10'),
('a68614919c4642701cdc016263d609831239639e2f97a5fe47d4b11bac864a6e3c858db1d09ea021', 7, 1, 'MyApp', '[]', 0, '2022-11-27 11:57:17', '2022-11-27 11:57:17', '2023-11-27 19:57:17'),
('a6b2c4e118135c0a106b866ad0ec17770b488427093a0167fc8b46458081e3953fc4dd1719e5277d', 7, 1, 'MyApp', '[]', 1, '2025-12-27 19:52:57', '2025-12-27 19:53:48', '2026-12-28 03:52:57'),
('a6ef21df668ab7ec5d223a34848bedb9118175bcaeb1d09aad8126920359ae20808cbbe377fbadce', 7, 1, 'MyApp', '[]', 1, '2022-11-25 23:47:41', '2022-11-25 23:48:47', '2023-11-26 07:47:41'),
('a8b7cbf59abf84502f632ae72f0449d7b6c4f3e594e25068e47cc6238eea25619a2d369aa8de9693', 7, 5, 'MyApp', '[]', 1, '2025-12-29 07:46:40', '2025-12-29 07:47:32', '2026-12-29 15:46:40'),
('aa44b94676b99dd4a7a492da6aa17b99bd5a4b73341ef0669e22e5234b8556b7000a833e16f5073f', 7, 1, 'MyApp', '[]', 1, '2022-11-25 17:48:10', '2022-11-25 23:16:06', '2023-11-26 01:48:10'),
('ab72addeeba341bc450bfe631f2109b8f24d64e49800819f99932e3d134a7ff8d57f2cc74b3cac34', 7, 5, 'MyApp', '[]', 0, '2025-12-29 07:17:38', '2025-12-29 07:17:38', '2026-12-29 15:17:38'),
('ab87c1ca4ac08f3fa4992a0ca2401fcd5314af5ff3d2af134d628f9ea596731c6fa2413616fb4837', 7, 5, 'MyApp', '[]', 0, '2025-12-29 07:17:38', '2025-12-29 07:17:38', '2026-12-29 15:17:38'),
('ade730f1c820b4ef934a2e7830f50e875440410236554990cacf7c6ed8c2acae34dcfa915d3a19a1', 7, 1, 'MyApp', '[]', 1, '2022-12-10 09:35:28', '2022-12-10 10:41:59', '2023-12-10 17:35:28'),
('b15f624cd261a5a09d82bc2d3f6ed43d14ced3552ef12bc4f36388a76322104496df0f8394541771', 6, 1, 'MyApp', '[]', 1, '2023-01-06 11:41:04', '2023-01-08 11:23:40', '2024-01-06 19:41:04'),
('b24658ee17bce7293457015280be0cd4fa8f82356a83f321dabcb38d549481ea81f2e008b6d9f298', 7, 1, 'MyApp', '[]', 1, '2022-11-12 16:31:15', '2022-11-12 18:24:19', '2023-11-13 00:31:15'),
('b2e1aae34736459d89d36cf38a0c67da54bece89d931fa919a1b985db69ba51b7b3c69e787c6f767', 2, 5, 'MyApp', '[]', 1, '2025-12-29 07:47:46', '2025-12-29 07:48:24', '2026-12-29 15:47:46'),
('b3dd5f4935fb84f908bee3fb65353185e38dda0da68fd6168c9a1339f3d47ac22d84fa4ec31d5dba', 7, 1, 'MyApp', '[]', 0, '2025-12-29 06:07:27', '2025-12-29 06:07:27', '2026-12-29 14:07:27'),
('b4983707bf8fca0f4d0851f1d8207c17217f6245195b17fc50f5355ce62fd42c85c93b1f8100ac49', 7, 1, 'MyApp', '[]', 1, '2022-12-02 01:36:40', '2022-12-04 23:41:29', '2023-12-02 09:36:40'),
('b4c0c31b0f55684b27ac272dda3607cea0df1d41fbd0a5fdc3d3bd1ff0cd78ceeecd11f6d840aee9', 7, 1, 'MyApp', '[]', 1, '2022-11-14 03:46:34', '2022-11-14 03:53:04', '2023-11-14 11:46:34'),
('b600c6d44db4c207dc7332a00671e034cd19f2cbe62d13dc62d0eac37aae91d6ccf3c4bcd7e69056', 2, 1, 'MyApp', '[]', 1, '2022-11-02 18:27:56', '2022-11-02 19:21:49', '2023-11-03 02:27:56'),
('b6d484afb9a090a8051d29e990d7eeb8a1c3c94466057010900152274e8f962b6a3c87f672753a54', 6, 1, 'MyApp', '[]', 0, '2025-12-26 16:35:56', '2025-12-26 16:35:56', '2026-12-27 00:35:56'),
('b80572f633f1c58ba0239cdf7424cdcd137c4fd7160c0eeefeef4ee63efbffb7bec8e031595bf86b', 6, 1, 'MyApp', '[]', 1, '2022-11-30 06:06:04', '2022-12-01 23:52:26', '2023-11-30 14:06:04'),
('b98c4786bb477298e32538a610f337a161f343eb5f55d555afcb6554ddfd0a3005c805a950a48f3c', 6, 1, 'MyApp', '[]', 1, '2022-11-04 18:26:26', '2022-11-04 18:26:32', '2023-11-05 02:26:26'),
('babe3e766798a5d4e47893ac01ea92daa1be5471b5520e1a6c6d867dfa076076432ddeef87ce98ef', 7, 1, 'MyApp', '[]', 1, '2022-12-04 23:42:19', '2022-12-05 02:06:24', '2023-12-05 07:42:19'),
('bcd9e9a5812e507baa64f051cdb44e30ed1d7d18b54307828b1adb7312f691ee9768d4187d9fb022', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:02', '2025-12-30 05:58:02', '2026-12-30 13:58:02'),
('bdb5ca3b4d03726a3002e47c5c42cb02fe11da5f03c8ae05a1e7b8f26cf2e2222914961a8e845e33', 6, 1, 'MyApp', '[]', 1, '2022-11-25 23:48:59', '2022-11-26 00:18:53', '2023-11-26 07:48:59'),
('bf2068e7c1d74d87b348c33b1c313298f1113b6fdf53a6775f284b5e943b2e7d0b8cfb7d4de550d5', 6, 1, 'MyApp', '[]', 0, '2025-12-27 09:39:35', '2025-12-27 09:39:35', '2026-12-27 17:39:35'),
('c0eafd38bca3544310fc80f04b932c4e0fe6ded1b11550574d1dc6e27329dbb90ebb535233c01a6f', 2, 1, 'MyApp', '[]', 0, '2025-12-27 07:30:19', '2025-12-27 07:30:19', '2026-12-27 15:30:19'),
('c22b95f5f233b658135999b2ce7a00367f8ec8325651f59f24eadc15c8afd22ee9b4d4c3dda88a89', 6, 1, 'MyApp', '[]', 1, '2025-12-27 13:45:07', '2025-12-28 10:36:36', '2026-12-27 21:45:07'),
('c3c82552314435dd9ecdc42a2217f0549c16bbdf72e92b7cafd8fb921cc5b71d5dcf81c95ca40a91', 7, 1, 'MyApp', '[]', 1, '2023-01-08 11:23:46', '2023-01-08 11:28:13', '2024-01-08 19:23:46'),
('c42d1fdab9c92435c9a0d35fd8dbe107361f665479fa14b5cc97aa246fa1b3711bb4e6b0245d2a21', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:14', '2025-12-30 05:58:14', '2026-12-30 13:58:14'),
('c43fc1a0ca57ce87282e59741f3ae2c91f0dad5c3009b88b833094e0afb8b11153dcc1f9e893e4b1', 6, 1, 'MyApp', '[]', 1, '2022-12-04 23:41:36', '2022-12-04 23:42:12', '2023-12-05 07:41:36'),
('c591e6855e5b293fe2002919a7ac3c5222cf974b8717ae8de70588e4c5af42a17e4238d321fa844f', 7, 5, 'MyApp', '[]', 0, '2025-12-30 06:29:32', '2025-12-30 06:29:32', '2026-12-30 14:29:32'),
('c7e064acd1a0116b805efba064503feccbe0456e6c733cf0df263d3f100afad0cf0c5b467b1c098b', 7, 5, 'MyApp', '[]', 1, '2025-12-30 06:39:04', '2025-12-30 06:44:34', '2026-12-30 14:39:04'),
('c82b2a80dc3e2958cb0daaa38746ab7f1634c259e33e60fc2bd05dc9bc74316f8dea19754e27adc2', 2, 1, 'MyApp', '[]', 1, '2022-11-12 16:30:50', '2022-11-12 16:31:09', '2023-11-13 00:30:50'),
('c8f54d64fb91031d9bca5b1573f398a981bd5550340620b1254210e3ad67561f158a7e105d21c408', 7, 1, 'MyApp', '[]', 1, '2022-11-30 05:52:08', '2022-11-30 06:05:56', '2023-11-30 13:52:08'),
('cb619b8fcb65cb520916ec421f125ebd5412d2d56e7bb7aa8f02ec6f0465cb48f8b2940b98637161', 6, 1, 'MyApp', '[]', 1, '2025-12-26 16:46:40', '2025-12-26 16:46:54', '2026-12-27 00:46:40'),
('cdde65bf9a64023993dc9dadff2b047f9a5d25b76e89f813a2ae02e8764972a49b92e49fdb8233a6', 6, 5, 'MyApp', '[]', 1, '2025-12-29 07:43:15', '2025-12-29 07:44:08', '2026-12-29 15:43:15'),
('ce3d1a3d164671cba607106ec23e68d45fd9a48b2b9b10d917e40751dd24f0739a4e467e49111d65', 6, 1, 'MyApp', '[]', 1, '2023-01-09 01:55:47', '2023-01-09 03:16:53', '2024-01-09 09:55:47'),
('d0c91a01420b9a88925c819f620e16c1d2c324ef9f02136d6f3ce657e0a9a4c4d88c7be5e4d83d85', 7, 1, 'MyApp', '[]', 1, '2022-11-29 00:22:27', '2022-11-29 02:13:40', '2023-11-29 08:22:27'),
('d17715d1b1e7d405e1d925e5d78637d54abefeb177d1c0c89229535bde17165ca11fd40ffc8e9e6e', 6, 1, 'MyApp', '[]', 0, '2022-12-10 12:04:31', '2022-12-10 12:04:31', '2023-12-10 20:04:31'),
('d6268dadf7e89a9ffffbfea6a3374e0218358a44ead1cdc9d7476815b0b82888242454397f736593', 7, 1, 'MyApp', '[]', 0, '2022-11-24 16:40:57', '2022-11-24 16:40:57', '2023-11-25 00:40:57'),
('d746001b560077e4c3a2d0b81a8be688964d90815cb3cb9e1b6d09557a7fcfecdddfb35d14370673', 7, 1, 'MyApp', '[]', 1, '2022-11-13 00:22:11', '2022-11-13 01:05:54', '2023-11-13 08:22:11'),
('d7523f4e029cd694e9f216d6d5cf9d60963878ac4bf5d6bdf4623f4565d8449bc9256b5f08814e08', 2, 1, 'MyApp', '[]', 1, '2022-11-05 09:22:05', '2022-11-05 09:27:00', '2023-11-05 17:22:05'),
('d938ff98b494da26059a24498ee50207d2aafac2eabaaa513262ab5fc629e856c9f04a5cf7185c07', 6, 1, 'MyApp', '[]', 0, '2025-12-27 07:32:38', '2025-12-27 07:32:38', '2026-12-27 15:32:38'),
('d94ca76869a9d3c685226b1e452c67aae5df86a50fc1b5d0a77c62ead93275b67c2e9b108b2281ad', 31, 1, 'MyApp', '[]', 0, '2025-12-27 12:11:36', '2025-12-27 12:11:36', '2026-12-27 20:11:36'),
('da7268d9386dec7a9cbb21f4d7dc90c6eccc0d4eb41a31df4f522183b59cb7afa8c96728b9c7215e', 7, 3, 'MyApp', '[]', 0, '2025-12-29 06:55:32', '2025-12-29 06:55:32', '2026-12-29 14:55:32'),
('dae154a33077d9c29c45962614498037ad3e9bc764ad5f38e3e96ae43b8fd0523d62fe568096b653', 6, 5, 'MyApp', '[]', 0, '2025-12-30 06:58:36', '2025-12-30 06:58:36', '2026-12-30 14:58:36'),
('db9438890ab41a1fc23c7b8c03b8708efc123af9f14f30a8ecb1a9dcdcd2f7d3fff7f55348f59d11', 17, 1, 'MyApp', '[]', 0, '2025-12-27 05:24:30', '2025-12-27 05:24:31', '2026-12-27 13:24:30'),
('df83255cc7d3dfef935ce5f1ae07d0e0746a3cc91c70e5de288096a782f14b712843e68a5103ebd5', 7, 5, 'MyApp', '[]', 0, '2025-12-30 05:58:04', '2025-12-30 05:58:04', '2026-12-30 13:58:04'),
('dfe4b286e136309da17b22a02d13669271947d883e2f6e6b0fc115715f8452dc0fc8d259dd20712c', 6, 1, 'MyApp', '[]', 0, '2025-12-27 03:02:18', '2025-12-27 03:02:18', '2026-12-27 11:02:18'),
('e12d97f006982086e9671715b1bac8ca3cc0f8b1817fdcd244f80d980c27723781f77c95a0b63813', 6, 1, 'MyApp', '[]', 1, '2023-01-11 00:43:25', '2023-01-11 00:45:21', '2024-01-11 08:43:25'),
('e1afcd8531189a14c1cb939e7c2c541641526ba2012cffe2fdf53e692e911f5adbf5b9fddcce5fff', 6, 1, 'MyApp', '[]', 0, '2025-12-26 16:35:59', '2025-12-26 16:36:00', '2026-12-27 00:35:59'),
('e1cb9082fb803012da752db025f3fe1767134f5a7366c79697ab0d37e7801dfd30205bf6bf517382', 6, 1, 'MyApp', '[]', 1, '2022-11-26 00:29:16', '2022-11-26 01:19:32', '2023-11-26 08:29:16'),
('e37ac318cb554c28f56b3c539c40a9f568e5fad6384b00b913428bd957b111c250ed89d7fe014f34', 6, 3, 'MyApp', '[]', 1, '2025-12-29 07:01:59', '2025-12-29 07:02:30', '2026-12-29 15:01:59'),
('e4d447a69e2fc252a7a8c8256a8cd76bcd54b4c3183d94413171b09c56109c3d070936fbdded07bc', 6, 1, 'MyApp', '[]', 0, '2022-11-05 08:51:38', '2022-11-05 08:51:38', '2023-11-05 16:51:38'),
('e871e29b789c5d7c5c5ef8bfb5bbd3d7c53f9ae43a4635fb7faa60cf1bd011b05a64b2f47fcc1812', 2, 1, 'MyApp', '[]', 1, '2022-11-04 05:33:28', '2022-11-04 05:39:30', '2023-11-04 13:33:28'),
('e8cab1151a02f3635644ab2a5ea20dfc3c67992254de151ddb51005be22d0ea5a068c34ad1dc1b36', 7, 1, 'MyApp', '[]', 0, '2025-12-29 04:23:23', '2025-12-29 04:23:23', '2026-12-29 12:23:23'),
('e9b54904fb9d10ea51a03d3030df17df9d03eac8ff0ddee2fb7775c9d8a1bc98926010bf32dc559a', 2, 1, 'MyApp', '[]', 1, '2025-12-27 02:59:51', '2025-12-27 03:01:31', '2026-12-27 10:59:51'),
('eb363b0ae78f3e69b4657017194d3b4d1f1e6d00431f53b3b0b82d12d872005124d27b266a7660f8', 6, 1, 'MyApp', '[]', 1, '2023-01-08 13:10:20', '2023-01-08 13:31:35', '2024-01-08 21:10:20'),
('eb795c789f1c86ebf777a7e9bc9fb552f51a8fac5768512624a796fa986854d8bdfa8f122a46eb17', 7, 1, 'MyApp', '[]', 0, '2022-11-26 04:39:42', '2022-11-26 04:39:42', '2023-11-26 12:39:42'),
('ebde1b5f750c7b58c7e80f8916d377d5f758e5d4db2092f9c721122552c58f05d95b64e37025049f', 7, 5, 'MyApp', '[]', 1, '2025-12-29 07:38:43', '2025-12-29 07:43:04', '2026-12-29 15:38:43'),
('ec1f9703a3e52a89795e778a2bf3da0ec8c7385687994099e01c4b8b908550f8d3b61e5c49c77efc', 7, 1, 'MyApp', '[]', 0, '2022-11-14 02:44:27', '2022-11-14 02:44:27', '2023-11-14 10:44:27'),
('ec4dab56ec66748253e6b83ba0499f98ae13845bc30397ee6eb628dc21b9d2c45f57cda2c5693c2f', 6, 1, 'MyApp', '[]', 1, '2023-07-27 23:31:34', '2023-07-27 23:31:50', '2024-07-28 07:31:34'),
('edec84a87440a631a76a2b0ec13b37a792c8d38e432d5af9fd12b86c24ee76b18f39247846213801', 17, 1, 'MyApp', '[]', 1, '2025-12-26 15:21:44', '2025-12-26 16:29:51', '2026-12-26 23:21:44'),
('edfd6412419f9ef8674a08f5536389b25e821a681fdfe0e402e787ce3c9afef682a36cf3f3d3d879', 7, 1, 'MyApp', '[]', 1, '2022-11-26 00:19:02', '2022-11-26 00:29:07', '2023-11-26 08:19:02'),
('eeff9183d68a019146a753605a2358aa189d5c0a87fdc08adec1a198cb05777f030c2d765d5c45c0', 6, 1, 'MyApp', '[]', 0, '2023-01-09 01:51:14', '2023-01-09 01:51:14', '2024-01-09 09:51:14'),
('ef09f7ab4d363065cda5a5eac15e505100afb14335f8f0c55bc5f0784937addf701881a44c408d45', 2, 1, 'MyApp', '[]', 0, '2022-11-03 14:42:20', '2022-11-03 14:42:20', '2023-11-03 22:42:20'),
('f0dec517721ac12b97979069274b2fb9eed896df3d530e8e3f37a261724deedc5dea5987c78b4c65', 7, 1, 'MyApp', '[]', 1, '2023-01-08 13:08:41', '2023-01-08 13:09:08', '2024-01-08 21:08:41'),
('f116eb5e6ce28ba99bdd4ad2f6f472e3cc341948251778e8deef0a45299a290cf83e6cf1131d2d82', 2, 1, 'MyApp', '[]', 0, '2025-12-27 02:59:47', '2025-12-27 02:59:47', '2026-12-27 10:59:47'),
('f487755c071f49d6066b7fec8a2704138327fa09f2be931cef75157f89452956b612d8d7bb020bd3', 6, 1, 'MyApp', '[]', 1, '2022-11-13 01:06:01', '2022-11-13 01:06:35', '2023-11-13 09:06:01'),
('f603527da9e4d0c53845447a38825ab28114e2ed553a0449f7fa55d0e47d4308f70523eaa88f8647', 6, 1, 'MyApp', '[]', 0, '2025-12-28 10:36:49', '2025-12-28 10:36:49', '2026-12-28 18:36:49'),
('f71993a9c4e0b28d80e03e9983a692758deec7c779cf779edf85193c9ecbe73f84869a549885baf1', 7, 1, 'MyApp', '[]', 1, '2022-11-26 01:19:39', '2022-11-26 04:38:37', '2023-11-26 09:19:39'),
('f781652d1030e1d8a388daaced060ad56cfa70f025fa537d3fe740e009a511deb71a19587704b707', 7, 1, 'MyApp', '[]', 1, '2023-01-06 11:33:28', '2023-01-06 11:40:57', '2024-01-06 19:33:28'),
('f90e9efcbe1cec26dcf3193b4cfe938fcf639edca57510d36d3af89485c5a6f75f588b0a07474de6', 7, 1, 'MyApp', '[]', 0, '2022-11-23 04:18:27', '2022-11-23 04:18:27', '2023-11-23 12:18:27'),
('f997de8afdcfef4bf8cbbce74ae17d822d24625ad6ac2624d741ba13e2033326e64e400d7b9cf394', 6, 1, 'MyApp', '[]', 1, '2022-11-14 03:59:09', '2022-11-14 04:07:57', '2023-11-14 11:59:09'),
('fa1556fe67927ad7b5981d1e12b1a4f57c8dd248c936c23a6abd01a0fd8ef7288c0dd4f8b50ac71c', 17, 1, 'MyApp', '[]', 0, '2025-12-27 11:36:35', '2025-12-27 11:36:35', '2026-12-27 19:36:35'),
('fac3293c8190a47f14f3c3daaad599142f93e5d2f1a9b36454a04a391c72ee778cb4d906a5eaecf7', 7, 1, 'MyApp', '[]', 1, '2022-11-13 01:37:32', '2022-11-13 01:38:22', '2023-11-13 09:37:32'),
('fb1bc03b217271081899cb4f5e4fb0eadd6b887c505cccb5927f4fb93174123303133f6c488b1d60', 6, 1, 'MyApp', '[]', 1, '2022-11-05 08:52:00', '2022-11-05 09:21:58', '2023-11-05 16:52:00'),
('fdc4a36dd2852d06cab76c1ec8259aa0f87e544eb0298ddd3fb8d4aea3da8bd6c137ba940eae909d', 26, 1, 'MyApp', '[]', 1, '2023-01-11 00:45:27', '2023-01-11 00:48:35', '2024-01-11 08:45:27'),
('fe60a0ae9afde1989fd362cb33e9ecc5fcb99200d50e79191e9b5f6346c242f91a244e32e2b3ce85', 25, 1, 'MyApp', '[]', 1, '2025-12-27 11:42:09', '2025-12-27 12:11:21', '2026-12-27 19:42:09'),
('ffcae9db09291dd8837a72b055f8e0551844de0408b09a5480c7b15aa958652298dec5116c828766', 7, 1, 'MyApp', '[]', 1, '2022-11-14 03:43:29', '2022-11-14 03:46:17', '2023-11-14 11:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'OEOB3JTMnrCtoK22JWMCWhK1Y0pxRGqRWatzOx6N', NULL, 'http://localhost', 1, 0, 0, '2022-11-02 15:25:42', '2022-11-02 15:25:42'),
(2, NULL, 'Laravel Password Grant Client', 'KalPkjd3wdF2cB1NajM1PPUa05jlgZpjq4KgUYwi', 'users', 'http://localhost', 0, 1, 0, '2022-11-02 15:25:42', '2022-11-02 15:25:42'),
(3, NULL, 'Laravel Personal Access Client', 'mdwFyZMehcI5b56XrIHaJLVZaKmEzg6ZMC1CBeLe', NULL, 'http://localhost', 1, 0, 0, '2025-12-29 06:53:29', '2025-12-29 06:53:29'),
(4, NULL, 'Laravel Password Grant Client', 'EK1cFb9jJ0jbvzYGp7vzHBBLTPfM6LtdQDaIJnsn', 'users', 'http://localhost', 0, 1, 0, '2025-12-29 06:53:29', '2025-12-29 06:53:29'),
(5, NULL, 'Laravel Personal Access Client', 'sEktoO9NRzxoz7wpxFlRW4hVrWjB0pXGxCYQBBiV', NULL, 'http://localhost', 1, 0, 0, '2025-12-29 07:13:56', '2025-12-29 07:13:56'),
(6, NULL, 'Laravel Password Grant Client', 'PBC3ou4WO1luWREpaJLSxA9O1eBEZpK38QecsqkR', 'users', 'http://localhost', 0, 1, 0, '2025-12-29 07:13:56', '2025-12-29 07:13:56');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2022-11-02 15:25:42', '2022-11-02 15:25:42'),
(2, 3, '2025-12-29 06:53:29', '2025-12-29 06:53:29'),
(3, 5, '2025-12-29 07:13:56', '2025-12-29 07:13:56');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penugasans`
--

CREATE TABLE `penugasans` (
  `id` bigint UNSIGNED NOT NULL,
  `classroom_id` bigint UNSIGNED NOT NULL,
  `jt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soal` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penugasans`
--

INSERT INTO `penugasans` (`id`, `classroom_id`, `jt`, `soal`, `created_at`, `updated_at`) VALUES
(6, 2, 'dfxgchjkl', 'dfg', '2025-12-29 05:04:06', '2025-12-29 05:04:06'),
(7, 2, 'retgethy', 'rgtgn', '2025-12-29 05:30:51', '2025-12-29 05:30:51'),
(8, 2, 'dscef', 'sfvfv', '2025-12-29 07:29:23', '2025-12-29 07:29:23');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pinjam_alats`
--

CREATE TABLE `pinjam_alats` (
  `id` bigint UNSIGNED NOT NULL,
  `katalog_id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tgl_pakai` date NOT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `jam_pakai` time NOT NULL,
  `jam_kembali` time DEFAULT NULL,
  `jam` int DEFAULT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keperluan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `lkpd` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pinjam_alats`
--

INSERT INTO `pinjam_alats` (`id`, `katalog_id`, `kelas_id`, `user_id`, `tgl_pakai`, `tgl_kembali`, `jam_pakai`, `jam_kembali`, `jam`, `lokasi`, `keperluan`, `status`, `created_at`, `updated_at`, `lkpd`) VALUES
(2, 2, 3, 7, '2022-11-28', '2022-11-28', '08:00:00', '09:00:00', 3, 'Kelas', 'Praktikum', 'dikembalikan', '2022-11-25 22:19:34', '2022-12-02 00:17:21', 'lkpd/n2zhZpLARiyTga0yVW3xFou2ETqRbgwNio1t2dCG.pdf'),
(3, 4, 3, 7, '2022-11-29', '2022-11-29', '10:01:00', '10:00:00', 3, 'Kelas', 'Praktikum', 'dikembalikan', '2022-11-26 00:27:37', '2023-01-09 02:08:19', 'lkpd/aMFZlsOe1T5l2zhHUxEPjSuNGGcozjAittHRUCyt.pdf'),
(4, 4, 3, 7, '2022-12-15', '2022-12-15', '09:00:00', '09:00:00', 4, 'sekolah', 'praktikum', 'disetujui', '2022-12-01 23:53:37', '2022-12-02 00:37:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pinjam_labs`
--

CREATE TABLE `pinjam_labs` (
  `id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `katalog_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tgl` date NOT NULL,
  `peminjam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pekan` int NOT NULL,
  `jam` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` char(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `lkpd` text COLLATE utf8mb4_unicode_ci,
  `jam_selesai` char(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pinjam_labs`
--

INSERT INTO `pinjam_labs` (`id`, `kelas_id`, `katalog_id`, `user_id`, `tgl`, `peminjam`, `pekan`, `jam`, `status`, `created_at`, `updated_at`, `lkpd`, `jam_selesai`) VALUES
(11, 3, 4, 7, '2022-11-23', 'muhammad akbar', 2, '07:00', 'diajukan', '2022-11-29 02:33:29', '2022-12-12 01:21:19', 'lkpd/kdohrP2GPQnyMYV7fG3zw8rFcSG81VxfKg5u5iUa.pdf', '09:30'),
(12, 3, 4, 7, '2022-11-16', 'muhammad akbar', 3, '09:30', 'disetujui', '2022-11-29 02:41:33', '2022-11-29 13:10:38', NULL, '10:30'),
(13, 3, 1, 7, '2022-11-09', 'muhammad akbar', 2, '09:30', 'disetujui', '2022-11-29 03:02:45', '2022-11-29 13:10:36', NULL, '10:30'),
(14, 3, 4, 7, '2022-11-23', 'muhammad akbar', 3, '09:30', 'disetujui', '2022-11-29 03:03:31', '2022-11-29 13:10:34', NULL, '12:30'),
(15, 3, 1, 7, '2022-11-16', 'muhammad akbar', 3, '07:00', 'disetujui', '2022-11-29 04:03:30', '2022-11-29 13:10:32', NULL, '08:00'),
(16, 3, 1, 7, '2022-11-28', 'muhammad akbar', 3, '10:00', 'disetujui', '2022-11-29 04:04:33', '2022-11-29 13:10:22', NULL, '10:45'),
(25, 3, 4, 7, '2022-11-23', 'muhammad akbar', 2, '07:00', 'diajukan', '2022-12-05 00:40:01', '2022-12-05 00:40:01', NULL, '09:30');

-- --------------------------------------------------------

--
-- Table structure for table `pinjam_lains`
--

CREATE TABLE `pinjam_lains` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tgl` date NOT NULL,
  `mulai` time NOT NULL,
  `selesai` time NOT NULL,
  `kegiatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` char(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pinjam_lains`
--

INSERT INTO `pinjam_lains` (`id`, `user_id`, `tgl`, `mulai`, `selesai`, `kegiatan`, `status`, `created_at`, `updated_at`) VALUES
(2, 7, '2022-11-30', '13:01:00', '15:00:00', 'Ulangan Bahasa Ingrris', 'disetujui', '2022-11-29 12:38:13', '2023-07-27 23:31:45');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, NULL),
(2, 'Laboran', NULL, NULL),
(3, 'Guru', NULL, NULL),
(4, 'Siswa', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sildes`
--

CREATE TABLE `sildes` (
  `id` bigint UNSIGNED NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ket` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sildes`
--

INSERT INTO `sildes` (`id`, `judul`, `ket`, `gambar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BELAJAR DI ERA DIGITAL', 'Hendaklah kamu semua mengusahakan ilmu pengetahuan itu sebelum dilenyapkan. Lenyapnya ilmu pengetahuan ialah dengan matinya orang-orang yang memberikan atau mengajarkannya. Seseorang itu tidaklah dilahirkan langsung pandai, jadi ilmu pengetahuan itu pastilah harus dengan belajar. – Ibnu Mas’ud', 'slide/mCu08RLwrOxA5YDbzBYqaX2oSy9yTAvlG4MClQ5L.jpg', 'on', '2022-12-13 00:54:43', '2023-01-10 07:29:59'),
(3, 'LABORATORIUM DIGITAL IPA TERPADU', 'Laboratorium Digital IPA (e-ipa) Terpadu  adalah laboratorium pembelajaran yang menyediakan bahan belajar dan praktikum  serta fasilitas praktikum yang mendukung interaksi antar guru dan peserta didik  Dengan menggunakan e-ipa kita dapat belajar di mana saja, kapan saja dengan siapa saja.', 'slide/okjFf1ED67JVvhdKcz3vGSgmFPI4i78yq88wGQ2d.jpg', 'on', '2022-12-13 01:37:29', '2023-01-10 07:27:16'),
(4, 'MTSN 1 KOTA MAKASSAR', 'adalah salah satu satuan pendidikan dengan jenjang MTs di Mannuruki, Kec. Tamalate, Kota Makassar, Sulawesi Selatan. Dalam menjalankan kegiatannya, MTSN 1 KOTA MAKASSAR berada di bawah naungan Kementerian Agama.', 'slide/DBfWGNkQOkj6o0EHnyZjC4wOC63UV7rZ7eMeC2DL.jpg', 'on', '2022-12-13 01:38:18', '2023-01-10 07:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'admin', 'admin', 'admin@mtsn.sch.id', '$2y$12$1lddG3g4.FvrTmXrKRCDpejjFg.sNc8nIv29LlVathw3QdKqbxq3y', 1, NULL, '2022-11-02 18:25:06', '2022-11-02 18:25:06'),
(6, 'Rahmat Dani S. Kom', 'dani', 'dani@sistelk.id', '$2y$12$1lddG3g4.FvrTmXrKRCDpejjFg.sNc8nIv29LlVathw3QdKqbxq3y', 2, NULL, '2022-11-04 05:00:48', '2022-11-04 05:00:48'),
(7, 'muhammad akbar', 'ali', 'ali@gmail.com', '$2y$10$SLM8rg1F1invjUaVBlTLsOI0y.pUC8ciEjf3/Qgy1QFY5A8C76VYO', 3, NULL, '2022-11-04 05:23:12', '2025-12-27 19:28:03'),
(17, 'siswa', 'siswa', 'siswa@mtsn1mks.sch.id', '$2y$12$1lddG3g4.FvrTmXrKRCDpejjFg.sNc8nIv29LlVathw3QdKqbxq3y', 4, NULL, '2022-11-04 20:38:58', '2022-11-26 04:39:29'),
(25, 'ayatullah', '3333', 'aya@gmail.com', '$2y$10$GFf4KiFdUCydM2TGFhwri.wjX3qmOeHWYbDgiJungP1uLstpWbfUO', 4, NULL, '2022-12-10 14:12:04', '2025-12-27 11:40:44'),
(26, 'yayu apriliani', 'yayu', 'yayu@gmail.com', '$2y$12$1lddG3g4.FvrTmXrKRCDpejjFg.sNc8nIv29LlVathw3QdKqbxq3y', 3, NULL, '2023-01-11 00:43:53', '2023-01-11 00:45:17'),
(27, 'Andi Fajriah, S.Ag.', 'fajriah', 'fajriah@gmail.com', '$2y$12$1lddG3g4.FvrTmXrKRCDpejjFg.sNc8nIv29LlVathw3QdKqbxq3y', 3, NULL, '2023-01-11 00:49:43', '2023-01-11 00:49:43'),
(28, 'Dra. Nahda', 'nahda', 'nahda@gmail.com', '$2y$12$1lddG3g4.FvrTmXrKRCDpejjFg.sNc8nIv29LlVathw3QdKqbxq3y', 3, NULL, '2023-01-11 00:52:01', '2023-01-11 00:52:01'),
(29, 'navia', '222', 'nav@mts.com', '$2y$10$reiQwNfgO164TUbPVFWgO.HkxeMrceupIKeUfPLDVtsG5YltRg5wy', 4, NULL, '2025-12-27 01:54:45', '2025-12-29 07:48:18'),
(31, 'fajrul okemi', 'timika', 'sdfghnmj@gmail.com', '$2y$10$DpXL8q9jk6Cm/1rM7xpOL.Z2M2NJpPURqdvvvvdC4uM8rUrNfIRV.', 4, NULL, '2025-12-27 12:11:12', '2025-12-27 12:11:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensis`
--
ALTER TABLE `absensis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensis_classroom_id_foreign` (`classroom_id`);

--
-- Indexes for table `biogurus`
--
ALTER TABLE `biogurus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `biogurus_user_id_foreign` (`user_id`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classrooms_user_id_foreign` (`user_id`),
  ADD KEY `classrooms_katalog_id_foreign` (`katalog_id`),
  ADD KEY `classrooms_kelas_id_foreign` (`kelas_id`);

--
-- Indexes for table `data_absens`
--
ALTER TABLE `data_absens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_absens_absensi_id_foreign` (`absensi_id`),
  ADD KEY `data_absens_user_id_foreign` (`user_id`);

--
-- Indexes for table `data_katalogs`
--
ALTER TABLE `data_katalogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_katalogs_katalog_id_foreign` (`katalog_id`),
  ADD KEY `data_katalogs_inventaris_id_foreign` (`inventaris_id`);

--
-- Indexes for table `data_siswas`
--
ALTER TABLE `data_siswas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_siswas_email_unique` (`email`),
  ADD KEY `data_siswas_kelas_id_foreign` (`kelas_id`);

--
-- Indexes for table `data_tugas`
--
ALTER TABLE `data_tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_tugas_penugasan_id_foreign` (`penugasan_id`),
  ADD KEY `data_tugas_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `foto_profiles`
--
ALTER TABLE `foto_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foto_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jumlah_pinjams`
--
ALTER TABLE `jumlah_pinjams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jumlah_pinjams_data_katalog_id_foreign` (`data_katalog_id`),
  ADD KEY `jumlah_pinjams_pinjam_lab_id_foreign` (`pinjam_lab_id`);

--
-- Indexes for table `jumlah_pinjam_alats`
--
ALTER TABLE `jumlah_pinjam_alats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jumlah_pinjam_alats_data_katalog_id_foreign` (`data_katalog_id`),
  ADD KEY `jumlah_pinjam_alats_pinjam_alat_id_foreign` (`pinjam_alat_id`);

--
-- Indexes for table `katalogs`
--
ALTER TABLE `katalogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas_siswas`
--
ALTER TABLE `kelas_siswas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_siswas_user_id_foreign` (`user_id`),
  ADD KEY `kelas_siswas_kelas_id_foreign` (`kelas_id`);

--
-- Indexes for table `materi_ajars`
--
ALTER TABLE `materi_ajars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materi_ajars_classroom_id_foreign` (`classroom_id`),
  ADD KEY `materi_ajars_modul_id_foreign` (`modul_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modul_lkpd`
--
ALTER TABLE `modul_lkpd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modul_lkpd_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `penugasans`
--
ALTER TABLE `penugasans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penugasans_classroom_id_foreign` (`classroom_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pinjam_alats`
--
ALTER TABLE `pinjam_alats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pinjam_alats_kelas_id_foreign` (`kelas_id`),
  ADD KEY `pinjam_alats_katalog_id_foreign` (`katalog_id`),
  ADD KEY `pinjam_alats_user_id_foreign` (`user_id`);

--
-- Indexes for table `pinjam_labs`
--
ALTER TABLE `pinjam_labs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pinjam_labs_kelas_id_foreign` (`kelas_id`),
  ADD KEY `pinjam_labs_katalog_id_foreign` (`katalog_id`),
  ADD KEY `pinjam_labs_user_id_foreign` (`user_id`);

--
-- Indexes for table `pinjam_lains`
--
ALTER TABLE `pinjam_lains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pinjam_lains_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sildes`
--
ALTER TABLE `sildes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensis`
--
ALTER TABLE `absensis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `biogurus`
--
ALTER TABLE `biogurus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_absens`
--
ALTER TABLE `data_absens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_katalogs`
--
ALTER TABLE `data_katalogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `data_siswas`
--
ALTER TABLE `data_siswas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `data_tugas`
--
ALTER TABLE `data_tugas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `foto_profiles`
--
ALTER TABLE `foto_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `inventaris`
--
ALTER TABLE `inventaris`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jumlah_pinjams`
--
ALTER TABLE `jumlah_pinjams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `jumlah_pinjam_alats`
--
ALTER TABLE `jumlah_pinjam_alats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `katalogs`
--
ALTER TABLE `katalogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kelas_siswas`
--
ALTER TABLE `kelas_siswas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `materi_ajars`
--
ALTER TABLE `materi_ajars`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `modul_lkpd`
--
ALTER TABLE `modul_lkpd`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penugasans`
--
ALTER TABLE `penugasans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pinjam_alats`
--
ALTER TABLE `pinjam_alats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pinjam_labs`
--
ALTER TABLE `pinjam_labs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pinjam_lains`
--
ALTER TABLE `pinjam_lains`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sildes`
--
ALTER TABLE `sildes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensis`
--
ALTER TABLE `absensis`
  ADD CONSTRAINT `absensis_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `biogurus`
--
ALTER TABLE `biogurus`
  ADD CONSTRAINT `biogurus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD CONSTRAINT `classrooms_katalog_id_foreign` FOREIGN KEY (`katalog_id`) REFERENCES `katalogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `classrooms_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `classrooms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `data_absens`
--
ALTER TABLE `data_absens`
  ADD CONSTRAINT `data_absens_absensi_id_foreign` FOREIGN KEY (`absensi_id`) REFERENCES `absensis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `data_absens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `data_katalogs`
--
ALTER TABLE `data_katalogs`
  ADD CONSTRAINT `data_katalogs_inventaris_id_foreign` FOREIGN KEY (`inventaris_id`) REFERENCES `inventaris` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `data_katalogs_katalog_id_foreign` FOREIGN KEY (`katalog_id`) REFERENCES `katalogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `data_siswas`
--
ALTER TABLE `data_siswas`
  ADD CONSTRAINT `data_siswas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `data_tugas`
--
ALTER TABLE `data_tugas`
  ADD CONSTRAINT `data_tugas_penugasan_id_foreign` FOREIGN KEY (`penugasan_id`) REFERENCES `penugasans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `data_tugas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `foto_profiles`
--
ALTER TABLE `foto_profiles`
  ADD CONSTRAINT `foto_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jumlah_pinjams`
--
ALTER TABLE `jumlah_pinjams`
  ADD CONSTRAINT `jumlah_pinjams_data_katalog_id_foreign` FOREIGN KEY (`data_katalog_id`) REFERENCES `data_katalogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jumlah_pinjams_pinjam_lab_id_foreign` FOREIGN KEY (`pinjam_lab_id`) REFERENCES `pinjam_labs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jumlah_pinjam_alats`
--
ALTER TABLE `jumlah_pinjam_alats`
  ADD CONSTRAINT `jumlah_pinjam_alats_data_katalog_id_foreign` FOREIGN KEY (`data_katalog_id`) REFERENCES `data_katalogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jumlah_pinjam_alats_pinjam_alat_id_foreign` FOREIGN KEY (`pinjam_alat_id`) REFERENCES `pinjam_alats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kelas_siswas`
--
ALTER TABLE `kelas_siswas`
  ADD CONSTRAINT `kelas_siswas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_siswas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `materi_ajars`
--
ALTER TABLE `materi_ajars`
  ADD CONSTRAINT `materi_ajars_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materi_ajars_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `modul_lkpd` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `modul_lkpd`
--
ALTER TABLE `modul_lkpd`
  ADD CONSTRAINT `modul_lkpd_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penugasans`
--
ALTER TABLE `penugasans`
  ADD CONSTRAINT `penugasans_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pinjam_alats`
--
ALTER TABLE `pinjam_alats`
  ADD CONSTRAINT `pinjam_alats_katalog_id_foreign` FOREIGN KEY (`katalog_id`) REFERENCES `katalogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pinjam_alats_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pinjam_alats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pinjam_labs`
--
ALTER TABLE `pinjam_labs`
  ADD CONSTRAINT `pinjam_labs_katalog_id_foreign` FOREIGN KEY (`katalog_id`) REFERENCES `katalogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pinjam_labs_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pinjam_labs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pinjam_lains`
--
ALTER TABLE `pinjam_lains`
  ADD CONSTRAINT `pinjam_lains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

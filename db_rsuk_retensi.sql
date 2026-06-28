-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2026 at 06:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_rsuk_retensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_user` varchar(100) DEFAULT NULL,
  `modul` varchar(50) DEFAULT NULL,
  `aksi` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `nama_user`, `modul`, `aksi`, `deskripsi`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, NULL, 'System/Guest', 'Laporan', 'Export CSV Retensi', 'User melakukan ekspor CSV Laporan Retensi', '203.17.85.140', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-24 21:14:59', '2026-06-24 21:14:59'),
(2, 3, 'berlian', 'Laporan', 'Export CSV Retensi', 'User melakukan ekspor CSV Laporan Retensi', '203.17.85.140', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-24 21:42:39', '2026-06-24 21:42:39'),
(3, 1, 'Administrator', 'Autentikasi', 'Login', 'User berhasil login ke sistem', '127.0.0.1', 'Symfony', '2026-06-26 07:36:47', '2026-06-26 07:36:47'),
(4, 1, 'Administrator', 'Autentikasi', 'Logout', 'User berhasil logout dari sistem', '127.0.0.1', 'Symfony', '2026-06-26 07:36:52', '2026-06-26 07:36:52'),
(5, 3, 'berlian', 'Laporan', 'Export CSV Retensi', 'User melakukan ekspor CSV Laporan Retensi', '112.78.133.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 02:01:47', '2026-06-27 02:01:47'),
(6, 3, 'berlian', 'Autentikasi', 'Login', 'User berhasil login ke sistem', '202.65.239.253', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 02:05:18', '2026-06-27 02:05:18'),
(7, 3, 'berlian', 'Autentikasi', 'Logout', 'User berhasil logout dari sistem', '202.65.239.253', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 03:05:32', '2026-06-27 03:05:32'),
(8, 3, 'berlian', 'Autentikasi', 'Login', 'User berhasil login ke sistem', '202.65.239.253', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 03:05:44', '2026-06-27 03:05:44'),
(9, 3, 'berlian', 'Autentikasi', 'Login', 'User berhasil login ke sistem', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 03:25:07', '2026-06-27 03:25:07'),
(10, 1, 'Administrator', 'Autentikasi', 'Login', 'User berhasil login ke sistem', '203.17.85.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 21:10:27', '2026-06-27 21:10:27');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `key`, `value`, `label`, `group`, `type`, `created_at`, `updated_at`) VALUES
(1, 'retention_update_interval', '24', 'Interval Update Retensi', 'general', 'number', '2026-05-09 21:16:03', '2026-06-24 23:39:24'),
(3, 'retention_update_unit', 'minutes', 'Satuan Interval Update Retensi', 'general', 'text', '2026-06-10 00:49:07', '2026-06-24 23:52:47'),
(4, 'last_retention_update', '2026-06-10 08:15:47', 'Last Retention Update', 'general', 'text', '2026-06-10 01:15:47', '2026-06-10 01:15:47'),
(5, 'mock_ai_interceptor', 'true', 'Mock AI Interceptor', 'general', 'boolean', '2026-06-22 00:26:56', '2026-06-22 00:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `berita_acara_pemusnahan`
--

CREATE TABLE `berita_acara_pemusnahan` (
  `id` int(11) NOT NULL,
  `id_pemusnahan` int(11) DEFAULT NULL,
  `nomor_berita_acara` varchar(50) DEFAULT NULL,
  `tanggal_pemusnahan` date DEFAULT NULL,
  `file_berita_acara` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-temp_doc_temp_6a3e51edb33e5_17824691017342', 'a:7:{s:2:\"id\";s:33:\"temp_6a3e51edb33e5_17824691017342\";s:9:\"nama_file\";s:19:\"RI_RM_M_NURLIZA.pdf\";s:13:\"file_original\";s:64:\"dokumen_rekam_medis/YsaEEZuRmNqMx4LJ9OlgMvsYytbDtAZOygFudWuY.pdf\";s:15:\"file_compressed\";s:291:\"[\"dokumen_rekam_medis\\/converted_6a3e51ed4fdd0_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3e51ed4fdd0_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3e51ed4fdd0_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3e51ed4fdd0_page_3.jpg\",\"dokumen_rekam_medis\\/converted_6a3e51ed4fdd0_page_4.jpg\"]\";s:7:\"user_id\";i:1;s:6:\"status\";s:7:\"success\";s:10:\"created_at\";s:19:\"2026-06-26 10:18:21\";}', 1782476301),
('laravel-cache-temp_doc_temp_6a3e546c8860a_17824697405586', 'a:8:{s:2:\"id\";s:33:\"temp_6a3e546c8860a_17824697405586\";s:9:\"nama_file\";s:19:\"RI_RM_M_NURLIZA.pdf\";s:13:\"file_original\";s:64:\"dokumen_rekam_medis/Ykou9fdTC83lok8FUjorv7aql0I9eiadvSVTBvin.pdf\";s:15:\"file_compressed\";s:291:\"[\"dokumen_rekam_medis\\/converted_6a3e546c1fc16_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3e546c1fc16_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3e546c1fc16_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3e546c1fc16_page_3.jpg\",\"dokumen_rekam_medis\\/converted_6a3e546c1fc16_page_4.jpg\"]\";s:7:\"user_id\";i:1;s:6:\"status\";s:7:\"success\";s:10:\"created_at\";s:19:\"2026-06-26 10:29:00\";s:10:\"ocr_result\";a:6:{s:19:\"fasilitas_kesehatan\";a:3:{s:16:\"nama_rumah_sakit\";s:26:\"RUMAH SAKIT UMUM KALIWATES\";s:9:\"alamat_rs\";s:44:\"Jl. Diah Pitaloka No. 4A Jember - Jawa Timur\";s:6:\"kontak\";a:3:{s:6:\"kantor\";s:13:\"(0331) 485967\";s:3:\"igd\";s:13:\"(0331) 483505\";s:5:\"email\";s:27:\"info_rsuk@rolasmedika.co.id\";}}s:16:\"identitas_pasien\";a:6:{s:8:\"nomor_rm\";s:6:\"265207\";s:11:\"nama_pasien\";s:7:\"SUYITNO\";s:13:\"tanggal_lahir\";s:10:\"16-04-1962\";s:13:\"jenis_kelamin\";s:1:\"L\";s:13:\"alamat_pasien\";s:37:\"Jl. Teuku Umar blok F-20, Tegal Besar\";s:13:\"nomor_telepon\";s:12:\"081350043879\";}s:14:\"data_kunjungan\";a:5:{s:9:\"tgl_masuk\";s:10:\"25/12/2025\";s:10:\"tgl_keluar\";s:10:\"27/12/2025\";s:12:\"lama_dirawat\";s:1:\"2\";s:10:\"alasan_mrs\";s:10:\"Nyeri dada\";s:15:\"diagnosis_utama\";s:2:\"CH\";}s:21:\"diagnosa_dan_tindakan\";a:1:{s:11:\"kode_icd_10\";s:10:\"I25.1, I50\";}s:12:\"tenaga_medis\";a:3:{s:11:\"dokter_dpjp\";s:18:\"dr. Suryono, Sp.JP\";s:26:\"dokter_penolong_persalinan\";s:0:\"\";s:31:\"dokter_bidan_saksi_serah_terima\";s:0:\"\";}s:18:\"informasi_keluarga\";a:3:{s:27:\"wali_hukum_penanggung_jawab\";a:4:{s:4:\"nama\";s:8:\"Meliyana\";s:8:\"hubungan\";s:5:\"Istri\";s:6:\"alamat\";s:37:\"Jl. Teuku Umar blok F-20, Tegal Besar\";s:13:\"nomor_telepon\";s:12:\"082153235096\";}s:13:\"identitas_ibu\";a:1:{s:4:\"nama\";s:0:\"\";}s:27:\"penerima_wewenang_informasi\";a:0:{}}}}', 1782476964);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daftar_pemusnahan`
--

CREATE TABLE `daftar_pemusnahan` (
  `id` int(11) NOT NULL,
  `no_rm` varchar(20) DEFAULT NULL,
  `tanggal_retensi` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `approved_kepala_rm` int(11) DEFAULT NULL,
  `tanggal_approval_rm` datetime DEFAULT NULL,
  `approved_direktur` int(11) DEFAULT NULL,
  `tanggal_approval_direktur` datetime DEFAULT NULL,
  `tanggal_pemusnahan` datetime DEFAULT NULL,
  `destroyed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_pemusnahan`
--

INSERT INTO `daftar_pemusnahan` (`id`, `no_rm`, `tanggal_retensi`, `status`, `approved_kepala_rm`, `tanggal_approval_rm`, `approved_direktur`, `tanggal_approval_direktur`, `tanggal_pemusnahan`, `destroyed_by`, `created_at`) VALUES
(3, '123456', '2026-06-25', 'menunggu_eksekusi', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 23:57:21'),
(4, '207165', '2026-06-26', 'menunggu_eksekusi', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-26 07:27:55');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_rekam_medis`
--

CREATE TABLE `dokumen_rekam_medis` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `no_rm` varchar(20) DEFAULT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `file_original` varchar(255) DEFAULT NULL,
  `file_compressed` text DEFAULT NULL,
  `engine` varchar(50) DEFAULT NULL,
  `status` enum('uploaded','processing','success','failed','validated','completed') DEFAULT 'uploaded',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokumen_rekam_medis`
--

INSERT INTO `dokumen_rekam_medis` (`id`, `user_id`, `no_rm`, `nama_file`, `file_original`, `file_compressed`, `engine`, `status`, `created_at`, `error_message`) VALUES
(1, 3, '242869', 'RM_SUNARSO.pdf', 'dokumen_rekam_medis/XTImF1YO8vtOIvtog2LElMRzzJppGUCnmqG030jF.pdf', '[\"dokumen_rekam_medis\\/converted_6a3aaf85ebc3e_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3aaf85ebc3e_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3aaf85ebc3e_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3aaf85ebc3e_page_3.jpg\",\"dokumen_rekam_medis\\/converted_6a3aaf85ebc3e_page_4.jpg\"]', 'gemini', 'validated', '2026-06-23 09:09:06', NULL),
(2, 3, '265207', 'RI_RM_SUYITNO.pdf', 'dokumen_rekam_medis/hJM6C2SETLTIjzwfkLj2aPo4JTmRt4bNJQst3Qma.pdf', '[\"dokumen_rekam_medis\\/converted_6a3b6372cf2a3_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3b6372cf2a3_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3b6372cf2a3_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3b6372cf2a3_page_3.jpg\",\"dokumen_rekam_medis\\/converted_6a3b6372cf2a3_page_4.jpg\"]', 'gemini', 'validated', '2026-06-23 22:02:28', NULL),
(3, 3, '167684', 'RJ_RM_ERNA_TRI.pdf', 'dokumen_rekam_medis/1S5JRvjdq3u3RdmPbYU6eGOYHusv9tkTDnGG7V09.pdf', '[\"dokumen_rekam_medis\\/converted_6a3b785772d87_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3b785772d87_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3b785772d87_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3b785772d87_page_3.jpg\",\"dokumen_rekam_medis\\/converted_6a3b785772d87_page_4.jpg\"]', 'gemini', 'validated', '2026-06-23 23:27:21', NULL),
(4, 1, '123456', 'RJ_RM_SUYATI.pdf', 'dokumen_rekam_medis/C10c5dWg2BfWUDsMxKfFORpqSrEKq4zk4rdnd50K.pdf', '[\"dokumen_rekam_medis\\/converted_6a3cd0e4558c7_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3cd0e4558c7_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3cd0e4558c7_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3cd0e4558c7_page_3.jpg\"]', 'gemini', 'validated', '2026-06-24 23:57:16', NULL),
(5, 1, '209773', 'RM_BY_OLIVIA CHRISANTI_TARDIANTO.pdf', 'dokumen_rekam_medis/VXaSySRQs7ssIXB3wsQyfDyNR0Ks5tM6Pk0zn97f.pdf', '[\"dokumen_rekam_medis\\/converted_6a3cfe7020a1b_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3cfe7020a1b_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3cfe7020a1b_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3cfe7020a1b_page_3.jpg\",\"dokumen_rekam_medis\\/converted_6a3cfe7020a1b_page_4.jpg\"]', 'gemini', 'validated', '2026-06-25 03:54:10', NULL),
(6, 1, '207165', 'RI_RM_M_NURLIZA.pdf', 'dokumen_rekam_medis/x8BghoScfVcts3DKRjnS6tTBpujG0QrmhHsZNSlO.pdf', '[\"dokumen_rekam_medis\\/converted_6a3e585a37358_page_0.jpg\",\"dokumen_rekam_medis\\/converted_6a3e585a37358_page_1.jpg\",\"dokumen_rekam_medis\\/converted_6a3e585a37358_page_2.jpg\",\"dokumen_rekam_medis\\/converted_6a3e585a37358_page_3.jpg\",\"dokumen_rekam_medis\\/converted_6a3e585a37358_page_4.jpg\"]', 'gemini', 'validated', '2026-06-26 04:32:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kasus_master`
--

CREATE TABLE `kasus_master` (
  `id` int(11) NOT NULL,
  `kelompok` varchar(50) DEFAULT NULL,
  `jenis_kasus` varchar(100) DEFAULT NULL,
  `masa_aktif_rj` int(11) DEFAULT NULL,
  `masa_inaktif_rj` int(11) DEFAULT NULL,
  `masa_aktif_ri` int(11) DEFAULT NULL,
  `masa_inaktif_ri` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kasus_master`
--

INSERT INTO `kasus_master` (`id`, `kelompok`, `jenis_kasus`, `masa_aktif_rj`, `masa_inaktif_rj`, `masa_aktif_ri`, `masa_inaktif_ri`, `keterangan`, `created_at`) VALUES
(1, 'UMUM', 'UMUM', 5, 2, 5, 2, NULL, '2026-03-20 01:28:08'),
(2, 'SPESIALIS', 'MATA', 5, 2, 10, 2, NULL, '2026-03-20 01:28:08'),
(3, 'PSIKIATRI', 'JIWA', 10, 5, 5, 5, NULL, '2026-03-20 01:28:08'),
(4, 'SPESIALIS', 'ORTHOPAEDI', 10, 2, 10, 2, NULL, '2026-03-20 01:28:08'),
(5, 'KHUSUS', 'KUSTA', 15, 2, 15, 2, NULL, '2026-03-20 01:28:08'),
(6, 'KHUSUS', 'KETERGANTUNGAN OBAT', 15, 2, 15, 2, NULL, '2026-03-20 01:28:08'),
(7, 'SPESIALIS', 'JANTUNG', 10, 2, 10, 2, NULL, '2026-03-20 01:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `kunjungan`
--

CREATE TABLE `kunjungan` (
  `id_kunjungan` int(11) NOT NULL,
  `no_rm` varchar(20) DEFAULT NULL,
  `nama_pasien` varchar(100) DEFAULT NULL,
  `jenis_kelamin` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kunjungan`
--

INSERT INTO `kunjungan` (`id_kunjungan`, `no_rm`, `nama_pasien`, `jenis_kelamin`, `alamat`, `tanggal_masuk`, `tanggal_keluar`, `diagnosa`, `created_at`) VALUES
(1, '242869', NULL, NULL, NULL, '2024-06-09', '2024-06-11', 'Benjolan paha kiri ()', '2026-06-23 16:09:06'),
(2, '265207', NULL, NULL, NULL, '2025-12-25', '2025-12-27', 'CH ()', '2026-06-24 05:02:28'),
(3, '167684', NULL, NULL, NULL, '2020-11-15', '2020-11-17', 'ASMA ()', '2026-06-24 06:27:21'),
(4, '123456', NULL, NULL, NULL, '2019-04-19', '2019-04-23', 'HT, Hemiparesis ()', '2026-06-25 06:57:16'),
(5, '209773', NULL, NULL, NULL, '2023-02-17', '2023-02-18', 'NA, SC, BBLC ()', '2026-06-25 10:54:10'),
(6, '207165', NULL, NULL, NULL, '2015-12-28', '2025-12-13', 'Dyspepsia ()', '2026-06-26 11:32:55');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_03_30_050133_fix_status_column_in_dokumen_rekam_medis', 2),
(4, '2026_03_30_050214_fix_engine_column_in_dokumen_rekam_medis', 3),
(5, '2026_03_20_000000_create_rsuk_retensi_tables', 4),
(6, '2026_03_30_095230_align_flow_db_structure', 4),
(7, '2026_04_01_192042_fix_missing_columns_for_ocr_and_logs', 4),
(8, '2026_04_28_065059_create_app_settings_table', 4),
(9, '2026_05_10_042028_add_user_id_to_dokumen_rekam_medis_table', 4),
(10, '2026_06_06_025702_change_file_compressed_to_text_in_dokumen_rekam_medis_table', 4),
(11, '2026_06_09_105014_add_status_column_to_retensi_table', 5),
(12, '2026_06_10_043902_add_tanggal_pemusnahan_to_daftar_pemusnahan_table', 6),
(13, '2026_06_14_102740_add_kasus_id_to_validasi_data_table', 7),
(14, '2026_06_14_104544_fix_validasi_data_foreign_key_cascade', 8),
(15, '2026_06_25_040843_add_destroyed_by_to_daftar_pemusnahan_table', 9),
(16, '2026_06_26_083700_add_batas_dates_to_retensi_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `ocr_result`
--

CREATE TABLE `ocr_result` (
  `id` int(11) NOT NULL,
  `dokumen_id` int(11) DEFAULT NULL,
  `ocr_text` longtext DEFAULT NULL,
  `ai_result` longtext DEFAULT NULL,
  `parsed_data` longtext DEFAULT NULL,
  `engine` varchar(50) DEFAULT NULL,
  `confidence` float DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `validated_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ocr_result`
--

INSERT INTO `ocr_result` (`id`, `dokumen_id`, `ocr_text`, `ai_result`, `parsed_data`, `engine`, `confidence`, `status`, `validated_at`, `created_at`, `updated_at`) VALUES
(1, 1, '{\n    \"nama_rs\": \"RUMAH SAKIT UMUM KALIWATES\",\n    \"alamat_rs\": \"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\n    \"nomor_rm\": \"242869\",\n    \"nama_pasien\": \"Sunarso\",\n    \"tanggal_lahir\": \"1949-05-11\",\n    \"jenis_kelamin\": \"L\",\n    \"alamat_pasien\": \"Dusun Krajan Barat RT.3\\/RW.7 Suko Jelbuk Jember\",\n    \"tanggal_masuk\": \"2024-06-09\",\n    \"tanggal_keluar\": \"2024-06-11\",\n    \"lama_dirawat\": \"10\",\n    \"alasan_mrs\": \"px dgn benjolan di paha kiri bagian belakang nyeri\",\n    \"diagnosis\": \"Benjolan paha kiri\",\n    \"dokter_dpjp\": \"dr. Ketut Sp.B\",\n    \"keterangan\": null,\n    \"kasus_id\": 1,\n    \"wali_nama\": \"Melda Oktaviana Sari\",\n    \"wali_hubungan\": \"Cucu\"\n}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\"nomor_rm\":\"242869\",\"nama_pasien\":\"Sunarso\",\"tanggal_lahir\":\"1949-05-11\",\"jenis_kelamin\":\"L\",\"alamat_pasien\":\"Dusun Krajan Barat RT.3\\/RW.7 Suko Jelbuk Jember\",\"tanggal_masuk\":\"2024-06-09\",\"tanggal_keluar\":\"2024-06-11\",\"lama_dirawat\":\"10\",\"alasan_mrs\":\"px dgn benjolan di paha kiri bagian belakang nyeri\",\"diagnosis\":\"Benjolan paha kiri\",\"dokter_dpjp\":\"dr. Ketut Sp.B\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":\"Melda Oktaviana Sari\",\"wali_hubungan\":\"Cucu\"}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\"nomor_rm\":\"242869\",\"nama_pasien\":\"Sunarso\",\"tanggal_lahir\":\"1949-05-11\",\"jenis_kelamin\":\"L\",\"alamat_pasien\":\"Dusun Krajan Barat RT.3\\/RW.7 Suko Jelbuk Jember\",\"tanggal_masuk\":\"2024-06-09\",\"tanggal_keluar\":\"2024-06-11\",\"lama_dirawat\":\"10\",\"alasan_mrs\":\"px dgn benjolan di paha kiri bagian belakang nyeri\",\"diagnosis\":\"Benjolan paha kiri\",\"dokter_dpjp\":\"dr. Ketut Sp.B\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":\"Melda Oktaviana Sari\",\"wali_hubungan\":\"Cucu\"}', 'gemini', NULL, 'validated', '2026-06-23 09:09:06', '2026-06-23 16:09:06', '2026-06-23 09:09:06'),
(2, 2, '{\n    \"nama_rs\": \"RUMAH SAKIT UMUM KALIWATES\",\n    \"alamat_rs\": \"Jl. Diah Pitaloka No. 4A Jember - Jawa Timur\",\n    \"nomor_rm\": \"265207\",\n    \"nama_pasien\": \"SUYITNO\",\n    \"tanggal_lahir\": \"1962-04-16\",\n    \"jenis_kelamin\": \"Laki-laki\",\n    \"alamat_pasien\": \"Jl. Teuku Umar blok F-20, Tegal Besar\",\n    \"tanggal_masuk\": \"2025-12-25\",\n    \"tanggal_keluar\": \"2025-12-27\",\n    \"lama_dirawat\": \"2\",\n    \"alasan_mrs\": \"Nyeri dada\",\n    \"diagnosis\": \"CH\",\n    \"dokter_dpjp\": \"dr. Suryono, Sp.JP\",\n    \"keterangan\": null,\n    \"kasus_id\": 1,\n    \"wali_nama\": \"Meliyana\",\n    \"wali_hubungan\": \"Istri\"\n}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember - Jawa Timur\",\"nomor_rm\":\"265207\",\"nama_pasien\":\"SUYITNO\",\"tanggal_lahir\":\"1962-04-16\",\"jenis_kelamin\":\"Laki-laki\",\"alamat_pasien\":\"Jl. Teuku Umar blok F-20, Tegal Besar\",\"tanggal_masuk\":\"2025-12-25\",\"tanggal_keluar\":\"2025-12-27\",\"lama_dirawat\":\"2\",\"alasan_mrs\":\"Nyeri dada\",\"diagnosis\":\"CH\",\"dokter_dpjp\":\"dr. Suryono, Sp.JP\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":\"Meliyana\",\"wali_hubungan\":\"Istri\"}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember - Jawa Timur\",\"nomor_rm\":\"265207\",\"nama_pasien\":\"SUYITNO\",\"tanggal_lahir\":\"1962-04-16\",\"jenis_kelamin\":\"Laki-laki\",\"alamat_pasien\":\"Jl. Teuku Umar blok F-20, Tegal Besar\",\"tanggal_masuk\":\"2025-12-25\",\"tanggal_keluar\":\"2025-12-27\",\"lama_dirawat\":\"2\",\"alasan_mrs\":\"Nyeri dada\",\"diagnosis\":\"CH\",\"dokter_dpjp\":\"dr. Suryono, Sp.JP\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":\"Meliyana\",\"wali_hubungan\":\"Istri\"}', 'gemini', NULL, 'validated', '2026-06-23 22:02:28', '2026-06-24 05:02:28', '2026-06-23 22:02:28'),
(3, 3, '{\n    \"nama_rs\": \"RUMAH SAKIT UMUM KALIWATES\",\n    \"alamat_rs\": \"Jl. Diah Pitaloka No. 4A Jember\",\n    \"nomor_rm\": \"167684\",\n    \"nama_pasien\": \"ERMA TRI M\",\n    \"tanggal_lahir\": \"1973-10-03\",\n    \"jenis_kelamin\": \"Perempuan\",\n    \"alamat_pasien\": \"Jl Kalingt Bromo\",\n    \"tanggal_masuk\": \"2020-11-15\",\n    \"tanggal_keluar\": \"2020-11-17\",\n    \"lama_dirawat\": \"2\",\n    \"alasan_mrs\": \"Nyeri dada \\/ Rujukan dari dr Suryono SpJP\",\n    \"diagnosis\": \"ASMA\",\n    \"dokter_dpjp\": \"dr. EKA DINA INDRIANI, SpOG, M.Kes \\/ dr. Suryono Sp.JP, FIHA\",\n    \"keterangan\": null,\n    \"kasus_id\": 1,\n    \"wali_nama\": null,\n    \"wali_hubungan\": null\n}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember\",\"nomor_rm\":\"167684\",\"nama_pasien\":\"ERMA TRI M\",\"tanggal_lahir\":\"1973-10-03\",\"jenis_kelamin\":\"Perempuan\",\"alamat_pasien\":\"Jl Kalingt Bromo\",\"tanggal_masuk\":\"2020-11-15\",\"tanggal_keluar\":\"2020-11-17\",\"lama_dirawat\":\"2\",\"alasan_mrs\":\"Nyeri dada \\/ Rujukan dari dr Suryono SpJP\",\"diagnosis\":\"ASMA\",\"dokter_dpjp\":\"dr. EKA DINA INDRIANI, SpOG, M.Kes \\/ dr. Suryono Sp.JP, FIHA\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":null,\"wali_hubungan\":null}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember\",\"nomor_rm\":\"167684\",\"nama_pasien\":\"ERMA TRI M\",\"tanggal_lahir\":\"1973-10-03\",\"jenis_kelamin\":\"Perempuan\",\"alamat_pasien\":\"Jl Kalingt Bromo\",\"tanggal_masuk\":\"2020-11-15\",\"tanggal_keluar\":\"2020-11-17\",\"lama_dirawat\":\"2\",\"alasan_mrs\":\"Nyeri dada \\/ Rujukan dari dr Suryono SpJP\",\"diagnosis\":\"ASMA\",\"dokter_dpjp\":\"dr. EKA DINA INDRIANI, SpOG, M.Kes \\/ dr. Suryono Sp.JP, FIHA\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":null,\"wali_hubungan\":null}', 'gemini', NULL, 'validated', '2026-06-23 23:27:21', '2026-06-24 06:27:21', '2026-06-23 23:27:21'),
(4, 4, '{\n    \"nama_rs\": \"RUMAH SAKIT UMUM KALIWATES\",\n    \"alamat_rs\": \"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\n    \"nomor_rm\": \"123456\",\n    \"nama_pasien\": \"Suyati\",\n    \"tanggal_lahir\": \"1967-03-02\",\n    \"jenis_kelamin\": \"Perempuan\",\n    \"alamat_pasien\": \"Togalroso Sabrang\",\n    \"tanggal_masuk\": \"2019-04-19\",\n    \"tanggal_keluar\": \"2019-04-23\",\n    \"lama_dirawat\": \"4\",\n    \"alasan_mrs\": null,\n    \"diagnosis\": \"HT, Hemiparesis\",\n    \"dokter_dpjp\": \"dr. H. Raben, Sp.PD\",\n    \"keterangan\": null,\n    \"kasus_id\": 1,\n    \"wali_nama\": null,\n    \"wali_hubungan\": null\n}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\"nomor_rm\":\"123456\",\"nama_pasien\":\"Suyati\",\"tanggal_lahir\":\"1967-03-02\",\"jenis_kelamin\":\"Perempuan\",\"alamat_pasien\":\"Togalroso Sabrang\",\"tanggal_masuk\":\"2019-04-19\",\"tanggal_keluar\":\"2019-04-23\",\"lama_dirawat\":\"4\",\"alasan_mrs\":null,\"diagnosis\":\"HT, Hemiparesis\",\"dokter_dpjp\":\"dr. H. Raben, Sp.PD\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":null,\"wali_hubungan\":null}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\"nomor_rm\":\"123456\",\"nama_pasien\":\"Suyati\",\"tanggal_lahir\":\"1967-03-02\",\"jenis_kelamin\":\"Perempuan\",\"alamat_pasien\":\"Togalroso Sabrang\",\"tanggal_masuk\":\"2019-04-19\",\"tanggal_keluar\":\"2019-04-23\",\"lama_dirawat\":\"4\",\"alasan_mrs\":null,\"diagnosis\":\"HT, Hemiparesis\",\"dokter_dpjp\":\"dr. H. Raben, Sp.PD\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":null,\"wali_hubungan\":null}', 'gemini', NULL, 'validated', '2026-06-24 23:57:16', '2026-06-25 06:57:16', '2026-06-24 23:57:16'),
(5, 5, '{\n    \"nama_rs\": \"RUMAH SAKIT UMUM KALIWATES\",\n    \"alamat_rs\": \"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\n    \"nomor_rm\": \"209773\",\n    \"nama_pasien\": \"By Ny. Olivia Chrisanti Tardianto\",\n    \"tanggal_lahir\": \"2023-02-17\",\n    \"jenis_kelamin\": \"Laki-laki\",\n    \"alamat_pasien\": \"Jalan Kota Blater no 5-7 pontang Ambulu\",\n    \"tanggal_masuk\": \"2023-02-17\",\n    \"tanggal_keluar\": \"2023-02-18\",\n    \"lama_dirawat\": \"2\",\n    \"alasan_mrs\": \"BBL SC\",\n    \"diagnosis\": \"NA, SC, BBLC\",\n    \"dokter_dpjp\": \"dr. Debora Sp.A\",\n    \"keterangan\": null,\n    \"kasus_id\": 1,\n    \"wali_nama\": \"Hendry Kurniawan\",\n    \"wali_hubungan\": \"Ayah\"\n}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\"nomor_rm\":\"209773\",\"nama_pasien\":\"By Ny. Olivia Chrisanti Tardianto\",\"tanggal_lahir\":\"2023-02-17\",\"jenis_kelamin\":\"Laki-laki\",\"alamat_pasien\":\"Jalan Kota Blater no 5-7 pontang Ambulu\",\"tanggal_masuk\":\"2023-02-17\",\"tanggal_keluar\":\"2023-02-18\",\"lama_dirawat\":\"2\",\"alasan_mrs\":\"BBL SC\",\"diagnosis\":\"NA, SC, BBLC\",\"dokter_dpjp\":\"dr. Debora Sp.A\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":\"Hendry Kurniawan\",\"wali_hubungan\":\"Ayah\"}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember Jawa Timur\",\"nomor_rm\":\"209773\",\"nama_pasien\":\"By Ny. Olivia Chrisanti Tardianto\",\"tanggal_lahir\":\"2023-02-17\",\"jenis_kelamin\":\"Laki-laki\",\"alamat_pasien\":\"Jalan Kota Blater no 5-7 pontang Ambulu\",\"tanggal_masuk\":\"2023-02-17\",\"tanggal_keluar\":\"2023-02-18\",\"lama_dirawat\":\"2\",\"alasan_mrs\":\"BBL SC\",\"diagnosis\":\"NA, SC, BBLC\",\"dokter_dpjp\":\"dr. Debora Sp.A\",\"keterangan\":null,\"kasus_id\":1,\"wali_nama\":\"Hendry Kurniawan\",\"wali_hubungan\":\"Ayah\"}', 'gemini', NULL, 'validated', '2026-06-25 03:54:10', '2026-06-25 10:54:10', '2026-06-25 03:54:10'),
(6, 6, '{\n    \"nama_rs\": \"RUMAH SAKIT UMUM KALIWATES\",\n    \"alamat_rs\": \"Jl. Diah Pitaloka No. 4A Jember - Jawa Timur\",\n    \"nomor_rm\": \"207165\",\n    \"nama_pasien\": \"MUHAMMAD NURLIZA\",\n    \"tanggal_lahir\": \"1974-07-16\",\n    \"jenis_kelamin\": \"Laki-laki\",\n    \"alamat_pasien\": \"Bondowoso\",\n    \"tanggal_masuk\": \"2015-12-28\",\n    \"tanggal_keluar\": \"2025-12-13\",\n    \"lama_dirawat\": \"3638 hari\",\n    \"alasan_mrs\": \"Nyeri ulu hati\",\n    \"diagnosis\": \"Dyspepsia\",\n    \"dokter_dpjp\": \"dr. Findy, Sp.PD\",\n    \"keterangan\": null,\n    \"kasus_id\": null,\n    \"wali_nama\": \"Yuli\",\n    \"wali_hubungan\": \"Istri\"\n}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember - Jawa Timur\",\"nomor_rm\":\"207165\",\"nama_pasien\":\"MUHAMMAD NURLIZA\",\"tanggal_lahir\":\"1974-07-16\",\"jenis_kelamin\":\"Laki-laki\",\"alamat_pasien\":\"Bondowoso\",\"tanggal_masuk\":\"2015-12-28\",\"tanggal_keluar\":\"2025-12-13\",\"lama_dirawat\":\"3638 hari\",\"alasan_mrs\":\"Nyeri ulu hati\",\"diagnosis\":\"Dyspepsia\",\"dokter_dpjp\":\"dr. Findy, Sp.PD\",\"keterangan\":null,\"kasus_id\":null,\"wali_nama\":\"Yuli\",\"wali_hubungan\":\"Istri\"}', '{\"nama_rs\":\"RUMAH SAKIT UMUM KALIWATES\",\"alamat_rs\":\"Jl. Diah Pitaloka No. 4A Jember - Jawa Timur\",\"nomor_rm\":\"207165\",\"nama_pasien\":\"MUHAMMAD NURLIZA\",\"tanggal_lahir\":\"1974-07-16\",\"jenis_kelamin\":\"Laki-laki\",\"alamat_pasien\":\"Bondowoso\",\"tanggal_masuk\":\"2015-12-28\",\"tanggal_keluar\":\"2025-12-13\",\"lama_dirawat\":\"3638 hari\",\"alasan_mrs\":\"Nyeri ulu hati\",\"diagnosis\":\"Dyspepsia\",\"dokter_dpjp\":\"dr. Findy, Sp.PD\",\"keterangan\":null,\"kasus_id\":null,\"wali_nama\":\"Yuli\",\"wali_hubungan\":\"Istri\"}', 'gemini', NULL, 'validated', '2026-06-26 04:32:55', '2026-06-26 11:32:55', '2026-06-26 04:32:55');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `no_rm` varchar(20) NOT NULL,
  `nama_pasien` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `status_rm` enum('Aktif','Inaktif') DEFAULT 'Aktif',
  `kasus_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`no_rm`, `nama_pasien`, `jenis_kelamin`, `tanggal_lahir`, `tempat_lahir`, `alamat`, `no_telepon`, `status_rm`, `kasus_id`, `created_at`, `updated_at`) VALUES
('123456', 'Suyati', 'Perempuan', '1967-03-02', NULL, NULL, NULL, 'Aktif', 1, '2026-06-24 23:57:16', '2026-06-24 23:57:16'),
('167684', 'ERMA TRI M', 'Perempuan', '1973-10-03', NULL, NULL, NULL, 'Aktif', 1, '2026-06-23 23:27:21', '2026-06-23 23:27:21'),
('207165', 'MUHAMMAD NURLIZA', 'Laki-laki', '1974-07-16', NULL, NULL, NULL, 'Aktif', 1, '2026-06-26 04:27:24', '2026-06-26 04:27:24'),
('209773', 'By Ny. Olivia Chrisanti Tardianto', 'Laki-laki', '2023-02-17', NULL, NULL, NULL, 'Aktif', 1, '2026-06-25 03:54:10', '2026-06-25 03:54:10'),
('242869', 'Sunarso', 'Laki-laki', '1949-05-11', NULL, NULL, NULL, 'Aktif', 1, '2026-06-23 09:09:06', '2026-06-23 09:09:06'),
('265207', 'SUYITNO', 'Laki-laki', '1962-04-16', NULL, NULL, NULL, 'Aktif', 1, '2026-06-23 22:02:28', '2026-06-23 22:02:28');

-- --------------------------------------------------------

--
-- Table structure for table `retensi`
--

CREATE TABLE `retensi` (
  `id` int(11) NOT NULL,
  `no_rm` varchar(20) DEFAULT NULL,
  `pasien_id` int(11) DEFAULT NULL,
  `kasus_id` int(11) DEFAULT NULL,
  `jenis_kasus_id` int(11) DEFAULT NULL,
  `jenis_layanan` enum('RJ','RI') DEFAULT NULL,
  `tanggal_kunjungan_terakhir` date DEFAULT NULL,
  `masa_aktif` int(11) DEFAULT NULL,
  `masa_inaktif` int(11) DEFAULT NULL,
  `selisih_tahun` int(11) DEFAULT NULL,
  `status` enum('Aktif','Inaktif','Siap Dimusnahkan','Dimusnahkan') NOT NULL DEFAULT 'Aktif',
  `tanggal_proses` datetime DEFAULT NULL,
  `tanggal_batas_aktif` datetime DEFAULT NULL,
  `tanggal_batas_musnah` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retensi`
--

INSERT INTO `retensi` (`id`, `no_rm`, `pasien_id`, `kasus_id`, `jenis_kasus_id`, `jenis_layanan`, `tanggal_kunjungan_terakhir`, `masa_aktif`, `masa_inaktif`, `selisih_tahun`, `status`, `tanggal_proses`, `tanggal_batas_aktif`, `tanggal_batas_musnah`, `created_at`, `updated_at`) VALUES
(1, '242869', NULL, 1, 1, NULL, '2024-06-09', 5, 2, NULL, 'Aktif', '2026-06-23 16:19:55', '2029-06-08 00:00:00', '2031-06-08 00:00:00', '2026-06-23 09:09:06', '2026-06-23 09:19:55'),
(2, '265207', NULL, 1, 1, NULL, '2025-12-25', 5, 2, NULL, 'Aktif', '2026-06-24 05:02:28', '2030-12-24 00:00:00', '2032-12-23 00:00:00', '2026-06-23 22:02:28', '2026-06-23 22:02:28'),
(3, '167684', NULL, 1, 1, NULL, '2016-11-15', 5, 2, NULL, 'Inaktif', '2026-06-24 06:27:21', '2021-11-15 00:00:00', '2023-11-15 00:00:00', '2026-06-23 23:27:21', '2026-06-24 01:08:26'),
(4, '123456', NULL, 1, 1, NULL, '2019-04-19', 5, 2, NULL, 'Siap Dimusnahkan', '2026-06-25 06:57:16', '2024-04-17 00:00:00', '2026-04-17 00:00:00', '2026-06-24 23:57:16', '2026-06-24 23:57:16'),
(5, '209773', NULL, 1, 1, NULL, '2023-02-17', 5, 2, NULL, 'Aktif', '2026-06-25 10:54:10', '2028-02-16 00:00:00', '2030-02-15 00:00:00', '2026-06-25 03:54:10', '2026-06-25 03:54:10'),
(6, '207165', NULL, NULL, 1, NULL, '2015-12-28', 5, 2, NULL, 'Siap Dimusnahkan', '2026-06-26 11:32:55', '2020-12-26 00:00:00', '2022-12-26 00:00:00', '2026-06-26 04:32:55', '2026-06-26 04:32:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('Administrator','Staff') DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `status`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$12$XhXHJv5hAA6QP8zjOwQqiePNMw0UBKhHZC6MRHhxBCby5RXm5D4U.', 'Administrator', 'admin@mail.com', 'Administrator', 'Aktif', '2026-06-28 04:10:27', '2026-03-20 01:28:08'),
(2, 'staff', '$2y$12$1pteOudNDnfZCq1n6xreKe7ReWp3VhJr1n3MUHamcvklQMfzQA9Xi', 'Staff User', 'staff@mail.com', 'Staff', 'Aktif', '2026-06-23 15:12:58', '2026-03-20 01:28:08'),
(3, 'berlian', '$2y$12$ZCJKrLYJ/P1FHxbv2xtBVOU9AR2csmVe4BOQ4jqmSamd3.5oc/o0e', 'berlian', 'berliana@gmail.com', 'Staff', 'Aktif', '2026-06-27 10:25:07', '2026-06-23 08:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `validasi_data`
--

CREATE TABLE `validasi_data` (
  `id` int(11) NOT NULL,
  `dokumen_id` int(11) DEFAULT NULL,
  `no_rm` varchar(20) DEFAULT NULL,
  `nama_pasien` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `jenis_kelamin` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `dokter` varchar(100) DEFAULT NULL,
  `kasus_id` int(11) DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `validasi_data`
--

INSERT INTO `validasi_data` (`id`, `dokumen_id`, `no_rm`, `nama_pasien`, `tanggal_lahir`, `tempat_lahir`, `jenis_kelamin`, `alamat`, `no_telepon`, `tanggal_masuk`, `tanggal_keluar`, `diagnosa`, `dokter`, `kasus_id`, `verified_by`, `created_at`) VALUES
(1, 1, '242869', 'Sunarso', '1949-05-11', NULL, 'L', 'Dusun Krajan Barat RT.3/RW.7 Suko Jelbuk Jember', NULL, '2024-06-09', '2024-06-11', 'Benjolan paha kiri', 'dr. Ketut Sp.B', 1, 3, '2026-06-23 16:09:06'),
(2, 2, '265207', 'SUYITNO', '1962-04-16', NULL, 'Laki-laki', 'Jl. Teuku Umar blok F-20, Tegal Besar', NULL, '2025-12-25', '2025-12-27', 'CH', 'dr. Suryono, Sp.JP', 1, 3, '2026-06-24 05:02:28'),
(3, 3, '167684', 'ERMA TRI M', '1973-10-03', NULL, 'Perempuan', 'Jl Kalingt Bromo', NULL, '2020-11-15', '2020-11-17', 'ASMA', 'dr. EKA DINA INDRIANI, SpOG, M.Kes / dr. Suryono Sp.JP, FIHA', 1, 3, '2026-06-24 06:27:21'),
(4, 4, '123456', 'Suyati', '1967-03-02', NULL, 'Perempuan', 'Togalroso Sabrang', NULL, '2019-04-19', '2019-04-23', 'HT, Hemiparesis', 'dr. H. Raben, Sp.PD', 1, 1, '2026-06-25 06:57:16'),
(5, 5, '209773', 'By Ny. Olivia Chrisanti Tardianto', '2023-02-17', NULL, 'Laki-laki', 'Jalan Kota Blater no 5-7 pontang Ambulu', NULL, '2023-02-17', '2023-02-18', 'NA, SC, BBLC', 'dr. Debora Sp.A', 1, 1, '2026-06-25 10:54:10'),
(6, 6, '207165', 'MUHAMMAD NURLIZA', '1974-07-16', NULL, 'Laki-laki', 'Bondowoso', NULL, '2015-12-28', '2025-12-13', 'Dyspepsia', 'dr. Findy, Sp.PD', NULL, 1, '2026-06-26 11:32:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_settings_key_unique` (`key`);

--
-- Indexes for table `berita_acara_pemusnahan`
--
ALTER TABLE `berita_acara_pemusnahan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pemusnahan` (`id_pemusnahan`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `daftar_pemusnahan`
--
ALTER TABLE `daftar_pemusnahan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_rm` (`no_rm`),
  ADD KEY `daftar_pemusnahan_destroyed_by_foreign` (`destroyed_by`);

--
-- Indexes for table `dokumen_rekam_medis`
--
ALTER TABLE `dokumen_rekam_medis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_rm` (`no_rm`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kasus_master`
--
ALTER TABLE `kasus_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD PRIMARY KEY (`id_kunjungan`),
  ADD KEY `no_rm` (`no_rm`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ocr_result`
--
ALTER TABLE `ocr_result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dokumen_id` (`dokumen_id`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`no_rm`);

--
-- Indexes for table `retensi`
--
ALTER TABLE `retensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_rm` (`no_rm`),
  ADD KEY `jenis_kasus_id` (`jenis_kasus_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `validasi_data`
--
ALTER TABLE `validasi_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `validasi_data_kasus_id_foreign` (`kasus_id`),
  ADD KEY `validasi_data_dokumen_id_foreign` (`dokumen_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `berita_acara_pemusnahan`
--
ALTER TABLE `berita_acara_pemusnahan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daftar_pemusnahan`
--
ALTER TABLE `daftar_pemusnahan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dokumen_rekam_medis`
--
ALTER TABLE `dokumen_rekam_medis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kasus_master`
--
ALTER TABLE `kasus_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kunjungan`
--
ALTER TABLE `kunjungan`
  MODIFY `id_kunjungan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ocr_result`
--
ALTER TABLE `ocr_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `retensi`
--
ALTER TABLE `retensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `validasi_data`
--
ALTER TABLE `validasi_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `berita_acara_pemusnahan`
--
ALTER TABLE `berita_acara_pemusnahan`
  ADD CONSTRAINT `berita_acara_pemusnahan_ibfk_1` FOREIGN KEY (`id_pemusnahan`) REFERENCES `daftar_pemusnahan` (`id`);

--
-- Constraints for table `daftar_pemusnahan`
--
ALTER TABLE `daftar_pemusnahan`
  ADD CONSTRAINT `daftar_pemusnahan_destroyed_by_foreign` FOREIGN KEY (`destroyed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `daftar_pemusnahan_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`);

--
-- Constraints for table `dokumen_rekam_medis`
--
ALTER TABLE `dokumen_rekam_medis`
  ADD CONSTRAINT `dokumen_rekam_medis_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD CONSTRAINT `kunjungan_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ocr_result`
--
ALTER TABLE `ocr_result`
  ADD CONSTRAINT `ocr_result_ibfk_1` FOREIGN KEY (`dokumen_id`) REFERENCES `dokumen_rekam_medis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `retensi`
--
ALTER TABLE `retensi`
  ADD CONSTRAINT `retensi_ibfk_1` FOREIGN KEY (`no_rm`) REFERENCES `pasien` (`no_rm`),
  ADD CONSTRAINT `retensi_ibfk_2` FOREIGN KEY (`jenis_kasus_id`) REFERENCES `kasus_master` (`id`);

--
-- Constraints for table `validasi_data`
--
ALTER TABLE `validasi_data`
  ADD CONSTRAINT `validasi_data_dokumen_id_foreign` FOREIGN KEY (`dokumen_id`) REFERENCES `dokumen_rekam_medis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `validasi_data_kasus_id_foreign` FOREIGN KEY (`kasus_id`) REFERENCES `kasus_master` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

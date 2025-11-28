-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2025 at 07:43 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_management_task_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Pekerjaan', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(2, 1, 'Pribadi', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(3, 2, 'Pekerjaan', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(4, 2, 'Pribadi', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(5, 3, 'Pekerjaan', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(6, 3, 'Pribadi', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(7, 4, 'Pekerjaan', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(8, 4, 'Pribadi', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(9, 5, 'Pekerjaan', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(10, 5, 'Pribadi', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(11, 6, 'Pekerjaan', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(12, 6, 'Pribadi', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(13, 7, 'Pekerjaan', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(14, 7, 'Pribadi', '2025-11-27 19:32:38', '2025-11-27 19:32:38');

-- --------------------------------------------------------

--
-- Table structure for table `phone_verifications`
--

CREATE TABLE `phone_verifications` (
  `id` bigint UNSIGNED NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `token` varchar(10) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `deadline` date NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `priority`, `deadline`, `status`, `created_at`, `updated_at`) VALUES
(48, 1, 1, 'Meeting Proyek A', 'Diskusi requirement dengan klien', 'high', '2025-11-25', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(49, 1, 1, 'Fix Bug Login', 'Memperbaiki error saat user login', 'high', '2025-11-26', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(50, 1, 2, 'Bayar Tagihan Listrik', 'Bayar sebelum tanggal 20', 'medium', '2025-11-20', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(51, 1, 1, 'Deploy ke Server', 'Deploy update terbaru ke production', 'high', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(52, 1, 2, 'Olahraga Pagi', 'Jogging keliling komplek', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(53, 1, 1, 'Buat Laporan Bulanan', 'Rekap data bulan November', 'medium', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(54, 1, 2, 'Servis Motor', 'Ganti oli dan cek rem', 'medium', '2025-12-01', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(55, 1, 1, 'Update Dokumentasi API', 'Menambahkan endpoint baru', 'low', '2025-12-02', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(56, 1, 2, 'Beli Kado Ulang Tahun', 'Untuk teman kantor', 'low', '2025-12-05', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(57, 1, 1, 'Review Code Junior', 'Review PR di GitHub', 'medium', '2025-11-27', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(58, 1, 2, 'Bersihkan Kamar', 'Weekend cleaning', 'low', '2025-12-03', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(59, 1, 1, 'Backup Database', 'Backup rutin mingguan', 'high', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(60, 2, 3, 'Desain UI Landing Page', 'Buat mockup di Figma', 'high', '2025-11-26', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(61, 2, 3, 'Revisi Logo', 'Sesuai feedback klien', 'medium', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(62, 2, 4, 'Belanja Bulanan', 'Beli kebutuhan dapur', 'high', '2025-11-25', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(63, 2, 3, 'Meeting Tim Desain', 'Brainstorming ide baru', 'medium', '2025-11-27', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(64, 2, 4, 'Nonton Bioskop', 'Refreshing weekend', 'low', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(65, 2, 3, 'Export Aset Grafis', 'Siapkan aset untuk developer', 'high', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(66, 2, 4, 'Perpanjang STNK', 'Ke Samsat terdekat', 'medium', '2025-12-01', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(67, 2, 3, 'Update Portofolio', 'Upload karya terbaru ke Behance', 'low', '2025-12-05', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(68, 2, 4, 'Masak Makan Malam', 'Menu spesial', 'low', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(69, 2, 3, 'Cek Email Klien', 'Balas inquiry yang masuk', 'medium', '2025-11-26', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(70, 2, 4, 'Yoga', 'Latihan rutin sore', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(71, 2, 3, 'Finalisasi Proposal', 'Kirim penawaran harga', 'high', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(72, 3, 5, 'Analisis Data Penjualan', 'Gunakan Excel atau Python', 'high', '2025-11-27', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(73, 3, 5, 'Presentasi Q3', 'Siapkan slide PowerPoint', 'high', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(74, 3, 6, 'Main Futsal', 'Jadwal rutin jumat malam', 'medium', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(75, 3, 5, 'Meeting Vendor', 'Negosiasi harga', 'medium', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(76, 3, 6, 'Cuci Sepatu', 'Sudah kotor', 'low', '2025-12-01', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(77, 3, 5, 'Cek Inventaris', 'Stok opname gudang', 'high', '2025-12-02', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(78, 3, 6, 'Makan Siang Bareng', 'Reuni teman SMA', 'low', '2025-12-03', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(79, 3, 5, 'Kirim Invoice', 'Tagihan ke customer A', 'medium', '2025-11-25', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(80, 3, 6, 'Beli Obat', 'Vitamin C dan Flu', 'high', '2025-11-26', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(81, 3, 5, 'Training Staff Baru', 'Materi pengenalan produk', 'medium', '2025-12-05', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(82, 3, 6, 'Download Film', 'Untuk ditonton weekend', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(83, 3, 5, 'Rekonsiliasi Bank', 'Cek mutasi rekening', 'high', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(84, 4, 7, 'Testing Aplikasi Mobile', 'UAT di device Android', 'high', '2025-11-26', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(85, 4, 7, 'Buat Test Case', 'Skenario positif dan negatif', 'medium', '2025-11-27', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(86, 4, 8, 'Ganti Oli Mobil', 'Ke bengkel langganan', 'medium', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(87, 4, 7, 'Report Bug Jira', 'Input temuan bug hari ini', 'high', '2025-11-25', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(88, 4, 8, 'Beli Tiket Kereta', 'Untuk mudik', 'high', '2025-12-01', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(89, 4, 7, 'Automated Testing', 'Setup script Selenium', 'medium', '2025-12-02', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(90, 4, 8, 'Bayar Internet', 'Indihome bulan ini', 'high', '2025-11-20', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(91, 4, 7, 'Meeting Developer', 'Bahas hasil testing', 'medium', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(92, 4, 8, 'Cari Kado Pernikahan', 'Untuk undangan minggu depan', 'low', '2025-12-04', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(93, 4, 7, 'Dokumentasi Bug', 'Buat PDF report', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(94, 4, 8, 'Lari Pagi', 'Minggu pagi di CFD', 'low', '2025-12-03', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(95, 4, 7, 'Retest Bug Fixes', 'Verifikasi perbaikan', 'high', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(96, 5, 9, 'Setup Server Baru', 'Install Ubuntu dan Nginx', 'high', '2025-11-26', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(97, 5, 9, 'Konfigurasi Firewall', 'Setup UFW rules', 'high', '2025-11-27', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(98, 5, 10, 'Beli Makanan Kucing', 'Stok habis', 'medium', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(99, 5, 9, 'Monitoring Server', 'Cek load CPU dan RAM', 'medium', '2025-11-25', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(100, 5, 10, 'Service AC', 'Panggil tukang service', 'low', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(101, 5, 9, 'Update SSL Certificate', 'Renew Let\'s Encrypt', 'high', '2025-12-01', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(102, 5, 10, 'Bayar BPJS', 'Untuk keluarga', 'high', '2025-12-05', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(103, 5, 9, 'Optimasi Database', 'Tuning query lambat', 'medium', '2025-12-02', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(104, 5, 10, 'Potong Rambut', 'Barbershop langganan', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(105, 5, 9, 'Meeting IT Support', 'Evaluasi tiket masuk', 'low', '2025-12-03', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(106, 5, 10, 'Cuci Mobil', 'Sudah berdebu', 'low', '2025-12-04', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(107, 5, 9, 'Backup Data Offsite', 'Upload ke S3', 'high', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(108, 6, 11, 'Buat Content Plan', 'Untuk sosmed bulan depan', 'high', '2025-11-27', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(109, 6, 11, 'Copywriting Iklan', 'Buat caption Facebook Ads', 'medium', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(110, 6, 12, 'Arisan Keluarga', 'Siapkan makanan', 'medium', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(111, 6, 11, 'Reply Comment Netizen', 'Engagement di Instagram', 'low', '2025-11-25', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(112, 6, 12, 'Beli Skincare', 'Cek diskon tanggal kembar', 'high', '2025-12-01', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(113, 6, 11, 'Analisis Insight IG', 'Cek performa postingan', 'high', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(114, 6, 12, 'Bayar Air PDAM', 'Sebelum kena denda', 'high', '2025-11-20', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(115, 6, 11, 'Take Video TikTok', 'Konten challenge baru', 'medium', '2025-12-02', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(116, 6, 12, 'Jalan-jalan sore', 'Ke taman kota', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(117, 6, 11, 'Meeting Marketing', 'Bahas strategi Q4', 'high', '2025-11-26', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(118, 6, 12, 'Baca Buku Novel', 'Selesaikan bab 5', 'low', '2025-12-05', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(119, 6, 11, 'Edit Video Reels', 'Pakai CapCut', 'medium', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(120, 7, 13, 'Audit Kode', 'Cek celah keamanan', 'high', '2025-11-28', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(121, 7, 13, 'Implementasi OAuth', 'Login with Google', 'high', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(122, 7, 14, 'Daftar Gym', 'Cari promo member baru', 'medium', '2025-12-01', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(123, 7, 13, 'Refactor Controller', 'Bersihkan kode duplikat', 'medium', '2025-11-26', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(124, 7, 14, 'Beli Sepatu Lari', 'Cari review online', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(125, 7, 13, 'Setup CI/CD Pipeline', 'Automatisasi deployment', 'high', '2025-12-03', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(126, 7, 14, 'Perbaiki Genteng Bocor', 'Panggil tukang', 'high', '2025-11-25', 'completed', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(127, 7, 13, 'Tulis Unit Test', 'Coverage minimal 80%', 'medium', '2025-12-02', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(128, 7, 14, 'Rencana Liburan', 'Booking hotel', 'low', '2025-12-10', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(129, 7, 13, 'Diskusi Fitur Baru', 'Dengan Product Manager', 'medium', '2025-11-27', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(130, 7, 14, 'Belajar Masak', 'Resep nasi goreng', 'low', '2025-11-30', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(131, 7, 13, 'Update Dependency', 'Composer update', 'low', '2025-11-29', 'pending', '2025-11-27 19:32:38', '2025-11-27 19:32:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_wa_notification_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone_number`, `password`, `is_wa_notification_active`, `created_at`, `updated_at`) VALUES
(1, 'abdillah', '+6287715555066', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(2, 'auliya', '+6281934598163', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(3, 'rifki', '+6287853338254', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(4, 'vemas', '+6287863409801', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(5, 'anam', '+6282228900559', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(6, 'revika', '+6287726611970', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-27 19:32:38', '2025-11-27 19:32:38'),
(7, 'rizkyan', '+6283134862672', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-27 19:32:38', '2025-11-27 19:32:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categories_user_id` (`user_id`);

--
-- Indexes for table `phone_verifications`
--
ALTER TABLE `phone_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tasks_user_id` (`user_id`),
  ADD KEY `fk_tasks_category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `phone_verifications`
--
ALTER TABLE `phone_verifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tasks_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

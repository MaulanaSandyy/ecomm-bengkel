-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Bulan Mei 2026 pada 04.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bengkel_mobil`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jasa_id` int(11) DEFAULT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_booking` time NOT NULL,
  `keluhan` text DEFAULT NULL,
  `status` enum('pending','dikonfirmasi','selesai','batal') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `jasa_id`, `tanggal_booking`, `jam_booking`, `keluhan`, `status`, `created_at`) VALUES
(1, 4, 3, '2026-03-31', '10:00:00', 'saya lagi dijalan ', 'selesai', '2026-03-25 05:39:39'),
(2, 4, 2, '2026-03-30', '11:00:00', 'gak tau', 'selesai', '2026-03-25 06:49:36'),
(3, 5, 2, '2026-04-16', '09:00:00', 'pengen tuneup aja', 'selesai', '2026-04-06 08:19:18'),
(4, 4, 4, '2026-04-09', '10:00:00', 'a', 'selesai', '2026-04-08 03:57:30'),
(5, 4, 3, '2026-04-23', '11:00:00', 'ber', 'dikonfirmasi', '2026-04-10 05:37:01'),
(6, 4, 4, '2026-04-30', '10:00:00', 'ga ada', 'selesai', '2026-04-28 21:15:07'),
(7, 6, 5, '2026-04-30', '11:00:00', 'mau tuning mesin biar gacor', 'selesai', '2026-04-28 21:28:58'),
(8, 4, 5, '2026-05-02', '16:00:00', 'super full mesin', 'batal', '2026-04-30 08:06:11'),
(9, 4, 6, '2026-05-14', '16:00:00', 'full power mesin', 'selesai', '2026-04-30 08:07:15'),
(10, 7, 1, '2026-03-15', '09:00:00', 'Service rutin berkala', 'selesai', '2026-03-10 03:00:00'),
(11, 8, 2, '2026-03-16', '10:00:00', 'Mesin kurang bertenaga', 'selesai', '2026-03-11 04:00:00'),
(12, 9, 3, '2026-03-17', '11:00:00', 'AC tidak dingin', 'selesai', '2026-03-12 05:00:00'),
(13, 10, 1, '2026-03-18', '13:00:00', 'Ganti oli mesin', 'selesai', '2026-03-13 01:00:00'),
(14, 11, 4, '2026-03-19', '09:00:00', 'Balance dan spooring roda', 'selesai', '2026-03-14 02:00:00'),
(15, 12, 5, '2026-03-20', '10:00:00', 'Mesin overheating', 'selesai', '2026-03-15 03:00:00'),
(16, 13, 6, '2026-03-21', '11:00:00', 'Perawatan AC mobil', 'selesai', '2026-03-16 04:00:00'),
(17, 14, 2, '2026-03-22', '13:00:00', 'Tune up mesin', 'selesai', '2026-03-17 06:00:00'),
(18, 15, 1, '2026-03-23', '09:00:00', 'Service berkala', 'selesai', '2026-03-18 02:00:00'),
(19, 16, 3, '2026-03-24', '10:00:00', 'AC kurang dingin', 'selesai', '2026-03-19 03:00:00'),
(20, 17, 4, '2026-03-25', '11:00:00', 'Ganti oli dan filter', 'selesai', '2026-03-20 04:00:00'),
(21, 18, 5, '2026-03-26', '13:00:00', 'Ban vibrasi saat kecepatan tinggi', 'selesai', '2026-03-21 06:00:00'),
(22, 19, 6, '2026-03-27', '09:00:00', 'Mesin Mogok', 'selesai', '2026-03-22 02:00:00'),
(23, 20, 1, '2026-03-28', '10:00:00', 'Service rutin berkala', 'selesai', '2026-03-23 03:00:00'),
(24, 21, 2, '2026-03-29', '11:00:00', 'Tune up mesin', 'selesai', '2026-03-24 04:00:00'),
(25, 22, 3, '2026-03-30', '13:00:00', 'AC bau tidak sedap', 'selesai', '2026-03-25 06:00:00'),
(26, 23, 4, '2026-04-01', '09:00:00', 'Ganti oli mesin', 'selesai', '2026-03-26 02:00:00'),
(27, 24, 5, '2026-04-02', '10:00:00', 'Wheel alignment', 'selesai', '2026-03-27 03:00:00'),
(28, 25, 6, '2026-04-03', '11:00:00', 'Overhaul mesin', 'selesai', '2026-03-28 04:00:00'),
(29, 26, 1, '2026-04-04', '13:00:00', 'Service rutin bulan ini', 'selesai', '2026-03-29 06:00:00'),
(30, 27, 2, '2026-04-05', '09:00:00', 'Mesin kurang Power', 'selesai', '2026-03-30 02:00:00'),
(31, 28, 3, '2026-04-06', '10:00:00', 'AC tidak berfungsi', 'dikonfirmasi', '2026-03-31 03:00:00'),
(32, 7, 4, '2026-04-07', '11:00:00', 'Ganti oli completo', 'selesai', '2026-04-01 04:00:00'),
(33, 8, 5, '2026-04-08', '13:00:00', 'Ban tidak Balance', 'selesai', '2026-04-02 06:00:00'),
(34, 9, 6, '2026-04-09', '09:00:00', 'Mesin rusak butuh overhaul', 'batal', '2026-04-03 02:00:00'),
(35, 10, 1, '2026-04-10', '10:00:00', 'Service ringan', 'selesai', '2026-04-04 03:00:00'),
(36, 11, 2, '2026-04-11', '11:00:00', 'Tune up lengkap', 'selesai', '2026-04-05 04:00:00'),
(37, 12, 3, '2026-04-12', '13:00:00', 'Perbaikan AC', 'selesai', '2026-04-06 06:00:00'),
(38, 13, 4, '2026-04-13', '09:00:00', 'Ganti oli mesin', 'selesai', '2026-04-07 02:00:00'),
(39, 14, 5, '2026-04-14', '10:00:00', 'Balancing roda', 'selesai', '2026-04-08 03:00:00'),
(40, 15, 6, '2026-04-15', '11:00:00', 'Perbaikan mesin total', 'selesai', '2026-04-09 04:00:00'),
(41, 16, 1, '2026-04-16', '13:00:00', 'Service rutin', 'selesai', '2026-04-10 06:00:00'),
(42, 17, 2, '2026-04-17', '09:00:00', 'Tune up mesin', 'selesai', '2026-04-11 02:00:00'),
(43, 18, 3, '2026-04-18', '10:00:00', 'Service AC', 'selesai', '2026-04-12 03:00:00'),
(44, 19, 4, '2026-04-19', '11:00:00', 'Ganti oli', 'selesai', '2026-04-13 04:00:00'),
(45, 20, 5, '2026-04-20', '13:00:00', 'Spooring roda', 'selesai', '2026-04-14 06:00:00'),
(46, 21, 6, '2026-04-21', '09:00:00', 'Perbaikan mesin besar', 'selesai', '2026-04-15 02:00:00'),
(47, 22, 1, '2026-04-22', '10:00:00', 'Service bulan ini', 'selesai', '2026-04-16 03:00:00'),
(48, 23, 2, '2026-04-23', '11:00:00', 'Tune up', 'selesai', '2026-04-17 04:00:00'),
(49, 24, 3, '2026-04-24', '13:00:00', 'Service AC', 'pending', '2026-04-18 06:00:00'),
(50, 25, 4, '2026-04-25', '09:00:00', 'Ganti oli', 'pending', '2026-04-19 02:00:00'),
(51, 26, 5, '2026-04-26', '10:00:00', 'Balance roda', 'pending', '2026-04-20 03:00:00'),
(52, 27, 6, '2026-04-27', '11:00:00', 'Overhaul', 'pending', '2026-04-21 04:00:00'),
(53, 28, 1, '2026-04-28', '13:00:00', 'Service rutin', 'pending', '2026-04-22 06:00:00'),
(54, 8, 2, '2026-04-29', '09:00:00', 'Tune up mesin', 'pending', '2026-04-23 02:00:00'),
(55, 9, 3, '2026-04-30', '10:00:00', 'Perbaikan AC', 'pending', '2026-04-24 03:00:00'),
(56, 10, 4, '2026-05-01', '11:00:00', 'Ganti oli', 'pending', '2026-04-25 04:00:00'),
(57, 11, 5, '2026-05-02', '13:00:00', 'Spooring', 'pending', '2026-04-26 06:00:00'),
(58, 12, 6, '2026-05-03', '09:00:00', 'Overhaul mesin', 'pending', '2026-04-27 02:00:00'),
(59, 13, 1, '2026-05-04', '10:00:00', 'Service', 'pending', '2026-04-28 03:00:00'),
(60, 14, 2, '2026-05-05', '11:00:00', 'Tune up', 'pending', '2026-04-29 04:00:00'),
(61, 15, 3, '2026-05-06', '13:00:00', 'AC service', 'pending', '2026-04-30 06:00:00'),
(62, 16, 4, '2026-05-07', '09:00:00', 'Ganti oli', 'pending', '2026-05-01 02:00:00'),
(63, 17, 5, '2026-05-08', '10:00:00', 'Balance', 'pending', '2026-05-02 03:00:00'),
(64, 18, 6, '2026-05-09', '11:00:00', 'Mesin besar', 'dikonfirmasi', '2026-05-03 04:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `item_type` enum('jasa','sparepart') NOT NULL,
  `item_id` int(11) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `jumlah` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id`, `transaksi_id`, `item_type`, `item_id`, `harga`, `jumlah`) VALUES
(26, 13, 'sparepart', 8, 350000.00, 1),
(27, 14, 'sparepart', 7, 95000.00, 2),
(28, 14, 'sparepart', 6, 125000.00, 3),
(29, 14, 'sparepart', 5, 850000.00, 3),
(31, 16, 'sparepart', 4, 180000.00, 1),
(32, 16, 'sparepart', 3, 250000.00, 2),
(33, 16, 'sparepart', 6, 125000.00, 1),
(34, 16, 'sparepart', 5, 850000.00, 1),
(35, 16, 'sparepart', 1, 150000.00, 1),
(36, 17, 'sparepart', 8, 350000.00, 4),
(37, 17, 'sparepart', 1, 150000.00, 4),
(38, 18, 'sparepart', 8, 350000.00, 2),
(39, 19, 'sparepart', 7, 95000.00, 1),
(40, 20, 'jasa', 1, 350000.00, 1),
(41, 21, 'sparepart', 6, 125000.00, 1),
(42, 22, 'jasa', 1, 350000.00, 1),
(43, 22, 'sparepart', 1, 25000.00, 1),
(44, 23, 'jasa', 2, 450000.00, 1),
(45, 23, 'sparepart', 4, 100000.00, 1),
(46, 24, 'jasa', 3, 400000.00, 1),
(47, 24, 'sparepart', 2, 25000.00, 1),
(48, 25, 'jasa', 4, 250000.00, 1),
(49, 26, 'jasa', 5, 300000.00, 1),
(50, 26, 'sparepart', 5, 380000.00, 1),
(51, 27, 'jasa', 6, 3500000.00, 1),
(52, 28, 'jasa', 1, 350000.00, 1),
(53, 29, 'jasa', 2, 450000.00, 1),
(54, 30, 'jasa', 3, 400000.00, 1),
(55, 31, 'jasa', 4, 250000.00, 1),
(56, 32, 'jasa', 5, 300000.00, 1),
(57, 33, 'jasa', 6, 3500000.00, 1),
(58, 34, 'jasa', 1, 350000.00, 1),
(59, 35, 'jasa', 2, 450000.00, 1),
(60, 36, 'sparepart', 8, 550000.00, 2),
(61, 36, 'sparepart', 1, 130000.00, 2),
(62, 37, 'sparepart', 7, 85000.00, 1),
(63, 37, 'sparepart', 3, 220000.00, 1),
(64, 38, 'sparepart', 4, 165000.00, 3),
(65, 38, 'sparepart', 6, 110000.00, 2),
(66, 39, 'sparepart', 5, 780000.00, 4),
(67, 40, 'sparepart', 2, 68000.00, 5),
(68, 41, 'sparepart', 8, 320000.00, 2),
(69, 41, 'sparepart', 6, 115000.00, 1),
(70, 42, 'sparepart', 1, 145000.00, 1),
(71, 43, 'sparepart', 3, 235000.00, 1),
(72, 43, 'sparepart', 7, 90000.00, 1),
(73, 44, 'sparepart', 4, 175000.00, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jasa`
--

CREATE TABLE `jasa` (
  `id` int(11) NOT NULL,
  `nama_jasa` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(15,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `estimasi_waktu` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jasa`
--

INSERT INTO `jasa` (`id`, `nama_jasa`, `deskripsi`, `harga`, `gambar`, `estimasi_waktu`, `created_at`) VALUES
(1, 'Service Rutin', 'Ganti oli, filter oli, dan pengecekan mesin lengkap', 350000.00, '1775209254_Gemini_Generated_Image_gu76r7gu76r7gu76 (1).png', '2 Jam', '2026-03-16 08:03:23'),
(2, 'Tune Up Mesin', 'Pembersihan dan penyetelan mesin untuk performa optimal', 450000.00, '1775208665_Gemini_Generated_Image_oucy8xoucy8xoucy (1).png', '3 Jam', '2026-03-16 08:03:23'),
(3, 'Service AC', 'Pengecekan dan perbaikan sistem AC mobil', 400000.00, '1775208304_Gemini_Generated_Image_zc6r0bzc6r0bzc6r (1).png', '2 Jam', '2026-03-16 08:03:23'),
(4, 'Ganti Oli', 'Ganti oli mesin dan filter oli', 250000.00, '1775207859_Gemini_Generated_Image_sp1djusp1djusp1d (1).png', '1 Jam', '2026-03-16 08:03:23'),
(5, 'Balancing & Spooring', 'Penyeimbangan roda dan penyelarasan ban', 300000.00, '1775208415_Gemini_Generated_Image_y9nsamy9nsamy9ns (1).png', '1.5 Jam', '2026-03-16 08:03:23'),
(6, 'Overhaul Mesin', 'Turun mesin dan perbaikan total', 3500000.00, '1775208214_Gemini_Generated_Image_r8fgtsr8fgtsr8fg (1).png', '3 Hari', '2026-03-16 08:03:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `metode` varchar(50) DEFAULT 'QRIS',
  `status` enum('pending','sukses','gagal') DEFAULT 'pending',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_payment` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil_bengkel`
--

CREATE TABLE `profil_bengkel` (
  `id` int(11) NOT NULL,
  `nama_bengkel` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jam_operasional` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `gambar_banner` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil_bengkel`
--

INSERT INTO `profil_bengkel` (`id`, `nama_bengkel`, `alamat`, `no_telp`, `email`, `deskripsi`, `jam_operasional`, `logo`, `gambar_banner`, `updated_at`) VALUES
(1, 'Bengkel Mobil Jaya Abadi', 'Jl. Raya Otomotif No. 123, Jakarta', '021-555-1234', 'info@jayabadi.com', 'Bengkel mobil profesional dengan teknisi berpengalaman. Melayani service rutin, perbaikan mesin, dan jual sparepart original.', 'Senin - Sabtu: 08:00 - 20:00, Minggu: 09:00 - 15:00', '1773650573_ChatGPT Image 16 Mar 2026, 15.42.45.png', '', '2026-03-16 08:42:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `qris`
--

CREATE TABLE `qris` (
  `id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `nama_bank` varchar(50) DEFAULT NULL,
  `atas_nama` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `qris`
--

INSERT INTO `qris` (`id`, `gambar`, `nama_bank`, `atas_nama`, `created_at`) VALUES
(1, '1773649936_qris (1).jpg', 'All Payment', 'Service HP Zlyne', '2026-03-16 08:03:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `nama_role`) VALUES
(1, 'admin'),
(2, 'owner'),
(3, 'pegawai'),
(4, 'customer');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `pegawai_id` int(11) DEFAULT NULL,
  `catatan_service` text DEFAULT NULL,
  `biaya_tambahan` decimal(15,2) DEFAULT 0.00,
  `status` enum('antri','dikerjakan','selesai') DEFAULT 'antri',
  `tanggal_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `service`
--

INSERT INTO `service` (`id`, `booking_id`, `pegawai_id`, `catatan_service`, `biaya_tambahan`, `status`, `tanggal_selesai`) VALUES
(1, 1, 3, NULL, 0.00, 'selesai', '2026-03-25'),
(2, 2, 3, '', 20000.00, 'selesai', '2026-03-25'),
(3, 3, 3, 'uang bensin dan perpindahan tempat', 40000.00, 'selesai', '2026-04-06'),
(4, 3, 3, NULL, 0.00, 'selesai', NULL),
(5, 4, 3, NULL, 0.00, 'selesai', '2026-04-08'),
(6, 7, 3, NULL, 0.00, 'selesai', '2026-04-30'),
(7, 6, 3, NULL, 0.00, 'selesai', '2026-04-30'),
(8, 5, 3, 'tidak ada kerusakan yang fatal', 0.00, 'selesai', '2026-04-30'),
(9, 10, NULL, 'Service rutin selesai', 0.00, 'selesai', '2026-03-18'),
(10, 11, NULL, 'Mesin tune up selesai', 50000.00, 'selesai', '2026-03-19'),
(11, 12, NULL, 'AC sudah нормал', 0.00, 'selesai', '2026-03-20'),
(12, 13, NULL, 'Oli sudah diganti', 0.00, 'selesai', '2026-03-21'),
(13, 14, NULL, 'Roda sudah balance', 0.00, 'selesai', '2026-03-22'),
(14, 15, NULL, 'Mesin sudah diperbaiki', 150000.00, 'selesai', '2026-03-23'),
(15, 16, NULL, 'AC berfungsi normal', 0.00, 'selesai', '2026-03-24'),
(16, 17, NULL, 'Tune up berhasil', 0.00, 'selesai', '2026-03-25'),
(17, 18, NULL, 'Service selesai', 0.00, 'selesai', '2026-03-26'),
(18, 19, NULL, 'AC sudah diperbaiki', 75000.00, 'selesai', '2026-03-27'),
(19, 20, NULL, 'Oli diganti', 0.00, 'selesai', '2026-03-28'),
(20, 21, NULL, 'Wheel alignment selesai', 0.00, 'selesai', '2026-03-29'),
(21, 22, NULL, 'Overhaul selesai', 0.00, 'selesai', '2026-03-30'),
(22, 23, NULL, 'Service bulan ini selesai', 0.00, 'selesai', '2026-04-01'),
(23, 24, NULL, 'Mesin sudah normal', 0.00, 'selesai', '2026-04-02'),
(24, 25, NULL, 'Service selesai', 0.00, 'selesai', '2026-04-03'),
(25, 26, NULL, 'Tune up lengkap selesai', 0.00, 'selesai', '2026-04-04'),
(26, 27, NULL, 'AC berfungsi', 0.00, 'selesai', '2026-04-05'),
(27, 28, NULL, 'Oli diganti completo', 0.00, 'selesai', '2026-04-06'),
(28, 29, NULL, 'Ban sudah balance', 0.00, 'selesai', '2026-04-07'),
(29, 30, NULL, 'Mesin sudah overhaul', 200000.00, 'selesai', '2026-04-08'),
(30, 31, NULL, 'Service rutin selesai', 0.00, 'selesai', '2026-04-09'),
(31, 32, NULL, 'Tune up mesin selesai', 0.00, 'selesai', '2026-04-10'),
(32, 33, NULL, 'Service AC selesai', 0.00, 'selesai', '2026-04-11'),
(33, 34, NULL, 'Oli sudah diganti', 0.00, 'selesai', '2026-04-12'),
(34, 35, NULL, 'Spooring selesai', 0.00, 'selesai', '2026-04-13'),
(35, 36, 3, 'Mesin besar selesai', 0.00, 'selesai', '2026-04-14'),
(36, 37, NULL, 'Service bulan ini', 0.00, 'selesai', '2026-04-15'),
(37, 38, NULL, 'Tune up selesai', 0.00, 'selesai', '2026-04-16'),
(38, 64, NULL, NULL, 0.00, 'antri', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sparepart`
--

CREATE TABLE `sparepart` (
  `id` int(11) NOT NULL,
  `nama_sparepart` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(15,2) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `merek` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sparepart`
--

INSERT INTO `sparepart` (`id`, `nama_sparepart`, `deskripsi`, `harga`, `stok`, `gambar`, `merek`, `created_at`) VALUES
(1, 'Oli Mesin 5W-30', 'Oli mesin sintetik untuk performa optimal', 150000.00, 42, '1775620554_69d5d1ca51fdc_Gemini_Generated_Image_66h3q266h3q266h3.png', 'Toyota', '2026-03-16 08:03:23'),
(2, 'Filter Oli', 'Filter oli original berkualitas', 75000.00, 26, '1775620163_69d5d0436cf72_Gemini_Generated_Image_l3eionl3eionl3ei.png', 'Honda', '2026-03-16 08:03:23'),
(3, 'Kampas Rem Depan', 'Kampas rem berkualitas tinggi', 250000.00, 23, '1775619865_69d5cf1909e62_Gemini_Generated_Image_310gri310gri310g.png', 'Aisin', '2026-03-16 08:03:23'),
(4, 'Busi Iridium', 'Busi iridium untuk pembakaran sempurna', 180000.00, 37, '1775619764_69d5ceb4a3265_Gemini_Generated_Image_ey5kg0ey5kg0ey5k.png', 'NGK', '2026-03-16 08:03:23'),
(5, 'Aki Mobil 12V', 'Aki kering dengan daya tahan lama', 850000.00, 10, '1775618678_69d5ca761aca0_Gemini_Generated_Image_j7t2d4j7t2d4j7t2.png', 'GS Astra', '2026-03-16 08:03:23'),
(6, 'Filter Udara', 'Filter udara mesin', 125000.00, 13, '1775618542_69d5c9eebb67b_Gemini_Generated_Image_4zhlps4zhlps4zhl.png', 'Denso', '2026-03-16 08:03:23'),
(7, 'V-Belt', 'Belt penggerak alternator dan AC', 95000.00, 24, '1775618534_69d5c9e6cd74d_Gemini_Generated_Image_31437e31437e3143.png', 'Bando', '2026-03-16 08:03:23'),
(8, 'Lampu Depan LED', 'Lampu LED putih terang', 350000.00, 3, '1775618515_69d5c9d368879_Gemini_Generated_Image_58xhkv58xhkv58xh.png', 'Philips', '2026-03-16 08:03:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `kode_transaksi` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `status` enum('pending','lunas','batal') DEFAULT 'pending',
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `kode_transaksi`, `user_id`, `total_harga`, `status`, `metode_pembayaran`, `created_at`) VALUES
(13, 'INV-20260429-21DC1F', 4, 350000.00, 'lunas', 'GoPay', '2026-04-29 08:20:02'),
(14, 'INV-20260429-C969CF', 4, 3115000.00, 'lunas', 'DANA', '2026-04-29 09:06:04'),
(16, 'INV-20260429-68AC09', 4, 1805000.00, 'lunas', 'QRIS', '2026-04-29 10:20:06'),
(17, 'INV-20260429-80B96A', 4, 2000000.00, 'lunas', 'QRIS', '2026-04-29 12:17:12'),
(18, 'INV-20260430-5153D5', 4, 700000.00, 'lunas', 'QRIS', '2026-04-30 08:02:45'),
(19, 'INV-20260512-FEC7DF', 4, 95000.00, 'lunas', 'QRIS', '2026-05-12 12:11:11'),
(20, 'INV-20260401-A1B2C3', 7, 350000.00, 'lunas', 'QRIS', '2026-04-01 03:00:00'),
(21, 'INV-20260402-D4E5F6', 8, 450000.00, 'lunas', 'GoPay', '2026-04-02 04:00:00'),
(22, 'INV-20260403-G7H8I9', 9, 400000.00, 'lunas', 'DANA', '2026-04-03 05:00:00'),
(23, 'INV-20260404-J0K1L2', 10, 250000.00, 'lunas', 'QRIS', '2026-04-04 06:00:00'),
(24, 'INV-20260405-M3N4O5', 11, 300000.00, 'lunas', 'ShopeePay', '2026-04-05 02:00:00'),
(25, 'INV-20260406-P6Q7R8', 12, 3500000.00, 'lunas', 'Transfer Bank', '2026-04-06 03:00:00'),
(26, 'INV-20260407-S9T0U1', 13, 350000.00, 'lunas', 'QRIS', '2026-04-07 04:00:00'),
(27, 'INV-20260408-V2W3X4', 14, 450000.00, 'lunas', 'GoPay', '2026-04-08 06:00:00'),
(28, 'INV-20260409-Y5Z6A7', 15, 350000.00, 'lunas', 'DANA', '2026-04-09 02:00:00'),
(29, 'INV-20260410-B8C9D0', 16, 400000.00, 'lunas', 'QRIS', '2026-04-10 03:00:00'),
(30, 'INV-20260411-E1F2G3', 17, 250000.00, 'lunas', 'ShopeePay', '2026-04-11 04:00:00'),
(31, 'INV-20260412-H4I5J6', 18, 300000.00, 'lunas', 'Transfer Bank', '2026-04-12 06:00:00'),
(32, 'INV-20260413-K7L8M9', 19, 3500000.00, 'batal', 'QRIS', '2026-04-13 02:00:00'),
(33, 'INV-20260414-N0O1P2', 20, 350000.00, 'lunas', 'GoPay', '2026-04-14 03:00:00'),
(34, 'INV-20260415-Q3R4S5', 21, 450000.00, 'lunas', 'DANA', '2026-04-15 04:00:00'),
(35, 'INV-20260416-T6U7V8', 22, 400000.00, 'lunas', 'QRIS', '2026-04-16 06:00:00'),
(36, 'INV-20260417-W9X0Y1', 23, 250000.00, 'pending', 'QRIS', '2026-04-17 02:00:00'),
(37, 'INV-20260418-Z2A3B4', 24, 300000.00, 'pending', 'GoPay', '2026-04-18 03:00:00'),
(38, 'INV-20260419-C5D6E7', 25, 3500000.00, 'pending', 'DANA', '2026-04-19 04:00:00'),
(39, 'INV-20260420-F8G9H0', 26, 350000.00, 'pending', 'QRIS', '2026-04-20 06:00:00'),
(40, 'INV-20260421-I1J2K3', 7, 125000.00, 'lunas', 'QRIS', '2026-04-21 07:00:00'),
(41, 'INV-20260422-L4M5N6', 8, 375000.00, 'lunas', 'GoPay', '2026-04-22 08:00:00'),
(42, 'INV-20260423-O7P8Q9', 9, 550000.00, 'lunas', 'DANA', '2026-04-23 09:00:00'),
(43, 'INV-20260424-R0S1T2', 10, 425000.00, 'lunas', 'QRIS', '2026-04-24 10:00:00'),
(44, 'INV-20260425-U3V4W5', 11, 680000.00, 'lunas', 'ShopeePay', '2026-04-25 11:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role_id` int(11) DEFAULT 4,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `password`, `email`, `no_hp`, `alamat`, `role_id`, `created_at`) VALUES
(1, 'Admin Bengkel', 'admin', '123', 'admin@bengkel.com', '081234567890', 'Jl. Bengkel No. 1', 1, '2026-03-16 08:03:23'),
(2, 'Pemilik Bengkel', 'owner', '123', 'owner@bengkel.com', '081234567891', 'Jl. Bengkel No. 2', 2, '2026-03-16 08:03:23'),
(3, 'Mekanik Senior', 'pegawai', '123', 'pegawai@bengkel.com', '081234567892', 'Jl. Bengkel No. 3', 3, '2026-03-16 08:03:23'),
(4, 'Customer Satu', 'customer', '123', 'customer@gmail.com', '081234567893', 'Jl. Pelanggan No. 1', 4, '2026-03-16 08:03:23'),
(5, 'maul', 'mmaul', 'maul123', 'maulanasandy@bengkel.com', '082217236612', 'jl.ahmad dani', 4, '2026-04-06 08:03:09'),
(6, 'maulanasandy', 'sandy991', 'sandy99123', 'sandy991@gmail.com', '08123455245', 'jl.dimana aja', 4, '2026-04-28 21:26:18'),
(7, 'Ahmad Pratama', 'ahmad_pt', '', 'ahmad.pratama@email.com', '081234567800', 'Jl. Merdeka No. 10, Jakarta', 4, '2026-05-19 02:19:17'),
(8, 'Siti Nurhaliza', 'siti_n', '', 'siti.nurhaliza@email.com', '081234567801', 'Jl. Sudirman No. 25, Jakarta', 4, '2026-05-19 02:19:17'),
(9, 'Budi Santoso', 'budi_s', '', 'budi.santoso@email.com', '081234567802', 'Jl. Thamrin No. 15, Jakarta', 4, '2026-05-19 02:19:17'),
(10, 'Dewi Lestari', 'dewi_l', '', 'dewi.lestari@email.com', '081234567803', 'Jl. Gatot Subroto No. 30, Jakarta', 4, '2026-05-19 02:19:17'),
(11, 'Eko Wahyudi', 'eko_w', '', 'eko.wahyudi@email.com', '081234567804', 'Jl. Ahmad Yani No. 12, Bekasi', 4, '2026-05-19 02:19:17'),
(12, 'Fitri Amalia', 'fitri_a', '', 'fitri.amalia@email.com', '081234567805', 'Jl. Dago No. 8, Bandung', 4, '2026-05-19 02:19:17'),
(13, 'Gunawan Hermawan', 'gunawan_h', '', 'gunawan.hermawan@email.com', '081234567806', 'Jl. Asia Afrika No. 20, Bandung', 4, '2026-05-19 02:19:17'),
(14, 'Hani Wijayanti', 'hani_w', '', 'hani.wijayanti@email.com', '081234567807', 'Jl. Braga No. 5, Bandung', 4, '2026-05-19 02:19:17'),
(15, 'Indra Mahendra', 'indra_m', '', 'indra.mahendra@email.com', '081234567808', 'Jl. Pangeran Diponegoro No. 18, Semarang', 4, '2026-05-19 02:19:17'),
(16, 'Jasmine Audrey', 'jasmine_a', '', 'jasmine.audrey@email.com', '081234567809', 'Jl. Ahmad Yani No. 45, Surabaya', 4, '2026-05-19 02:19:17'),
(17, 'Kurnia Wahyu', 'kurnia_w', '', 'kurnia.wahyu@email.com', '081234567810', 'Jl. Basuki Rahmat No. 22, Surabaya', 4, '2026-05-19 02:19:17'),
(18, 'Lina Kusuma', 'lina_k', '', 'lina.kusuma@email.com', '081234567811', 'Jl. Dharmahijau No. 11, Surabaya', 4, '2026-05-19 02:19:17'),
(19, 'Muhamad Rizki', 'rizki_m', '', 'muhamad.rizki@email.com', '081234567812', 'Jl. Imam Bonjol No. 7, Tangerang', 4, '2026-05-19 02:19:17'),
(20, 'Nina Sari', 'nina_s', '', 'nina.sari@email.com', '081234567813', 'Jl. Raya BSD No. 35, Tangerang', 4, '2026-05-19 02:19:17'),
(21, 'Oscar Tannady', 'oscar_t', '', 'oscar.tannady@email.com', '081234567814', 'Jl. Mangkutana No. 9, Makassar', 4, '2026-05-19 02:19:17'),
(22, 'Putri Handayani', 'putri_h', '', 'putri.handayani@email.com', '081234567815', 'Jl. Pettarani No. 16, Makassar', 4, '2026-05-19 02:19:17'),
(23, 'Qori Amalia', 'qori_a', '', 'qori.amalia@email.com', '081234567816', 'Jl. Letjen MT Haryono No. 28, Jakarta', 4, '2026-05-19 02:19:17'),
(24, 'Rendi Kuswantoro', 'rendi_k', '', 'rendi.kuswantoro@email.com', '081234567817', 'Jl. Veteran No. 14, Yogyakarta', 4, '2026-05-19 02:19:17'),
(25, 'Sari Dewi', 'sari_d', '', 'sari.dewi@email.com', '081234567818', 'Jl. Malioboro No. 42, Yogyakarta', 4, '2026-05-19 02:19:17'),
(26, 'Toni Hartono', 'toni_h', '', 'toni.hartono@email.com', '081234567819', 'Jl. Magelang No. 6, Yogyakarta', 4, '2026-05-19 02:19:17'),
(27, 'Ulfa Maharani', 'ulfa_m', '', 'ulfa.maharani@email.com', '081234567820', 'Jl. Slamet Riyadi No. 19, Solo', 4, '2026-05-19 02:19:17'),
(28, 'Vina Octavia', 'vina_o', '', 'vina.octavia@email.com', '081234567821', 'Jl. Ir. Sukarno No. 31, Solo', 4, '2026-05-19 02:19:17'),
(29, 'Wawan Setiawan', 'wawan_s', '', 'wawan.setiawan@email.com', '081234567822', 'Jl. MT Haryono No. 12, Palembang', 4, '2026-05-19 02:19:17'),
(30, 'Yanti Kusumawati', 'yanti_k', '', 'yanti.kusumawati@email.com', '081234567823', 'Jl. Jendral Sudirman No. 8, Palembang', 4, '2026-05-19 02:19:17'),
(31, 'Zainal Abidin', 'zainal_a', '', 'zainal.abidin@email.com', '081234567824', 'Jl. Pademangan No. 25, Jakarta', 4, '2026-05-19 02:19:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `jasa_id` (`jasa_id`);

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`);

--
-- Indeks untuk tabel `jasa`
--
ALTER TABLE `jasa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaksi_id` (`transaksi_id`);

--
-- Indeks untuk tabel `profil_bengkel`
--
ALTER TABLE `profil_bengkel`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `qris`
--
ALTER TABLE `qris`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indeks untuk tabel `sparepart`
--
ALTER TABLE `sparepart`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT untuk tabel `jasa`
--
ALTER TABLE `jasa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `profil_bengkel`
--
ALTER TABLE `profil_bengkel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `qris`
--
ALTER TABLE `qris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `sparepart`
--
ALTER TABLE `sparepart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`jasa_id`) REFERENCES `jasa` (`id`);

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`);

--
-- Ketidakleluasaan untuk tabel `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`),
  ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`pegawai_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

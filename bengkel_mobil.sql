-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Bulan Mei 2026 pada 14.13
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
(9, 4, 6, '2026-05-14', '16:00:00', 'full power mesin', 'selesai', '2026-04-30 08:07:15');

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
(39, 19, 'sparepart', 7, 95000.00, 1);

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
(8, 5, 3, 'tidak ada kerusakan yang fatal', 0.00, 'selesai', '2026-04-30');

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
(19, 'INV-20260512-FEC7DF', 4, 95000.00, 'lunas', 'QRIS', '2026-05-12 12:11:11');

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
(6, 'maulanasandy', 'sandy991', 'sandy99123', 'sandy991@gmail.com', '08123455245', 'jl.dimana aja', 4, '2026-04-28 21:26:18');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `sparepart`
--
ALTER TABLE `sparepart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

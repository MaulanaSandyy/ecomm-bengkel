-- Membuat Database
CREATE DATABASE IF NOT EXISTS db_bengkel;
USE db_bengkel;

-- =====================================================
-- Tabel roles
-- =====================================================
CREATE TABLE roles (
    id_role INT PRIMARY KEY AUTO_INCREMENT,
    nama_role VARCHAR(50) NOT NULL,
    keterangan TEXT
);

INSERT INTO roles (nama_role, keterangan) VALUES
('admin', 'Memiliki akses penuh ke sistem'),
('owner', 'Melihat laporan dan statistik'),
('pegawai', 'Mengelola service dan booking'),
('customer', 'Melakukan booking dan melihat riwayat');

-- =====================================================
-- Tabel users
-- =====================================================
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    id_role INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    no_telepon VARCHAR(15),
    alamat TEXT,
    foto VARCHAR(255) DEFAULT 'default.jpg',
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_role) REFERENCES roles(id_role)
);

-- Password: admin123 (sudah di-hash)
INSERT INTO users (id_role, username, password, nama_lengkap, email, no_telepon, alamat) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Bengkel', 'admin@bengkel.com', '081234567890', 'Jl. Raya No. 1'),
(2, 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pemilik Bengkel', 'owner@bengkel.com', '081234567891', 'Jl. Raya No. 2'),
(3, 'pegawai', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mekanik Handal', 'pegawai@bengkel.com', '081234567892', 'Jl. Raya No. 3'),
(4, 'customer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Andi Customer', 'andi@gmail.com', '081234567893', 'Jl. Mawar No. 1'),
(4, 'customer2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Customer', 'budi@gmail.com', '081234567894', 'Jl. Melati No. 2');

-- =====================================================
-- Tabel profil_bengkel
-- =====================================================
CREATE TABLE profil_bengkel (
    id_profil INT PRIMARY KEY AUTO_INCREMENT,
    nama_bengkel VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    jam_buka VARCHAR(50),
    deskripsi TEXT,
    logo VARCHAR(255) DEFAULT 'logo.png',
    facebook VARCHAR(100),
    instagram VARCHAR(100),
    maps_embed TEXT
);

INSERT INTO profil_bengkel (nama_bengkel, alamat, no_telepon, email, jam_buka, deskripsi) VALUES
('Bengkel Mobil Jaya Abadi', 'Jl. Ahmad Yani No. 123, Jakarta', '021-555-1234', 'info@jayabadi.com', 'Senin - Sabtu: 08:00 - 20:00', 'Bengkel mobil profesional dengan mekanik berpengalaman. Melayani service ringan, berat, ganti oli, dan perbaikan mesin. Juga menjual sparepart original dan berkualitas.');

-- =====================================================
-- Tabel jasa
-- =====================================================
CREATE TABLE jasa (
    id_jasa INT PRIMARY KEY AUTO_INCREMENT,
    nama_jasa VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    estimasi_waktu VARCHAR(50),
    gambar VARCHAR(255) DEFAULT 'jasa.jpg',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif'
);

INSERT INTO jasa (nama_jasa, deskripsi, harga, estimasi_waktu) VALUES
('Ganti Oli Mesin', 'Mengganti oli mesin dengan oli berkualitas', 250000, '30 menit'),
('Service Rem', 'Pengecekan dan perbaikan sistem rem', 350000, '1 jam'),
('Tune Up Mesin', 'Penyetelan dan perawatan mesin', 500000, '2 jam'),
('Service AC', 'Pengecekan dan perbaikan AC mobil', 450000, '1.5 jam'),
('Spooring & Balancing', 'Penyeimbangan dan pelurusan ban', 300000, '45 menit'),
('Ganti Aki', 'Penggantian aki mobil baru', 850000, '20 menit');

-- =====================================================
-- Tabel sparepart
-- =====================================================
CREATE TABLE sparepart (
    id_sparepart INT PRIMARY KEY AUTO_INCREMENT,
    kode_sparepart VARCHAR(20) UNIQUE NOT NULL,
    nama_sparepart VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga_jual DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255) DEFAULT 'sparepart.jpg',
    merk VARCHAR(50),
    tipe_mobil TEXT,
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia'
);

INSERT INTO sparepart (kode_sparepart, nama_sparepart, deskripsi, harga_jual, stok, merk, tipe_mobil) VALUES
('SP001', 'Oli Mesin 5W-30', 'Oli mesin sintetik 1 liter', 95000, 50, 'Shell', 'Semua mobil'),
('SP002', 'Filter Oli', 'Filter oli original', 45000, 30, 'Toyota', 'Avanza, Xenia'),
('SP003', 'Kampas Rem Depan', 'Kampas rem depan original', 250000, 20, 'Honda', 'Civic, HRV'),
('SP004', 'Aki Mobil 50Ah', 'Aki kering mobil', 750000, 10, 'GS Battery', 'Avanza, Xenia, Ertiga'),
('SP005', 'Busi Iridium', 'Busi Iridium 4 buah', 350000, 25, 'NGK', 'Semua mobil'),
('SP006', 'V-belt', 'V-belt mesin', 125000, 15, 'Mitsuboshi', 'Avanza, Xenia');

-- =====================================================
-- Tabel booking
-- =====================================================
CREATE TABLE booking (
    id_booking INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_jasa INT NOT NULL,
    tgl_booking DATE NOT NULL,
    jam_booking TIME NOT NULL,
    no_plat VARCHAR(15) NOT NULL,
    merk_mobil VARCHAR(50),
    tipe_mobil VARCHAR(50),
    tahun_mobil YEAR,
    keluhan TEXT,
    status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending',
    tgl_pembuatan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    FOREIGN KEY (id_jasa) REFERENCES jasa(id_jasa)
);

INSERT INTO booking (id_user, id_jasa, tgl_booking, jam_booking, no_plat, merk_mobil, tipe_mobil, tahun_mobil, keluhan, status) VALUES
(4, 1, '2024-01-15', '09:00:00', 'B 1234 ABC', 'Toyota', 'Avanza', 2020, 'Oli sudah 5000 km', 'selesai'),
(4, 2, '2024-01-20', '10:30:00', 'B 1234 ABC', 'Toyota', 'Avanza', 2020, 'Rem bunyi', 'selesai'),
(5, 3, '2024-02-01', '13:00:00', 'D 5678 EFG', 'Honda', 'Civic', 2019, 'Mesin brebet', 'diproses');

-- =====================================================
-- Tabel service
-- =====================================================
CREATE TABLE service (
    id_service INT PRIMARY KEY AUTO_INCREMENT,
    id_booking INT UNIQUE NOT NULL,
    id_mekanik INT,
    tgl_masuk DATE,
    tgl_keluar DATE,
    catatan_mekanik TEXT,
    biaya_tambahan DECIMAL(10,2) DEFAULT 0,
    status ENUM('antrian', 'dikerjakan', 'selesai', 'diambil') DEFAULT 'antrian',
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking),
    FOREIGN KEY (id_mekanik) REFERENCES users(id_user)
);

INSERT INTO service (id_booking, id_mekanik, tgl_masuk, tgl_keluar, catatan_mekanik, biaya_tambahan, status) VALUES
(1, 3, '2024-01-15', '2024-01-15', 'Ganti oli mesin, kondisi oli sudah hitam', 0, 'selesai'),
(2, 3, '2024-01-20', '2024-01-20', 'Ganti kampas rem depan, sudah aus', 250000, 'selesai');

-- =====================================================
-- Tabel transaksi
-- =====================================================
CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(20) UNIQUE NOT NULL,
    id_user INT NOT NULL,
    id_booking INT,
    tgl_transaksi DATE NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('tunai', 'transfer', 'kartu kredit', 'qris') DEFAULT 'tunai',
    status_pembayaran ENUM('belum bayar', 'lunas') DEFAULT 'belum bayar',
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking)
);

INSERT INTO transaksi (kode_transaksi, id_user, id_booking, tgl_transaksi, total_harga, metode_pembayaran, status_pembayaran) VALUES
('TRX20240115001', 4, 1, '2024-01-15', 250000, 'tunai', 'lunas'),
('TRX20240120001', 4, 2, '2024-01-20', 600000, 'transfer', 'lunas');

-- =====================================================
-- Tabel detail_transaksi
-- =====================================================
CREATE TABLE detail_transaksi (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT NOT NULL,
    tipe_item ENUM('jasa', 'sparepart') NOT NULL,
    id_item INT NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi)
);

INSERT INTO detail_transaksi (id_transaksi, tipe_item, id_item, jumlah, harga_satuan, subtotal) VALUES
(1, 'jasa', 1, 1, 250000, 250000),
(2, 'jasa', 2, 1, 350000, 350000),
(2, 'sparepart', 3, 1, 250000, 250000);
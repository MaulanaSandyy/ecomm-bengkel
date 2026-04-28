-- Membuat Database
CREATE DATABASE IF NOT EXISTS bengkel_mobil;
USE bengkel_mobil;

-- Tabel roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL
);

INSERT INTO roles (nama_role) VALUES 
('admin'), ('owner'), ('pegawai'), ('customer');

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    no_hp VARCHAR(15),
    alamat TEXT,
    role_id INT DEFAULT 4,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Insert default users (password: 123)
INSERT INTO users (nama_lengkap, username, password, email, no_hp, alamat, role_id) VALUES
('Admin Bengkel', 'admin', '123', 'admin@bengkel.com', '081234567890', 'Jl. Bengkel No. 1', 1),
('Pemilik Bengkel', 'owner', '123', 'owner@bengkel.com', '081234567891', 'Jl. Bengkel No. 2', 2),
('Mekanik Senior', 'pegawai', '123', 'pegawai@bengkel.com', '081234567892', 'Jl. Bengkel No. 3', 3),
('Customer Satu', 'customer', '123', 'customer@gmail.com', '081234567893', 'Jl. Pelanggan No. 1', 4);

-- Tabel profil_bengkel
CREATE TABLE profil_bengkel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_bengkel VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telp VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    jam_operasional VARCHAR(100),
    logo VARCHAR(255),
    gambar_banner VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO profil_bengkel (nama_bengkel, alamat, no_telp, email, deskripsi, jam_operasional) VALUES
('Bengkel Mobil Jaya Abadi', 'Jl. Raya Otomotif No. 123, Jakarta', '021-555-1234', 'info@jayabadi.com', 'Bengkel mobil profesional dengan teknisi berpengalaman. Melayani service rutin, perbaikan mesin, dan jual sparepart original.', 'Senin - Sabtu: 08:00 - 20:00, Minggu: 09:00 - 15:00');

-- Tabel jasa
CREATE TABLE jasa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jasa VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(15,2) NOT NULL,
    gambar VARCHAR(255),
    estimasi_waktu VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO jasa (nama_jasa, deskripsi, harga, estimasi_waktu) VALUES
('Service Rutin', 'Ganti oli, filter oli, dan pengecekan mesin lengkap', 350000, '2 Jam'),
('Tune Up Mesin', 'Pembersihan dan penyetelan mesin untuk performa optimal', 450000, '3 Jam'),
('Service AC', 'Pengecekan dan perbaikan sistem AC mobil', 400000, '2 Jam'),
('Ganti Oli', 'Ganti oli mesin dan filter oli', 250000, '1 Jam'),
('Balancing & Spooring', 'Penyeimbangan roda dan penyelarasan ban', 300000, '1.5 Jam'),
('Overhaul Mesin', 'Turun mesin dan perbaikan total', 3500000, '3 Hari');

-- Tabel sparepart
CREATE TABLE sparepart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sparepart VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(15,2) NOT NULL,
    stok INT DEFAULT 0,
    gambar VARCHAR(255),
    merek VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO sparepart (nama_sparepart, deskripsi, harga, stok, merek) VALUES
('Oli Mesin 5W-30', 'Oli mesin sintetik untuk performa optimal', 150000, 50, 'Toyota'),
('Filter Oli', 'Filter oli original berkualitas', 75000, 30, 'Honda'),
('Kampas Rem Depan', 'Kampas rem berkualitas tinggi', 250000, 25, 'Aisin'),
('Busi Iridium', 'Busi iridium untuk pembakaran sempurna', 180000, 40, 'NGK'),
('Aki Mobil 12V', 'Aki kering dengan daya tahan lama', 850000, 15, 'GS Astra'),
('Filter Udara', 'Filter udara mesin', 125000, 20, 'Denso'),
('V-Belt', 'Belt penggerak alternator dan AC', 95000, 35, 'Bando'),
('Lampu Depan LED', 'Lampu LED putih terang', 350000, 18, 'Philips');

-- Tabel booking
CREATE TABLE booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    jasa_id INT,
    tanggal_booking DATE NOT NULL,
    jam_booking TIME NOT NULL,
    keluhan TEXT,
    status ENUM('pending', 'dikonfirmasi', 'selesai', 'batal') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (jasa_id) REFERENCES jasa(id)
);

-- Tabel service
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    pegawai_id INT,
    catatan_service TEXT,
    biaya_tambahan DECIMAL(15,2) DEFAULT 0,
    status ENUM('antri', 'dikerjakan', 'selesai') DEFAULT 'antri',
    tanggal_selesai DATE,
    FOREIGN KEY (booking_id) REFERENCES booking(id),
    FOREIGN KEY (pegawai_id) REFERENCES users(id)
);

-- Tabel transaksi
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    total_harga DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'lunas', 'batal') DEFAULT 'pending',
    metode_pembayaran VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
-- update tabel transaksi (tambahin kolom no_resi)
ALTER TABLE transaksi 
ADD no_resi VARCHAR(100) NULL;
-- update tb transaksi
ALTER TABLE transaksi 
MODIFY status ENUM('dikemas','dikirim','selesai') NOT NULL;

-- Tabel detail_transaksi
CREATE TABLE detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    item_type ENUM('jasa', 'sparepart') NOT NULL,
    item_id INT NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    jumlah INT DEFAULT 1,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE
);

-- Tabel qris
CREATE TABLE qris (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gambar VARCHAR(255) NOT NULL,
    nama_bank VARCHAR(50),
    atas_nama VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO qris (gambar, nama_bank, atas_nama) VALUES
('qris-default.jpg', 'BCA', 'Bengkel Jaya Abadi');

-- Tabel payment
CREATE TABLE payment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaksi_id INT NOT NULL,
    metode VARCHAR(50) DEFAULT 'QRIS',
    status ENUM('pending', 'sukses', 'gagal') DEFAULT 'pending',
    bukti_pembayaran VARCHAR(255),
    tanggal_payment TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id)
);
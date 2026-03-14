<?php
// File: includes/koneksi.php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_bengkel';

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengatur charset agar tidak error dengan karakter khusus
mysqli_set_charset($koneksi, 'utf8');

// Fungsi untuk menjalankan query dan menangani error
function query($sql) {
    global $koneksi;
    $result = mysqli_query($koneksi, $sql);
    
    if (!$result) {
        die("Error query: " . mysqli_error($koneksi));
    }
    
    return $result;
}

// Fungsi untuk mengambil data sebagai array asosiatif
function fetch_array($result) {
    return mysqli_fetch_assoc($result);
}

// Fungsi untuk mengambil semua data
function fetch_all($result) {
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi untuk menghitung jumlah baris
function num_rows($result) {
    return mysqli_num_rows($result);
}

// Fungsi untuk mengambil ID terakhir yang di-insert
function insert_id() {
    global $koneksi;
    return mysqli_insert_id($koneksi);
}

// Fungsi untuk membersihkan input
function bersihkan_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

// Fungsi untuk mengecek login session
function cek_login() {
    session_start();
    if (!isset($_SESSION['id_user'])) {
        header('Location: ../login.php');
        exit;
    }
}

// Fungsi untuk mengecek role user
function cek_role($role_diperbolehkan) {
    session_start();
    if (!isset($_SESSION['role'])) {
        header('Location: ../login.php');
        exit;
    }
    
    if (!in_array($_SESSION['role'], $role_diperbolehkan)) {
        header('Location: ../index.php');
        exit;
    }
}

// Fungsi untuk format tanggal Indonesia
function tgl_indo($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

// Fungsi untuk format rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi untuk membuat kode transaksi otomatis
function buat_kode_transaksi() {
    $tanggal = date('Ymd');
    $query = "SELECT COUNT(*) as total FROM transaksi WHERE tgl_transaksi = CURDATE()";
    $result = query($query);
    $data = fetch_array($result);
    $urutan = $data['total'] + 1;
    
    return 'TRX' . $tanggal . sprintf('%03d', $urutan);
}
?>
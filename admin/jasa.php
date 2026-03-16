<?php
// File: admin/jasa.php
session_start();
include '../includes/koneksi.php';

// Cek login dan role
if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$title = "Kelola Jasa";
include '../includes/header.php';

// ========== HANDLE CRUD ==========

// TAMBAH JASA
if(isset($_POST['tambah'])) {
    $nama_jasa = bersihkan_input($_POST['nama_jasa']);
    $deskripsi = bersihkan_input($_POST['deskripsi']);
    $harga = bersihkan_input($_POST['harga']);
    $estimasi_waktu = bersihkan_input($_POST['estimasi_waktu']);
    $status = bersihkan_input($_POST['status']);
    
    $query = "INSERT INTO jasa (nama_jasa, deskripsi, harga, estimasi_waktu, status) 
              VALUES ('$nama_jasa', '$deskripsi', $harga, '$estimasi_waktu', '$status')";
    
    if(query($query)) {
        $_SESSION['success'] = "Jasa berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan jasa!";
    }
    header('Location: jasa.php');
    exit;
}

// EDIT JASA
if(isset($_POST['edit'])) {
    $id_jasa = $_POST['id_jasa'];
    $nama_jasa = bersihkan_input($_POST['nama_jasa']);
    $deskripsi = bersihkan_input($_POST['deskripsi']);
    $harga = bersihkan_input($_POST['harga']);
    $estimasi_waktu = bersihkan_input($_POST['estimasi_waktu']);
    $status = bersihkan_input($_POST['status']);
    
    $query = "UPDATE jasa SET 
              nama_jasa = '$nama_jasa',
              deskripsi = '$deskripsi',
              harga = $harga,
              estimasi_waktu = '$estimasi_waktu',
              status = '$status'
              WHERE id_jasa = $id_jasa";
    
    if(query($query)) {
        $_SESSION['success'] = "Jasa berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate jasa!";
    }
    header('Location: jasa.php');
    exit;
}

// HAPUS JASA
if(isset($_GET['hapus'])) {
    $id_jasa = $_GET['hapus'];
    
    // Cek apakah jasa digunakan di booking
    $check = query("SELECT * FROM booking WHERE id_jasa = $id_jasa");
    if(num_rows($check) > 0) {
        $_SESSION['error'] = "Jasa tidak dapat dihapus karena masih digunakan di booking!";
    } else {
        $query = "DELETE FROM jasa WHERE id_jasa = $id_jasa";
        if(query($query)) {
            $_SESSION['success'] = "Jasa berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus jasa!";
        }
    }
    header('Location: jasa.php');
    exit;
}

// AMBIL SEMUA JASA
$jasa = fetch_all(query("SELECT * FROM jasa ORDER BY id_jasa DESC"));
?>

<!-- HTML sama seperti user.php dengan penyesuaian kolom -->
<!-- ... (saya akan sertakan file lengkapnya di folder) ... -->
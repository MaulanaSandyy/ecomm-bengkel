<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "bengkel_mobil";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi untuk menjalankan query
function query($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

// Fungsi untuk mengambil data array
function fetch_array($result) {
    return mysqli_fetch_array($result);
}

// Fungsi untuk mengambil data assoc
function fetch_assoc($result) {
    return mysqli_fetch_assoc($result);
}

// Fungsi untuk menghitung baris
function num_rows($result) {
    return mysqli_num_rows($result);
}

// Fungsi untuk escaping string
function escape_string($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

// PERBAIKAN: Fungsi untuk upload gambar
function upload_gambar($file, $folder) {
    // Buat direktori jika belum ada
    $target_dir = "../uploads/" . $folder . "/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Cek apakah ada file yang diupload
    if (!isset($file) || $file['error'] == 4 || $file['size'] == 0) {
        return false;
    }
    
    // Generate nama file unik
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Validasi file
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    // Validasi ukuran (max 5MB)
    if ($file['size'] > 5000000) {
        return false;
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $new_filename;
    }
    
    return false;
}

// Cek login
function cek_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Cek role
function cek_role($role_id) {
    if ($_SESSION['role_id'] != $role_id) {
        header("Location: ../index.php");
        exit();
    }
}
?>
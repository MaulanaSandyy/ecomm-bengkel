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

// Fungsi untuk upload gambar
function upload_gambar($file, $folder) {
    $target_dir = "../uploads/" . $folder . "/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    if (!isset($file) || $file['error'] == 4 || $file['size'] == 0) {
        return false;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > 5000000) {
        return false;
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $new_filename;
    }
    
    return false;
}

// PERBAIKAN: Fungsi cek login
function cek_login() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Silakan login terlebih dahulu!";
        header("Location: ../auth/login.php");
        exit();
    }
}

// PERBAIKAN: Fungsi cek role
function cek_role($role_id) {
    if (!isset($_SESSION['role_id'])) {
        $_SESSION['error'] = "Sesi tidak valid!";
        header("Location: ../auth/login.php");
        exit();
    }
    
    if ($_SESSION['role_id'] != $role_id) {
        $_SESSION['error'] = "Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.";
        // Redirect berdasarkan role yang sebenarnya
        switch($_SESSION['role_id']) {
            case 1:
                header("Location: ../admin/index.php");
                break;
            case 2:
                header("Location: ../owner/index.php");
                break;
            case 3:
                header("Location: ../pegawai/index.php");
                break;
            case 4:
                header("Location: ../customer/index.php");
                break;
            default:
                header("Location: ../index.php");
        }
        exit();
    }
}
?>
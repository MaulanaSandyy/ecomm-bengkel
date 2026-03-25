<?php
session_start();
include '../includes/koneksi.php';

header('Content-Type: application/json');

// Cek login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID produk tidak ditemukan']);
    exit();
}

$id = $_GET['id'];
$sparepart = fetch_assoc(query("SELECT * FROM sparepart WHERE id = $id"));

if (!$sparepart) {
    echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan']);
    exit();
}

if ($sparepart['stok'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Stok sparepart habis!']);
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update cart item
if (isset($_SESSION['cart'][$id])) {
    // Cek apakah stok mencukupi
    if ($_SESSION['cart'][$id]['qty'] + 1 <= $sparepart['stok']) {
        $_SESSION['cart'][$id]['qty']++;
        $message = "Jumlah sparepart ditambah!";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Stok tidak mencukupi! Stok tersedia: ' . $sparepart['stok']]);
        exit();
    }
} else {
    $_SESSION['cart'][$id] = [
        'id' => $sparepart['id'],
        'nama' => $sparepart['nama_sparepart'],
        'harga' => $sparepart['harga'],
        'merek' => $sparepart['merek'],
        'gambar' => $sparepart['gambar'],
        'stok' => $sparepart['stok'],
        'qty' => 1
    ];
    $message = "Sparepart berhasil ditambahkan ke keranjang!";
}

// Hitung total item di keranjang
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_count += $item['qty'];
}

echo json_encode([
    'status' => 'success', 
    'message' => $message,
    'cart_count' => $cart_count
]);
?>
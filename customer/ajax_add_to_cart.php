<?php
session_start();
include '../includes/koneksi.php';

header('Content-Type: application/json');

// Cek login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu']);
    exit();
}

// Ambil data dari POST
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID produk tidak valid']);
    exit();
}

$sparepart = fetch_assoc(query("SELECT * FROM sparepart WHERE id = $id"));

if (!$sparepart) {
    echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan']);
    exit();
}

if ($sparepart['stok'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Stok sparepart habis!']);
    exit();
}

if ($qty > $sparepart['stok']) {
    echo json_encode(['status' => 'error', 'message' => 'Jumlah melebihi stok! Stok tersedia: ' . $sparepart['stok']]);
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update cart item
if (isset($_SESSION['cart'][$id])) {
    $new_qty = $_SESSION['cart'][$id]['qty'] + $qty;
    if ($new_qty <= $sparepart['stok']) {
        $_SESSION['cart'][$id]['qty'] = $new_qty;
        $message = "Jumlah " . $sparepart['nama_sparepart'] . " ditambah menjadi " . $new_qty . "!";
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
        'qty' => $qty
    ];
    $message = $qty . " x " . $sparepart['nama_sparepart'] . " berhasil ditambahkan ke keranjang!";
}

// Hitung total item di keranjang (total quantity)
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
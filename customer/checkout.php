<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4); // Hanya customer

$user_id = $_SESSION['user_id'];

// Handle Add to Cart from URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sparepart = fetch_assoc(query("SELECT * FROM sparepart WHERE id = $id"));
    
    if ($sparepart && $sparepart['stok'] > 0) {

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {

            if ($_SESSION['cart'][$id]['qty'] + 1 <= $sparepart['stok']) {
                $_SESSION['cart'][$id]['qty']++;
                $_SESSION['success'] = "Jumlah sparepart ditambah!";
            } else {
                $_SESSION['error'] = "Stok tidak mencukupi!";
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

            $_SESSION['success'] = "Sparepart berhasil ditambahkan ke keranjang!";
        }

    } else {
        $_SESSION['error'] = "Stok sparepart habis!";
    }

    header("Location: checkout.php");
    exit();
}

// Handle Clear Cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    $_SESSION['success'] = "Keranjang berhasil dikosongkan!";
    header("Location: beli.php");
    exit();
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];

    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        $_SESSION['success'] = "Item berhasil dihapus dari keranjang!";
    }

    header("Location: checkout.php");
    exit();
}

// Handle Update Quantity
if (isset($_POST['update_qty'])) {

    $id = $_POST['id'];
    $qty = $_POST['qty'];

    if (isset($_SESSION['cart'][$id]) && $qty > 0) {

        $sparepart = fetch_assoc(
            query("SELECT stok FROM sparepart WHERE id = $id")
        );

        if ($qty <= $sparepart['stok']) {

            $_SESSION['cart'][$id]['qty'] = $qty;
            $_SESSION['success'] = "Jumlah berhasil diupdate!";

        } else {

            $_SESSION['error'] =
                "Stok tidak mencukupi! Maksimal " . $sparepart['stok'];

        }
    }

    header("Location: checkout.php");
    exit();
}

// Handle Checkout
if (isset($_POST['checkout'])) {

    if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {

        $_SESSION['error'] = "Keranjang belanja kosong!";
        header("Location: beli.php");
        exit();
    }

    // Hitung total
    $total = 0;

    foreach ($_SESSION['cart'] as $item) {
        $total += $item['harga'] * $item['qty'];
    }
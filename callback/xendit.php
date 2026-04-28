<?php
include '../includes/koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

// log
file_put_contents('log.txt', json_encode($data) . PHP_EOL, FILE_APPEND);

// ambil data dari INVOICE webhook
$kode = $data['external_id'] ?? null;
$status = $data['status'] ?? null;
$metode = $data['payment_channel'] ?? 'Xendit';

// kalau sukses
if ($status == 'PAID' || $status == 'SETTLED') {
    query("UPDATE transaksi 
           SET status='dikemas', metode_pembayaran='$metode' 
           WHERE kode_transaksi='$kode'");
}
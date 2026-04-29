<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_GET['transaksi_id'])) {
    die("Transaksi tidak ditemukan");
}

$transaksi_id = $_GET['transaksi_id'];
$metode = $_GET['metode'] ?? 'QRIS'; // Ambil metode dari URL

$transaksi = fetch_assoc(query("SELECT * FROM transaksi WHERE id = $transaksi_id"));
$user = fetch_assoc(query("SELECT email, nama_lengkap FROM users WHERE id = " . $transaksi['user_id']));

if (!$transaksi) {
    die("Transaksi tidak ditemukan");
}

$external_id = $transaksi['kode_transaksi'];
$amount = $transaksi['total_harga'];
$user_email = $user['email'] ?? 'customer@gmail.com';
$user_name = $user['nama_lengkap'] ?? 'Customer';

// API Key Xendit (Development)
$apiKey = "xnd_development_Z19zovxgowMpF0FR6cO5mdrOY0RWmtTpbgeRWH5Dnpr9sB9TSHPQ2yE58JZ2BRm";

// Mapping metode pembayaran ke Xendit
$payment_methods = [
    'BCA' => 'BCA',
    'MANDIRI' => 'MANDIRI',
    'BNI' => 'BNI',
    'BRI' => 'BRI',
    'PERMATA' => 'PERMATA',
    'QRIS' => 'QRIS',
    'GoPay' => 'GOPAY',
    'OVO' => 'OVO',
    'DANA' => 'DANA'
];

$selected_payment = $payment_methods[$metode] ?? 'QRIS';

// Data untuk Xendit
$payload = [
    "external_id" => $external_id,
    "amount" => $amount,
    "payer_email" => $user_email,
    "description" => "Pembayaran Bengkel Jaya Abadi - " . $user_name,
    "success_redirect_url" => "http://localhost/ecomm-bengkel/customer/success.php?kode=$external_id",
    "failure_redirect_url" => "http://localhost/ecomm-bengkel/customer/riwayat.php",
    "currency" => "IDR",
    "metadata" => [
        "kode_transaksi" => $external_id,
        "user_id" => $transaksi['user_id'],
        "metode_yang_dipilih" => $metode
    ]
];

// Tambahkan payment_method jika bukan QRIS (QRIS default)
if ($selected_payment != 'QRIS') {
    $payload["payment_methods"] = [$selected_payment];
}

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.xendit.co/v2/invoices',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($apiKey . ':')
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$result = json_decode($response, true);

// Log untuk debugging
file_put_contents('xendit_log.txt', date('Y-m-d H:i:s') . ' - Request: ' . json_encode($payload) . PHP_EOL, FILE_APPEND);
file_put_contents('xendit_log.txt', date('Y-m-d H:i:s') . ' - Response: ' . $response . PHP_EOL, FILE_APPEND);

if ($httpCode != 200 || !isset($result['invoice_url'])) {
    echo "<h3>Gagal membuat invoice</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    exit();
}

// Simpan payment_method ke database sementara (akan diupdate webhook nanti)
query("UPDATE transaksi SET metode_pembayaran = '$metode' WHERE id = $transaksi_id");

// Redirect ke halaman pembayaran Xendit
header("Location: " . $result['invoice_url']);
exit();
?>
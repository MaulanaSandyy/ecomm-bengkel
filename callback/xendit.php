<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_GET['transaksi_id'])) {
    die("Transaksi tidak ditemukan");
}

if (!isset($_GET['metode'])) {
    die("Metode pembayaran tidak dipilih");
}

$transaksi_id = $_GET['transaksi_id'];
$metode_dipilih = $_GET['metode'];

$transaksi = fetch_assoc(query("SELECT * FROM transaksi WHERE id = $transaksi_id"));
$user = fetch_assoc(query("SELECT email, nama_lengkap, no_hp FROM users WHERE id = " . $transaksi['user_id']));

if (!$transaksi) {
    die("Transaksi tidak ditemukan");
}

$external_id = $transaksi['kode_transaksi'];
$amount = $transaksi['total_harga'];
$user_email = $user['email'] ?? 'customer@gmail.com';
$user_name = $user['nama_lengkap'] ?? 'Customer';
$user_phone = $user['no_hp'] ?? '';

// API Key Xendit
$apiKey = "xnd_development_Z19zovxgowMpF0FR6cO5mdrOY0RWmtTpbgeRWH5Dnpr9sB9TSHPQ2yE58JZ2BRm";

// ============================================
// MAPPING METODE PEMBAYARAN KE XENDIT
// ============================================

// Mapping untuk jenis pembayaran yang didukung Xendit
$payment_method_mapping = [
    // Bank Transfer (Virtual Account)
    'BCA' => ['type' => 'BANK_TRANSFER', 'channel' => 'BCA'],
    'Mandiri' => ['type' => 'BANK_TRANSFER', 'channel' => 'MANDIRI'],
    'BNI' => ['type' => 'BANK_TRANSFER', 'channel' => 'BNI'],
    'BRI' => ['type' => 'BANK_TRANSFER', 'channel' => 'BRI'],
    'CIMB Niaga' => ['type' => 'BANK_TRANSFER', 'channel' => 'CIMB'],
    'Permata Bank' => ['type' => 'BANK_TRANSFER', 'channel' => 'PERMATA'],
    'BSI' => ['type' => 'BANK_TRANSFER', 'channel' => 'BSI'],
    'Bank Muamalat' => ['type' => 'BANK_TRANSFER', 'channel' => 'MUAMALAT'],
    'Bank BJB' => ['type' => 'BANK_TRANSFER', 'channel' => 'BJB'],
    'Bank Sampoerna' => ['type' => 'BANK_TRANSFER', 'channel' => 'SAMPOERNA'],
    'Neobank' => ['type' => 'BANK_TRANSFER', 'channel' => 'NEOBANK'],
    'Other Banks' => ['type' => 'BANK_TRANSFER', 'channel' => 'OTHER'],
    
    // E-Wallet
    'GoPay' => ['type' => 'EWALLET', 'channel' => 'GOPAY'],
    'OVO' => ['type' => 'EWALLET', 'channel' => 'OVO'],
    'DANA' => ['type' => 'EWALLET', 'channel' => 'DANA'],
    'ShopeePay' => ['type' => 'EWALLET', 'channel' => 'SHOPEEPAY'],
    'LinkAja' => ['type' => 'EWALLET', 'channel' => 'LINKAJA'],
    
    // Kartu Kredit
    'Visa' => ['type' => 'CREDIT_CARD', 'channel' => 'VISA'],
    'Mastercard' => ['type' => 'CREDIT_CARD', 'channel' => 'MASTERCARD'],
    'JCB' => ['type' => 'CREDIT_CARD', 'channel' => 'JCB'],
    'American Express' => ['type' => 'CREDIT_CARD', 'channel' => 'AMEX'],
    
    // QRIS
    'QRIS' => ['type' => 'QR_CODE', 'channel' => 'QRIS'],
    
    // Direct Debit
    'BRI Direct Debit' => ['type' => 'DIRECT_DEBIT', 'channel' => 'BRI'],
    'Mandiri Direct Debit' => ['type' => 'DIRECT_DEBIT', 'channel' => 'MANDIRI'],
    
    // Outlet Ritel
    'Alfamart / Alfamidi' => ['type' => 'RETAIL_OUTLET', 'channel' => 'ALFAMART'],
    'Indomaret' => ['type' => 'RETAIL_OUTLET', 'channel' => 'INDOMARET']
];

// Mapping metode ke format yang tepat untuk Xendit
$channel_code_mapping = [
    'BCA' => 'BCA',
    'Mandiri' => 'MANDIRI_BILL',
    'BNI' => 'BNI',
    'BRI' => 'BRI',
    'CIMB Niaga' => 'CIMB',
    'Permata Bank' => 'PERMATA',
    'BSI' => 'BSI',
    'Bank Muamalat' => 'MUAMALAT',
    'Bank BJB' => 'BJB',
    'GoPay' => 'ID_GOPAY',
    'OVO' => 'ID_OVO',
    'DANA' => 'ID_DANA',
    'ShopeePay' => 'ID_SHOPEEPAY',
    'QRIS' => 'QRIS',
    'Visa' => 'VISA',
    'Mastercard' => 'MASTERCARD'
];

// Cek apakah metode yang dipilih didukung
$mapping = $payment_method_mapping[$metode_dipilih] ?? null;
$channel_code = $channel_code_mapping[$metode_dipilih] ?? null;

// ============================================
// BUILD PAYLOAD SESUAI METODE YANG DIPILIH
// ============================================

// Base payload untuk semua metode
$payload = [
    "external_id" => $external_id,
    "amount" => $amount,
    "payer_email" => $user_email,
    "description" => "Pembayaran Bengkel Jaya Abadi - " . $user_name,
    "success_redirect_url" => "http://localhost/ecomm-bengkel/customer/success.php?kode=$external_id",
    "failure_redirect_url" => "http://localhost/ecomm-bengkel/customer/riwayat.php",
    "currency" => "IDR"
];

// Tambahkan payment_method berdasarkan tipe
if ($mapping) {
    switch ($mapping['type']) {
        case 'BANK_TRANSFER':
            // Bank Transfer - Virtual Account
            $payload['payment_methods'] = ['BANK_TRANSFER'];
            $payload['bank_code'] = $mapping['channel'];
            break;
            
        case 'EWALLET':
            // E-Wallet
            $payload['payment_methods'] = ['EWALLET'];
            $payload['channel_code'] = $channel_code;
            break;
            
        case 'CREDIT_CARD':
            // Kartu Kredit
            $payload['payment_methods'] = ['CREDIT_CARD'];
            break;
            
        case 'QR_CODE':
            // QRIS
            $payload['payment_methods'] = ['QR_CODE'];
            $payload['channel_code'] = 'QRIS';
            break;
            
        case 'DIRECT_DEBIT':
            // Direct Debit
            $payload['payment_methods'] = ['DIRECT_DEBIT'];
            $payload['channel_code'] = $channel_code;
            break;
            
        case 'RETAIL_OUTLET':
            // Retail Outlet
            $payload['payment_methods'] = ['RETAIL_OUTLET'];
            $payload['channel_code'] = $mapping['channel'];
            break;
            
        default:
            // Default ke semua metode
            $payload['payment_methods'] = ['BANK_TRANSFER', 'EWALLET', 'QR_CODE', 'CREDIT_CARD'];
            break;
    }
} else {
    // Jika metode tidak dikenal, tampilkan semua metode
    $payload['payment_methods'] = ['BANK_TRANSFER', 'EWALLET', 'QR_CODE', 'CREDIT_CARD'];
}

// Tambahkan success_redirect_url untuk semua
$payload['success_redirect_url'] = "http://localhost/ecomm-bengkel/customer/success.php?kode=$external_id";

// Tambahan untuk E-Wallet tertentu
if ($metode_dipilih == 'GoPay') {
    $payload['channel_code'] = 'ID_GOPAY';
    $payload['payment_methods'] = ['EWALLET'];
} elseif ($metode_dipilih == 'OVO') {
    $payload['channel_code'] = 'ID_OVO';
    $payload['payment_methods'] = ['EWALLET'];
    if ($user_phone) {
        $payload['mobile_number'] = $user_phone;
    }
} elseif ($metode_dipilih == 'DANA') {
    $payload['channel_code'] = 'ID_DANA';
    $payload['payment_methods'] = ['EWALLET'];
} elseif ($metode_dipilih == 'ShopeePay') {
    $payload['channel_code'] = 'ID_SHOPEEPAY';
    $payload['payment_methods'] = ['EWALLET'];
} elseif ($metode_dipilih == 'QRIS') {
    $payload['payment_methods'] = ['QR_CODE'];
    $payload['channel_code'] = 'QRIS';
}

// Simpan metode pembayaran ke database
query("UPDATE transaksi SET metode_pembayaran = '$metode_dipilih' WHERE id = $transaksi_id");

// ============================================
// KIRIM REQUEST KE XENDIT
// ============================================

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
file_put_contents('xendit_log.txt', date('Y-m-d H:i:s') . ' - Metode: ' . $metode_dipilih . PHP_EOL, FILE_APPEND);
file_put_contents('xendit_log.txt', date('Y-m-d H:i:s') . ' - Payload: ' . json_encode($payload) . PHP_EOL, FILE_APPEND);
file_put_contents('xendit_log.txt', date('Y-m-d H:i:s') . ' - Response: ' . $response . PHP_EOL, FILE_APPEND);

if ($httpCode != 200 || !isset($result['invoice_url'])) {
    echo "<div style='text-align: center; padding: 50px; font-family: Arial;'>";
    echo "<h1 style='color: #ef4444;'>Gagal Membuat Invoice</h1>";
    echo "<p>Metode: " . htmlspecialchars($metode_dipilih) . "</p>";
    echo "<pre style='background: #f3f4f6; padding: 15px; border-radius: 10px; text-align: left;'>";
    print_r($result);
    echo "</pre>";
    echo "<a href='pembayaran.php?transaksi_id=$transaksi_id' style='display: inline-block; margin-top: 20px; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 10px;'>Kembali ke Pembayaran</a>";
    echo "</div>";
    exit();
}

// Redirect ke halaman pembayaran Xendit (sudah otomatis memilih metode)
header("Location: " . $result['invoice_url']);
exit();
?>
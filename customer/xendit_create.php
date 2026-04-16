<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_GET['transaksi_id'])) {
    die("Transaksi tidak ditemukan");
}

$transaksi_id = $_GET['transaksi_id'];

$data = fetch_assoc(query("SELECT * FROM transaksi WHERE id = $transaksi_id"));

$external_id = $data['kode_transaksi'];
$amount = $data['total_harga'];

$apiKey = "xnd_development_Z19zovxgowMpF0FR6cO5mdrOY0RWmtTpbgeRWH5Dnpr9sB9TSHPQ2yE58JZ2BRm";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.xendit.co/v2/invoices',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS => json_encode([
    "external_id" => $external_id,
    "amount" => $amount,
    "payer_email" => "customer@gmail.com",
    "description" => "Pembayaran Bengkel",

    "success_redirect_url" => "http://localhost/ecomm-bengkel/customer/success.php?kode=$external_id",

    "metadata" => [
        "kode_transaksi" => $external_id
    ]
]),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($apiKey . ':')
  ),
));

$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);

if (!isset($result['invoice_url'])) {
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    exit();
}

header("Location: " . $result['invoice_url']);
exit();
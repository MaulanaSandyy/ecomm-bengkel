<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

$id = $_GET['id'];

query("UPDATE transaksi SET status='selesai' WHERE id=$id AND user_id=".$_SESSION['user_id']);

header("Location: riwayat.php");
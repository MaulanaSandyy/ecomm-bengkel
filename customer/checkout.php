<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4); // Hanya customer

$user_id = $_SESSION['user_id'];
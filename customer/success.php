<?php
include '../includes/koneksi.php';

$kode = $_GET['kode'];

// ambil data saja (tanpa update)
$data = fetch_assoc(query("SELECT * FROM transaksi WHERE kode_transaksi='$kode'"));
?>

<h2>Pembayaran Berhasil 🎉</h2>
<p>Kode Transaksi: <?php echo $kode; ?></p>
<p>Status: <?php echo $data['status']; ?></p>

<a href="riwayat.php">Lihat Riwayat</a>
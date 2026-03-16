<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

$title = "Riwayat Transaksi";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

$transaksi = query("
SELECT * FROM transaksi
WHERE user_id = $user_id
ORDER BY created_at DESC
");
?>

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 px-0">
<div class="sidebar">

<h4 class="text-white mb-4">
<i class="fas fa-user me-2"></i>Menu Customer
</h4>

<a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
<a href="beli.php"><i class="fas fa-shopping-cart me-2"></i>Beli Sparepart</a>
<a href="booking.php"><i class="fas fa-calendar-alt me-2"></i>Booking</a>
<a href="checkout.php"><i class="fas fa-credit-card me-2"></i>Checkout</a>
<a href="pembayaran.php"><i class="fas fa-qrcode me-2"></i>Pembayaran</a>
<a href="riwayat.php" class="active"><i class="fas fa-history me-2"></i>Riwayat</a>

</div>
</div>

<!-- Main -->
<div class="col-md-9 col-lg-10 p-4">

<h2 class="mb-4">Riwayat Transaksi</h2>

<div class="card">

<div class="card-header bg-primary text-white">
<h5 class="mb-0">
<i class="fas fa-history me-2"></i>Data Transaksi Anda
</h5>
</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover">

<thead>
<tr>
<th>Kode Transaksi</th>
<th>Total</th>
<th>Status</th>
<th>Pembayaran</th>
<th>Tanggal</th>
</tr>
</thead>

<tbody>

<?php if(num_rows($transaksi) > 0): ?>

<?php while($row = fetch_assoc($transaksi)): ?>

<tr>

<td>
<strong><?php echo $row['kode_transaksi']; ?></strong>
</td>

<td>
Rp <?php echo number_format($row['total_harga'],0,',','.'); ?>
</td>

<td>

<?php
$status_color = [
'pending' => 'warning',
'lunas' => 'success',
'batal' => 'danger'
];
?>

<span class="badge bg-<?php echo $status_color[$row['status']]; ?>">
<?php echo ucfirst($row['status']); ?>
</span>

</td>

<td><?php echo $row['metode_pembayaran'] ?: 'QRIS'; ?></td>

<td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="5" class="text-center text-muted">
Belum ada transaksi
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>
</div>

<?php include '../includes/footer.php'; ?>
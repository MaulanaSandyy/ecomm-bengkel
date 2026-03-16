<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

$title = "Beli Sparepart";
include '../includes/header.php';

$sparepart = query("SELECT * FROM sparepart ORDER BY id DESC");
?>

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 px-0">

<div class="sidebar">

<h4 class="text-white mb-4">
<i class="fas fa-user me-2"></i>Menu
</h4>

<a href="index.php">
<i class="fas fa-home me-2"></i>Dashboard
</a>

<a href="beli.php" class="active">
<i class="fas fa-shopping-cart me-2"></i>Beli Sparepart
</a>

<a href="booking.php">
<i class="fas fa-calendar-alt me-2"></i>Booking Service
</a>

<a href="checkout.php">
<i class="fas fa-credit-card me-2"></i>Checkout
</a>

<a href="pembayaran.php">
<i class="fas fa-qrcode me-2"></i>Pembayaran
</a>

<a href="riwayat.php">
<i class="fas fa-history me-2"></i>Riwayat Transaksi
</a>

</div>

</div>

<!-- Main Content -->
<div class="col-md-9 col-lg-10 p-4">

<h2 class="mb-4">Beli Sparepart</h2>

<div class="row">

<?php while($row = fetch_assoc($sparepart)): ?>

<div class="col-lg-4 col-md-6 mb-4">

<div class="card h-100">

<img src="../uploads/sparepart/<?php echo $row['gambar'] ?: 'default.jpg'; ?>"
class="card-img-top"
style="height:200px;object-fit:cover">

<div class="card-body">

<h5 class="card-title">
<?php echo $row['nama_sparepart']; ?>
</h5>

<p class="card-text text-muted">
<?php echo substr($row['deskripsi'],0,80); ?>...
</p>

<p class="text-primary fw-bold">
Rp <?php echo number_format($row['harga'],0,',','.'); ?>
</p>

<p class="text-muted">
Stok: <?php echo $row['stok']; ?> |
Merek: <?php echo $row['merek']; ?>
</p>

<a href="checkout.php?id=<?php echo $row['id']; ?>"
class="btn btn-success w-100">
<i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
</a>

</div>

</div>

</div>

<?php endwhile; ?>

</div>

</div>

</div>
</div>

<?php include '../includes/footer.php'; ?>
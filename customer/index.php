<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4); // hanya customer

$title = "Dashboard Customer";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <h4 class="text-white mb-4">
                    <i class="fas fa-user me-2"></i>Menu
                </h4>

                <a href="index.php" class="active">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>

                <a href="beli.php">
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

            <h2 class="mb-4">Dashboard</h2>

            <!-- Card menu sederhana -->
            <div class="row">

                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                        <h5>Beli Sparepart</h5>
                        <a href="beli.php" class="btn btn-success mt-2">Buka</a>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                        <h5>Booking Service</h5>
                        <a href="booking.php" class="btn btn-primary mt-2">Buka</a>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <i class="fas fa-credit-card fa-3x text-warning mb-3"></i>
                        <h5>Pembayaran</h5>
                        <a href="pembayaran.php" class="btn btn-warning mt-2">Buka</a>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card text-center p-4">
                        <i class="fas fa-history fa-3x text-info mb-3"></i>
                        <h5>Riwayat Transaksi</h5>
                        <a href="riwayat.php" class="btn btn-info mt-2">Lihat</a>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card text-center p-4">
                        <i class="fas fa-credit-card fa-3x text-danger mb-3"></i>
                        <h5>Checkout</h5>
                        <a href="checkout.php" class="btn btn-danger mt-2">Buka</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
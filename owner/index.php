<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(2); // hanya owner

$title = "Dashboard Owner";
include '../includes/header.php';

// Statistik
$total_transaksi = num_rows(query("SELECT * FROM transaksi"));
$total_pendapatan = fetch_assoc(query("SELECT SUM(total_harga) as total FROM transaksi WHERE status='lunas'"))['total'];
$total_customer = num_rows(query("SELECT * FROM users WHERE role_id=4"));
$total_booking = num_rows(query("SELECT * FROM booking"));
?>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <h4 class="text-white mb-4">
                    <i class="fas fa-user-tie me-2"></i>Menu Owner
                </h4>

                <a href="index.php" class="active">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>

                <a href="transaksi.php">
                    <i class="fas fa-credit-card me-2"></i>Transaksi
                </a>

                <a href="laporan.php">
                    <i class="fas fa-chart-bar me-2"></i>Laporan Keuangan
                </a>

            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Dashboard Owner</h2>

            <div class="row">

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Total Transaksi</h6>
                                <h2 class="text-white"><?php echo $total_transaksi; ?></h2>
                            </div>
                            <i class="fas fa-credit-card fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card" style="background: linear-gradient(135deg,#10b981,#059669);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Pendapatan</h6>
                                <h2 class="text-white">
                                    Rp <?php echo number_format($total_pendapatan,0,',','.'); ?>
                                </h2>
                            </div>
                            <i class="fas fa-money-bill-wave fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card" style="background: linear-gradient(135deg,#3b82f6,#2563eb);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Customer</h6>
                                <h2 class="text-white"><?php echo $total_customer; ?></h2>
                            </div>
                            <i class="fas fa-users fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Booking</h6>
                                <h2 class="text-white"><?php echo $total_booking; ?></h2>
                            </div>
                            <i class="fas fa-calendar-alt fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Shortcut -->
            <div class="row mt-4">

                <div class="col-md-6 mb-4">
                    <div class="card text-center p-4">
                        <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                        <h5>Data Transaksi</h5>
                        <a href="transaksi.php" class="btn btn-primary mt-2">Lihat</a>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card text-center p-4">
                        <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                        <h5>Laporan Keuangan</h5>
                        <a href="laporan.php" class="btn btn-success mt-2">Lihat</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
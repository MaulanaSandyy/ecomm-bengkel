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

<div class="container-fluid px-0 px-lg-4 mt-2" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">

        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-tie me-2"></i>Menu Owner
                </h5>

                <a href="index.php" class="active">
                    <i class="fas fa-home"></i>Dashboard
                </a>

                <a href="transaksi.php">
                    <i class="fas fa-credit-card"></i>Transaksi
                </a>

                <a href="laporan.php">
                    <i class="fas fa-chart-bar"></i>Laporan Keuangan
                </a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Dashboard Owner</h3>
                    <p class="text-muted small mb-0">Ringkasan performa dan statistik bengkel Anda hari ini.</p>
                </div>
                <div class="mt-3 mt-md-0 text-muted small fw-medium">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i> <?php echo date('d F Y'); ?>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-xl-3 col-sm-6">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--primary-gradient);">
                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Total Transaksi</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_transaksi; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-credit-card fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--success-gradient);">
                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Pendapatan</p>
                                <h3 class="text-white fw-bold mb-0 text-truncate" style="max-width: 140px;" data-bs-toggle="tooltip" title="Rp <?php echo number_format($total_pendapatan,0,',','.'); ?>">
                                    Rp <?php echo number_format($total_pendapatan,0,',','.'); ?>
                                </h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-money-bill-wave fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="dashboard-card shadow-sm h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Customer Aktif</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_customer; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--warning-gradient);">
                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Total Booking</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_booking; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-check fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 text-dark">Akses Cepat</h5>
            <div class="row g-4">
                
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 text-center p-5 border-0">
                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-4 transition-all" style="width: 80px; height: 80px; background: rgba(79, 70, 229, 0.1);">
                                <i class="fas fa-cash-register fa-2x" style="color: var(--primary-color);"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Kelola Transaksi</h5>
                            <p class="text-muted small mb-4">Pantau dan verifikasi pembayaran dari pelanggan bengkel.</p>
                            <a href="transaksi.php" class="btn btn-primary px-4 rounded-pill shadow-sm w-75">Lihat Data <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 text-center p-5 border-0">
                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-4 transition-all" style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1);">
                                <i class="fas fa-file-invoice-dollar fa-2x text-success"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Laporan Keuangan</h5>
                            <p class="text-muted small mb-4">Cetak dan analisis riwayat pendapatan bengkel secara berkala.</p>
                            <a href="laporan.php" class="btn btn-success px-4 rounded-pill shadow-sm w-75">Lihat Laporan <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
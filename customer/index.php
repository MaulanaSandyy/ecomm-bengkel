<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4); // hanya customer

$title = "Dashboard Customer";
include '../includes/header.php';
?>

<div class="container-fluid px-0 px-lg-4" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">

        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-circle me-2"></i>Menu Pelanggan
                </h5>

                <a href="index.php" class="active"><i class="fas fa-home"></i>Dashboard</a>
                <a href="beli.php"><i class="fas fa-shopping-bag"></i>Beli Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-check"></i>Booking Service</a>
                <a href="checkout.php"><i class="fas fa-shopping-cart"></i>Keranjang / Checkout</a>
                <a href="pembayaran.php"><i class="fas fa-wallet"></i>Pembayaran</a>
                <a href="riwayat.php"><i class="fas fa-receipt"></i>Riwayat Transaksi</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a8a 100%);">
                <div class="card-body p-4 p-lg-5 text-white position-relative">
                    <i class="fas fa-car-side fa-10x position-absolute text-white opacity-10" style="bottom: -20px; right: -20px; transform: rotate(-15deg);"></i>
                    
                    <div class="position-relative z-1">
                        <h2 class="fw-bold mb-2">Selamat Datang, <?php echo isset($_SESSION['nama_lengkap']) ? explode(' ', $_SESSION['nama_lengkap'])[0] : 'Pelanggan'; ?>! 👋</h2>
                        <p class="mb-0 opacity-75 fs-6">Mau perawatan rutin atau cari sparepart ori? Semua bisa diatur dari sini. Pilih menu layanan di bawah untuk memulai!</p>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold text-dark mb-3">Layanan Cepat</h4>

            <div class="row g-4">

                <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="50">
                    <a href="beli.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
                            <div class="card-body p-4 text-center">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Beli Sparepart</h5>
                                <p class="text-muted small mb-0">Cari dan pesan sparepart original untuk kendaraan Anda.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                    <a href="booking.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
                            <div class="card-body p-4 text-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Booking Service</h5>
                                <p class="text-muted small mb-0">Jadwalkan perbaikan atau service rutin tanpa perlu antre.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="150">
                    <a href="pembayaran.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
                            <div class="card-body p-4 text-center">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-qrcode fa-2x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Pembayaran (QRIS)</h5>
                                <p class="text-muted small mb-0">Lakukan konfirmasi pembayaran dengan aman & cepat.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="fw-bold text-dark mb-1"><i class="fas fa-history text-info me-2"></i>Riwayat Transaksi</h5>
                                <p class="text-muted small mb-0">Pantau status pesanan dan history service kendaraan Anda.</p>
                            </div>
                            <a href="riwayat.php" class="btn btn-info text-white rounded-pill px-4 shadow-sm">Lihat</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="fw-bold text-dark mb-1"><i class="fas fa-shopping-bag text-danger me-2"></i>Keranjang Saya</h5>
                                <p class="text-muted small mb-0">Selesaikan pesanan sparepart atau jasa yang tertunda.</p>
                            </div>
                            <a href="checkout.php" class="btn btn-danger text-white rounded-pill px-4 shadow-sm">Buka</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
/* Efek hover khusus halaman customer agar lebih interaktif */
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>

<?php include '../includes/footer.php'; ?>
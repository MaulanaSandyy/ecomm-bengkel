<?php
session_start();
include 'includes/koneksi.php';

$title = "Beranda";
include 'includes/header.php';

// Ambil data jasa
$jasa = query("SELECT * FROM jasa ORDER BY id DESC LIMIT 6");

// Ambil data sparepart
$sparepart = query("SELECT * FROM sparepart ORDER BY id DESC LIMIT 6");

// Ambil profil bengkel
$profil = fetch_assoc(query("SELECT * FROM profil_bengkel WHERE id = 1"));
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-4">Selamat Datang di <?php echo $profil['nama_bengkel']; ?></h1>
                <p class="lead mb-4"><?php echo $profil['deskripsi']; ?></p>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="auth/register.php" class="btn btn-warning btn-lg me-3"><i class="fas fa-user-plus me-2"></i>Daftar Sekarang</a>
                <?php endif; ?>
                <a href="#jasa" class="btn btn-outline-light btn-lg"><i class="fas fa-arrow-down me-2"></i>Lihat Layanan</a>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="assets/img/hero-car.png" alt="Hero Image" class="img-fluid animate__animated animate__bounceIn">
            </div>
        </div>
    </div>
</section>

<!-- Statistik Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <?php
            $total_jasa = num_rows(query("SELECT * FROM jasa"));
            $total_sparepart = num_rows(query("SELECT * FROM sparepart"));
            $total_customer = num_rows(query("SELECT * FROM users WHERE role_id = 4"));
            $total_booking = num_rows(query("SELECT * FROM booking"));
            ?>
            <div class="col-md-3 mb-4" data-aos="zoom-in">
                <div class="card text-center p-4">
                    <i class="fas fa-wrench fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold"><?php echo $total_jasa; ?></h3>
                    <p class="text-muted">Jasa Service</p>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card text-center p-4">
                    <i class="fas fa-oil-can fa-3x text-success mb-3"></i>
                    <h3 class="fw-bold"><?php echo $total_sparepart; ?></h3>
                    <p class="text-muted">Sparepart</p>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="card text-center p-4">
                    <i class="fas fa-users fa-3x text-warning mb-3"></i>
                    <h3 class="fw-bold"><?php echo $total_customer; ?></h3>
                    <p class="text-muted">Customer</p>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="card text-center p-4">
                    <i class="fas fa-calendar-check fa-3x text-info mb-3"></i>
                    <h3 class="fw-bold"><?php echo $total_booking; ?></h3>
                    <p class="text-muted">Booking</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Jasa Section -->
<section id="jasa" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5" data-aos="fade-up">Layanan Jasa Kami</h2>
        <div class="row">
            <?php while ($row = fetch_assoc($jasa)): ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                <div class="card h-100">
                    <img src="uploads/jasa/<?php echo $row['gambar'] ?: 'default.jpg'; ?>" class="card-img-top" alt="<?php echo $row['nama_jasa']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nama_jasa']; ?></h5>
                        <p class="card-text text-muted"><?php echo substr($row['deskripsi'], 0, 100); ?>...</p>
                        <p class="text-primary fw-bold">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        <p class="text-muted"><i class="fas fa-clock me-1"></i>Estimasi: <?php echo $row['estimasi_waktu']; ?></p>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
                            <a href="customer/booking.php?jasa_id=<?php echo $row['id']; ?>" class="btn btn-primary w-100">Booking Sekarang</a>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-primary w-100">Login untuk Booking</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="#" class="btn btn-outline-primary btn-lg">Lihat Semua Jasa</a>
        </div>
    </div>
</section>

<!-- Sparepart Section -->
<section id="sparepart" class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5" data-aos="fade-up">Sparepart Original</h2>
        <div class="row">
            <?php while ($row = fetch_assoc($sparepart)): ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                <div class="card h-100">
                    <img src="uploads/sparepart/<?php echo $row['gambar'] ?: 'default.jpg'; ?>" class="card-img-top" alt="<?php echo $row['nama_sparepart']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nama_sparepart']; ?></h5>
                        <p class="card-text text-muted"><?php echo substr($row['deskripsi'], 0, 100); ?>...</p>
                        <p class="text-primary fw-bold">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        <p class="text-muted"><i class="fas fa-box me-1"></i>Stok: <?php echo $row['stok']; ?> | Merek: <?php echo $row['merek']; ?></p>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
                            <a href="customer/beli.php?id=<?php echo $row['id']; ?>" class="btn btn-success w-100">Beli Sekarang</a>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-success w-100">Login untuk Membeli</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white" data-aos="zoom-in">
        <h2 class="fw-bold mb-4">Siap untuk Service Mobil Anda?</h2>
        <p class="lead mb-4">Booking sekarang dan dapatkan pelayanan terbaik dari teknisi profesional kami</p>
        <a href="#jasa" class="btn btn-warning btn-lg me-3"><i class="fas fa-calendar-alt me-2"></i>Booking Jasa</a>
        <a href="#sparepart" class="btn btn-outline-light btn-lg"><i class="fas fa-shopping-cart me-2"></i>Beli Sparepart</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
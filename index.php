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

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <div class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill">
                    <i class="fas fa-star text-warning me-1"></i> Layanan Bengkel Terpercaya
                </div>
                <h1 class="display-4 fw-bold mb-4 lh-sm">Selamat Datang di <br><span class="text-warning"><?php echo $profil['nama_bengkel']; ?></span></h1>
                <p class="lead mb-5 opacity-75 fw-light" style="max-width: 500px;"><?php echo $profil['deskripsi']; ?></p>
                
                <div class="d-flex flex-wrap gap-3">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="auth/register.php" class="btn btn-warning btn-lg px-4"><i class="fas fa-user-plus me-2"></i>Daftar Sekarang</a>
                    <?php endif; ?>
                    <a href="#jasa" class="btn btn-outline-light btn-lg px-4"><i class="fas fa-arrow-down me-2"></i>Lihat Layanan</a>
                </div>
            </div>
            <div class="col-lg-6 position-relative" data-aos="fade-left">
                <div class="position-absolute top-50 start-50 translate-middle w-75 h-75 bg-white bg-opacity-10 rounded-circle blur-3xl" style="filter: blur(40px); z-index: 0;"></div>
                <img src="assets/img/banner.png" alt="Hero Image" class="img-fluid position-relative animate__animated animate__pulse animate__infinite animate__slower" style="z-index: 1; drop-shadow: 0 20px 30px rgba(0,0,0,0.2);">
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="margin-top: -60px; position: relative; z-index: 10;">
    <div class="container">
        <div class="row g-4">
            <?php
            $total_jasa = num_rows(query("SELECT * FROM jasa"));
            $total_sparepart = num_rows(query("SELECT * FROM sparepart"));
            $total_customer = num_rows(query("SELECT * FROM users WHERE role_id = 4"));
            $total_booking = num_rows(query("SELECT * FROM booking"));
            ?>
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="card h-100 border-0 shadow-sm p-4 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: rgba(79, 70, 229, 0.1);">
                        <i class="fas fa-wrench fa-2x" style="color: var(--primary-color);"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $total_jasa; ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Jasa Service</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm p-4 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1);">
                        <i class="fas fa-oil-can fa-2x text-success"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $total_sparepart; ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Sparepart</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm p-4 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: rgba(245, 158, 11, 0.1);">
                        <i class="fas fa-users fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $total_customer; ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Customer</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm p-4 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: rgba(14, 165, 233, 0.1);">
                        <i class="fas fa-calendar-check fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $total_booking; ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Total Booking</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="jasa" class="py-5 mt-4">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-bold text-uppercase small letter-spacing-2">Pelayanan Kami</span>
            <h2 class="fw-extrabold mt-2 text-dark">Layanan Jasa Terbaik</h2>
            <div class="mx-auto mt-3 rounded" style="width: 60px; height: 4px; background: var(--primary-color);"></div>
        </div>
        
        <div class="row g-4">
            <?php while ($row = fetch_assoc($jasa)): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card h-100 d-flex flex-column border-0">
                    <div class="position-relative overflow-hidden p-2">
                        <img src="uploads/jasa/<?php echo $row['gambar'] ?: 'default.jpg'; ?>" class="card-img-top rounded-4" alt="<?php echo $row['nama_jasa']; ?>" style="height: 220px; object-fit: cover;">
                        <span class="badge bg-primary position-absolute top-0 end-0 m-4 shadow-sm px-3 py-2"><i class="fas fa-clock me-1"></i> <?php echo $row['estimasi_waktu']; ?></span>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-2"><?php echo $row['nama_jasa']; ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?php echo substr($row['deskripsi'], 0, 90); ?>...</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 mb-4 pt-3 border-top">
                            <span class="text-muted small">Mulai dari</span>
                            <span class="fs-5 fw-bold text-primary">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
                            <a href="customer/booking.php?jasa_id=<?php echo $row['id']; ?>" class="btn btn-primary w-100 mt-auto"><i class="fas fa-calendar-plus me-2"></i>Booking Sekarang</a>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-outline-primary w-100 mt-auto"><i class="fas fa-sign-in-alt me-2"></i>Login untuk Booking</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="#" class="btn btn-outline-primary px-5 py-3 fw-bold">Lihat Semua Layanan <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<section id="sparepart" class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-success fw-bold text-uppercase small letter-spacing-2">Katalog Toko</span>
            <h2 class="fw-extrabold mt-2 text-dark">Sparepart Original</h2>
            <div class="mx-auto mt-3 rounded" style="width: 60px; height: 4px; background: var(--success-gradient);"></div>
        </div>

        <div class="row g-4">
            <?php while ($row = fetch_assoc($sparepart)): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card h-100 d-flex flex-column border-0">
                    <div class="position-relative p-3 text-center bg-light m-2 rounded-4">
                        <img src="uploads/sparepart/<?php echo $row['gambar'] ?: 'default.jpg'; ?>" class="img-fluid" alt="<?php echo $row['nama_sparepart']; ?>" style="height: 180px; object-fit: contain; mix-blend-mode: multiply;">
                        <?php if($row['stok'] > 0): ?>
                            <span class="badge bg-success position-absolute top-0 start-0 m-3 px-3 py-2 shadow-sm">Stok: <?php echo $row['stok']; ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2 shadow-sm">Habis</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-dark mb-0"><?php echo $row['nama_sparepart']; ?></h5>
                        </div>
                        <p class="text-muted small mb-3"><i class="fas fa-tag me-1 text-secondary"></i> <?php echo $row['merek']; ?></p>
                        
                        <p class="card-text text-muted small flex-grow-1"><?php echo substr($row['deskripsi'], 0, 80); ?>...</p>
                        
                        <h4 class="fw-bold text-dark mt-2 mb-4">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></h4>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
                            <a href="customer/beli.php?id=<?php echo $row['id']; ?>" class="btn btn-success w-100 mt-auto <?php echo ($row['stok'] <= 0) ? 'disabled' : ''; ?>"><i class="fas fa-shopping-cart me-2"></i>Beli Sekarang</a>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-outline-success w-100 mt-auto"><i class="fas fa-sign-in-alt me-2"></i>Login untuk Membeli</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="py-5 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: var(--primary-gradient); z-index: -2;"></div>
    <div class="position-absolute top-0 end-0 w-50 h-100 bg-white opacity-10 rounded-start-pill" style="z-index: -1; transform: translateX(20%);"></div>
    
    <div class="container text-center text-white py-5" data-aos="zoom-in">
        <h2 class="display-5 fw-bold mb-3">Mobil Anda Butuh Perawatan?</h2>
        <p class="lead mb-5 fw-light opacity-75 mx-auto" style="max-width: 600px;">Jangan tunggu sampai rusak. Booking sekarang dan dapatkan pelayanan terbaik langsung dari teknisi profesional kami.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#jasa" class="btn btn-warning btn-lg px-5 py-3 shadow-lg"><i class="fas fa-calendar-alt me-2"></i>Booking Jadwal</a>
            <a href="#sparepart" class="btn btn-outline-light btn-lg px-5 py-3"><i class="fas fa-box-open me-2"></i>Cari Sparepart</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
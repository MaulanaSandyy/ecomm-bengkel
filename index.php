<?php
// File: index.php
$title = "Beranda";
include 'includes/koneksi.php';
include 'includes/header.php';

// Ambil data profil bengkel
$query_profil = "SELECT * FROM profil_bengkel WHERE id_profil = 1";
$result_profil = query($query_profil);
$profil = fetch_array($result_profil);

// Ambil data jasa service (4 data terbaru)
$query_jasa = "SELECT * FROM jasa WHERE status = 'aktif' ORDER BY id_jasa DESC LIMIT 4";
$result_jasa = query($query_jasa);
$jasa_list = fetch_all($result_jasa);

// Ambil data sparepart populer (stok terbanyak)
$query_sparepart = "SELECT * FROM sparepart WHERE status = 'tersedia' ORDER BY stok DESC LIMIT 4";
$result_sparepart = query($query_sparepart);
$sparepart_list = fetch_all($result_sparepart);
?>

<!-- Hero Section -->
<section class="hero-section d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 text-white" data-aos="fade-right">
                <h1 class="display-3 fw-bold mb-4"><?php echo $profil['nama_bengkel']; ?></h1>
                <p class="lead mb-4"><?php echo $profil['deskripsi']; ?></p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if(!isset($_SESSION['id_user'])): ?>
                        <a href="register.php" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-semibold">
                            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                        </a>
                    <?php endif; ?>
                    <a href="booking.php" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 fw-semibold">
                        <i class="bi bi-calendar-check me-2"></i>Booking Service
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="assets/images/bengkel-hero.jpg" alt="Bengkel Mobil" class="img-fluid rounded-4 shadow-glow" onerror="this.src='https://via.placeholder.com/600x400?text=Bengkel+Mobil'">
            </div>
        </div>
    </div>
</section>

<!-- Statistik Section -->
<section class="container py-5">
    <div class="row g-4 text-center">
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
            <div class="stat-card bg-gradient-primary">
                <i class="bi bi-tools display-1"></i>
                <h3 class="fw-bold mt-3">10+</h3>
                <p class="mb-0">Jasa Service</p>
            </div>
        </div>
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
            <div class="stat-card bg-gradient-success">
                <i class="bi bi-box display-1"></i>
                <h3 class="fw-bold mt-3">50+</h3>
                <p class="mb-0">Sparepart</p>
            </div>
        </div>
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
            <div class="stat-card bg-gradient-warning">
                <i class="bi bi-people display-1"></i>
                <h3 class="fw-bold mt-3">1000+</h3>
                <p class="mb-0">Pelanggan</p>
            </div>
        </div>
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
            <div class="stat-card bg-gradient-info">
                <i class="bi bi-calendar-check display-1"></i>
                <h3 class="fw-bold mt-3">500+</h3>
                <p class="mb-0">Booking</p>
            </div>
        </div>
    </div>
</section>

<!-- Jasa Service Section -->
<section class="container py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-5 fw-bold">Layanan Jasa Service</h2>
        <p class="lead text-muted">Kami menyediakan berbagai layanan service untuk mobil Anda</p>
    </div>

    <div class="row g-4">
        <?php foreach($jasa_list as $jasa): ?>
        <div class="col-md-3" data-aos="flip-left" data-aos-delay="<?php echo $jasa['id_jasa'] * 50; ?>">
            <div class="card service-card h-100 border-0 shadow-sm">
                <div class="card-img-top position-relative overflow-hidden">
                    <img src="assets/images/jasa/<?php echo $jasa['gambar']; ?>" class="img-fluid" alt="<?php echo $jasa['nama_jasa']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=Jasa'">
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold"><?php echo $jasa['nama_jasa']; ?></h5>
                    <p class="card-text text-muted"><?php echo $jasa['deskripsi']; ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="h5 text-primary fw-bold"><?php echo rupiah($jasa['harga']); ?></span>
                        <span class="badge bg-light text-dark"><i class="bi bi-clock me-1"></i><?php echo $jasa['estimasi_waktu']; ?></span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    <a href="detail.php?type=jasa&id=<?php echo $jasa['id_jasa']; ?>" class="btn btn-outline-primary w-100 rounded-pill">Lihat Detail</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
        <a href="jasa.php" class="btn btn-primary rounded-pill px-5 py-3 fw-semibold">
            Lihat Semua Jasa <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>
</section>

<!-- Sparepart Populer Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold">Sparepart Populer</h2>
            <p class="lead text-muted">Sparepart berkualitas dengan harga terjangkau</p>
        </div>

        <div class="row g-4">
            <?php foreach($sparepart_list as $sparepart): ?>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="<?php echo $sparepart['id_sparepart'] * 50; ?>">
                <div class="card sparepart-card h-100 border-0 shadow-sm">
                    <div class="card-img-top position-relative overflow-hidden">
                        <img src="assets/images/sparepart/<?php echo $sparepart['gambar']; ?>" class="img-fluid" alt="<?php echo $sparepart['nama_sparepart']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=Sparepart'">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?php echo $sparepart['nama_sparepart']; ?></h5>
                        <p class="card-text text-muted"><?php echo $sparepart['deskripsi']; ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="h5 text-primary fw-bold"><?php echo rupiah($sparepart['harga_jual']); ?></span>
                            <span class="badge bg-light text-dark"><i class="bi bi-box me-1"></i>Stok: <?php echo $sparepart['stok']; ?></span>
                        </div>
                        <p class="mt-2 mb-0"><i class="bi bi-tag me-1"></i><?php echo $sparepart['merk']; ?></p>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <a href="detail.php?type=sparepart&id=<?php echo $sparepart['id_sparepart']; ?>" class="btn btn-outline-primary w-100 rounded-pill">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="produk.php" class="btn btn-primary rounded-pill px-5 py-3 fw-semibold">
                Lihat Semua Sparepart <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Keunggulan Section -->
<section class="container py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-5 fw-bold">Kenapa Memilih Kami?</h2>
    </div>

    <div class="row g-4">
        <div class="col-md-4" data-aos="fade-right">
            <div class="feature-card text-center p-4 rounded-4 shadow-sm">
                <div class="feature-icon mx-auto mb-4">
                    <i class="bi bi-tools fs-1"></i>
                </div>
                <h4 class="fw-bold">Mekanik Profesional</h4>
                <p class="text-muted">Tim mekanik kami berpengalaman dan bersertifikat</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up">
            <div class="feature-card text-center p-4 rounded-4 shadow-sm">
                <div class="feature-icon mx-auto mb-4">
                    <i class="bi bi-shield-check fs-1"></i>
                </div>
                <h4 class="fw-bold">Garansi Service</h4>
                <p class="text-muted">Semua service mendapatkan garansi 1 bulan</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-left">
            <div class="feature-card text-center p-4 rounded-4 shadow-sm">
                <div class="feature-icon mx-auto mb-4">
                    <i class="bi bi-clock-history fs-1"></i>
                </div>
                <h4 class="fw-bold">Tepat Waktu</h4>
                <p class="text-muted">Pengerjaan cepat dan sesuai estimasi waktu</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimoni Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold">Testimoni Pelanggan</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-right">
                <div class="testimonial-card p-4 rounded-4 shadow-sm bg-white">
                    <div class="stars mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="testimonial-text fst-italic">"Service cepat, mekaniknya ramah dan profesional. Harga juga terjangkau. Recommended!"</p>
                    <div class="d-flex align-items-center mt-4">
                        <i class="bi bi-person-circle fs-1 me-3 text-primary"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Andi Customer</h6>
                            <small class="text-muted">Pelanggan Setia</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up">
                <div class="testimonial-card p-4 rounded-4 shadow-sm bg-white">
                    <div class="stars mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="testimonial-text fst-italic">"Sparepart original, harganya bersaing. Sudah langganan service di sini sejak 2020."</p>
                    <div class="d-flex align-items-center mt-4">
                        <i class="bi bi-person-circle fs-1 me-3 text-primary"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Budi Customer</h6>
                            <small class="text-muted">Pemilik Avanza</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-left">
                <div class="testimonial-card p-4 rounded-4 shadow-sm bg-white">
                    <div class="stars mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                    </div>
                    <p class="testimonial-text fst-italic">"Pelayanan ramah, tempatnya bersih. Saya puas dengan hasil service AC mobil saya."</p>
                    <div class="d-flex align-items-center mt-4">
                        <i class="bi bi-person-circle fs-1 me-3 text-primary"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Citra Pelanggan</h6>
                            <small class="text-muted">Pemilik Honda HRV</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
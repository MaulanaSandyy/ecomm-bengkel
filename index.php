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
<section class="bg-primary text-white py-5 mb-5 animate__animated animate__fadeIn">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold"><?php echo $profil['nama_bengkel']; ?></h1>
                <p class="lead"><?php echo $profil['deskripsi']; ?></p>
                <div class="mt-4">
                    <?php if(!isset($_SESSION['id_user'])): ?>
                        <a href="register.php" class="btn btn-light btn-lg me-2">
                            <i class="bi bi-person-plus"></i> Daftar Sekarang
                        </a>
                    <?php endif; ?>
                    <a href="booking.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-calendar-check"></i> Booking Service
                    </a>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <img src="assets/images/bengkel-hero.jpg" alt="Bengkel Mobil" class="img-fluid rounded-3 shadow" onerror="this.src='https://via.placeholder.com/600x400?text=Bengkel+Mobil'">
            </div>
        </div>
    </div>
</section>

<!-- Statistik Section -->
<section class="container mb-5">
    <div class="row text-center">
        <div class="col-md-3 mb-3" data-aos="zoom-in" data-aos-delay="100">
            <div class="card bg-primary text-white p-3">
                <i class="bi bi-tools display-1"></i>
                <h3>10+</h3>
                <p>Jasa Service</p>
            </div>
        </div>
        <div class="col-md-3 mb-3" data-aos="zoom-in" data-aos-delay="200">
            <div class="card bg-success text-white p-3">
                <i class="bi bi-box display-1"></i>
                <h3>50+</h3>
                <p>Sparepart</p>
            </div>
        </div>
        <div class="col-md-3 mb-3" data-aos="zoom-in" data-aos-delay="300">
            <div class="card bg-warning text-white p-3">
                <i class="bi bi-people display-1"></i>
                <h3>1000+</h3>
                <p>Pelanggan</p>
            </div>
        </div>
        <div class="col-md-3 mb-3" data-aos="zoom-in" data-aos-delay="400">
            <div class="card bg-info text-white p-3">
                <i class="bi bi-calendar-check display-1"></i>
                <h3>500+</h3>
                <p>Booking</p>
            </div>
        </div>
    </div>
</section>

<!-- Jasa Service Section -->
<section class="container mb-5">
    <h2 class="text-center mb-4" data-aos="fade-up">Layanan Jasa Service</h2>
    <p class="text-center mb-5" data-aos="fade-up">Kami menyediakan berbagai layanan service untuk mobil Anda</p>
    
    <div class="row">
        <?php foreach($jasa_list as $jasa): ?>
        <div class="col-md-3 mb-4" data-aos="flip-left" data-aos-delay="<?php echo $jasa['id_jasa'] * 50; ?>">
            <div class="card h-100 shadow-sm hover-card">
                <img src="assets/images/jasa/<?php echo $jasa['gambar']; ?>" class="card-img-top" alt="<?php echo $jasa['nama_jasa']; ?>" style="height: 200px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x200?text=Jasa'">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $jasa['nama_jasa']; ?></h5>
                    <p class="card-text text-truncate"><?php echo $jasa['deskripsi']; ?></p>
                    <p class="text-primary fw-bold"><?php echo rupiah($jasa['harga']); ?></p>
                    <p class="text-muted"><i class="bi bi-clock"></i> <?php echo $jasa['estimasi_waktu']; ?></p>
                    <a href="detail.php?type=jasa&id=<?php echo $jasa['id_jasa']; ?>" class="btn btn-primary w-100">Lihat Detail</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-3">
        <a href="jasa.php" class="btn btn-outline-primary">Lihat Semua Jasa <i class="bi bi-arrow-right"></i></a>
    </div>
</section>

<!-- Sparepart Populer Section -->
<section class="container mb-5 bg-light py-5 rounded">
    <h2 class="text-center mb-4" data-aos="fade-up">Sparepart Populer</h2>
    <p class="text-center mb-5" data-aos="fade-up">Sparepart berkualitas dengan harga terjangkau</p>
    
    <div class="row">
        <?php foreach($sparepart_list as $sparepart): ?>
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $sparepart['id_sparepart'] * 50; ?>">
            <div class="card h-100 shadow-sm">
                <img src="assets/images/sparepart/<?php echo $sparepart['gambar']; ?>" class="card-img-top" alt="<?php echo $sparepart['nama_sparepart']; ?>" style="height: 200px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x200?text=Sparepart'">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $sparepart['nama_sparepart']; ?></h5>
                    <p class="card-text text-truncate"><?php echo $sparepart['deskripsi']; ?></p>
                    <p class="text-primary fw-bold"><?php echo rupiah($sparepart['harga_jual']); ?></p>
                    <p class="text-muted">
                        <i class="bi bi-box"></i> Stok: <?php echo $sparepart['stok']; ?> | 
                        <i class="bi bi-tag"></i> <?php echo $sparepart['merk']; ?>
                    </p>
                    <a href="detail.php?type=sparepart&id=<?php echo $sparepart['id_sparepart']; ?>" class="btn btn-primary w-100">Lihat Detail</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-3">
        <a href="produk.php" class="btn btn-outline-primary">Lihat Semua Sparepart <i class="bi bi-arrow-right"></i></a>
    </div>
</section>

<!-- Keunggulan Section -->
<section class="container mb-5">
    <h2 class="text-center mb-5" data-aos="fade-up">Kenapa Memilih Kami?</h2>
    
    <div class="row">
        <div class="col-md-4 mb-4" data-aos="fade-right">
            <div class="text-center">
                <i class="bi bi-tools display-1 text-primary"></i>
                <h4>Mekanik Profesional</h4>
                <p>Tim mekanik kami berpengalaman dan bersertifikat</p>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up">
            <div class="text-center">
                <i class="bi bi-shield-check display-1 text-primary"></i>
                <h4>Garansi Service</h4>
                <p>Semua service mendapatkan garansi 1 bulan</p>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-left">
            <div class="text-center">
                <i class="bi bi-clock-history display-1 text-primary"></i>
                <h4>Tepat Waktu</h4>
                <p>Pengerjaan cepat dan sesuai estimasi waktu</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimoni Section -->
<section class="container mb-5">
    <h2 class="text-center mb-5" data-aos="fade-up">Testimoni Pelanggan</h2>
    
    <div class="row">
        <div class="col-md-4 mb-4" data-aos="fade-right">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="card-text">"Service cepat, mekaniknya ramah dan profesional. Harga juga terjangkau. Recommended!"</p>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-1 me-3"></i>
                        <div>
                            <h6 class="mb-0">Andi Customer</h6>
                            <small class="text-muted">Pelanggan Setia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="card-text">"Sparepart original, harganya bersaing. Sudah langganan service di sini sejak 2020."</p>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-1 me-3"></i>
                        <div>
                            <h6 class="mb-0">Budi Customer</h6>
                            <small class="text-muted">Pemilik Avanza</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-left">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                    </div>
                    <p class="card-text">"Pelayanan ramah, tempatnya bersih. Saya puas dengan hasil service AC mobil saya."</p>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-1 me-3"></i>
                        <div>
                            <h6 class="mb-0">Citra Pelanggan</h6>
                            <small class="text-muted">Pemilik Honda HRV</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
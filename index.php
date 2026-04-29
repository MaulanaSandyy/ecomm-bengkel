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

// Ambil gambar dari tabel profil
$banner_image = !empty($profil['gambar_banner']) && file_exists("uploads/profil/" . $profil['gambar_banner']) 
                ? "uploads/profil/" . $profil['gambar_banner'] 
                : "assets/img/banner.png";
?>

<style>

/* ============================================
   SMOOTH SCROLL ANIMATION
   ============================================ */

html {
    scroll-behavior: smooth;
}

/* Smooth scroll dengan easing yang lebih baik */
.smooth-scroll {
    scroll-behavior: smooth;
}

/* Animasi highlight section saat di-scroll */
@keyframes sectionHighlight {
    0% {
        transform: translateY(20px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes glowPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4);
        border-color: rgba(102, 126, 234, 0.6);
    }
    50% {
        box-shadow: 0 0 0 20px rgba(102, 126, 234, 0);
        border-color: rgba(102, 126, 234, 1);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
        border-color: rgba(102, 126, 234, 0.6);
    }
}

/* Class untuk efek highlight pada section */
.section-highlight {
    animation: glowPulse 0.8s ease-out;
    position: relative;
    z-index: 1;
}

.section-highlight::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: rgba(102, 126, 234, 0.05);
    border-radius: 20px;
    z-index: -1;
    animation: sectionHighlight 0.5s ease-out;
}

/* Scroll indicator animation */
.scroll-indicator {
    position: relative;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(10px);
    }
}

/* Navbar active link indicator */
.nav-link.active-section {
    color: #667eea !important;
    font-weight: 700;
    position: relative;
}

.nav-link.active-section::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
    animation: underlineSlide 0.3s ease-out;
}

@keyframes underlineSlide {
    from {
        width: 0;
        opacity: 0;
    }
    to {
        width: 30px;
        opacity: 1;
    }
}

/* Loading progress bar saat scroll */
.scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    z-index: 9999;
    transition: width 0.3s ease;
    box-shadow: 0 0 10px rgba(102,126,234,0.5);
}

/* Scroll to top button */
.scroll-top-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 5px 15px rgba(102,126,234,0.3);
}

.scroll-top-btn.show {
    opacity: 1;
    visibility: visible;
}

.scroll-top-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(102,126,234,0.4);
}

/* ============================================
   HERO SECTION WITH MODERN CARD DESIGN
   ============================================ */

.hero-section {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    overflow: hidden;
    padding: 100px 0;
}

/* Background animated shapes */
.hero-bg-shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    animation: float 20s infinite ease-in-out;
}

.shape-1 {
    width: 300px;
    height: 300px;
    top: -100px;
    right: -100px;
    animation-delay: 0s;
}

.shape-2 {
    width: 500px;
    height: 500px;
    bottom: -200px;
    left: -200px;
    animation-delay: 5s;
}

.shape-3 {
    width: 200px;
    height: 200px;
    top: 40%;
    right: 20%;
    animation-delay: 10s;
}

.shape-4 {
    width: 150px;
    height: 150px;
    bottom: 20%;
    left: 10%;
    animation-delay: 15s;
}

@keyframes float {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(50px, 30px) rotate(5deg);
    }
    50% {
        transform: translate(100px, 50px) rotate(10deg);
    }
    75% {
        transform: translate(50px, 30px) rotate(5deg);
    }
}

/* Hero Card Modern */
.hero-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 40px;
    padding: 50px;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
    z-index: 10;
    transition: all 0.3s ease;
}

.hero-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.25);
}

/* Badge */
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, rgba(102,126,234,0.15), rgba(118,75,162,0.15));
    backdrop-filter: blur(10px);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #667eea;
    margin-bottom: 20px;
    border: 1px solid rgba(102,126,234,0.3);
}

.hero-badge i {
    font-size: 1rem;
}

/* Title */
.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-title span {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Description */
.hero-description {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #64748b;
    margin-bottom: 30px;
}

/* Button Group */
.hero-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-hero-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 14px 35px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 10px 20px rgba(102,126,234,0.3);
}

.btn-hero-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(102,126,234,0.4);
    color: white;
}

.btn-hero-outline {
    background: transparent;
    color: #667eea;
    padding: 14px 35px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid #667eea;
}

.btn-hero-outline:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
    border-color: transparent;
}

/* Right Side Card (Glassmorphism) */
.hero-right-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    z-index: 10;
    transition: all 0.3s ease;
}

.hero-right-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.15);
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: white;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Floating elements */
.floating-element {
    position: absolute;
    animation: floatElement 6s ease-in-out infinite;
}

@keyframes floatElement {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}

/* Service card and sparepart card */
.service-card, .sparepart-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.service-card:hover, .sparepart-card:hover {
    transform: translateY(-10px);
}

/* Responsive */
@media (max-width: 991px) {
    .hero-card {
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-description {
        font-size: 1rem;
    }
    
    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .hero-card {
        padding: 20px;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-buttons {
        flex-direction: column;
    }
    
    .btn-hero-primary, .btn-hero-outline {
        width: 100%;
        text-align: center;
    }
    
    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .stat-item {
        padding: 10px;
    }
    
    .stat-number {
        font-size: 1.2rem;
    }
    
    .stat-label {
        font-size: 0.7rem;
    }
}
</style>

<section class="hero-section">
    <!-- Background animated shapes -->
    <div class="hero-bg-shape shape-1"></div>
    <div class="hero-bg-shape shape-2"></div>
    <div class="hero-bg-shape shape-3"></div>
    <div class="hero-bg-shape shape-4"></div>
    
    <div class="container">
        <div class="row align-items-center g-4">
            <!-- Left Side - Hero Card -->
            <div class="col-lg-7" data-aos="fade-right" data-aos-duration="1000">
                <div class="hero-card">
                    <div class="hero-badge">
                        <i class="fas fa-star"></i>
                        <span>Layanan Bengkel Terpercaya</span>
                        <i class="fas fa-check-circle"></i>
                    </div>
                    
                    <h1 class="hero-title">
                        Selamat Datang di <br>
                        <span><?php echo $profil['nama_bengkel']; ?></span>
                    </h1>
                    
                    <p class="hero-description">
                        <?php echo substr($profil['deskripsi'], 0, 200); ?>...
                    </p>
                    
                    <div class="hero-buttons">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="auth/register.php" class="btn-hero-primary">
                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                            </a>
                        <?php endif; ?>
                        <a href="#jasa" class="btn-hero-outline">
                            <i class="fas fa-arrow-down me-2"></i>Lihat Layanan
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Glassmorphism Card dengan Gambar -->
            <div class="col-lg-5" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div class="hero-right-card">
                    <div class="text-center mb-4">
                        <img src="<?php echo $banner_image; ?>" alt="Hero Image" class="img-fluid rounded-4" style="max-height: 250px; object-fit: contain;">
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo num_rows(query("SELECT * FROM users WHERE role_id = 4")); ?>+</div>
                            <div class="stat-label">Customer Puas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo num_rows(query("SELECT * FROM jasa")); ?>+</div>
                            <div class="stat-label">Layanan</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo num_rows(query("SELECT * FROM sparepart")); ?>+</div>
                            <div class="stat-label">Sparepart</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo num_rows(query("SELECT * FROM booking")); ?>+</div>
                            <div class="stat-label">Booking</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistik Cards -->
<section class="py-5" style="margin-top: -40px; position: relative; z-index: 20;">
    <div class="container">
        <div class="row g-4">
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="card h-100 border-0 shadow-lg p-4 d-flex flex-row align-items-center gap-3 rounded-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.1));">
                        <i class="fas fa-wrench fa-2x" style="color: #667eea;"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo num_rows(query("SELECT * FROM jasa")); ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Jasa Service</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-lg p-4 d-flex flex-row align-items-center gap-3 rounded-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.1));">
                        <i class="fas fa-oil-can fa-2x text-success"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo num_rows(query("SELECT * FROM sparepart")); ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Sparepart</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-lg p-4 d-flex flex-row align-items-center gap-3 rounded-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(245,158,11,0.1));">
                        <i class="fas fa-users fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo num_rows(query("SELECT * FROM users WHERE role_id = 4")); ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Customer</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-lg p-4 d-flex flex-row align-items-center gap-3 rounded-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: linear-gradient(135deg, rgba(14,165,233,0.1), rgba(2,132,199,0.1));">
                        <i class="fas fa-calendar-check fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo num_rows(query("SELECT * FROM booking")); ?></h3>
                        <p class="text-muted mb-0 fw-medium small text-uppercase letter-spacing-1">Total Booking</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Jasa Service Section -->
<section id="jasa" class="py-5 mt-4">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-bold text-uppercase small letter-spacing-2">Pelayanan Kami</span>
            <h2 class="fw-extrabold mt-2 text-dark display-5">Layanan Jasa Terbaik</h2>
            <div class="mx-auto mt-3 rounded" style="width: 80px; height: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
            <p class="text-muted mt-3 mx-auto" style="max-width: 600px;">Kami menyediakan berbagai layanan service mobil dengan teknisi profesional dan peralatan modern</p>
        </div>
        
        <div class="row g-4">
            <?php while ($row = fetch_assoc($jasa)): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card service-card h-100 d-flex flex-column border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="position-relative overflow-hidden">
                        <img src="uploads/jasa/<?php echo $row['gambar'] ?: 'default.jpg'; ?>" class="card-img-top" alt="<?php echo $row['nama_jasa']; ?>" style="height: 240px; object-fit: cover; transition: transform 0.5s ease;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
                                <i class="fas fa-clock me-1"></i> <?php echo $row['estimasi_waktu']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-2"><?php echo $row['nama_jasa']; ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?php echo substr($row['deskripsi'], 0, 100); ?>...</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <span class="text-muted small">Mulai dari</span>
                            <span class="fs-4 fw-bold text-primary">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
                            <a href="customer/booking.php?jasa_id=<?php echo $row['id']; ?>" class="btn btn-primary w-100 mt-4 rounded-pill">
                                <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                            </a>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-outline-primary w-100 mt-4 rounded-pill">
                                <i class="fas fa-sign-in-alt me-2"></i>Login untuk Booking
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="#" class="btn btn-outline-primary px-5 py-3 fw-bold rounded-pill">
                Lihat Semua Layanan <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Sparepart Section -->
<section id="sparepart" class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-success fw-bold text-uppercase small letter-spacing-2">Katalog Toko</span>
            <h2 class="fw-extrabold mt-2 text-dark display-5">Sparepart Original</h2>
            <div class="mx-auto mt-3 rounded" style="width: 80px; height: 4px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);"></div>
            <p class="text-muted mt-3 mx-auto" style="max-width: 600px;">Dapatkan sparepart original dengan harga terbaik dan garansi resmi</p>
        </div>

        <div class="row g-4">
            <?php while ($row = fetch_assoc($sparepart)): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card sparepart-card h-100 d-flex flex-column border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="position-relative p-4 text-center bg-white m-2 rounded-4">
                        <img src="uploads/sparepart/<?php echo $row['gambar'] ?: 'default.jpg'; ?>" class="img-fluid" alt="<?php echo $row['nama_sparepart']; ?>" style="height: 160px; object-fit: contain;">
                        <?php if($row['stok'] > 0): ?>
                            <span class="badge bg-success position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-box me-1"></i> Stok: <?php echo $row['stok']; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-times-circle me-1"></i> Habis
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-dark mb-0"><?php echo $row['nama_sparepart']; ?></h5>
                        </div>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-tag me-1 text-secondary"></i> <?php echo $row['merek']; ?>
                        </p>
                        
                        <p class="card-text text-muted small flex-grow-1"><?php echo substr($row['deskripsi'], 0, 80); ?>...</p>
                        
                        <div class="mt-3 pt-3 border-top">
                            <h4 class="fw-bold text-dark mb-3">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></h4>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
                                <a href="customer/beli.php?id=<?php echo $row['id']; ?>" class="btn btn-success w-100 rounded-pill <?php echo ($row['stok'] <= 0) ? 'disabled' : ''; ?>">
                                    <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
                                </a>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-outline-success w-100 rounded-pill">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login untuk Membeli
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: -2;"></div>
    <div class="position-absolute top-0 end-0 w-50 h-100 bg-white rounded-circle" style="opacity: 0.1; z-index: -1; transform: translateX(30%) scale(1.5);"></div>
    <div class="position-absolute bottom-0 start-0 w-25 h-50 bg-white rounded-circle" style="opacity: 0.05; z-index: -1; transform: translateX(-30%) scale(1.5);"></div>
    
    <div class="container text-center text-white py-5" data-aos="zoom-in">
        <h2 class="display-5 fw-bold mb-3">Mobil Anda Butuh Perawatan?</h2>
        <p class="lead mb-5 fw-light opacity-75 mx-auto" style="max-width: 600px;">Jangan tunggu sampai rusak. Booking sekarang dan dapatkan pelayanan terbaik langsung dari teknisi profesional kami.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#jasa" class="btn btn-warning btn-lg px-5 py-3 shadow-lg rounded-pill">
                <i class="fas fa-calendar-alt me-2"></i>Booking Jadwal
            </a>
            <a href="#sparepart" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill">
                <i class="fas fa-box-open me-2"></i>Cari Sparepart
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
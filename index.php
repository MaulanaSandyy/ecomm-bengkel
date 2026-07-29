<?php
session_start();
include 'includes/koneksi.php';

$title = "Beranda";
include 'includes/header.php';

$jasa = query("SELECT * FROM jasa ORDER BY id DESC LIMIT 6");
$sparepart = query("SELECT * FROM sparepart ORDER BY id DESC LIMIT 6");
$profil = fetch_assoc(query("SELECT * FROM profil_bengkel WHERE id = 1"));

$banner_image = !empty($profil['gambar_banner']) && file_exists("uploads/profil/" . $profil['gambar_banner']) 
                ? "uploads/profil/" . $profil['gambar_banner'] 
                : "assets/img/banner.png";

$count_jasa      = num_rows(query("SELECT * FROM jasa"));
$count_sparepart = num_rows(query("SELECT * FROM sparepart"));
$count_customer  = num_rows(query("SELECT * FROM users WHERE role_id = 4"));
$count_booking   = num_rows(query("SELECT * FROM booking"));
?>

<!-- ═══════ HERO ═══════ -->
<section class="hero">
  <div class="hero-grid"></div>
  <div class="hero-orb hero-orb--1"></div>
  <div class="hero-orb hero-orb--2"></div>
  <div class="hero-orb hero-orb--3"></div>

  <div class="container" style="position:relative;z-index:10;">
    <div class="row align-items-center g-4 g-lg-5">

      <div class="col-lg-7 reveal">
        <div class="hero-card">
          <div class="hero-card__body">
            <div class="hero-badge">
              <i class="fas fa-shield-halved" style="font-size:0.6rem;"></i>
              Layanan Bengkel Terpercaya
              <i class="fas fa-circle-check" style="font-size:0.5rem;"></i>
            </div>

            <h1 class="hero-title">
              Selamat Datang di<br><em><?php echo htmlspecialchars($profil['nama_bengkel']); ?></em>
            </h1>

            <p class="hero-desc">
              <?php echo htmlspecialchars(substr($profil['deskripsi'], 0, 200)); ?>...
            </p>

            <div class="hero-actions">
              <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="auth/register.php" class="btn btn-primary btn-lg">
                  Daftar Sekarang
                  <span class="ico"><i class="fas fa-arrow-right"></i></span>
                </a>
              <?php endif; ?>
              <a href="#jasa" class="btn btn-outline-light btn-lg">
                Lihat Layanan
                <span class="ico"><i class="fas fa-arrow-down"></i></span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 reveal reveal-d2">
        <div class="hero-glass">
          <div class="hero-glass__body">
            <div class="text-center p-3 pb-0">
              <img src="<?php echo htmlspecialchars($banner_image); ?>" alt="Banner"
                   class="img-fluid" style="max-height:180px;object-fit:contain;border-radius:var(--r-md);">
            </div>
            <div class="hero-stats">
              <div class="hero-stat">
                <div class="hero-stat__num"><?php echo $count_customer; ?>+</div>
                <div class="hero-stat__label">Customer Puas</div>
              </div>
              <div class="hero-stat">
                <div class="hero-stat__num"><?php echo $count_jasa; ?>+</div>
                <div class="hero-stat__label">Layanan</div>
              </div>
              <div class="hero-stat">
                <div class="hero-stat__num"><?php echo $count_sparepart; ?>+</div>
                <div class="hero-stat__label">Sparepart</div>
              </div>
              <div class="hero-stat">
                <div class="hero-stat__num"><?php echo $count_booking; ?>+</div>
                <div class="hero-stat__label">Booking</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══════ STATISTICS ═══════ -->
<section class="sec-tight" style="margin-top:-24px;position:relative;z-index:20;">
  <div class="container">
    <div class="row g-3 g-md-4">
      <div class="col-xl-3 col-md-6 reveal">
        <div class="stat-card">
          <div class="stat-icon stat-icon--accent"><i class="fas fa-wrench"></i></div>
          <div><div class="stat-val"><?php echo $count_jasa; ?></div><div class="stat-lbl">Jasa Service</div></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 reveal reveal-d1">
        <div class="stat-card">
          <div class="stat-icon stat-icon--green"><i class="fas fa-oil-can"></i></div>
          <div><div class="stat-val"><?php echo $count_sparepart; ?></div><div class="stat-lbl">Sparepart</div></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 reveal reveal-d2">
        <div class="stat-card">
          <div class="stat-icon stat-icon--amber"><i class="fas fa-users"></i></div>
          <div><div class="stat-val"><?php echo $count_customer; ?></div><div class="stat-lbl">Customer</div></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 reveal reveal-d3">
        <div class="stat-card">
          <div class="stat-icon stat-icon--sky"><i class="fas fa-calendar-check"></i></div>
          <div><div class="stat-val"><?php echo $count_booking; ?></div><div class="stat-lbl">Total Booking</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ JASA SERVICE ═══════ -->
<section id="jasa" class="sec">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="eyebrow mx-auto"><i class="fas fa-wrench" style="font-size:0.55rem;"></i> Pelayanan Kami</div>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:800;letter-spacing:-0.03em;">Layanan Jasa Terbaik</h2>
      <p class="mt-3 mx-auto" style="max-width:520px;color:var(--c-ink-muted);font-size:0.92rem;">
        Teknisi profesional dengan peralatan modern untuk perawatan kendaraan Anda
      </p>
    </div>

    <div class="row g-4">
      <?php while ($row = fetch_assoc($jasa)): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="pcard h-100 d-flex flex-column">
          <div class="pcard__img">
            <img src="uploads/jasa/<?php echo htmlspecialchars($row['gambar'] ?: 'default.jpg'); ?>"
                 alt="<?php echo htmlspecialchars($row['nama_jasa']); ?>">
            <div class="pcard__tag">
              <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($row['estimasi_waktu']); ?>
            </div>
          </div>
          <div class="pcard__body d-flex flex-column flex-grow-1">
            <h5 class="pcard__name"><?php echo htmlspecialchars($row['nama_jasa']); ?></h5>
            <p class="pcard__desc flex-grow-1"><?php echo htmlspecialchars(substr($row['deskripsi'], 0, 100)); ?>...</p>
            <div class="pcard__divider"></div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span style="font-size:0.75rem;color:var(--c-ink-faint);">Mulai dari</span>
              <span class="pcard__price" style="color:var(--c-accent);">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
            </div>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
              <a href="customer/booking.php?jasa_id=<?php echo $row['id']; ?>" class="btn btn-primary w-100">
                <i class="fas fa-calendar-plus"></i> Booking Sekarang
                <span class="ico"><i class="fas fa-arrow-right"></i></span>
              </a>
            <?php else: ?>
              <a href="auth/login.php" class="btn btn-outline-primary w-100">
                <i class="fas fa-right-to-bracket"></i> Login untuk Booking
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- ═══════ SPAREPART ═══════ -->
<section id="sparepart" class="sec" style="background:var(--c-surface-2);">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="eyebrow eyebrow--green mx-auto"><i class="fas fa-box-open" style="font-size:0.55rem;"></i> Katalog Toko</div>
      <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:800;letter-spacing:-0.03em;">Sparepart Original</h2>
      <p class="mt-3 mx-auto" style="max-width:520px;color:var(--c-ink-muted);font-size:0.92rem;">
        Sparepart berkualitas dengan garansi resmi dan harga terbaik
      </p>
    </div>

    <div class="row g-4">
      <?php while ($row = fetch_assoc($sparepart)): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="pcard h-100 d-flex flex-column">
          <div class="pcard__img" style="background:var(--c-surface);padding:20px;">
            <img src="uploads/sparepart/<?php echo htmlspecialchars($row['gambar'] ?: 'default.jpg'); ?>"
                 alt="<?php echo htmlspecialchars($row['nama_sparepart']); ?>"
                 style="object-fit:contain;">
            <?php if ($row['stok'] > 0): ?>
              <div class="pcard__tag pcard__tag--stock"><i class="fas fa-box me-1"></i> Stok: <?php echo $row['stok']; ?></div>
            <?php else: ?>
              <div class="pcard__tag pcard__tag--oos"><i class="fas fa-times-circle me-1"></i> Habis</div>
            <?php endif; ?>
          </div>
          <div class="pcard__body d-flex flex-column flex-grow-1">
            <h5 class="pcard__name"><?php echo htmlspecialchars($row['nama_sparepart']); ?></h5>
            <div class="pcard__meta"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($row['merek']); ?></div>
            <p class="pcard__desc flex-grow-1"><?php echo htmlspecialchars(substr($row['deskripsi'], 0, 80)); ?>...</p>
            <div class="pcard__divider"></div>
            <div class="pcard__price mb-3">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 4): ?>
              <a href="customer/beli.php?id=<?php echo $row['id']; ?>"
                 class="btn btn-success w-100 <?php echo ($row['stok'] <= 0) ? 'disabled' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Beli Sekarang
                <span class="ico"><i class="fas fa-arrow-right"></i></span>
              </a>
            <?php else: ?>
              <a href="auth/login.php" class="btn btn-outline-success w-100">
                <i class="fas fa-right-to-bracket"></i> Login untuk Membeli
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- ═══════ CTA ═══════ -->
<section class="cta">
  <div class="container text-center" style="position:relative;z-index:10;">
    <div class="reveal">
      <div class="eyebrow mx-auto" style="background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.65);border-color:rgba(255,255,255,0.06);">
        <i class="fas fa-bell" style="font-size:0.55rem;"></i> Jangan Tunda Lagi
      </div>
      <h2 style="color:#fff;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:800;letter-spacing:-0.03em;">Mobil Anda Butuh Perawatan?</h2>
      <p class="mt-3 mx-auto" style="max-width:500px;color:rgba(255,255,255,0.50);font-size:0.95rem;">
        Booking sekarang dan dapatkan pelayanan terbaik langsung dari teknisi profesional kami.
      </p>
      <div class="d-flex justify-content-center gap-3 flex-wrap mt-5">
        <a href="#jasa" class="btn btn-warning btn-lg">
          <i class="fas fa-calendar-alt"></i> Booking Jadwal
          <span class="ico"><i class="fas fa-arrow-right"></i></span>
        </a>
        <a href="#sparepart" class="btn btn-outline-light btn-lg">
          <i class="fas fa-box-open"></i> Cari Sparepart
        </a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

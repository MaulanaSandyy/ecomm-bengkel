<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid px-2">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/index.php">
      <div class="d-flex align-items-center justify-content-center"
           style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--c-accent),#7c3aed);color:#fff;font-size:0.8rem;">
        <i class="fas fa-car-side"></i>
      </div>
      <span>Jaya Abadi</span>
    </a>

    <button class="navbar-toggler border-0 shadow-none p-1" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php">
            <i class="fas fa-house" style="font-size:0.78rem;"></i> Beranda
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#jasa">
            <i class="fas fa-wrench" style="font-size:0.78rem;"></i> Jasa
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#sparepart">
            <i class="fas fa-box-open" style="font-size:0.78rem;"></i> Sparepart
          </a>
        </li>

        <li class="nav-item d-none d-lg-block mx-1">
          <div style="width:1px;height:20px;background:rgba(0,0,0,0.08);"></div>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ($_SESSION['role_id'] == 1): ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/admin/"><i class="fas fa-chart-pie" style="font-size:0.78rem;"></i> Dashboard</a></li>
          <?php elseif ($_SESSION['role_id'] == 2): ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/owner/"><i class="fas fa-chart-line" style="font-size:0.78rem;"></i> Dashboard</a></li>
          <?php elseif ($_SESSION['role_id'] == 3): ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/pegawai/"><i class="fas fa-list-check" style="font-size:0.78rem;"></i> Dashboard</a></li>
          <?php elseif ($_SESSION['role_id'] == 4): ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/customer/"><i class="fas fa-user" style="font-size:0.78rem;"></i> Dashboard</a></li>
          <?php endif; ?>

          <li class="nav-item dropdown ms-lg-1">
            <a class="nav-link dropdown-toggle d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill"
               href="#" data-bs-toggle="dropdown"
               style="background:var(--c-surface-2);font-size:0.82rem;font-weight:600;color:var(--c-ink);">
              <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,var(--c-accent),#7c3aed);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.65rem;">
                <i class="fas fa-user"></i>
              </div>
              <span><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 mt-2 py-2"
                style="background:rgba(255,255,255,0.96);backdrop-filter:blur(20px);border:1px solid var(--c-border);">
              <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-4 fw-medium"
                     href="<?php echo BASE_URL; ?>/auth/logout.php"
                     style="color:var(--c-rose);font-size:0.85rem;">
                <i class="fas fa-right-from-bracket"></i> Keluar
              </a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item ms-lg-1">
            <a class="btn btn-ghost rounded-pill px-4" href="<?php echo BASE_URL; ?>/auth/login.php" style="font-size:0.82rem;">
              <i class="fas fa-right-to-bracket"></i> Masuk
            </a>
          </li>
          <li class="nav-item ms-lg-1 mt-1 mt-lg-0">
            <a class="btn btn-primary rounded-pill px-4" href="<?php echo BASE_URL; ?>/auth/register.php" style="font-size:0.82rem;">
              <i class="fas fa-user-plus"></i> Daftar
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

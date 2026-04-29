<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg fixed-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/ecomm-bengkel/index.php">
            <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-car-side"></i>
            </div>
            <span class="fw-bolder tracking-tight text-dark">Jaya Abadi</span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <li class="nav-item">
                    <a class="nav-link px-3 fw-medium text-secondary" href="/ecomm-bengkel/index.php"><i class="fas fa-home me-1 text-primary"></i>Beranda</a>
                </li>
               <li class="nav-item">
                    <a class="nav-link px-3 fw-medium text-secondary" href="#jasa">
                        <i class="fas fa-wrench me-1 text-primary"></i>Jasa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-medium text-secondary" href="#sparepart">
                        <i class="fas fa-box-open me-1 text-primary"></i>Sparepart
                    </a>
                </li>
                
                <li class="nav-item d-none d-lg-block mx-2">
                    <div class="vr h-100 bg-secondary opacity-25"></div>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role_id'] == 1): // Admin ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium text-secondary" href="/ecomm-bengkel/admin/"><i class="fas fa-chart-pie me-1 text-success"></i>Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 2): // Owner ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium text-secondary" href="/ecomm-bengkel/owner/"><i class="fas fa-chart-line me-1 text-success"></i>Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 3): // Pegawai ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium text-secondary" href="/ecomm-bengkel/pegawai/"><i class="fas fa-tasks me-1 text-success"></i>Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 4): // Customer ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium text-secondary" href="/ecomm-bengkel/customer/"><i class="fas fa-user-circle me-1 text-success"></i>Dashboard</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle btn btn-light px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2 text-dark border" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="fw-semibold small"><?php echo $_SESSION['nama_lengkap']; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 py-2">
                            <li><a class="dropdown-item py-2 px-4 d-flex align-items-center gap-2 text-danger fw-medium" href="/ecomm-bengkel/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-primary px-4 rounded-pill fw-semibold w-100" href="/ecomm-bengkel/auth/login.php"><i class="fas fa-sign-in-alt me-2"></i>Masuk</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-primary px-4 rounded-pill fw-semibold w-100 shadow-sm" href="/ecomm-bengkel/auth/register.php"><i class="fas fa-user-plus me-2"></i>Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
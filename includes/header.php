<?php

// File: includes/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Bengkel Jaya Abadi</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" id="mainNav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-gear-wide-connected me-2"></i>Bengkel Jaya Abadi
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>" href="profil.php">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'jasa.php' ? 'active' : ''; ?>" href="jasa.php">Jasa Service</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : ''; ?>" href="produk.php">Sparepart</a>
                </li>

                <?php if(isset($_SESSION['id_user'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?php echo $_SESSION['nama_lengkap']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end glass-dropdown">
                            <?php if($_SESSION['role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                            <?php elseif($_SESSION['role'] == 'owner'): ?>
                                <li><a class="dropdown-item" href="owner/index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard Owner</a></li>
                            <?php elseif($_SESSION['role'] == 'pegawai'): ?>
                                <li><a class="dropdown-item" href="pegawai/index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard Pegawai</a></li>
                            <?php elseif($_SESSION['role'] == 'customer'): ?>
                                <li><a class="dropdown-item" href="customer/index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard Customer</a></li>
                                <li><a class="dropdown-item" href="booking.php"><i class="bi bi-calendar-check me-2"></i>Booking Service</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light rounded-pill px-4" href="register.php">Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Konten Utama -->
<main>
    <!-- Menampilkan pesan flash/session -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</main>
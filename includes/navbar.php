<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-car me-2"></i>Bengkel Jaya Abadi
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php"><i class="fas fa-home me-1"></i>Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#jasa"><i class="fas fa-wrench me-1"></i>Jasa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#sparepart"><i class="fas fa-oil-can me-1"></i>Sparepart</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role_id'] == 1): // Admin ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../admin/"><i class="fas fa-dashboard me-1"></i>Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 2): // Owner ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../owner/"><i class="fas fa-dashboard me-1"></i>Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 3): // Pegawai ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../pegawai/"><i class="fas fa-dashboard me-1"></i>Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role_id'] == 4): // Customer ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../customer/"><i class="fas fa-dashboard me-1"></i>Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo $_SESSION['nama_lengkap']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
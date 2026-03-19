<?php
session_start();
include '../includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape_string($_POST['username']);
    $password = $_POST['password']; // Password plain text sesuai permintaan
    
    $query = "SELECT u.*, r.nama_role FROM users u 
              JOIN roles r ON u.role_id = r.id 
              WHERE u.username = '$username' AND u.password = '$password'";
    $result = query($query);
    
    if (num_rows($result) > 0) {
        $user = fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role'] = $user['nama_role'];
        
        // Redirect berdasarkan role
        switch ($user['role_id']) {
            case 1:
                header("Location: ../admin/");
                break;
            case 2:
                header("Location: ../owner/");
                break;
            case 3:
                header("Location: ../pegawai/");
                break;
            case 4:
                header("Location: ../customer/");
                break;
            default:
                header("Location: ../index.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

$title = "Login";
include '../includes/header.php';
?>

<div class="position-absolute top-0 start-0 w-100 h-100" style="background: var(--bg-light); z-index: -1;"></div>
<div class="position-absolute bottom-0 start-0 w-50 h-50 bg-primary opacity-10 rounded-end-pill" style="z-index: -1; transform: translateX(-20%);"></div>

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="row w-100 justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" data-aos="zoom-in" data-aos-duration="600">
                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="text-center mb-5">
                        <div class="bg-primary bg-gradient d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="fas fa-car-side fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Selamat Datang Kembali</h3>
                        <p class="text-muted small">Masuk ke akun Jaya Abadi Anda</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger rounded-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label text-dark fw-medium small">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-start-0 ps-0 py-2" name="username" placeholder="Masukkan username" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label text-dark fw-medium small mb-0">Password</label>
                            </div>
                            <div class="input-group mt-2">
                                <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" class="form-control bg-light border-start-0 ps-0 py-2" name="password" placeholder="Masukkan password" required>
                                <button class="btn btn-light border border-start-0 toggle-password" type="button"><i class="fas fa-eye text-muted"></i></button>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label text-muted small" for="rememberMe">Ingat saya</label>
                            </div>
                            <a href="#" class="text-primary small fw-medium text-decoration-none">Lupa Password?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 mb-4 rounded-pill shadow-sm fw-bold">
                            Masuk <i class="fas fa-sign-in-alt ms-2"></i>
                        </button>
                        
                        <p class="text-center mb-0 text-muted">
                            Belum punya akun? 
                            <a href="register.php" class="text-primary fw-bold text-decoration-none">Daftar sekarang</a>
                        </p>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4">
                    <p class="text-center text-muted fw-bold small text-uppercase letter-spacing-1 mb-3">Informasi Akun Demo</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-2 text-center border border-light transition-all hover-shadow-sm cursor-pointer" onclick="document.getElementsByName('username')[0].value='admin'; document.getElementsByName('password')[0].value='123';">
                                <span class="d-block text-dark fw-bold small mb-1">Admin</span>
                                <span class="badge bg-secondary bg-opacity-25 text-dark">admin / 123</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-2 text-center border border-light transition-all hover-shadow-sm cursor-pointer" onclick="document.getElementsByName('username')[0].value='owner'; document.getElementsByName('password')[0].value='123';">
                                <span class="d-block text-dark fw-bold small mb-1">Owner</span>
                                <span class="badge bg-secondary bg-opacity-25 text-dark">owner / 123</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-2 text-center border border-light transition-all hover-shadow-sm cursor-pointer" onclick="document.getElementsByName('username')[0].value='pegawai'; document.getElementsByName('password')[0].value='123';">
                                <span class="d-block text-dark fw-bold small mb-1">Pegawai</span>
                                <span class="badge bg-secondary bg-opacity-25 text-dark">pegawai / 123</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-2 text-center border border-light transition-all hover-shadow-sm cursor-pointer" onclick="document.getElementsByName('username')[0].value='customer'; document.getElementsByName('password')[0].value='123';">
                                <span class="d-block text-dark fw-bold small mb-1">Customer</span>
                                <span class="badge bg-secondary bg-opacity-25 text-dark">customer / 123</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
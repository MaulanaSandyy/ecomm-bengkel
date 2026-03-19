<?php
session_start();
include '../includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = escape_string($_POST['nama_lengkap']);
    $username = escape_string($_POST['username']);
    $email = escape_string($_POST['email']);
    $password = $_POST['password']; // Plain text
    $no_hp = escape_string($_POST['no_hp']);
    $alamat = escape_string($_POST['alamat']);
    
    // Cek username sudah ada
    $check = query("SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if (num_rows($check) > 0) {
        $error = "Username atau email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (nama_lengkap, username, password, email, no_hp, alamat, role_id) 
                  VALUES ('$nama_lengkap', '$username', '$password', '$email', '$no_hp', '$alamat', 4)";
        
        if (query($query)) {
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registrasi gagal!";
        }
    }
}

$title = "Register";
include '../includes/header.php';
?>

<div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); z-index: -1;"></div>
<div class="position-absolute top-0 end-0 w-50 h-100 bg-primary opacity-10 rounded-start-pill" style="z-index: -1; transform: translateX(30%);"></div>

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="row w-100 justify-content-center">
        <div class="col-md-10 col-lg-8 col-xl-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" data-aos="zoom-in" data-aos-duration="600">
                <div class="row g-0">
                    
                    <div class="col-12 p-4 p-md-5 bg-white">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-gradient d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 60px; height: 60px;">
                                <i class="fas fa-user-plus fa-2x text-white"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-1">Buat Akun Baru</h3>
                            <p class="text-muted small">Daftar sekarang untuk kemudahan booking service</p>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger rounded-3" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-medium small">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-medium small">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-at text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0 ps-0" name="username" placeholder="Username unik" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-medium small">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" class="form-control bg-light border-start-0 ps-0" name="email" placeholder="contoh@email.com" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-medium small">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" class="form-control bg-light border-start-0 ps-0" name="password" placeholder="Minimal 6 karakter" required>
                                        <button class="btn btn-light border border-start-0 toggle-password" type="button"><i class="fas fa-eye text-muted"></i></button>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-medium small">No. Handphone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0 ps-0" name="no_hp" placeholder="08xxxxxxxxxx" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label text-dark fw-medium small">Alamat Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 px-3"><i class="fas fa-map-marker-alt text-muted mt-2 align-self-start"></i></span>
                                    <textarea class="form-control bg-light border-start-0 ps-0" name="alamat" rows="2" placeholder="Detail alamat untuk mempermudah layanan" required></textarea>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3 mb-4 rounded-pill shadow-sm fw-bold">
                                Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            
                            <p class="text-center mb-0 text-muted">
                                Sudah punya akun? 
                                <a href="login.php" class="text-primary fw-bold text-decoration-none">Masuk di sini</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
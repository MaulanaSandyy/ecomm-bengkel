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

<div class="container" style="min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5" data-aos="fade-up">
                <div class="card-header bg-transparent border-0 text-center pt-4">
                    <h3 class="text-primary fw-bold"><i class="fas fa-user-plus me-2"></i>Register</h3>
                    <p class="text-muted">Daftar sebagai customer</p>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                                <input type="text" class="form-control" name="nama_lengkap" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-at text-primary"></i></span>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock text-primary"></i></span>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">No. HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-phone text-primary"></i></span>
                                    <input type="text" class="form-control" name="no_hp" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                <textarea class="form-control" name="alamat" rows="2" required></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </button>
                        
                        <p class="text-center mb-0">
                            Sudah punya akun? 
                            <a href="login.php" class="text-primary fw-bold">Login disini</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
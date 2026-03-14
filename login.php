<?php
// File: login.php
session_start();

// Jika sudah login, redirect ke halaman sesuai role
if(isset($_SESSION['id_user'])) {
    if($_SESSION['role'] == 'admin') {
        header('Location: admin/index.php');
    } elseif($_SESSION['role'] == 'owner') {
        header('Location: owner/index.php');
    } elseif($_SESSION['role'] == 'pegawai') {
        header('Location: pegawai/index.php');
    } elseif($_SESSION['role'] == 'customer') {
        header('Location: customer/index.php');
    }
    exit;
}

$title = "Login";
include 'includes/koneksi.php';
include 'includes/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = bersihkan_input($_POST['username']);
    $password = $_POST['password'];
    
    $query = "SELECT u.*, r.nama_role FROM users u 
              JOIN roles r ON u.id_role = r.id_role 
              WHERE u.username = '$username' OR u.email = '$username'";
    $result = query($query);
    
    if(num_rows($result) > 0) {
        $user = fetch_array($result);
        
        // Verifikasi password
        if(password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['nama_role'];
            
            // Redirect sesuai role
            if($user['nama_role'] == 'admin') {
                header('Location: admin/index.php');
            } elseif($user['nama_role'] == 'owner') {
                header('Location: owner/index.php');
            } elseif($user['nama_role'] == 'pegawai') {
                header('Location: pegawai/index.php');
            } elseif($user['nama_role'] == 'customer') {
                header('Location: customer/index.php');
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username/Email tidak ditemukan!";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg animate__animated animate__fadeIn">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Login</h3>
                </div>
                <div class="card-body p-4">
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success_register'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i> <?php echo $_SESSION['success_register']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success_register']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username atau Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required 
                                       placeholder="Masukkan username atau email">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required 
                                       placeholder="Masukkan password">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ingat Saya</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p>Belum punya akun? <a href="register.php" class="text-primary">Daftar disini</a></p>
                        <p><a href="#" class="text-muted">Lupa Password?</a></p>
                    </div>
                    
                    <!-- Demo Credentials -->
                    <div class="alert alert-info mt-3">
                        <small>
                            <strong>Demo Akun:</strong><br>
                            Admin: admin / admin123<br>
                            Owner: owner / admin123<br>
                            Pegawai: pegawai / admin123<br>
                            Customer: customer1 / admin123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
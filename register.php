<?php
// File: register.php
session_start();

// Jika sudah login, redirect ke halaman utama
if(isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit;
}

$title = "Register";
include 'includes/koneksi.php';
include 'includes/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = bersihkan_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = bersihkan_input($_POST['nama_lengkap']);
    $email = bersihkan_input($_POST['email']);
    $no_telepon = bersihkan_input($_POST['no_telepon']);
    $alamat = bersihkan_input($_POST['alamat']);
    
    // Validasi
    $errors = [];
    
    // Cek username sudah ada
    $check = query("SELECT * FROM users WHERE username = '$username'");
    if(num_rows($check) > 0) {
        $errors[] = "Username sudah digunakan!";
    }
    
    // Cek email sudah ada
    $check = query("SELECT * FROM users WHERE email = '$email'");
    if(num_rows($check) > 0) {
        $errors[] = "Email sudah terdaftar!";
    }
    
    // Cek password
    if(strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter!";
    }
    
    if($password != $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok!";
    }
    
    // Jika tidak ada error
    if(empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (id_role, username, password, nama_lengkap, email, no_telepon, alamat) 
                  VALUES (4, '$username', '$password_hash', '$nama_lengkap', '$email', '$no_telepon', '$alamat')";
        
        if(query($query)) {
            $_SESSION['success_register'] = "Registrasi berhasil! Silakan login.";
            header('Location: login.php');
            exit;
        } else {
            $errors[] = "Gagal registrasi. Silakan coba lagi.";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg animate__animated animate__fadeIn">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0"><i class="bi bi-person-plus"></i> Daftar Akun Baru</h3>
                </div>
                <div class="card-body p-4">
                    
                    <?php if(!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                           required value="<?php echo isset($_POST['nama_lengkap']) ? $_POST['nama_lengkap'] : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           required value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="no_telepon" class="form-label">No. Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" 
                                           value="<?php echo isset($_POST['no_telepon']) ? $_POST['no_telepon'] : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"><?php echo isset($_POST['alamat']) ? $_POST['alamat'] : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya setuju dengan <a href="#">Syarat & Ketentuan</a>
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus"></i> Daftar
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p>Sudah punya akun? <a href="login.php" class="text-primary">Login disini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
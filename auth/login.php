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

<div class="container" style="min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5" data-aos="fade-up">
                <div class="card-header bg-transparent border-0 text-center pt-4">
                    <h3 class="text-primary fw-bold"><i class="fas fa-sign-in-alt me-2"></i>Login</h3>
                    <p class="text-muted">Silakan login untuk melanjutkan</p>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label text-muted">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-primary"></i></span>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                        
                        <p class="text-center mb-0">
                            Belum punya akun? 
                            <a href="register.php" class="text-primary fw-bold">Daftar disini</a>
                        </p>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="text-muted mb-2">Akun Demo:</p>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <small class="d-block"><strong>Admin</strong></small>
                                    <small class="text-muted">admin / 123</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <small class="d-block"><strong>Owner</strong></small>
                                    <small class="text-muted">owner / 123</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <small class="d-block"><strong>Pegawai</strong></small>
                                    <small class="text-muted">pegawai / 123</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <small class="d-block"><strong>Customer</strong></small>
                                    <small class="text-muted">customer / 123</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
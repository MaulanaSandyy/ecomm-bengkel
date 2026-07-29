<?php
session_start();
include '../includes/koneksi.php';

if (isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = escape_string($_POST['nama_lengkap']);
    $username = escape_string($_POST['username']);
    $email = escape_string($_POST['email']);
    $password = $_POST['password'];
    $no_hp = escape_string($_POST['no_hp']);
    $alamat = escape_string($_POST['alamat']);
    
    $check = query("SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if (num_rows($check) > 0) {
        $error = "Username atau email sudah terdaftar!";
    } else {
        $q = "INSERT INTO users (nama_lengkap, username, password, email, no_hp, alamat, role_id) VALUES ('$nama_lengkap', '$username', '$password', '$email', '$no_hp', '$alamat', 4)";
        if (query($q)) {
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

<div class="container d-flex align-items-center justify-content-center" style="min-height:100dvh;padding:120px 0 60px;">
  <div class="row w-100 justify-content-center">
    <div class="col-md-8 col-lg-7 col-xl-6">

      <div class="card-bezel reveal">
        <div class="card-bezel__inner p-4 p-md-5">
          <div class="text-center mb-4">
            <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,var(--c-accent),#7c3aed);color:#fff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
              <i class="fas fa-user-plus fa-lg"></i>
            </div>
            <h3 style="font-size:1.4rem;font-weight:800;letter-spacing:-0.03em;">Buat Akun Baru</h3>
            <p style="color:var(--c-ink-muted);font-size:0.85rem;margin-top:4px;">Daftar untuk kemudahan booking service</p>
          </div>

          <?php if (isset($error)): ?>
            <div class="d-flex align-items-center gap-2 py-3 px-4 mb-4"
                 style="background:var(--c-rose-bg);color:var(--c-rose);border-radius:var(--r-sm);font-size:0.85rem;font-weight:500;">
              <i class="fas fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="" class="needs-validation" novalidate>
            <div class="mb-3">
              <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">Nama Lengkap</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user" style="font-size:0.82rem;"></i></span>
                <input type="text" class="form-control" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">Username</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-at" style="font-size:0.82rem;"></i></span>
                  <input type="text" class="form-control" name="username" placeholder="Username unik" required>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-envelope" style="font-size:0.82rem;"></i></span>
                  <input type="email" class="form-control" name="email" placeholder="contoh@email.com" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-lock" style="font-size:0.82rem;"></i></span>
                  <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter" required>
                  <button class="btn toggle-password" type="button"
                          style="border:1.5px solid var(--c-surface-3);border-left:none;background:var(--c-surface-2);color:var(--c-ink-faint);">
                    <i class="fas fa-eye" style="font-size:0.82rem;"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">No. Handphone</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-phone" style="font-size:0.82rem;"></i></span>
                  <input type="text" class="form-control" name="no_hp" placeholder="08xxxxxxxxxx" required>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">Alamat Lengkap</label>
              <div class="input-group">
                <span class="input-group-text align-self-start pt-3"><i class="fas fa-location-dot" style="font-size:0.82rem;"></i></span>
                <textarea class="form-control" name="alamat" rows="2" placeholder="Detail alamat" required></textarea>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 mb-4 fw-bold">
              Daftar Sekarang <span class="ico"><i class="fas fa-arrow-right"></i></span>
            </button>

            <p class="text-center mb-0" style="color:var(--c-ink-muted);font-size:0.85rem;">
              Sudah punya akun? <a href="login.php" class="fw-bold text-decoration-none" style="color:var(--c-accent);">Masuk di sini</a>
            </p>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

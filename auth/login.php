<?php
session_start();
include '../includes/koneksi.php';

if (isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape_string($_POST['username']);
    $password = $_POST['password'];
    $result = query("SELECT u.*, r.nama_role FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = '$username' AND u.password = '$password'");
    
    if (num_rows($result) > 0) {
        $user = fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role'] = $user['nama_role'];
        switch ($user['role_id']) {
            case 1: header("Location: ../admin/"); break;
            case 2: header("Location: ../owner/"); break;
            case 3: header("Location: ../pegawai/"); break;
            case 4: header("Location: ../customer/"); break;
            default: header("Location: ../index.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

$title = "Login";
include '../includes/header.php';
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height:100dvh;padding:120px 0 60px;">
  <div class="row w-100 justify-content-center">
    <div class="col-md-6 col-lg-5 col-xl-4">

      <div class="card-bezel reveal">
        <div class="card-bezel__inner p-4 p-md-5">
          <div class="text-center mb-4">
            <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--c-accent),#7c3aed);color:#fff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px;">
              <i class="fas fa-car-side fa-lg"></i>
            </div>
            <h3 style="font-size:1.4rem;font-weight:800;letter-spacing:-0.03em;">Selamat Datang Kembali</h3>
            <p style="color:var(--c-ink-muted);font-size:0.85rem;margin-top:4px;">Masuk ke akun Jaya Abadi Anda</p>
          </div>

          <?php if (isset($error)): ?>
            <div class="d-flex align-items-center gap-2 py-3 px-4 mb-4"
                 style="background:var(--c-rose-bg);color:var(--c-rose);border-radius:var(--r-sm);font-size:0.85rem;font-weight:500;">
              <i class="fas fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="" class="needs-validation" novalidate>
            <div class="mb-3">
              <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">Username</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user" style="font-size:0.82rem;"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-medium small" style="color:var(--c-ink-soft);">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock" style="font-size:0.82rem;"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                <button class="btn toggle-password" type="button"
                        style="border:1.5px solid var(--c-surface-3);border-left:none;background:var(--c-surface-2);color:var(--c-ink-faint);">
                  <i class="fas fa-eye" style="font-size:0.82rem;"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 mb-4 fw-bold">
              Masuk <span class="ico"><i class="fas fa-arrow-right"></i></span>
            </button>

            <p class="text-center mb-0" style="color:var(--c-ink-muted);font-size:0.85rem;">
              Belum punya akun? <a href="register.php" class="fw-bold text-decoration-none" style="color:var(--c-accent);">Daftar sekarang</a>
            </p>
          </form>
        </div>
      </div>

      <div class="mt-3 p-4 reveal reveal-d1"
           style="background:var(--c-surface);border:1px solid var(--c-border);border-radius:var(--r-md);box-shadow:var(--sh-xs);">
        <p class="text-center fw-bold mb-3"
           style="color:var(--c-ink-faint);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;">
          Akun Demo
        </p>
        <div class="row g-2">
          <?php
          $demos = [['Admin','admin','123','var(--c-accent)'],['Owner','owner','123','var(--c-green)'],['Pegawai','pegawai','123','var(--c-amber)'],['Customer','customer','123','var(--c-sky)']];
          foreach ($demos as $d): ?>
          <div class="col-6">
            <div class="text-center py-2 rounded-2"
                 style="background:var(--c-surface-2);border:1px solid var(--c-border-2);cursor:pointer;transition:all 0.2s ease;"
                 onmouseenter="this.style.borderColor='<?php echo $d[3]; ?>' "
                 onmouseleave="this.style.borderColor='var(--c-border-2)'"
                 onclick="document.getElementsByName('username')[0].value='<?php echo $d[1]; ?>';document.getElementsByName('password')[0].value='<?php echo $d[2]; ?>';">
              <span class="d-block fw-bold" style="font-size:0.78rem;"><?php echo $d[0]; ?></span>
              <span style="font-size:0.68rem;color:var(--c-ink-faint);"><?php echo $d[1]; ?> / <?php echo $d[2]; ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

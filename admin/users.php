<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (query("DELETE FROM users WHERE id = $id")) {
        $_SESSION['success'] = "User berhasil dihapus!";
    } else {
        $_SESSION['error'] = "User gagal dihapus!";
    }
    header("Location: users.php");
    exit();
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = escape_string($_POST['nama_lengkap']);
    $username = escape_string($_POST['username']);
    $email = escape_string($_POST['email']);
    $password = $_POST['password'];
    $no_hp = escape_string($_POST['no_hp']);
    $alamat = escape_string($_POST['alamat']);
    $role_id = $_POST['role_id'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = $_POST['id'];
        if (!empty($password)) {
            $query = "UPDATE users SET 
                      nama_lengkap = '$nama_lengkap',
                      username = '$username',
                      email = '$email',
                      password = '$password',
                      no_hp = '$no_hp',
                      alamat = '$alamat',
                      role_id = $role_id
                      WHERE id = $id";
        } else {
            $query = "UPDATE users SET 
                      nama_lengkap = '$nama_lengkap',
                      username = '$username',
                      email = '$email',
                      no_hp = '$no_hp',
                      alamat = '$alamat',
                      role_id = $role_id
                      WHERE id = $id";
        }
        $message = "Data user berhasil diupdate!";
    } else {
        // Insert
        $query = "INSERT INTO users (nama_lengkap, username, email, password, no_hp, alamat, role_id) 
                  VALUES ('$nama_lengkap', '$username', '$email', '$password', '$no_hp', '$alamat', $role_id)";
        $message = "User baru berhasil ditambahkan!";
    }
    
    if (query($query)) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan sistem, data gagal disimpan!";
    }
    header("Location: users.php");
    exit();
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = query("SELECT * FROM users WHERE id = $id");
    $edit_data = fetch_assoc($result);
}

// Get all users
$users = query("SELECT u.*, r.nama_role FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC");

$title = "Kelola Users";
include '../includes/header.php';
?>

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-shield-alt me-2"></i>Menu Admin
                </h5>
                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="users.php" class="active"><i class="fas fa-users"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-box-open"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-cash-register"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode"></i>Upload QRIS</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold mb-4 text-dark">Manajemen Hak Akses & Pengguna</h3>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-down">
                <div class="card-header bg-white pt-4 pb-0 px-4 border-bottom-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas <?php echo $edit_data ? 'fa-user-edit text-warning' : 'fa-user-plus text-primary'; ?> me-2"></i>
                        <?php echo $edit_data ? 'Edit Data User' : 'Tambah User Baru'; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light" name="nama_lengkap" 
                                       value="<?php echo $edit_data['nama_lengkap'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Username</label>
                                <input type="text" class="form-control bg-light" name="username" 
                                       value="<?php echo $edit_data['username'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email</label>
                                <input type="email" class="form-control bg-light" name="email" 
                                       value="<?php echo $edit_data['email'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Password <?php echo $edit_data ? '<span class="text-danger fw-normal">(Kosongkan jika tidak diubah)</span>' : ''; ?></label>
                                <input type="password" class="form-control bg-light" name="password" 
                                       <?php echo $edit_data ? '' : 'required'; ?>>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">No. Handphone</label>
                                <input type="text" class="form-control bg-light" name="no_hp" 
                                       value="<?php echo $edit_data['no_hp'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Role (Hak Akses)</label>
                                <select class="form-select bg-light" name="role_id" required>
                                    <option value="" disabled <?php echo !$edit_data ? 'selected' : ''; ?>>Pilih Hak Akses...</option>
                                    <?php
                                    $roles = query("SELECT * FROM roles");
                                    while ($role = fetch_assoc($roles)):
                                    ?>
                                    <option value="<?php echo $role['id']; ?>"
                                        <?php echo ($edit_data && $edit_data['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                        <?php echo strtoupper($role['nama_role']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Alamat Lengkap</label>
                                <textarea class="form-control bg-light" name="alamat" rows="2" required><?php echo $edit_data['alamat'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i>Simpan Data
                            </button>
                            <?php if ($edit_data): ?>
                                <a href="users.php" class="btn btn-light border rounded-pill px-4">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-list text-success me-2"></i>Daftar Pengguna Sistem</h5>
                    
                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 ps-0" id="searchUser" onkeyup="searchTable('searchUser', 'userTable')" placeholder="Cari nama...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="userTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Info Pengguna</th>
                                    <th>Kontak</th>
                                    <th>Alamat</th>
                                    <th>Role Akses</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php while($row = fetch_assoc($users)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                <?php echo strtoupper(substr($row['nama_lengkap'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo $row['nama_lengkap']; ?></h6>
                                                <small class="text-muted">@<?php echo $row['username']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column small">
                                            <span class="text-dark"><i class="fas fa-envelope text-muted me-2"></i><?php echo $row['email']; ?></span>
                                            <span class="text-dark mt-1"><i class="fas fa-phone text-muted me-2"></i><?php echo $row['no_hp']; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="small text-muted mb-0 text-truncate" style="max-width: 200px;" title="<?php echo $row['alamat']; ?>">
                                            <?php echo $row['alamat']; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <?php
                                        $role_color = [
                                            'admin' => 'danger',
                                            'owner' => 'primary',
                                            'pegawai' => 'info',
                                            'customer' => 'success'
                                        ];
                                        $warna = isset($role_color[strtolower($row['nama_role'])]) ? $role_color[strtolower($row['nama_role'])] : 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $warna; ?> bg-opacity-10 text-<?php echo $warna; ?> rounded-pill px-3 py-2 text-uppercase small">
                                            <?php echo $row['nama_role']; ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if($row['id'] != $_SESSION['user_id']): ?>
                                            <a href="#" onclick="return confirmDelete('?delete=<?php echo $row['id']; ?>', 'Data user <?php echo $row['nama_lengkap']; ?> akan dihapus permanen!')" 
                                               class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" data-bs-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
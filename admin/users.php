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
        $message = "User berhasil diupdate!";
    } else {
        // Insert
        $query = "INSERT INTO users (nama_lengkap, username, email, password, no_hp, alamat, role_id) 
                  VALUES ('$nama_lengkap', '$username', '$email', '$password', '$no_hp', '$alamat', $role_id)";
        $message = "User berhasil ditambahkan!";
    }
    
    if (query($query)) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = "User gagal disimpan!";
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

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <h4 class="text-white mb-4"><i class="fas fa-dashboard me-2"></i>Menu Admin</h4>
                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="users.php" class="active"><i class="fas fa-users me-2"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench me-2"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-oil-can me-2"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-credit-card me-2"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Kelola Users</h2>
            
            <!-- Form Tambah/Edit -->
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo $edit_data ? 'Edit User' : 'Tambah User Baru'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" 
                                       value="<?php echo $edit_data['nama_lengkap'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" 
                                       value="<?php echo $edit_data['username'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo $edit_data['email'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <?php echo $edit_data ? '(Kosongkan jika tidak diubah)' : ''; ?></label>
                                <input type="password" class="form-control" name="password" 
                                       <?php echo $edit_data ? '' : 'required'; ?>>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" name="no_hp" 
                                       value="<?php echo $edit_data['no_hp'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-control" name="role_id" required>
                                    <?php
                                    $roles = query("SELECT * FROM roles");
                                    while ($role = fetch_assoc($roles)):
                                    ?>
                                    <option value="<?php echo $role['id']; ?>"
                                        <?php echo ($edit_data && $edit_data['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                        <?php echo $role['nama_role']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="2" required><?php echo $edit_data['alamat'] ?? ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="users.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Tabel Users -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while($row = fetch_assoc($users)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['no_hp']; ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $row['nama_role']; ?></span>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="return confirmDelete('?delete=<?php echo $row['id']; ?>')" 
                                           class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
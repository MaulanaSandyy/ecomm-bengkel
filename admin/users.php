<?php
// File: admin/user.php
session_start();
include '../includes/koneksi.php';

// Cek login dan role
if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$title = "Kelola User";
include '../includes/header.php';

// ========== HANDLE CRUD ==========

// TAMBAH USER
if(isset($_POST['tambah'])) {
    $id_role = bersihkan_input($_POST['id_role']);
    $username = bersihkan_input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_lengkap = bersihkan_input($_POST['nama_lengkap']);
    $email = bersihkan_input($_POST['email']);
    $no_telepon = bersihkan_input($_POST['no_telepon']);
    $alamat = bersihkan_input($_POST['alamat']);
    
    $query = "INSERT INTO users (id_role, username, password, nama_lengkap, email, no_telepon, alamat) 
              VALUES ($id_role, '$username', '$password', '$nama_lengkap', '$email', '$no_telepon', '$alamat')";
    
    if(query($query)) {
        $_SESSION['success'] = "User berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan user!";
    }
    header('Location: user.php');
    exit;
}

// EDIT USER
if(isset($_POST['edit'])) {
    $id_user = $_POST['id_user'];
    $id_role = bersihkan_input($_POST['id_role']);
    $username = bersihkan_input($_POST['username']);
    $nama_lengkap = bersihkan_input($_POST['nama_lengkap']);
    $email = bersihkan_input($_POST['email']);
    $no_telepon = bersihkan_input($_POST['no_telepon']);
    $alamat = bersihkan_input($_POST['alamat']);
    
    $query = "UPDATE users SET 
              id_role = $id_role,
              username = '$username',
              nama_lengkap = '$nama_lengkap',
              email = '$email',
              no_telepon = '$no_telepon',
              alamat = '$alamat'
              WHERE id_user = $id_user";
    
    // Jika ada password baru
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET 
                  id_role = $id_role,
                  username = '$username',
                  password = '$password',
                  nama_lengkap = '$nama_lengkap',
                  email = '$email',
                  no_telepon = '$no_telepon',
                  alamat = '$alamat'
                  WHERE id_user = $id_user";
    }
    
    if(query($query)) {
        $_SESSION['success'] = "User berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate user!";
    }
    header('Location: user.php');
    exit;
}

// HAPUS USER
if(isset($_GET['hapus'])) {
    $id_user = $_GET['hapus'];
    
    // Cek apakah user ini digunakan di tabel lain
    $check_booking = query("SELECT * FROM booking WHERE id_user = $id_user");
    $check_transaksi = query("SELECT * FROM transaksi WHERE id_user = $id_user");
    
    if(num_rows($check_booking) > 0 || num_rows($check_transaksi) > 0) {
        $_SESSION['error'] = "User tidak dapat dihapus karena masih memiliki data terkait!";
    } else {
        $query = "DELETE FROM users WHERE id_user = $id_user";
        if(query($query)) {
            $_SESSION['success'] = "User berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus user!";
        }
    }
    header('Location: user.php');
    exit;
}

// AMBIL DATA UNTUK EDIT
$edit_data = null;
if(isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $result = query("SELECT * FROM users WHERE id_user = $id_edit");
    $edit_data = fetch_array($result);
}

// AMBIL SEMUA USER
$users = fetch_all(query("SELECT u.*, r.nama_role FROM users u JOIN roles r ON u.id_role = r.id_role ORDER BY u.id_user DESC"));

// AMBIL DATA ROLE UNTUK DROPDOWN
$roles = fetch_all(query("SELECT * FROM roles"));
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Admin -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                    <span>Menu Admin</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="user.php">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jasa.php">
                            <i class="bi bi-tools"></i> Kelola Jasa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sparepart.php">
                            <i class="bi bi-box"></i> Kelola Sparepart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">
                            <i class="bi bi-calendar-check"></i> Kelola Booking
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaksi.php">
                            <i class="bi bi-cash-stack"></i> Kelola Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">
                            <i class="bi bi-building"></i> Profil Bengkel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Kelola User</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle"></i> Tambah User
                </button>
            </div>

            <!-- Tabel User -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="dataTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>No. Telepon</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($users as $user): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['nama_lengkap']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $user['nama_role'] == 'admin' ? 'danger' : 
                                                ($user['nama_role'] == 'owner' ? 'warning' : 
                                                ($user['nama_role'] == 'pegawai' ? 'info' : 'success')); 
                                        ?>">
                                            <?php echo $user['nama_role']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['no_telepon']; ?></td>
                                    <td><?php echo tgl_indo(date('Y-m-d', strtotime($user['tanggal_daftar']))); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $user['id_user']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?hapus=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- Modal Edit untuk setiap user -->
                                <div class="modal fade" id="modalEdit<?php echo $user['id_user']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Role</label>
                                                            <select class="form-select" name="id_role" required>
                                                                <option value="">Pilih Role</option>
                                                                <?php foreach($roles as $role): ?>
                                                                <option value="<?php echo $role['id_role']; ?>" <?php echo $user['id_role'] == $role['id_role'] ? 'selected' : ''; ?>>
                                                                    <?php echo $role['nama_role']; ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Username</label>
                                                            <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                                                            <input type="password" class="form-control" name="password">
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Nama Lengkap</label>
                                                            <input type="text" class="form-control" name="nama_lengkap" value="<?php echo $user['nama_lengkap']; ?>" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">No. Telepon</label>
                                                            <input type="text" class="form-control" name="no_telepon" value="<?php echo $user['no_telepon']; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Alamat</label>
                                                        <textarea class="form-control" name="alamat" rows="2"><?php echo $user['alamat']; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit" class="btn btn-warning">Update User</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="id_role" required>
                                <option value="">Pilih Role</option>
                                <?php foreach($roles as $role): ?>
                                <option value="<?php echo $role['id_role']; ?>"><?php echo $role['nama_role']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" name="no_telepon">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        }
    });
});

// Auto open modal edit jika ada parameter edit di URL
<?php if(isset($_GET['edit'])): ?>
    $(document).ready(function() {
        $('#modalEdit<?php echo $_GET['edit']; ?>').modal('show');
    });
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
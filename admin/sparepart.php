<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sparepart = fetch_assoc(query("SELECT gambar FROM sparepart WHERE id = $id"));
    if ($sparepart['gambar'] && file_exists("../uploads/sparepart/" . $sparepart['gambar'])) {
        unlink("../uploads/sparepart/" . $sparepart['gambar']);
    }
    
    if (query("DELETE FROM sparepart WHERE id = $id")) {
        $_SESSION['success'] = "Sparepart berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Sparepart gagal dihapus!";
    }
    header("Location: sparepart.php");
    exit();
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sparepart = escape_string($_POST['nama_sparepart']);
    $deskripsi = escape_string($_POST['deskripsi']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $merek = escape_string($_POST['merek']);
    
    $gambar = '';
    if ($_FILES['gambar']['name']) {
        $gambar = upload_gambar($_FILES['gambar'], 'sparepart');
    }
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = $_POST['id'];
        if ($gambar) {
            $old = fetch_assoc(query("SELECT gambar FROM sparepart WHERE id = $id"));
            if ($old['gambar'] && file_exists("../uploads/sparepart/" . $old['gambar'])) {
                unlink("../uploads/sparepart/" . $old['gambar']);
            }
            $query = "UPDATE sparepart SET 
                      nama_sparepart = '$nama_sparepart',
                      deskripsi = '$deskripsi',
                      harga = $harga,
                      stok = $stok,
                      merek = '$merek',
                      gambar = '$gambar'
                      WHERE id = $id";
        } else {
            $query = "UPDATE sparepart SET 
                      nama_sparepart = '$nama_sparepart',
                      deskripsi = '$deskripsi',
                      harga = $harga,
                      stok = $stok,
                      merek = '$merek'
                      WHERE id = $id";
        }
        $message = "Sparepart berhasil diupdate!";
    } else {
        // Insert
        $query = "INSERT INTO sparepart (nama_sparepart, deskripsi, harga, stok, merek, gambar) 
                  VALUES ('$nama_sparepart', '$deskripsi', $harga, $stok, '$merek', '$gambar')";
        $message = "Sparepart berhasil ditambahkan!";
    }
    
    if (query($query)) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = "Sparepart gagal disimpan!";
    }
    header("Location: sparepart.php");
    exit();
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = query("SELECT * FROM sparepart WHERE id = $id");
    $edit_data = fetch_assoc($result);
}

// Get all sparepart
$sparepart = query("SELECT * FROM sparepart ORDER BY id DESC");

$title = "Kelola Sparepart";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <h4 class="text-white mb-4"><i class="fas fa-dashboard me-2"></i>Menu Admin</h4>
                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="users.php"><i class="fas fa-users me-2"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench me-2"></i>Kelola Jasa</a>
                <a href="sparepart.php" class="active"><i class="fas fa-oil-can me-2"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-credit-card me-2"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Kelola Sparepart</h2>
            
            <!-- Form Tambah/Edit -->
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo $edit_data ? 'Edit Sparepart' : 'Tambah Sparepart Baru'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Sparepart</label>
                                <input type="text" class="form-control" name="nama_sparepart" 
                                       value="<?php echo $edit_data['nama_sparepart'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Merek</label>
                                <input type="text" class="form-control" name="merek" 
                                       value="<?php echo $edit_data['merek'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" class="form-control" name="harga" 
                                       value="<?php echo $edit_data['harga'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" 
                                       value="<?php echo $edit_data['stok'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3" required><?php echo $edit_data['deskripsi'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Gambar</label>
                            <input type="file" class="form-control" name="gambar" accept="image/*"
                                   <?php echo $edit_data ? '' : 'required'; ?>>
                            <?php if ($edit_data && $edit_data['gambar']): ?>
                                <small class="text-muted">Gambar lama: <?php echo $edit_data['gambar']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="sparepart.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Tabel Sparepart -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Sparepart</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama</th>
                                    <th>Merek</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while($row = fetch_assoc($sparepart)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <?php if ($row['gambar']): ?>
                                            <img src="../uploads/sparepart/<?php echo $row['gambar']; ?>" 
                                                 width="50" height="50" style="object-fit: cover; border-radius: 10px;">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['nama_sparepart']; ?></td>
                                    <td><?php echo $row['merek']; ?></td>
                                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['stok'] > 10 ? 'success' : ($row['stok'] > 0 ? 'warning' : 'danger'); ?>">
                                            <?php echo $row['stok']; ?>
                                        </span>
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
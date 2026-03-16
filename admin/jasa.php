<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Hapus gambar
    $jasa = fetch_assoc(query("SELECT gambar FROM jasa WHERE id = $id"));
    if ($jasa['gambar'] && file_exists("../uploads/jasa/" . $jasa['gambar'])) {
        unlink("../uploads/jasa/" . $jasa['gambar']);
    }
    
    if (query("DELETE FROM jasa WHERE id = $id")) {
        $_SESSION['success'] = "Jasa berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Jasa gagal dihapus!";
    }
    header("Location: jasa.php");
    exit();
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_jasa = escape_string($_POST['nama_jasa']);
    $deskripsi = escape_string($_POST['deskripsi']);
    $harga = $_POST['harga'];
    $estimasi_waktu = escape_string($_POST['estimasi_waktu']);
    
    $gambar = '';
    if ($_FILES['gambar']['name']) {
        $gambar = upload_gambar($_FILES['gambar'], 'jasa');
    }
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = $_POST['id'];
        if ($gambar) {
            // Hapus gambar lama
            $old = fetch_assoc(query("SELECT gambar FROM jasa WHERE id = $id"));
            if ($old['gambar'] && file_exists("../uploads/jasa/" . $old['gambar'])) {
                unlink("../uploads/jasa/" . $old['gambar']);
            }
            $query = "UPDATE jasa SET 
                      nama_jasa = '$nama_jasa',
                      deskripsi = '$deskripsi',
                      harga = $harga,
                      estimasi_waktu = '$estimasi_waktu',
                      gambar = '$gambar'
                      WHERE id = $id";
        } else {
            $query = "UPDATE jasa SET 
                      nama_jasa = '$nama_jasa',
                      deskripsi = '$deskripsi',
                      harga = $harga,
                      estimasi_waktu = '$estimasi_waktu'
                      WHERE id = $id";
        }
        $message = "Jasa berhasil diupdate!";
    } else {
        // Insert
        $query = "INSERT INTO jasa (nama_jasa, deskripsi, harga, estimasi_waktu, gambar) 
                  VALUES ('$nama_jasa', '$deskripsi', $harga, '$estimasi_waktu', '$gambar')";
        $message = "Jasa berhasil ditambahkan!";
    }
    
    if (query($query)) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = "Jasa gagal disimpan!";
    }
    header("Location: jasa.php");
    exit();
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = query("SELECT * FROM jasa WHERE id = $id");
    $edit_data = fetch_assoc($result);
}

// Get all jasa
$jasa = query("SELECT * FROM jasa ORDER BY id DESC");

$title = "Kelola Jasa";
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
                <a href="jasa.php" class="active"><i class="fas fa-wrench me-2"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-oil-can me-2"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-credit-card me-2"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Kelola Jasa Service</h2>
            
            <!-- Form Tambah/Edit -->
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo $edit_data ? 'Edit Jasa' : 'Tambah Jasa Baru'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Jasa</label>
                                <input type="text" class="form-control" name="nama_jasa" 
                                       value="<?php echo $edit_data['nama_jasa'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" class="form-control" name="harga" 
                                       value="<?php echo $edit_data['harga'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3" required><?php echo $edit_data['deskripsi'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimasi Waktu</label>
                                <input type="text" class="form-control" name="estimasi_waktu" 
                                       value="<?php echo $edit_data['estimasi_waktu'] ?? ''; ?>" 
                                       placeholder="Contoh: 2 Jam" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar</label>
                                <input type="file" class="form-control" name="gambar" accept="image/*"
                                       <?php echo $edit_data ? '' : 'required'; ?>>
                                <?php if ($edit_data && $edit_data['gambar']): ?>
                                    <small class="text-muted">Gambar lama: <?php echo $edit_data['gambar']; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="jasa.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Tabel Jasa -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Jasa</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama Jasa</th>
                                    <th>Harga</th>
                                    <th>Estimasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while($row = fetch_assoc($jasa)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <?php if ($row['gambar']): ?>
                                            <img src="../uploads/jasa/<?php echo $row['gambar']; ?>" 
                                                 width="50" height="50" style="object-fit: cover; border-radius: 10px;">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['nama_jasa']; ?></td>
                                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $row['estimasi_waktu']; ?></td>
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
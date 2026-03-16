<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1);

// Handle Upload QRIS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_bank = escape_string($_POST['nama_bank']);
    $atas_nama = escape_string($_POST['atas_nama']);
    
    if ($_FILES['gambar']['name']) {
        $gambar = upload_gambar($_FILES['gambar'], 'qris');
        
        if ($gambar) {
            // Hapus gambar lama
            $old = query("SELECT * FROM qris WHERE id = 1");
            if (num_rows($old) > 0) {
                $old_data = fetch_assoc($old);
                if ($old_data['gambar'] && file_exists("../uploads/qris/" . $old_data['gambar'])) {
                    unlink("../uploads/qris/" . $old_data['gambar']);
                }
                $query = "UPDATE qris SET gambar = '$gambar', nama_bank = '$nama_bank', atas_nama = '$atas_nama' WHERE id = 1";
            } else {
                $query = "INSERT INTO qris (gambar, nama_bank, atas_nama) VALUES ('$gambar', '$nama_bank', '$atas_nama')";
            }
            
            if (query($query)) {
                $_SESSION['success'] = "QRIS berhasil diupload!";
            } else {
                $_SESSION['error'] = "QRIS gagal diupload!";
            }
        } else {
            $_SESSION['error'] = "Gagal upload gambar!";
        }
    } else {
        $_SESSION['error'] = "Pilih gambar terlebih dahulu!";
    }
    header("Location: qris.php");
    exit();
}

// Ambil data QRIS
$qris = query("SELECT * FROM qris WHERE id = 1");
$data_qris = num_rows($qris) > 0 ? fetch_assoc($qris) : null;

$title = "Upload QRIS";
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
                <a href="sparepart.php"><i class="fas fa-oil-can me-2"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-credit-card me-2"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php" class="active"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Upload QRIS Pembayaran</h2>
            
            <div class="row">
                <div class="col-md-6" data-aos="fade-right">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i>Upload QRIS Baru</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Nama Bank</label>
                                    <input type="text" class="form-control" name="nama_bank" 
                                           value="<?php echo $data_qris['nama_bank'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Atas Nama</label>
                                    <input type="text" class="form-control" name="atas_nama" 
                                           value="<?php echo $data_qris['atas_nama'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Gambar QRIS</label>
                                    <input type="file" class="form-control" name="gambar" accept="image/*" required>
                                    <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 5MB</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-upload me-2"></i>Upload QRIS
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6" data-aos="fade-left">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview QRIS</h5>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($data_qris && $data_qris['gambar']): ?>
                                <img src="../uploads/qris/<?php echo $data_qris['gambar']; ?>" 
                                     alt="QRIS" class="img-fluid mb-3" style="max-width: 300px;">
                                <p class="mb-1"><strong>Bank:</strong> <?php echo $data_qris['nama_bank']; ?></p>
                                <p><strong>Atas Nama:</strong> <?php echo $data_qris['atas_nama']; ?></p>
                            <?php else: ?>
                                <div class="text-muted py-5">
                                    <i class="fas fa-qrcode fa-5x mb-3"></i>
                                    <p>Belum ada QRIS</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
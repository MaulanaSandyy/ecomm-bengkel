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
                $_SESSION['success'] = "Kode QRIS berhasil diunggah dan disimpan!";
            } else {
                $_SESSION['error'] = "Terjadi kesalahan database, QRIS gagal disimpan!";
            }
        } else {
            $_SESSION['error'] = "Format gambar tidak didukung atau ukuran terlalu besar!";
        }
    } else {
        $_SESSION['error'] = "Pilih file gambar QRIS terlebih dahulu!";
    }
    header("Location: qris.php");
    exit();
}

// Ambil data QRIS
$qris = query("SELECT * FROM qris WHERE id = 1");
$data_qris = num_rows($qris) > 0 ? fetch_assoc($qris) : null;

$title = "Kelola QRIS";
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
                <a href="users.php"><i class="fas fa-users"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-box-open"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-cash-register"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="qris.php" class="active"><i class="fas fa-qrcode"></i>Upload QRIS</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold mb-4 text-dark">Manajemen Pembayaran QRIS</h3>
            
            <div class="row g-4 align-items-start">
                
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex align-items-center">
                            <i class="fas fa-upload text-primary me-3 fs-5"></i>
                            <h5 class="fw-bold text-dark mb-0">Upload Barcode Baru</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info rounded-3 mb-4 d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <span class="small">QRIS ini akan ditampilkan kepada customer di halaman riwayat transaksi bagi yang memilih metode pembayaran Transfer/QRIS.</span>
                            </div>

                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Penyedia / Nama Bank</label>
                                    <input type="text" class="form-control bg-light" name="nama_bank" 
                                           placeholder="Cth: BCA, Mandiri, Dana, GoPay" value="<?php echo $data_qris['nama_bank'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Nama Pemilik Rekening (Atas Nama)</label>
                                    <input type="text" class="form-control bg-light" name="atas_nama" 
                                           placeholder="Cth: Bengkel Jaya Abadi / Bpk Budi" value="<?php echo $data_qris['atas_nama'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">File Gambar QRIS (Bentuk Kotak)</label>
                                    <input type="file" class="form-control bg-light" name="gambar" accept="image/*" required id="qrisInput" onchange="previewImage(this, 'previewQrisTemp')">
                                    <small class="text-muted fst-italic mt-1 d-block">Ekstensi .jpg, .png. Disarankan rasio 1:1.</small>
                                </div>
                                
                                <img id="previewQrisTemp" style="display:none;">

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill shadow-sm fw-bold mt-2">
                                    Simpan & Perbarui QRIS <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6" data-aos="fade-down">
                    <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                        <div class="card-body p-5 text-center d-flex flex-column align-items-center justify-content-center h-100">
                            
                            <h5 class="fw-bold text-white mb-4 text-uppercase letter-spacing-1">Preview QRIS Saat Ini</h5>
                            
                            <div class="bg-white p-4 rounded-4 shadow mb-4 d-inline-block position-relative" style="max-width: 300px;">
                                <div class="position-absolute top-0 start-0 border-top border-start border-primary border-4 rounded-tl-4" style="width: 20px; height: 20px; margin: 10px;"></div>
                                <div class="position-absolute top-0 end-0 border-top border-end border-primary border-4 rounded-tr-4" style="width: 20px; height: 20px; margin: 10px;"></div>
                                <div class="position-absolute bottom-0 start-0 border-bottom border-start border-primary border-4 rounded-bl-4" style="width: 20px; height: 20px; margin: 10px;"></div>
                                <div class="position-absolute bottom-0 end-0 border-bottom border-end border-primary border-4 rounded-br-4" style="width: 20px; height: 20px; margin: 10px;"></div>

                                <?php if ($data_qris && $data_qris['gambar']): ?>
                                    <img src="../uploads/qris/<?php echo $data_qris['gambar']; ?>" alt="QRIS Code" class="img-fluid rounded" style="width: 100%; aspect-ratio: 1/1; object-fit: contain;">
                                <?php else: ?>
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted" style="width: 200px; height: 200px;">
                                        <i class="fas fa-qrcode fa-4x mb-2 opacity-50"></i>
                                        <span class="small fw-bold">BELUM ADA QRIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($data_qris && $data_qris['gambar']): ?>
                                <div class="bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 mb-2 d-inline-block">
                                    <i class="fas fa-building me-2 text-warning"></i><?php echo strtoupper($data_qris['nama_bank']); ?>
                                </div>
                                <p class="text-white-50 small mb-0">A.N. <strong class="text-white"><?php echo strtoupper($data_qris['atas_nama']); ?></strong></p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
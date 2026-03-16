<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1); // Hanya admin

// Ambil data profil
$profil = query("SELECT * FROM profil_bengkel WHERE id = 1");
if (num_rows($profil) == 0) {
    // Insert default profil jika belum ada
    query("INSERT INTO profil_bengkel (nama_bengkel, alamat, no_telp, email, deskripsi, jam_operasional) 
           VALUES ('Bengkel Mobil Jaya Abadi', 'Jl. Raya Otomotif No. 123, Jakarta', '021-555-1234', 
                   'info@jayabadi.com', 'Bengkel mobil profesional dengan teknisi berpengalaman.', 
                   'Senin - Sabtu: 08:00 - 20:00, Minggu: 09:00 - 15:00')");
    $profil = query("SELECT * FROM profil_bengkel WHERE id = 1");
}
$data = fetch_assoc($profil);

// Handle Update Profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_bengkel = escape_string($_POST['nama_bengkel']);
    $alamat = escape_string($_POST['alamat']);
    $no_telp = escape_string($_POST['no_telp']);
    $email = escape_string($_POST['email']);
    $deskripsi = escape_string($_POST['deskripsi']);
    $jam_operasional = escape_string($_POST['jam_operasional']);
    
    $logo = $data['logo'];
    $gambar_banner = $data['gambar_banner'];
    
    // Upload logo
    if ($_FILES['logo']['name']) {
        $upload_logo = upload_gambar($_FILES['logo'], 'profil');
        if ($upload_logo) {
            // Hapus logo lama
            if ($logo && file_exists("../uploads/profil/" . $logo)) {
                unlink("../uploads/profil/" . $logo);
            }
            $logo = $upload_logo;
        }
    }
    
    // Upload banner
    if ($_FILES['gambar_banner']['name']) {
        $upload_banner = upload_gambar($_FILES['gambar_banner'], 'profil');
        if ($upload_banner) {
            // Hapus banner lama
            if ($gambar_banner && file_exists("../uploads/profil/" . $gambar_banner)) {
                unlink("../uploads/profil/" . $gambar_banner);
            }
            $gambar_banner = $upload_banner;
        }
    }
    
    $query = "UPDATE profil_bengkel SET 
              nama_bengkel = '$nama_bengkel',
              alamat = '$alamat',
              no_telp = '$no_telp',
              email = '$email',
              deskripsi = '$deskripsi',
              jam_operasional = '$jam_operasional',
              logo = '$logo',
              gambar_banner = '$gambar_banner'
              WHERE id = 1";
    
    if (query($query)) {
        $_SESSION['success'] = "Profil bengkel berhasil diupdate!";
        // Refresh data
        $profil = query("SELECT * FROM profil_bengkel WHERE id = 1");
        $data = fetch_assoc($profil);
    } else {
        $_SESSION['error'] = "Profil bengkel gagal diupdate!";
    }
}

$title = "Profil Bengkel";
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
                <a href="profil.php" class="active"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Profil Bengkel</h2>
            
            <!-- Preview Banner -->
            <?php if ($data['gambar_banner']): ?>
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-body p-0">
                    <img src="../uploads/profil/<?php echo $data['gambar_banner']; ?>" 
                         alt="Banner" class="img-fluid rounded" style="width: 100%; max-height: 300px; object-fit: cover;">
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Form Edit Profil -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Profil Bengkel</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Bengkel</label>
                                <input type="text" class="form-control" name="nama_bengkel" 
                                       value="<?php echo $data['nama_bengkel']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" name="no_telp" 
                                       value="<?php echo $data['no_telp']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo $data['email']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam Operasional</label>
                                <input type="text" class="form-control" name="jam_operasional" 
                                       value="<?php echo $data['jam_operasional']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3" required><?php echo $data['alamat']; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Bengkel</label>
                            <textarea class="form-control" name="deskripsi" rows="5" required><?php echo $data['deskripsi']; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo Bengkel</label>
                                <input type="file" class="form-control" name="logo" accept="image/*">
                                <?php if ($data['logo']): ?>
                                    <div class="mt-2">
                                        <img src="../uploads/profil/<?php echo $data['logo']; ?>" 
                                             alt="Logo" style="max-width: 100px; max-height: 100px;" 
                                             class="img-thumbnail">
                                        <p class="text-muted mt-1">Logo saat ini: <?php echo $data['logo']; ?></p>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Banner</label>
                                <input type="file" class="form-control" name="gambar_banner" accept="image/*">
                                <?php if ($data['gambar_banner']): ?>
                                    <div class="mt-2">
                                        <img src="../uploads/profil/<?php echo $data['gambar_banner']; ?>" 
                                             alt="Banner" style="max-width: 200px; max-height: 100px;" 
                                             class="img-thumbnail">
                                        <p class="text-muted mt-1">Banner saat ini: <?php echo $data['gambar_banner']; ?></p>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 5MB</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Preview Profil -->
            <div class="card mt-4" data-aos="fade-up">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Profil</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <?php if ($data['logo']): ?>
                                <img src="../uploads/profil/<?php echo $data['logo']; ?>" 
                                     alt="Logo" class="img-fluid mb-3" style="max-width: 150px;">
                            <?php else: ?>
                                <i class="fas fa-building fa-5x text-muted mb-3"></i>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h3><?php echo $data['nama_bengkel']; ?></h3>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i><?php echo $data['alamat']; ?></p>
                            <p><i class="fas fa-phone text-primary me-2"></i><?php echo $data['no_telp']; ?></p>
                            <p><i class="fas fa-envelope text-primary me-2"></i><?php echo $data['email']; ?></p>
                            <p><i class="fas fa-clock text-primary me-2"></i><?php echo $data['jam_operasional']; ?></p>
                            <p class="mt-3"><?php echo $data['deskripsi']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
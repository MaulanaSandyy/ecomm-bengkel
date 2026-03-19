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
        $_SESSION['success'] = "Profil bengkel berhasil diperbarui!";
        // Refresh data
        $profil = query("SELECT * FROM profil_bengkel WHERE id = 1");
        $data = fetch_assoc($profil);
    } else {
        $_SESSION['error'] = "Terjadi kesalahan sistem. Profil gagal diupdate!";
    }
}

$title = "Profil Bengkel";
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
                <a href="profil.php" class="active"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode"></i>Upload QRIS</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold mb-4 text-dark">Informasi Profil Bengkel</h3>
            
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100" data-aos="fade-up">
                        <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex align-items-center">
                            <i class="fas fa-edit text-primary me-3 fs-5"></i>
                            <h5 class="fw-bold text-dark mb-0">Ubah Data Profil</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Nama Bengkel</label>
                                        <input type="text" class="form-control bg-light" name="nama_bengkel" value="<?php echo $data['nama_bengkel']; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Email Bisnis</label>
                                        <input type="email" class="form-control bg-light" name="email" value="<?php echo $data['email']; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Nomor Telepon/WA</label>
                                        <input type="text" class="form-control bg-light" name="no_telp" value="<?php echo $data['no_telp']; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Jam Operasional</label>
                                        <input type="text" class="form-control bg-light" name="jam_operasional" value="<?php echo $data['jam_operasional']; ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small fw-bold">Alamat Lengkap</label>
                                        <textarea class="form-control bg-light" name="alamat" rows="2" required><?php echo $data['alamat']; ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small fw-bold">Deskripsi/Tentang Bengkel</label>
                                        <textarea class="form-control bg-light" name="deskripsi" rows="4" required><?php echo $data['deskripsi']; ?></textarea>
                                    </div>
                                    
                                    <div class="col-12 mt-4"><hr class="text-muted opacity-25"></div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Logo Bengkel (JPG/PNG)</label>
                                        <input type="file" class="form-control bg-light mb-2" name="logo" accept="image/*" id="inputLogo" onchange="previewImage(this, 'previewLogo')">
                                        <div class="text-center p-3 bg-light rounded border">
                                            <img id="previewLogo" src="<?php echo $data['logo'] ? '../uploads/profil/'.$data['logo'] : ''; ?>" 
                                                 alt="Logo" class="img-fluid rounded" style="max-height: 80px; <?php echo !$data['logo'] ? 'display:none;' : ''; ?>">
                                            <?php if(!$data['logo']): ?><p class="text-muted small mb-0" id="textLogo">Belum ada logo</p><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Banner/Sampul Depan (JPG/PNG)</label>
                                        <input type="file" class="form-control bg-light mb-2" name="gambar_banner" accept="image/*" id="inputBanner" onchange="previewImage(this, 'previewBanner')">
                                        <div class="text-center p-3 bg-light rounded border">
                                            <img id="previewBanner" src="<?php echo $data['gambar_banner'] ? '../uploads/profil/'.$data['gambar_banner'] : ''; ?>" 
                                                 alt="Banner" class="img-fluid rounded" style="max-height: 80px; object-fit: cover; width: 100%; <?php echo !$data['gambar_banner'] ? 'display:none;' : ''; ?>">
                                            <?php if(!$data['gambar_banner']): ?><p class="text-muted small mb-0" id="textBanner">Belum ada banner</p><?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm fw-bold">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-xl-top" style="top: 100px;" data-aos="fade-down">
                        <div class="position-relative">
                            <?php if ($data['gambar_banner']): ?>
                                <img src="../uploads/profil/<?php echo $data['gambar_banner']; ?>" alt="Banner" class="w-100" style="height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary bg-opacity-25 w-100 d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="fas fa-image text-muted fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="position-absolute start-50 translate-middle" style="top: 150px;">
                                <div class="bg-white p-1 rounded-circle shadow">
                                    <?php if ($data['logo']): ?>
                                        <img src="../uploads/profil/<?php echo $data['logo']; ?>" alt="Logo" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="fas fa-building fa-2x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body text-center mt-5 pt-3 pb-4 px-4">
                            <h5 class="fw-bold text-dark mb-1"><?php echo $data['nama_bengkel']; ?></h5>
                            <p class="small text-muted mb-4"><?php echo $data['deskripsi']; ?></p>
                            
                            <ul class="list-unstyled text-start small text-muted mb-0 d-flex flex-column gap-3">
                                <li><i class="fas fa-map-marker-alt text-primary w-20px text-center me-2"></i><?php echo $data['alamat']; ?></li>
                                <li><i class="fas fa-phone-alt text-primary w-20px text-center me-2"></i><?php echo $data['no_telp']; ?></li>
                                <li><i class="fas fa-envelope text-primary w-20px text-center me-2"></i><?php echo $data['email']; ?></li>
                                <li><i class="fas fa-clock text-primary w-20px text-center me-2"></i><?php echo $data['jam_operasional']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi preview gambar untuk input logo & banner
document.getElementById('inputLogo').addEventListener('change', function() {
    if(this.files && this.files[0]) {
        document.getElementById('previewLogo').style.display = 'inline-block';
        let txt = document.getElementById('textLogo'); if(txt) txt.style.display = 'none';
    }
});
document.getElementById('inputBanner').addEventListener('change', function() {
    if(this.files && this.files[0]) {
        document.getElementById('previewBanner').style.display = 'inline-block';
        let txt = document.getElementById('textBanner'); if(txt) txt.style.display = 'none';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
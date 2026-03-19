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
        $message = "Informasi jasa berhasil diupdate!";
    } else {
        // Insert
        $query = "INSERT INTO jasa (nama_jasa, deskripsi, harga, estimasi_waktu, gambar) 
                  VALUES ('$nama_jasa', '$deskripsi', $harga, '$estimasi_waktu', '$gambar')";
        $message = "Layanan jasa baru berhasil ditambahkan!";
    }
    
    if (query($query)) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = "Data jasa gagal disimpan!";
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

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-shield-alt me-2"></i>Menu Admin
                </h5>
                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="users.php"><i class="fas fa-users"></i>Kelola Users</a>
                <a href="jasa.php" class="active"><i class="fas fa-wrench"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-box-open"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-cash-register"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode"></i>Upload QRIS</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold mb-4 text-dark">Katalog Layanan Jasa</h3>
            
            <div class="row g-4 flex-column-reverse flex-lg-row">
                
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-list text-primary me-2"></i>Daftar Jasa Service</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Layanan</th>
                                            <th>Tarif Harga</th>
                                            <th>Estimasi</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <?php $no = 1; while($row = fetch_assoc($jasa)): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?php echo $no++; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if ($row['gambar']): ?>
                                                        <img src="../uploads/jasa/<?php echo $row['gambar']; ?>" 
                                                             alt="Jasa" class="rounded-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted border" style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark"><?php echo $row['nama_jasa']; ?></h6>
                                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;"><?php echo $row['deskripsi']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-success">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                            <td><span class="badge bg-light text-dark border"><i class="far fa-clock text-warning me-1"></i><?php echo $row['estimasi_waktu']; ?></span></td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" onclick="return confirmDelete('?delete=<?php echo $row['id']; ?>', 'Layanan <?php echo $row['nama_jasa']; ?> akan dihapus secara permanen!')" 
                                                       class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                        
                                        <?php if(num_rows($jasa) == 0): ?>
                                            <tr><td colspan="5" class="text-center py-4 text-muted">Katalog jasa masih kosong.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-down">
                    <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 100px; z-index: 10;">
                        <div class="card-header <?php echo $edit_data ? 'bg-warning' : 'bg-primary'; ?> text-white pt-4 pb-3 px-4 rounded-top-4 border-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas <?php echo $edit_data ? 'fa-edit' : 'fa-plus-circle'; ?> me-2"></i>
                                <?php echo $edit_data ? 'Edit Jasa' : 'Tambah Jasa Baru'; ?>
                            </h5>
                        </div>
                        <div class="card-body p-4 bg-white rounded-bottom-4">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?php if ($edit_data): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Nama Jasa</label>
                                    <input type="text" class="form-control bg-light" name="nama_jasa" 
                                           value="<?php echo $edit_data['nama_jasa'] ?? ''; ?>" placeholder="Cth: Tune Up Mesin" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Tarif Harga (Rp)</label>
                                    <input type="number" class="form-control bg-light fw-bold text-success" name="harga" 
                                           value="<?php echo $edit_data['harga'] ?? ''; ?>" placeholder="0" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Estimasi Waktu Pengerjaan</label>
                                    <input type="text" class="form-control bg-light" name="estimasi_waktu" 
                                           value="<?php echo $edit_data['estimasi_waktu'] ?? ''; ?>" 
                                           placeholder="Cth: 1 - 2 Jam" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Deskripsi Layanan</label>
                                    <textarea class="form-control bg-light" name="deskripsi" rows="3" placeholder="Jelaskan detail tindakan yang dilakukan pada layanan ini..." required><?php echo $edit_data['deskripsi'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold">Foto/Ilustrasi <?php echo $edit_data ? '<span class="fw-normal">(Opsional)</span>' : ''; ?></label>
                                    
                                    <div class="position-relative">
                                        <input type="file" class="form-control bg-light" name="gambar" accept="image/*" id="gambarInput"
                                               <?php echo $edit_data ? '' : 'required'; ?> onchange="previewImage(this, 'previewImg')">
                                    </div>

                                    <div class="mt-3 text-center rounded-3 bg-light border p-2 <?php echo ($edit_data && $edit_data['gambar']) ? '' : 'd-none'; ?>" id="previewContainer">
                                        <img id="previewImg" src="<?php echo ($edit_data && $edit_data['gambar']) ? '../uploads/jasa/' . $edit_data['gambar'] : ''; ?>" 
                                             class="img-fluid rounded-3" style="max-height: 150px; object-fit: cover;">
                                        <?php if ($edit_data && $edit_data['gambar']): ?>
                                            <small class="d-block mt-2 text-muted fst-italic">File terpasang: <?php echo $edit_data['gambar']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn <?php echo $edit_data ? 'btn-warning' : 'btn-primary'; ?> flex-grow-1 rounded-pill shadow-sm fw-bold text-white">
                                        <i class="fas fa-save me-2"></i><?php echo $edit_data ? 'Update Jasa' : 'Simpan Jasa'; ?>
                                    </button>
                                    <?php if ($edit_data): ?>
                                        <a href="jasa.php" class="btn btn-light border rounded-pill px-4">Batal</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Penambahan sedikit JS untuk menampilkan preview box saat user memilih gambar baru
document.getElementById('gambarInput').addEventListener('change', function() {
    if(this.files && this.files[0]) {
        document.getElementById('previewContainer').classList.remove('d-none');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
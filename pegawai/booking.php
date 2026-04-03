<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(3); // khusus pegawai

$title = "Kelola Booking";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Proses update status booking
if(isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $query = "UPDATE booking SET status = '$status' WHERE id = $id";
    if(query($query)) {
        echo "<script>alert('Status booking berhasil diupdate'); window.location.href='booking.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate status');</script>";
    }
}

// Ambil data booking
$bookings = query("SELECT b.*, u.nama_lengkap, u.no_hp, u.alamat, j.nama_jasa, j.harga as harga_jasa
                   FROM booking b 
                   JOIN users u ON b.user_id = u.id 
                   LEFT JOIN jasa j ON b.jasa_id = j.id 
                   WHERE b.status IN ('pending','dikonfirmasi')
                   ORDER BY b.created_at DESC");

// Jika ada edit ID
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_data = fetch_assoc(query("SELECT b.*, u.nama_lengkap, u.no_hp, u.alamat, j.nama_jasa 
                                     FROM booking b 
                                     JOIN users u ON b.user_id = u.id 
                                     LEFT JOIN jasa j ON b.jasa_id = j.id 
                                     WHERE b.id = $edit_id"));
}
?>

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-cog me-2"></i>Menu Pegawai
                </h5>
                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="booking.php" class="active"><i class="fas fa-calendar-alt"></i>Booking</a>
                <a href="service.php"><i class="fas fa-tools"></i>Service</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Kelola Booking</h3>
                    <p class="text-muted small mb-0">Kelola dan update status booking customer.</p>
                </div>
            </div>
            
            <!-- Daftar Booking -->
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-check text-warning me-2"></i>Daftar Booking Masuk</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Customer</th>
                                    <th>Jasa Service</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php $no = 1; while($row = fetch_assoc($bookings)): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="fw-medium text-dark"><?php echo $row['nama_lengkap']; ?></div>
                                        <div class="small text-muted"><?php echo $row['no_hp']; ?></div>
                                    </td>
                                    <td><?php echo $row['nama_jasa'] ?: '-'; ?></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-dark"><?php echo date('d M Y', strtotime($row['tanggal_booking'])); ?></span>
                                            <span class="small text-muted"><i class="far fa-clock me-1"></i><?php echo date('H:i', strtotime($row['jam_booking'])); ?> WIB</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = [
                                            'pending' => 'warning',
                                            'dikonfirmasi' => 'info',
                                            'selesai' => 'success',
                                            'batal' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $badge[$row['status']]; ?> bg-opacity-10 text-<?php echo $badge[$row['status']]; ?> rounded-pill px-3 py-2 text-uppercase small">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    
                                </tr>
                                
                                <!-- Modal Update Status -->
                                <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header border-0 pt-4 px-4">
                                                <h5 class="modal-title fw-bold">Update Status Booking</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body px-4">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Customer</label>
                                                        <input type="text" class="form-control bg-light" value="<?php echo $row['nama_lengkap']; ?>" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Jasa</label>
                                                        <input type="text" class="form-control bg-light" value="<?php echo $row['nama_jasa'] ?: '-'; ?>" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Jadwal</label>
                                                        <input type="text" class="form-control bg-light" value="<?php echo date('d M Y H:i', strtotime($row['tanggal_booking'] . ' ' . $row['jam_booking'])); ?> WIB" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Status</label>
                                                        <select name="status" class="form-select rounded-3" required>
                                                            <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="dikonfirmasi" <?php echo $row['status'] == 'dikonfirmasi' ? 'selected' : ''; ?>>Dikonfirmasi</option>
                                                            <option value="selesai" <?php echo $row['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                                            <option value="batal" <?php echo $row['status'] == 'batal' ? 'selected' : ''; ?>>Batal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pb-4 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="update_status" class="btn btn-primary rounded-pill px-4">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                
                                <?php if(num_rows($bookings) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada booking masuk.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto show modal if edit parameter exists
    <?php if(isset($_GET['edit'])): ?>
    var myModal = new bootstrap.Modal(document.getElementById('updateModal<?php echo $_GET['edit']; ?>'));
    myModal.show();
    <?php endif; ?>
});
</script>

<?php include '../includes/footer.php'; ?>
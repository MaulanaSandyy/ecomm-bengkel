<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(3); // khusus pegawai

$title = "Kelola Service";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Proses update status service
if(isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['update_status'];

    if($status == 'selesai'){
        $query = "UPDATE service 
                  SET status = 'selesai', tanggal_selesai = NOW() 
                  WHERE id = $id";
    } else {
        $query = "UPDATE service 
                  SET status = '$status' 
                  WHERE id = $id";
    }

    query($query);

    echo "<script>window.location='service.php';</script>";
    exit();
}

// Proses tambah catatan
if(isset($_POST['save_catatan'])) {
    $id = $_POST['id'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    
    $query = "UPDATE service SET catatan_service = '$catatan' WHERE id = $id";
    query($query);

    echo "<script>window.location='service.php';</script>";
    exit();
}

// Ambil data service untuk pegawai ini
$services = query("SELECT s.*, b.tanggal_booking, b.jam_booking, u.nama_lengkap, u.no_hp, u.alamat, j.nama_jasa, j.harga as harga_jasa
                   FROM service s
                   JOIN booking b ON s.booking_id = b.id
                   JOIN users u ON b.user_id = u.id
                   LEFT JOIN jasa j ON b.jasa_id = j.id
                   WHERE s.pegawai_id = $user_id
                   ORDER BY s.id DESC");

// Jika ada edit ID
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_data = fetch_assoc(query("SELECT s.*, b.tanggal_booking, b.jam_booking, u.nama_lengkap, u.no_hp, j.nama_jasa 
                                     FROM service s
                                     JOIN booking b ON s.booking_id = b.id
                                     JOIN users u ON b.user_id = u.id
                                     LEFT JOIN jasa j ON b.jasa_id = j.id
                                     WHERE s.id = $edit_id"));
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
                <a href="booking.php"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="service.php" class="active"><i class="fas fa-tools"></i>Service Saya</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Kelola Service</h3>
                    <p class="text-muted small mb-0">Kelola pekerjaan service yang ditugaskan kepada Anda.</p>
                </div>
            </div>
            
            <!-- Daftar Service -->
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tools text-success me-2"></i>Daftar Service Saya</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Customer</th>
                                    <th>Jasa Service</th>
                                    <th>Jadwal Booking</th>
                                    <th>Status Service</th>
                                    <th>Catatan</th>
                                    <th class="text-end pe-4">Aksi</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php $no = 1; while($row = fetch_assoc($services)): ?>
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
                                        $badge_status = [
                                            'menunggu' => 'warning',
                                            'dikerjakan' => 'primary',
                                            'selesai' => 'success',
                                            'batal' => 'danger'
                                        ];
                                        $status_color = isset($badge_status[$row['status']]) ? $badge_status[$row['status']] : 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $status_color; ?> bg-opacity-10 text-<?php echo $status_color; ?> rounded-pill px-3 py-2 text-uppercase small">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                            <textarea name="catatan" class="form-control" rows="2"
                                                placeholder="Isi catatan..."><?php echo htmlspecialchars($row['catatan_service'] ?? ''); ?></textarea>

                                            <button type="submit" name="save_catatan" class="btn btn-sm btn-outline-primary mt-1">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                            <?php if($row['status'] == 'antri'): ?>
                                                <button type="submit" name="update_status" value="dikerjakan" class="btn btn-sm btn-primary">
                                                    Mulai Kerjakan
                                                </button>

                                            <?php elseif($row['status'] == 'dikerjakan'): ?>
                                                <button type="submit" name="update_status" value="selesai" class="btn btn-sm btn-success">
                                                    Selesai Dikerjakan
                                                </button>

                                            <?php else: ?>
                                                <span>-</span>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td>
                                        <?php echo $row['tanggal_selesai'] ? date('d M Y', strtotime($row['tanggal_selesai'])) : '-'; ?>
                                    </td>
                                </tr>
                                
                                <!-- Modal Update Service -->
                                <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header border-0 pt-4 px-4">
                                                <h5 class="modal-title fw-bold">Update Service</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body px-4">
                                                <!-- Form Update Status -->
                                                <form method="POST">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Customer</label>
                                                        <input type="text" class="form-control bg-light" value="<?php echo $row['nama_lengkap']; ?>" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Jasa Service</label>
                                                        <input type="text" class="form-control bg-light" value="<?php echo $row['nama_jasa'] ?: '-'; ?>" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Jadwal Booking</label>
                                                        <input type="text" class="form-control bg-light" value="<?php echo date('d M Y H:i', strtotime($row['tanggal_booking'] . ' ' . $row['jam_booking'])); ?> WIB" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Status Pengerjaan</label>
                                                        <select name="status" class="form-select rounded-3" required>
                                                            <option value="menunggu" <?php echo $row['status'] == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                                            <option value="dikerjakan" <?php echo $row['status'] == 'dikerjakan' ? 'selected' : ''; ?>>Sedang Dikerjakan</option>
                                                            <option value="selesai" <?php echo $row['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                                            <option value="batal" <?php echo $row['status'] == 'batal' ? 'selected' : ''; ?>>Batal</option>
                                                        </select>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" name="update_status" class="btn btn-primary rounded-pill px-4 flex-grow-1">Update Status</button>
                                                    </div>
                                                </form>
                                                
                                                <hr class="my-4">
                                                
                                                <!-- Form Catatan -->
                                                <form method="POST">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">
                                                            <i class="fas fa-sticky-note me-1"></i> Catatan Pengerjaan
                                                        </label>
                                                        <textarea name="catatan" class="form-control rounded-3" rows="3" placeholder="Isi catatan tentang kendala, sparepart yang digunakan, dll..."><?php echo htmlspecialchars($row['catatan'] ?? ''); ?></textarea>
                                                    </div>
                                                    <button type="submit" name="save_catatan" class="btn btn-outline-primary rounded-pill px-4">Simpan Catatan</button>
                                                </form>
                                            </div>
                                            <div class="modal-footer border-0 pb-4 px-4">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                
                                <?php if(num_rows($services) == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada service yang ditugaskan kepada Anda.</td>
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
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Auto show modal if edit parameter exists
    <?php if(isset($_GET['edit'])): ?>
    var myModal = new bootstrap.Modal(document.getElementById('updateModal<?php echo $_GET['edit']; ?>'));
    myModal.show();
    <?php endif; ?>
});
</script>

<?php include '../includes/footer.php'; ?>
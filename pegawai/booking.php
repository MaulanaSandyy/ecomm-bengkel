<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(3); // khusus pegawai

$title = "Kelola Booking";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['nama_lengkap'];


if(isset($_POST['ambil_booking']) && !empty($_POST['booking_id'])) {

    $booking_id = (int) $_POST['booking_id']; // 🔒 amankan
    $pegawai_id = (int) $_SESSION['user_id'];

    // cek apakah sudah diambil pegawai lain
    $cek = query("SELECT COUNT(*) as total 
                  FROM service 
                  WHERE booking_id = $booking_id 
                  AND pegawai_id IS NOT NULL");

    $data = fetch_assoc($cek);

    if($data['total'] == 0){

        query("INSERT INTO service (booking_id, pegawai_id, status) 
               VALUES ($booking_id, $pegawai_id, 'antri')");

        $_SESSION['success'] = "Booking berhasil diambil!";

    } else {
        $_SESSION['error'] = "Booking sudah diambil!";
    }

    echo "<script>window.location='booking.php';</script>";
    exit();
}

// Ambil data booking
$bookings = query("SELECT b.*, u.nama_lengkap, u.no_hp, u.alamat, j.nama_jasa, j.harga as harga_jasa,
       (
         SELECT id 
         FROM service 
         WHERE booking_id = b.id 
         AND pegawai_id IS NOT NULL 
         LIMIT 1
       ) as service_id
FROM booking b 
JOIN users u ON b.user_id = u.id 
LEFT JOIN jasa j ON b.jasa_id = j.id 
WHERE b.status = 'dikonfirmasi'
ORDER BY b.created_at DESC");


?>

<style>
.booking-card {
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 50px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}
.status-pending { background: #fef3c7; color: #d97706; }
.status-dikonfirmasi { background: #dbeafe; color: #2563eb; }
.status-selesai { background: #d1fae5; color: #059669; }
.status-batal { background: #fee2e2; color: #dc2626; }

</style>

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-cog me-2"></i>Menu Pegawai
                </h5>
                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="booking.php" class="active"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="service.php"><i class="fas fa-tools me-2"></i>Service Saya</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Kelola Booking</h3>
                    <p class="text-muted small mb-0">Kelola dan update status booking customer.</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge bg-success rounded-pill px-3 py-2">
                        <i class="fas fa-user-check me-1"></i> Login sebagai: <?php echo $user_name; ?>
                    </span>
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
                                    <th class="text-end pe-4">Aksi</th>
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
                                    <td>
                                        <?php if($row['service_id'] == NULL): ?>
                                            <form method="POST">
                                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="ambil_booking" class="btn btn-success btn-sm">
                                                    Ambil
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                Sudah Diambil
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <!-- <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-primary rounded-pill" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateModal<?php echo $row['id']; ?>">
                                            <i class="fas fa-edit me-1"></i> Update Status
                                        </button>
                                    </td> -->
                                </tr>
                                
                                <!-- Modal Update Status -->
                                <!-- <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1">
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
                                </div> -->
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});

<?php if(isset($_SESSION['success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?php echo $_SESSION['success']; ?>',
    timer: 3000,
    showConfirmButton: true,
    confirmButtonColor: '#667eea'
});
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?php echo $_SESSION['error']; ?>',
    timer: 3000,
    showConfirmButton: true,
    confirmButtonColor: '#ef4444'
});
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
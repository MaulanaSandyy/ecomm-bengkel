<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1); // Hanya admin

// Handle Update Status
if (isset($_GET['update_status'])) {
    $id = $_GET['update_status'];
    $status = $_GET['status'];
    
    $query = "UPDATE booking SET status = '$status' WHERE id = $id";
    
    if (query($query)) {
        // Jika status dikonfirmasi, buat entri di tabel service
        if ($status == 'dikonfirmasi') {
            $booking = fetch_assoc(query("SELECT * FROM booking WHERE id = $id"));
            query("INSERT INTO service (booking_id, status) VALUES ($id, 'antri')");
        }
        $_SESSION['success'] = "Status booking berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Status booking gagal diupdate!";
    }
    header("Location: booking.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Hapus service terkait dulu
    query("DELETE FROM service WHERE booking_id = $id");
    
    if (query("DELETE FROM booking WHERE id = $id")) {
        $_SESSION['success'] = "Booking berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Booking gagal dihapus!";
    }
    header("Location: booking.php");
    exit();
}

// Handle Filter
$where = "";
if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $status = $_GET['filter_status'];
    $where = " WHERE b.status = '$status'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = escape_string($_GET['search']);
    $where = $where ? $where . " AND " : " WHERE ";
    $where .= "(u.nama_lengkap LIKE '%$search%' OR j.nama_jasa LIKE '%$search%')";
}

// Get all bookings
$bookings = query("SELECT b.*, u.nama_lengkap, u.no_hp, j.nama_jasa, j.harga,
                  s.id as service_id, s.status as service_status
                  FROM booking b 
                  JOIN users u ON b.user_id = u.id 
                  LEFT JOIN jasa j ON b.jasa_id = j.id
                  LEFT JOIN service s ON b.id = s.booking_id
                  $where
                  ORDER BY b.created_at DESC");

// Statistik Booking
$total_booking = num_rows(query("SELECT * FROM booking"));
$pending = num_rows(query("SELECT * FROM booking WHERE status = 'pending'"));
$dikonfirmasi = num_rows(query("SELECT * FROM booking WHERE status = 'dikonfirmasi'"));
$selesai = num_rows(query("SELECT * FROM booking WHERE status = 'selesai'"));
$batal = num_rows(query("SELECT * FROM booking WHERE status = 'batal'"));

$title = "Kelola Booking";
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
                <a href="booking.php" class="active"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-cash-register"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode"></i>Upload QRIS</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold mb-4 text-dark">Manajemen Antrean & Booking</h3>
            
            <div class="row g-3 mb-4 row-cols-2 row-cols-md-5">
                <div class="col" data-aos="fade-up">
                    <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-primary h-100">
                        <div class="card-body p-3 text-center">
                            <h6 class="text-muted mb-2 small fw-bold text-uppercase">Total Booking</h6>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo $total_booking; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="fade-up" data-aos-delay="100">
                    <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-warning h-100">
                        <div class="card-body p-3 text-center">
                            <h6 class="text-muted mb-2 small fw-bold text-uppercase">Pending</h6>
                            <h2 class="mb-0 fw-bold text-warning"><?php echo $pending; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="fade-up" data-aos-delay="200">
                    <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-info h-100">
                        <div class="card-body p-3 text-center">
                            <h6 class="text-muted mb-2 small fw-bold text-uppercase">Dikonfirmasi</h6>
                            <h2 class="mb-0 fw-bold text-info"><?php echo $dikonfirmasi; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="fade-up" data-aos-delay="300">
                    <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-success h-100">
                        <div class="card-body p-3 text-center">
                            <h6 class="text-muted mb-2 small fw-bold text-uppercase">Selesai</h6>
                            <h2 class="mb-0 fw-bold text-success"><?php echo $selesai; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="fade-up" data-aos-delay="400">
                    <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-danger h-100">
                        <div class="card-body p-3 text-center">
                            <h6 class="text-muted mb-2 small fw-bold text-uppercase">Dibatalkan</h6>
                            <h2 class="mb-0 fw-bold text-danger"><?php echo $batal; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom">
                    <form method="GET" action="" class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Filter Status</label>
                            <select name="filter_status" class="form-select bg-light border-0">
                                <option value="">Semua Status</option>
                                <option value="pending" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="dikonfirmasi" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'dikonfirmasi') ? 'selected' : ''; ?>>Dikonfirmasi</option>
                                <option value="selesai" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="batal" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Pencarian Data</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 px-3"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Ketik nama customer atau layanan jasa..." 
                                       value="<?php echo $_GET['search'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill shadow-sm">Filter</button>
                            <a href="booking.php" class="btn btn-light border rounded-pill px-3" data-bs-toggle="tooltip" title="Reset Filter">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="bookingTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Kode / Waktu</th>
                                    <th>Customer Info</th>
                                    <th>Layanan Dipilih</th>
                                    <th>Status Flow</th>
                                    <th class="text-end pe-4">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php while($row = fetch_assoc($bookings)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">#BKG-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                            <span class="small text-muted mt-1"><i class="far fa-calendar-alt me-1"></i><?php echo date('d M Y', strtotime($row['tanggal_booking'])); ?></span>
                                            <span class="small text-muted"><i class="far fa-clock me-1"></i><?php echo date('H:i', strtotime($row['jam_booking'])); ?> WIB</span>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                            <?php echo $row['nama_lengkap']; ?>
                                            <?php if($row['keluhan']): ?>
                                            <button type="button" class="btn btn-sm btn-link text-warning p-0 m-0" data-bs-toggle="popover" data-bs-trigger="focus"
                                                    title="Keluhan Customer" data-bs-content="<?php echo htmlspecialchars($row['keluhan']); ?>">
                                                <i class="fas fa-comment-dots fs-5"></i>
                                            </button>
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted"><i class="fas fa-phone-alt me-1"></i><?php echo $row['no_hp']; ?></small>
                                    </td>
                                    <td>
                                        <span class="d-block fw-medium text-dark"><?php echo $row['nama_jasa'] ?: '<span class="text-muted fst-italic">Konsultasi/Cek Fisik</span>'; ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = ['pending' => 'warning', 'dikonfirmasi' => 'info', 'selesai' => 'success', 'batal' => 'danger'];
                                        ?>
                                        <div class="d-flex flex-column gap-1 align-items-start">
                                            <span class="badge bg-<?php echo $badge[$row['status']]; ?> bg-opacity-10 text-<?php echo $badge[$row['status']]; ?> border border-<?php echo $badge[$row['status']]; ?> border-opacity-25 rounded-pill px-2 py-1 text-uppercase small" style="font-size: 0.65rem;">
                                                <i class="fas fa-circle me-1" style="font-size: 6px; vertical-align: middle;"></i> BKG: <?php echo $row['status']; ?>
                                            </span>
                                            
                                            <?php if ($row['service_status']): ?>
                                            <span class="badge bg-<?php echo $row['service_status'] == 'selesai' ? 'success' : 'primary'; ?> bg-opacity-10 text-<?php echo $row['service_status'] == 'selesai' ? 'success' : 'primary'; ?> rounded-pill px-2 py-1 text-uppercase small" style="font-size: 0.65rem;">
                                                <i class="fas fa-wrench me-1"></i> SVC: <?php echo $row['service_status']; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-pill px-3 dropdown-toggle shadow-sm text-dark fw-medium" type="button" data-bs-toggle="dropdown">
                                                Update
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                <li><h6 class="dropdown-header">Ubah Status Booking</h6></li>
                                                <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=pending"><i class="fas fa-clock text-warning me-2 w-20px"></i>Pending</a></li>
                                                <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=dikonfirmasi"><i class="fas fa-check-circle text-info me-2 w-20px"></i>Konfirmasi</a></li>
                                                <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=selesai"><i class="fas fa-check-double text-success me-2 w-20px"></i>Selesai</a></li>
                                                <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=batal"><i class="fas fa-times-circle text-danger me-2 w-20px"></i>Batalkan</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="return confirmDelete('?delete=<?php echo $row['id']; ?>', 'Hapus permanen booking ini?')"><i class="fas fa-trash me-2 w-20px"></i>Hapus Permanen</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if(num_rows($bookings) == 0): ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Tidak ditemukan data booking yang sesuai kriteria pencarian.</td></tr>
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
document.addEventListener("DOMContentLoaded", function(){
    // Inisialisasi popover Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, { html: true })
    });
});
</script>

<?php include '../includes/footer.php'; ?>
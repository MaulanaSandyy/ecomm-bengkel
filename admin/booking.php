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
                <a href="booking.php" class="active"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-credit-card me-2"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Kelola Booking Service</h2>
            
            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-md-2 col-6 mb-3" data-aos="fade-up">
                    <div class="card bg-primary text-white p-3">
                        <h6>Total</h6>
                        <h3><?php echo $total_booking; ?></h3>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card bg-warning text-white p-3">
                        <h6>Pending</h6>
                        <h3><?php echo $pending; ?></h3>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card bg-info text-white p-3">
                        <h6>Dikonfirmasi</h6>
                        <h3><?php echo $dikonfirmasi; ?></h3>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card bg-success text-white p-3">
                        <h6>Selesai</h6>
                        <h3><?php echo $selesai; ?></h3>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="card bg-danger text-white p-3">
                        <h6>Batal</h6>
                        <h3><?php echo $batal; ?></h3>
                    </div>
                </div>
            </div>
            
            <!-- Filter dan Search -->
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <select name="filter_status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="pending" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="dikonfirmasi" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'dikonfirmasi') ? 'selected' : ''; ?>>Dikonfirmasi</option>
                                <option value="selesai" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="batal" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari customer atau jasa..." 
                                       value="<?php echo $_GET['search'] ?? ''; ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a href="booking.php" class="btn btn-secondary w-100">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabel Booking -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Booking</h5>
                    <button class="btn btn-light btn-sm" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="bookingTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Booking</th>
                                    <th>Customer</th>
                                    <th>No. HP</th>
                                    <th>Jasa</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Keluhan</th>
                                    <th>Status Booking</th>
                                    <th>Status Service</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1; 
                                while($row = fetch_assoc($bookings)): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <strong>#BKG-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></strong>
                                    </td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['no_hp']; ?></td>
                                    <td><?php echo $row['nama_jasa'] ?: 'Tidak memilih jasa'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_booking'])); ?></td>
                                    <td><?php echo $row['jam_booking']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="popover" 
                                                title="Keluhan" data-bs-content="<?php echo $row['keluhan']; ?>">
                                            <i class="fas fa-comment"></i>
                                        </button>
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
                                        <span class="badge bg-<?php echo $badge[$row['status']]; ?>">
                                            <?php echo strtoupper($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['service_status']): ?>
                                        <span class="badge bg-<?php echo $row['service_status'] == 'selesai' ? 'success' : 'primary'; ?>">
                                            <?php echo strtoupper($row['service_status']); ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">BELUM SERVICE</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=pending">
                                                        <i class="fas fa-clock text-warning me-2"></i>Set Pending
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=dikonfirmasi">
                                                        <i class="fas fa-check-circle text-info me-2"></i>Konfirmasi
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=selesai">
                                                        <i class="fas fa-check-double text-success me-2"></i>Selesai
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=batal">
                                                        <i class="fas fa-times-circle text-danger me-2"></i>Batalkan
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" 
                                                       onclick="return confirmDelete('?delete=<?php echo $row['id']; ?>')">
                                                        <i class="fas fa-trash me-2"></i>Hapus
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
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

<script>
// Inisialisasi popover
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
})
</script>

<?php include '../includes/footer.php'; ?>
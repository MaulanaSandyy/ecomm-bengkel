<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(2); // Hanya owner

// Handle Update Status Booking
if (isset($_GET['update_status'])) {
    $id = $_GET['update_status'];
    $status = $_GET['status'];
    
    // Validasi status
    if (!in_array($status, ['pending', 'dikonfirmasi', 'selesai', 'batal'])) {
        $_SESSION['error'] = "Status tidak valid!";
        header("Location: booking.php");
        exit();
    }
    
    $query = "UPDATE booking SET status = '$status' WHERE id = $id";
    
    if (query($query)) {
        // Jika status dikonfirmasi, buat entri di tabel service
        if ($status == 'dikonfirmasi') {
            $booking = fetch_assoc(query("SELECT * FROM booking WHERE id = $id"));
            // Cek apakah sudah ada service
            $cek_service = query("SELECT * FROM service WHERE booking_id = $id");
            if (num_rows($cek_service) == 0) {
                query("INSERT INTO service (booking_id, status) VALUES ($id, 'antri')");
                $_SESSION['success'] = "Booking berhasil dikonfirmasi dan masuk antrian service!";
            } else {
                $_SESSION['success'] = "Status booking berhasil diupdate menjadi DIKONFIRMASI!";
            }
        } else {
            $_SESSION['success'] = "Status booking berhasil diupdate!";
        }
    } else {
        $_SESSION['error'] = "Status booking gagal diupdate!";
    }
    header("Location: booking.php");
    exit();
}

// Handle Assign Pegawai ke Service
if (isset($_POST['assign_pegawai'])) {
    $service_id = $_POST['service_id'];
    $pegawai_id = $_POST['pegawai_id'];
    
    $query = "UPDATE service SET pegawai_id = $pegawai_id WHERE id = $service_id";
    
    if (query($query)) {
        $_SESSION['success'] = "Pegawai berhasil ditugaskan ke service!";
    } else {
        $_SESSION['error'] = "Gagal menugaskan pegawai!";
    }
    header("Location: booking.php");
    exit();
}

// Handle Update Status Service
if (isset($_GET['update_service'])) {
    $service_id = $_GET['update_service'];
    $status = $_GET['service_status'];
    
    if (!in_array($status, ['antri', 'dikerjakan', 'selesai'])) {
        $_SESSION['error'] = "Status service tidak valid!";
        header("Location: booking.php");
        exit();
    }
    
    $query = "UPDATE service SET status = '$status'";
    
    // Jika selesai, tambahkan tanggal selesai
    if ($status == 'selesai') {
        $query .= ", tanggal_selesai = CURDATE()";
        // Update juga status booking menjadi selesai
        $service = fetch_assoc(query("SELECT booking_id FROM service WHERE id = $service_id"));
        if ($service) {
            query("UPDATE booking SET status = 'selesai' WHERE id = " . $service['booking_id']);
        }
    }
    
    $query .= " WHERE id = $service_id";
    
    if (query($query)) {
        $_SESSION['success'] = "Status service berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Status service gagal diupdate!";
    }
    header("Location: booking.php");
    exit();
}

// Handle Add Service Note
if (isset($_POST['add_note'])) {
    $service_id = $_POST['service_id'];
    $catatan = escape_string($_POST['catatan_service']);
    $biaya_tambahan = $_POST['biaya_tambahan'] ?? 0;
    
    $query = "UPDATE service SET catatan_service = '$catatan', biaya_tambahan = $biaya_tambahan WHERE id = $service_id";
    
    if (query($query)) {
        $_SESSION['success'] = "Catatan service berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan catatan!";
    }
    header("Location: booking.php");
    exit();
}

$title = "Laporan Booking";
include '../includes/header.php';

// Filter dan Pencarian
$where = "WHERE 1=1";

if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $status = escape_string($_GET['filter_status']);
    $where .= " AND b.status = '$status'";
}

// Service status filter - only applied to main query
$service_filter = "";
if (isset($_GET['filter_service_status']) && !empty($_GET['filter_service_status'])) {
    $service_status = escape_string($_GET['filter_service_status']);
    $service_filter = " AND s.status = '$service_status'";
}

if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $start_date = escape_string($_GET['start_date']);
    $where .= " AND DATE(b.tanggal_booking) >= '$start_date'";
}

if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = escape_string($_GET['end_date']);
    $where .= " AND DATE(b.tanggal_booking) <= '$end_date'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = escape_string($_GET['search']);
    $where .= " AND (u.nama_lengkap LIKE '%$search%' OR u.no_hp LIKE '%$search%' OR j.nama_jasa LIKE '%$search%' OR b.keluhan LIKE '%$search%')";
}

// Pagination
$limit = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$total_result = num_rows(query("SELECT b.* FROM booking b JOIN users u ON b.user_id = u.id LEFT JOIN jasa j ON b.jasa_id = j.id LEFT JOIN service s ON b.id = s.booking_id $where $service_filter"));
$total_pages = ceil($total_result / $limit);

// Ambil data booking dengan detail lengkap
$bookings = query("SELECT b.*, 
                   u.nama_lengkap as customer_name, 
                   u.no_hp, 
                   u.email,
                   j.nama_jasa, 
                   j.harga as jasa_harga,
                   j.estimasi_waktu,
                   s.id as service_id,
                   s.status as service_status, 
                   s.catatan_service, 
                   s.biaya_tambahan,
                   s.tanggal_selesai,
                   p.nama_lengkap as pilihan_pegawai_name,
                   p.id as pilihan_pegawai_id
                   FROM booking b 
                   JOIN users u ON b.user_id = u.id 
                   LEFT JOIN jasa j ON b.jasa_id = j.id 
                   LEFT JOIN service s ON b.id = s.booking_id
                   LEFT JOIN users p ON s.pegawai_id = p.id
                   $where $service_filter
                   ORDER BY 
                       CASE b.status 
                           WHEN 'pending' THEN 1
                           WHEN 'dikonfirmasi' THEN 2
                           WHEN 'selesai' THEN 3
                           WHEN 'batal' THEN 4
                       END,
                       b.tanggal_booking DESC,
                       b.jam_booking DESC
                   LIMIT $start, $limit");

// Ambil daftar pegawai untuk assign
$pegawai_list = query("SELECT id, nama_lengkap FROM users WHERE role_id = 3 ORDER BY nama_lengkap");

// Statistik Booking (tanpa service table)
$total_booking = num_rows(query("SELECT * FROM booking b JOIN users u ON b.user_id = u.id $where"));

$total_pending = num_rows(query("SELECT * FROM booking b JOIN users u ON b.user_id = u.id $where AND b.status = 'pending'"));

$total_dikonfirmasi = num_rows(query("SELECT * FROM booking b JOIN users u ON b.user_id = u.id $where AND b.status = 'dikonfirmasi'"));

$total_selesai = num_rows(query("SELECT * FROM booking b JOIN users u ON b.user_id = u.id $where AND b.status = 'selesai'"));

$total_batal = num_rows(query("SELECT * FROM booking b JOIN users u ON b.user_id = u.id $where AND b.status = 'batal'"));

// Statistik Service
$service_where = "WHERE 1=1";
if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $service_where .= " AND b.status = '" . escape_string($_GET['filter_status']) . "'";
}
if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $service_where .= " AND DATE(b.tanggal_booking) >= '" . escape_string($_GET['start_date']) . "'";
}
if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $service_where .= " AND DATE(b.tanggal_booking) <= '" . escape_string($_GET['end_date']) . "'";
}
if (isset($_GET['filter_service_status']) && !empty($_GET['filter_service_status'])) {
    $service_where .= " AND s.status = '" . escape_string($_GET['filter_service_status']) . "'";
}

$total_service = num_rows(query("SELECT * FROM service s JOIN booking b ON s.booking_id = b.id JOIN users u ON b.user_id = u.id $service_where"));

$service_antri = num_rows(query("SELECT * FROM service s JOIN booking b ON s.booking_id = b.id JOIN users u ON b.user_id = u.id $service_where AND s.status = 'antri'"));

$service_dikerjakan = num_rows(query("SELECT * FROM service s JOIN booking b ON s.booking_id = b.id JOIN users u ON b.user_id = u.id $service_where AND s.status = 'dikerjakan'"));

$service_selesai = num_rows(query("SELECT * FROM service s JOIN booking b ON s.booking_id = b.id JOIN users u ON b.user_id = u.id $service_where AND s.status = 'selesai'"));

// Statistik Per Hari (untuk chart)
$booking_per_hari = query("SELECT DATE(tanggal_booking) as tanggal, 
                           COUNT(*) as total,
                           SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                           SUM(CASE WHEN status = 'dikonfirmasi' THEN 1 ELSE 0 END) as dikonfirmasi,
                           SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
                           SUM(CASE WHEN status = 'batal' THEN 1 ELSE 0 END) as batal
                           FROM booking b
                           JOIN users u ON b.user_id = u.id
                           $where
                           GROUP BY DATE(tanggal_booking)
                           ORDER BY tanggal DESC
                           LIMIT 30");

// Statistik Per Jam (untuk analisis jam sibuk)
$booking_per_jam = query("SELECT jam_booking, 
                          COUNT(*) as total,
                          SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
                          FROM booking b
                          JOIN users u ON b.user_id = u.id
                          $where
                          GROUP BY jam_booking
                          ORDER BY FIELD(jam_booking, '08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00')");

// Top Customer
$top_customer = query("SELECT u.nama_lengkap, u.no_hp, u.email,
                       COUNT(b.id) as total_booking,
                       SUM(CASE WHEN b.status = 'selesai' THEN 1 ELSE 0 END) as selesai
                       FROM booking b
                       JOIN users u ON b.user_id = u.id
                       $where
                       GROUP BY b.user_id
                       ORDER BY total_booking DESC
                       LIMIT 10");

// Top Jasa
$top_jasa = query("SELECT j.nama_jasa, 
                   COUNT(b.id) as total_booking,
                   SUM(CASE WHEN b.status = 'selesai' THEN 1 ELSE 0 END) as selesai
                   FROM booking b
                   JOIN users u ON b.user_id = u.id
                   LEFT JOIN jasa j ON b.jasa_id = j.id
                   WHERE b.jasa_id IS NOT NULL
                   GROUP BY b.jasa_id
                   ORDER BY total_booking DESC
                   LIMIT 10");
?>

<style>
.booking-card {
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.booking-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.stat-card {
    border-radius: 15px;
    color: white;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-card .card-body {
    padding: 1rem;
}

.stat-card .opacity-50 {
    opacity: 0.5;
}

.filter-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
}

.table-booking {
    font-size: 0.9rem;
}

.table-booking th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    padding: 12px;
    white-space: nowrap;
}

.table-booking td {
    vertical-align: middle;
    padding: 12px;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.status-pending { background: #fef3c7; color: #d97706; }
.status-dikonfirmasi { background: #dbeafe; color: #2563eb; }
.status-selesai { background: #d1fae5; color: #059669; }
.status-batal { background: #fee2e2; color: #dc2626; }

.service-status-antri { background: #fed7aa; color: #c2410c; }
.service-status-dikerjakan { background: #c7d2fe; color: #4338ca; }
.service-status-selesai { background: #d1fae5; color: #059669; }

.action-buttons .btn {
    margin: 2px;
    padding: 4px 8px;
    font-size: 0.75rem;
}

.detail-modal .modal-content {
    border-radius: 20px;
    overflow: hidden;
}

.detail-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    color: white;
}

.detail-section {
    background: #f9fafb;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
}

.detail-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    margin-bottom: 10px;
    font-weight: 600;
}

@media print {
    .sidebar, .navbar, footer, .btn, .filter-card, .stat-card, .no-print, .action-buttons {
        display: none !important;
    }
    .table-booking {
        font-size: 10pt;
    }
}
</style>

<div class="container-fluid px-0 px-lg-4 mt-2" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">

        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-tie me-2"></i>Menu Owner
                </h5>
                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="booking.php" class="active"><i class="fas fa-calendar-alt me-2"></i>Data Booking</a>
                <a href="laporan.php"><i class="fas fa-chart-bar me-2"></i>Laporan Keuangan</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Kelola Booking & Service</h2>
                <div>
                    <button class="btn btn-success me-2" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <button class="btn btn-secondary" onclick="printBookingReport()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-2 mb-3" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm rounded-4 stat-card bg-primary">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50 small">Total Booking</small>
                                    <h3 class="text-white mb-0 fw-bold"><?php echo number_format($total_booking); ?></h3>
                                </div>
                                <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2 mb-3" data-aos="fade-up" data-aos-delay="50">
                    <div class="card h-100 border-0 shadow-sm rounded-4 stat-card bg-warning">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50 small">Pending</small>
                                    <h3 class="text-white mb-0 fw-bold"><?php echo number_format($total_pending); ?></h3>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2 mb-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-sm rounded-4 stat-card bg-info">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50 small">Dikonfirmasi</small>
                                    <h3 class="text-white mb-0 fw-bold"><?php echo number_format($total_dikonfirmasi); ?></h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2 mb-3" data-aos="fade-up" data-aos-delay="150">
                    <div class="card h-100 border-0 shadow-sm rounded-4 stat-card bg-success">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50 small">Selesai</small>
                                    <h3 class="text-white mb-0 fw-bold"><?php echo number_format($total_selesai); ?></h3>
                                </div>
                                <i class="fas fa-check-double fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2 mb-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 shadow-sm rounded-4 stat-card bg-danger">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50 small">Batal</small>
                                    <h3 class="text-white mb-0 fw-bold"><?php echo number_format($total_batal); ?></h3>
                                </div>
                                <i class="fas fa-times-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2 mb-3" data-aos="fade-up" data-aos-delay="250">
                    <div class="card h-100 border-0 shadow-sm rounded-4 stat-card bg-secondary">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50 small">Service Aktif</small>
                                    <h3 class="text-white mb-0 fw-bold"><?php echo number_format($service_antri + $service_dikerjakan); ?></h3>
                                    <small class="text-white-50 small">Antri: <?php echo $service_antri; ?> | Kerja: <?php echo $service_dikerjakan; ?></small>
                                </div>
                                <i class="fas fa-wrench fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="filter-card mb-4" data-aos="fade-down">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Status Booking</label>
                        <select name="filter_status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="dikonfirmasi" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'dikonfirmasi') ? 'selected' : ''; ?>>Dikonfirmasi</option>
                            <option value="selesai" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                            <option value="batal" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Status Service</label>
                        <select name="filter_service_status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="antri" <?php echo (isset($_GET['filter_service_status']) && $_GET['filter_service_status'] == 'antri') ? 'selected' : ''; ?>>Antri</option>
                            <option value="dikerjakan" <?php echo (isset($_GET['filter_service_status']) && $_GET['filter_service_status'] == 'dikerjakan') ? 'selected' : ''; ?>>Dikerjakan</option>
                            <option value="selesai" <?php echo (isset($_GET['filter_service_status']) && $_GET['filter_service_status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="<?php echo $_GET['start_date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="<?php echo $_GET['end_date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Cari</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Customer / No HP / Jasa..." 
                                   value="<?php echo $_GET['search'] ?? ''; ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="booking.php" class="btn btn-secondary w-100">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="booking-card" data-aos="fade-up">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Booking</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-booking mb-0" id="bookingTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Customer</th>
                                    <th>No. HP</th>
                                    <th>Jasa</th>
                                    <th>Keluhan</th>
                                    <th>Status Booking</th>
                                    <th>Status Service</th>
                                    <th>Mekanik</th>
                                    <th>Aksi</th>
                                 </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1; 
                                if(num_rows($bookings) > 0):
                                while($row = fetch_assoc($bookings)): 
                                ?>
                                <tr class="align-middle">
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <strong><?php echo date('d/m/Y', strtotime($row['tanggal_booking'])); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo date('D', strtotime($row['tanggal_booking'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $row['jam_booking']; ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo $row['customer_name']; ?></strong>
                                    </td>
                                    <td><?php echo $row['no_hp']; ?></td>
                                    <td>
                                        <?php if($row['nama_jasa']): ?>
                                            <?php echo $row['nama_jasa']; ?>
                                            <br>
                                            <small class="text-muted">Rp <?php echo number_format($row['jasa_harga'], 0, ',', '.'); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Konsultasi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="lihatKeluhan('<?php echo addslashes($row['keluhan']); ?>')"
                                                data-bs-toggle="tooltip" title="Lihat Keluhan">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'pending' => 'status-pending',
                                            'dikonfirmasi' => 'status-dikonfirmasi',
                                            'selesai' => 'status-selesai',
                                            'batal' => 'status-batal'
                                        ];
                                        ?>
                                        <span class="status-badge <?php echo $status_class[$row['status']]; ?>">
                                            <?php echo strtoupper($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($row['service_status']): ?>
                                            <span class="badge service-status-<?php echo $row['service_status']; ?>">
                                                <?php echo strtoupper($row['service_status']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">BELUM</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row['pilihan_pegawai_name']): ?>
                                            <?php echo $row['pilihan_pegawai_name']; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <?php if($row['status'] == 'pending'): ?>
                                            <a href="?update_status=<?php echo $row['id']; ?>&status=dikonfirmasi" 
                                               class="btn btn-sm btn-success" 
                                               onclick="return confirm('Konfirmasi booking ini?')"
                                               data-bs-toggle="tooltip" title="Konfirmasi Booking">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                            <a href="?update_status=<?php echo $row['id']; ?>&status=batal" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Batalkan booking ini?')"
                                               data-bs-toggle="tooltip" title="Batalkan Booking">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($row['status'] == 'dikonfirmasi' && $row['service_status'] == 'antri'): ?>
                                            <a href="?update_service=<?php echo $row['service_id']; ?>&service_status=dikerjakan" 
                                               class="btn btn-sm btn-warning"
                                               onclick="return confirm('Mulai mengerjakan service ini?')"
                                               data-bs-toggle="tooltip" title="Mulai Dikerjakan">
                                                <i class="fas fa-play"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($row['service_status'] == 'dikerjakan'): ?>
                                            <a href="?update_service=<?php echo $row['service_id']; ?>&service_status=selesai" 
                                               class="btn btn-sm btn-success"
                                               onclick="return confirm('Selesaikan service ini?')"
                                               data-bs-toggle="tooltip" title="Selesaikan Service">
                                                <i class="fas fa-check-double"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($row['service_id'] && !$row['pilihan_pegawai_id']): ?>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="showAssignModal(<?php echo $row['service_id']; ?>, '<?php echo $row['customer_name']; ?>')"
                                                    data-bs-toggle="tooltip" title="Assign Mekanik">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if($row['service_id']): ?>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="showNoteModal(<?php echo $row['service_id']; ?>, '<?php echo addslashes($row['catatan_service']); ?>', <?php echo $row['biaya_tambahan']; ?>)"
                                                    data-bs-toggle="tooltip" title="Tambah Catatan">
                                                <i class="fas fa-sticky-note"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button type="button" class="btn btn-sm btn-secondary" 
                                                onclick="lihatDetail(<?php echo $row['id']; ?>)"
                                                data-bs-toggle="tooltip" title="Detail Booking">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; 
                                else: ?>
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Tidak ada data booking</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white py-3 px-4 border-top">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Tampilkan:</span>
                            <select class="form-select form-select-sm rounded-pill" style="width: 80px;" onchange="window.location.href='booking.php?per_page='+this.value+'&page=1<?php echo isset($_GET['filter_status']) ? '&filter_status=' . $_GET['filter_status'] : ''; ?><?php echo isset($_GET['filter_service']) ? '&filter_service=' . $_GET['filter_service'] : ''; ?><?php echo isset($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : ''; ?><?php echo isset($_GET['end_date']) ? '&end_date=' . $_GET['end_date'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>'">
                                <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5</option>
                                <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                                <option value="15" <?php echo $limit == 15 ? 'selected' : ''; ?>>15</option>
                                <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option>
                                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                                <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                            </select>
                            <span class="text-muted small">data per halaman</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Total: <?php echo $total_result; ?> data</span>
                            <?php if($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    <?php if($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link rounded-pill me-1" href="booking.php?page=<?php echo $page-1; ?>&per_page=<?php echo $limit; ?><?php echo isset($_GET['filter_status']) ? '&filter_status=' . $_GET['filter_status'] : ''; ?><?php echo isset($_GET['filter_service']) ? '&filter_service=' . $_GET['filter_service'] : ''; ?><?php echo isset($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : ''; ?><?php echo isset($_GET['end_date']) ? '&end_date=' . $_GET['end_date'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    for($i = $start_page; $i <= $end_page; $i++):
                                    ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link rounded-pill me-1" href="booking.php?page=<?php echo $i; ?>&per_page=<?php echo $limit; ?><?php echo isset($_GET['filter_status']) ? '&filter_status=' . $_GET['filter_status'] : ''; ?><?php echo isset($_GET['filter_service']) ? '&filter_service=' . $_GET['filter_service'] : ''; ?><?php echo isset($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : ''; ?><?php echo isset($_GET['end_date']) ? '&end_date=' . $_GET['end_date'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <?php if($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link rounded-pill" href="booking.php?page=<?php echo $page+1; ?>&per_page=<?php echo $limit; ?><?php echo isset($_GET['filter_status']) ? '&filter_status=' . $_GET['filter_status'] : ''; ?><?php echo isset($_GET['filter_service']) ? '&filter_service=' . $_GET['filter_service'] : ''; ?><?php echo isset($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : ''; ?><?php echo isset($_GET['end_date']) ? '&end_date=' . $_GET['end_date'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Assign Mekanik</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="service_id" id="assign_service_id">
                    <p>Menugaskan mekanik untuk service <strong id="assign_customer_name"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Pilih Mekanik</label>
                        <select name="pegawai_id" class="form-control" required>
                            <option value="">-- Pilih Mekanik --</option>
                            <?php while($pegawai = fetch_assoc($pegawai_list)): ?>
                            <option value="<?php echo $pegawai['id']; ?>"><?php echo $pegawai['nama_lengkap']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="assign_pegawai" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-sticky-note me-2"></i>Catatan Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="service_id" id="note_service_id">
                    <div class="mb-3">
                        <label class="form-label">Catatan Service</label>
                        <textarea name="catatan_service" class="form-control" rows="4" id="note_text" 
                                  placeholder="Catatan tentang pengerjaan service..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Tambahan (Opsional)</label>
                        <input type="number" name="biaya_tambahan" class="form-control" id="note_biaya" 
                               placeholder="0" value="0">
                        <small class="text-muted">Biaya tambahan jika ada part tambahan atau perbaikan lain</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_note" class="btn btn-primary">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content detail-modal">
            <div class="detail-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Detail Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printDetail()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="keluhanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-comment-dots me-2"></i>Detail Keluhan Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="keluhanText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fungsi untuk modal assign pegawai
function showAssignModal(serviceId, customerName) {
    document.getElementById('assign_service_id').value = serviceId;
    document.getElementById('assign_customer_name').innerText = customerName;
    var modal = new bootstrap.Modal(document.getElementById('assignModal'));
    modal.show();
}

// Fungsi untuk modal tambah catatan
function showNoteModal(serviceId, currentNote, currentBiaya) {
    document.getElementById('note_service_id').value = serviceId;
    document.getElementById('note_text').value = currentNote || '';
    document.getElementById('note_biaya').value = currentBiaya || 0;
    var modal = new bootstrap.Modal(document.getElementById('noteModal'));
    modal.show();
}

// Lihat Detail Booking
function lihatDetail(id) {
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data...</p>
        </div>
    `;
    
    fetch('get_detail_booking.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
            var modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <p>Gagal memuat data. Silakan coba lagi.</p>
                </div>
            `;
        });
}

// Print Detail Booking - open new window with data from database
function printDetail() {
    var detailContent = document.getElementById('detailContent');
    var printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Detail Booking</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                * { font-size: 12px; }
                body { padding: 20px; background: white; }
                .header-report { border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
                .detail-section { background: #f9fafb; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
                .detail-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 10px; font-weight: 600; }
                .status-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
                .status-pending { background: #fef3c7; color: #d97706; }
                .status-dikonfirmasi { background: #dbeafe; color: #2563eb; }
                .status-selesai { background: #d1fae5; color: #059669; }
                .status-batal { background: #fee2e2; color: #dc2626; }
                .service-status-antri { background: #fed7aa; color: #c2410c; }
                .service-status-dikerjakan { background: #c7d2fe; color: #4338ca; }
                .service-status-selesai { background: #d1fae5; color: #059669; }
                .footer-report { border-top: 1px solid #ccc; margin-top: 30px; padding-top: 15px; text-align: center; font-size: 0.85rem; color: #666; }
                .btn-print { display: block; margin: 30px auto; padding: 10px 30px; font-size: 14px; }
                @page { size: A4; margin: 10mm; }
            </style>
        </head>
        <body>
            ${detailContent.innerHTML}
            <div class="container-fluid" style="text-align: center; margin-top: 30px;">
                <button class="btn btn-primary btn-print" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <button class="btn btn-secondary btn-print" onclick="window.close()">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    setTimeout(function() {
        printWindow.focus();
    }, 500);
}

// Lihat Keluhan
function lihatKeluhan(keluhan) {
    document.getElementById('keluhanText').innerHTML = keluhan;
    var modal = new bootstrap.Modal(document.getElementById('keluhanModal'));
    modal.show();
}

// Export to Excel
function exportToExcel() {
    var params = new URLSearchParams(window.location.search);
    var url = 'export_booking_excel.php';
    if (params.toString()) {
        url += '?' + params.toString();
    }
    window.location.href = url;
}

// Print Booking Report
function printBookingReport() {
    var params = new URLSearchParams(window.location.search);
    var url = 'print_booking_report.php';
    if (params.toString()) {
        url += '?' + params.toString();
    }
    window.open(url, '_blank');
}

// Tooltip initialization
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<?php include '../includes/footer.php'; ?>
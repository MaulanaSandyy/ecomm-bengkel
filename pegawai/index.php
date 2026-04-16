<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(3); // khusus pegawai

$title = "Dashboard Pegawai";
include '../includes/header.php';

// Ambil user login
$user_id = $_SESSION['user_id'];

// Statistik
$total_booking = num_rows(query("SELECT * FROM booking WHERE status IN ('pending','dikonfirmasi')"));
$total_service_saya = num_rows(query("SELECT * FROM service WHERE pegawai_id = $user_id"));
$total_dikerjakan = num_rows(query("SELECT * FROM service WHERE pegawai_id = $user_id AND status = 'dikerjakan'"));
$total_selesai = num_rows(query("SELECT * FROM service WHERE pegawai_id = $user_id AND status = 'selesai'"));

// Booking terbaru
$bookings = query("SELECT b.*, u.nama_lengkap, j.nama_jasa 
                   FROM booking b 
                   JOIN users u ON b.user_id = u.id 
                   LEFT JOIN jasa j ON b.jasa_id = j.id 
                   WHERE b.status IN ('pending','dikonfirmasi')
                   ORDER BY b.created_at DESC LIMIT 5");

// Service saya
$services = query("SELECT s.*, b.tanggal_booking, u.nama_lengkap, j.nama_jasa
                   FROM service s
                   JOIN booking b ON s.booking_id = b.id
                   JOIN users u ON b.user_id = u.id
                   LEFT JOIN jasa j ON b.jasa_id = j.id
                   WHERE s.pegawai_id = $user_id
                   ORDER BY s.id DESC LIMIT 5");
?>

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-cog me-2"></i>Menu Pegawai
                </h5>
                <a href="index.php" class="active"><i class="fas fa-home"></i>Dashboard</a>
                <a href="booking.php"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="service.php"><i class="fas fa-tools"></i>Service Saya</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Dashboard Pegawai</h3>
                    <p class="text-muted small mb-0">Overview layanan dan progress pekerjaan Anda.</p>
                </div>
            </div>
            
            <!-- Statistik Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-sm-6" data-aos="fade-up">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--primary-gradient);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Booking Masuk</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_booking; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-check fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="dashboard-card shadow-sm h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Service Saya</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_service_saya; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-tasks fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--warning-gradient);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Sedang Dikerjakan</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_dikerjakan; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-wrench fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--success-gradient);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Selesai</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_selesai; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Daftar Booking Masuk -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i>Booking Masuk</h5>
                    <a href="booking.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
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
                                    <td class="fw-medium text-dark"><?php echo $row['nama_lengkap']; ?></td>
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
                                <?php endwhile; ?>
                                
                                <?php if(num_rows($bookings) == 0): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada booking masuk.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Daftar Service Saya -->
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tools text-success me-2"></i>Service Saya</h5>
                    <a href="service.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Customer</th>
                                    <th>Jasa Service</th>
                                    <th>Tanggal Booking</th>
                                    <th>Status Service</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php $no = 1; while($row = fetch_assoc($services)): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?php echo $no++; ?></td>
                                    <td class="fw-medium text-dark"><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['nama_jasa'] ?: '-'; ?></td>
                                    <td>
                                        <span class="fw-medium text-dark"><?php echo date('d M Y', strtotime($row['tanggal_booking'])); ?></span>
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
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if(num_rows($services) == 0): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada service yang ditugaskan.</td></tr>
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
});
</script>

<?php include '../includes/footer.php'; ?>
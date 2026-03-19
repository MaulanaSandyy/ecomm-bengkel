<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1); // Hanya admin

$title = "Dashboard Admin";
include '../includes/header.php';

// Statistik
$total_users = num_rows(query("SELECT * FROM users"));
$total_customer = num_rows(query("SELECT * FROM users WHERE role_id = 4"));
$total_jasa = num_rows(query("SELECT * FROM jasa"));
$total_sparepart = num_rows(query("SELECT * FROM sparepart"));
$total_booking = num_rows(query("SELECT * FROM booking"));
$total_transaksi = num_rows(query("SELECT * FROM transaksi"));
$total_pendapatan = fetch_assoc(query("SELECT SUM(total_harga) as total FROM transaksi WHERE status = 'lunas'"))['total'];

// Booking terbaru
$bookings = query("SELECT b.*, u.nama_lengkap, j.nama_jasa 
                   FROM booking b 
                   JOIN users u ON b.user_id = u.id 
                   LEFT JOIN jasa j ON b.jasa_id = j.id 
                   ORDER BY b.created_at DESC LIMIT 5");
?>

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-shield-alt me-2"></i>Menu Admin
                </h5>
                <a href="index.php" class="active"><i class="fas fa-home"></i>Dashboard</a>
                <a href="users.php"><i class="fas fa-users"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-box-open"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-cash-register"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode"></i>Upload QRIS</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Dashboard Admin</h3>
                    <p class="text-muted small mb-0">Overview sistem dan operasional bengkel secara real-time.</p>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-sm-6" data-aos="fade-up">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--primary-gradient);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Total Users</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_users; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--warning-gradient);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Customer</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_customer; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-user-tie fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="dashboard-card shadow-sm h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Jasa & Part</p>
                                <h2 class="text-white fw-bold mb-0"><?php echo $total_jasa + $total_sparepart; ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-boxes fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="dashboard-card shadow-sm h-100" style="background: var(--success-gradient);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white opacity-75 mb-1 small fw-medium text-uppercase letter-spacing-1">Pendapatan</p>
                                <h3 class="text-white fw-bold mb-0 text-truncate" style="max-width: 140px;" title="Rp <?php echo number_format($total_pendapatan ?: 0, 0, ',', '.'); ?>">
                                    Rp <?php echo number_format($total_pendapatan ?: 0, 0, ',', '.'); ?>
                                </h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-money-bill-wave fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white pt-4 pb-2 px-4 border-bottom-0">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Status Booking</h5>
                        </div>
                        <div class="card-body p-4 d-flex align-items-center justify-content-center">
                            <?php
                            $booking_status = [
                                'pending' => num_rows(query("SELECT * FROM booking WHERE status = 'pending'")),
                                'dikonfirmasi' => num_rows(query("SELECT * FROM booking WHERE status = 'dikonfirmasi'")),
                                'selesai' => num_rows(query("SELECT * FROM booking WHERE status = 'selesai'")),
                                'batal' => num_rows(query("SELECT * FROM booking WHERE status = 'batal'"))
                            ];
                            ?>
                            <div style="height: 250px; width: 100%;">
                                <canvas id="bookingChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7" data-aos="fade-left">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white pt-4 pb-2 px-4 border-bottom-0">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-area text-success me-2"></i>Tren Transaksi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div style="height: 250px; width: 100%;">
                                <canvas id="transaksiChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-check text-warning me-2"></i>Booking Terbaru</h5>
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
                                    <th class="text-end pe-4">Aksi</th>
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
                                    <td class="text-end pe-4">
                                        <a href="booking.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" data-bs-toggle="tooltip" title="Proses Booking">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if(num_rows($bookings) == 0): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data booking terbaru.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Booking Chart (Doughnut style modern)
    const ctxBooking = document.getElementById('bookingChart');
    if(ctxBooking) {
        new Chart(ctxBooking, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Dikonfirmasi', 'Selesai', 'Batal'],
                datasets: [{
                    data: [
                        <?php echo $booking_status['pending']; ?>,
                        <?php echo $booking_status['dikonfirmasi']; ?>,
                        <?php echo $booking_status['selesai']; ?>,
                        <?php echo $booking_status['batal']; ?>
                    ],
                    backgroundColor: ['#f59e0b', '#0ea5e9', '#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, font: { family: "'Plus Jakarta Sans', sans-serif" } } }
                }
            }
        });
    }

    // Transaksi Chart (Area chart modern)
    const ctxTransaksi = document.getElementById('transaksiChart');
    if(ctxTransaksi) {
        const gradient = ctxTransaksi.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(ctxTransaksi, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: [12, 19, 15, 17, 14, 23],
                    backgroundColor: gradient,
                    borderColor: '#10b981',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { borderDash: [5, 5], color: '#e2e8f0' }, beginAtZero: true }
                }
            }
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
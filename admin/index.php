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

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <h4 class="text-white mb-4"><i class="fas fa-dashboard me-2"></i>Menu Admin</h4>
                <a href="index.php" class="active"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="users.php"><i class="fas fa-users me-2"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench me-2"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-oil-can me-2"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="transaksi.php"><i class="fas fa-credit-card me-2"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Dashboard Admin</h2>
            
            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Total Users</h6>
                                <h2 class="text-white"><?php echo $total_users; ?></h2>
                            </div>
                            <i class="fas fa-users fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="dashboard-card" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Customer</h6>
                                <h2 class="text-white"><?php echo $total_customer; ?></h2>
                            </div>
                            <i class="fas fa-user-tie fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="dashboard-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Jasa & Sparepart</h6>
                                <h2 class="text-white"><?php echo $total_jasa + $total_sparepart; ?></h2>
                            </div>
                            <i class="fas fa-boxes fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="dashboard-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Pendapatan</h6>
                                <h2 class="text-white">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h2>
                            </div>
                            <i class="fas fa-money-bill-wave fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Grafik Sederhana -->
            <div class="row mb-4">
                <div class="col-md-6" data-aos="fade-right">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik Booking</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $booking_status = [
                                'pending' => num_rows(query("SELECT * FROM booking WHERE status = 'pending'")),
                                'dikonfirmasi' => num_rows(query("SELECT * FROM booking WHERE status = 'dikonfirmasi'")),
                                'selesai' => num_rows(query("SELECT * FROM booking WHERE status = 'selesai'")),
                                'batal' => num_rows(query("SELECT * FROM booking WHERE status = 'batal'"))
                            ];
                            ?>
                            <canvas id="bookingChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6" data-aos="fade-left">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Transaksi Bulanan</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="transaksiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Terbaru -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Booking Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Jasa</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while($row = fetch_assoc($bookings)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['nama_jasa'] ?: '-'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_booking'])); ?></td>
                                    <td><?php echo $row['jam_booking']; ?></td>
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
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="booking.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Booking Chart
new Chart(document.getElementById('bookingChart'), {
    type: 'pie',
    data: {
        labels: ['Pending', 'Dikonfirmasi', 'Selesai', 'Batal'],
        datasets: [{
            data: [
                <?php echo $booking_status['pending']; ?>,
                <?php echo $booking_status['dikonfirmasi']; ?>,
                <?php echo $booking_status['selesai']; ?>,
                <?php echo $booking_status['batal']; ?>
            ],
            backgroundColor: ['#fbbf24', '#3b82f6', '#10b981', '#ef4444']
        }]
    }
});

// Transaksi Chart
new Chart(document.getElementById('transaksiChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
            label: 'Jumlah Transaksi',
            data: [12, 19, 15, 17, 14, 23],
            borderColor: '#10b981',
            tension: 0.1
        }]
    }
});
</script>

<?php include '../includes/footer.php'; ?>
<?php
// File: admin/index.php
session_start();
include '../includes/koneksi.php';

// Cek login dan role
if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$title = "Dashboard Admin";
include '../includes/header.php';

// Ambil statistik
$total_user = fetch_array(query("SELECT COUNT(*) as total FROM users"))['total'];
$total_jasa = fetch_array(query("SELECT COUNT(*) as total FROM jasa"))['total'];
$total_sparepart = fetch_array(query("SELECT COUNT(*) as total FROM sparepart"))['total'];
$total_booking = fetch_array(query("SELECT COUNT(*) as total FROM booking"))['total'];
$total_transaksi = fetch_array(query("SELECT COUNT(*) as total FROM transaksi"))['total'];
$total_pendapatan = fetch_array(query("SELECT SUM(total_harga) as total FROM transaksi WHERE status_pembayaran='lunas'"))['total'];

// Ambil 5 booking terbaru
$booking_terbaru = fetch_all(query("SELECT b.*, u.nama_lengkap, j.nama_jasa 
                                    FROM booking b 
                                    JOIN users u ON b.id_user = u.id_user 
                                    JOIN jasa j ON b.id_jasa = j.id_jasa 
                                    ORDER BY b.tgl_pembuatan DESC LIMIT 5"));
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                    <span>Menu Admin</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user.php">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jasa.php">
                            <i class="bi bi-tools"></i> Kelola Jasa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sparepart.php">
                            <i class="bi bi-box"></i> Kelola Sparepart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">
                            <i class="bi bi-calendar-check"></i> Kelola Booking
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaksi.php">
                            <i class="bi bi-cash-stack"></i> Kelola Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">
                            <i class="bi bi-building"></i> Profil Bengkel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Admin</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="text-muted">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></span>
                </div>
            </div>

            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total User</h6>
                                    <h2 class="mb-0"><?php echo $total_user; ?></h2>
                                </div>
                                <i class="bi bi-people display-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Jasa</h6>
                                    <h2 class="mb-0"><?php echo $total_jasa; ?></h2>
                                </div>
                                <i class="bi bi-tools display-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Sparepart</h6>
                                    <h2 class="mb-0"><?php echo $total_sparepart; ?></h2>
                                </div>
                                <i class="bi bi-box display-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Booking</h6>
                                    <h2 class="mb-0"><?php echo $total_booking; ?></h2>
                                </div>
                                <i class="bi bi-calendar-check display-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Transaksi</h6>
                                    <h2 class="mb-0"><?php echo $total_transaksi; ?></h2>
                                </div>
                                <i class="bi bi-cash-stack display-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Pendapatan</h6>
                                    <h2 class="mb-0"><?php echo $total_pendapatan ? rupiah($total_pendapatan) : 'Rp 0'; ?></h2>
                                </div>
                                <i class="bi bi-currency-dollar display-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Terbaru -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Booking Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Jasa</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>No Plat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($booking_terbaru)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada booking</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($booking_terbaru as $b): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $b['nama_lengkap']; ?></td>
                                        <td><?php echo $b['nama_jasa']; ?></td>
                                        <td><?php echo tgl_indo($b['tgl_booking']); ?></td>
                                        <td><?php echo $b['jam_booking']; ?></td>
                                        <td><?php echo $b['no_plat']; ?></td>
                                        <td>
                                            <?php
                                            $badge = '';
                                            if($b['status'] == 'pending') $badge = 'warning';
                                            elseif($b['status'] == 'diproses') $badge = 'info';
                                            elseif($b['status'] == 'selesai') $badge = 'success';
                                            elseif($b['status'] == 'dibatalkan') $badge = 'danger';
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>"><?php echo $b['status']; ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Grafik Sederhana -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Status Booking</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $status = query("SELECT status, COUNT(*) as jumlah FROM booking GROUP BY status");
                            $status_data = fetch_all($status);
                            ?>
                            <canvas id="bookingChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Top Jasa</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $top_jasa = query("SELECT j.nama_jasa, COUNT(b.id_booking) as jumlah 
                                              FROM booking b 
                                              JOIN jasa j ON b.id_jasa = j.id_jasa 
                                              GROUP BY b.id_jasa 
                                              ORDER BY jumlah DESC LIMIT 5");
                            $top_jasa_data = fetch_all($top_jasa);
                            ?>
                            <canvas id="jasaChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart untuk status booking
const ctx1 = document.getElementById('bookingChart').getContext('2d');
new Chart(ctx1, {
    type: 'pie',
    data: {
        labels: [<?php foreach($status_data as $s) echo "'" . $s['status'] . "',"; ?>],
        datasets: [{
            data: [<?php foreach($status_data as $s) echo $s['jumlah'] . ","; ?>],
            backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545']
        }]
    }
});

// Chart untuk top jasa
const ctx2 = document.getElementById('jasaChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [<?php foreach($top_jasa_data as $j) echo "'" . $j['nama_jasa'] . "',"; ?>],
        datasets: [{
            label: 'Jumlah Booking',
            data: [<?php foreach($top_jasa_data as $j) echo $j['jumlah'] . ","; ?>],
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 1
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(2); // Hanya owner

// Ambil data profil
$profil = query("SELECT * FROM profil_bengkel WHERE id = 1");
$data = fetch_assoc($profil);

$title = "Profil Bengkel";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Owner -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <h4 class="text-white mb-4"><i class="fas fa-dashboard me-2"></i>Menu Owner</h4>
                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="transaksi.php"><i class="fas fa-credit-card me-2"></i>Transaksi</a>
                <a href="laporan.php"><i class="fas fa-chart-bar me-2"></i>Laporan Keuangan</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Profil Bengkel</h2>
            
            <!-- Preview Banner -->
            <?php if ($data['gambar_banner']): ?>
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-body p-0">
                    <img src="../uploads/profil/<?php echo $data['gambar_banner']; ?>" 
                         alt="Banner" class="img-fluid rounded" style="width: 100%; max-height: 300px; object-fit: cover;">
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Informasi Profil -->
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-right">
                    <div class="card text-center">
                        <div class="card-body">
                            <?php if ($data['logo']): ?>
                                <img src="../uploads/profil/<?php echo $data['logo']; ?>" 
                                     alt="Logo" class="img-fluid mb-3" style="max-width: 150px;">
                            <?php else: ?>
                                <i class="fas fa-building fa-5x text-muted mb-3"></i>
                            <?php endif; ?>
                            <h4><?php echo $data['nama_bengkel']; ?></h4>
                            <p class="text-muted">ID: #B001</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8 mb-4" data-aos="fade-left">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Lengkap</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Bengkel</strong></td>
                                    <td>: <?php echo $data['nama_bengkel']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                    <td>: <?php echo $data['alamat']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>No. Telepon</strong></td>
                                    <td>: <?php echo $data['no_telp']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: <?php echo $data['email']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jam Operasional</strong></td>
                                    <td>: <?php echo $data['jam_operasional']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi</strong></td>
                                    <td>: <?php echo $data['deskripsi']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update</strong></td>
                                    <td>: <?php echo date('d/m/Y H:i', strtotime($data['updated_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistik Bengkel -->
            <div class="row" data-aos="fade-up">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik Bengkel</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $total_pegawai = num_rows(query("SELECT * FROM users WHERE role_id = 3"));
                            $total_customer = num_rows(query("SELECT * FROM users WHERE role_id = 4"));
                            $total_jasa = num_rows(query("SELECT * FROM jasa"));
                            $total_sparepart = num_rows(query("SELECT * FROM sparepart"));
                            ?>
                            <canvas id="statistikChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Informasi Tambahan</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Pegawai
                                    <span class="badge bg-primary rounded-pill"><?php echo $total_pegawai; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Customer
                                    <span class="badge bg-success rounded-pill"><?php echo $total_customer; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Jumlah Jasa Service
                                    <span class="badge bg-warning rounded-pill"><?php echo $total_jasa; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Jumlah Sparepart
                                    <span class="badge bg-info rounded-pill"><?php echo $total_sparepart; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Rata-rata Rating
                                    <span class="badge bg-danger rounded-pill">4.8 / 5.0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Tahun Berdiri
                                    <span class="badge bg-secondary rounded-pill">2015</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lokasi Bengkel -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Bengkel</h5>
                </div>
                <div class="card-body">
                    <div class="ratio ratio-16x9">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.56347862248!2d106.64271718671875!3d-6.2144497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f4c5b1b1b1b1%3A0x0!2sJakarta!5e0!3m2!1sid!2sid!4v1620000000000!5m2!1sid!2sid" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    <p class="mt-3 text-center">
                        <i class="fas fa-map-pin text-primary me-2"></i>
                        <?php echo $data['alamat']; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('statistikChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pegawai', 'Customer', 'Jasa', 'Sparepart'],
        datasets: [{
            data: [<?php echo $total_pegawai; ?>, <?php echo $total_customer; ?>, <?php echo $total_jasa; ?>, <?php echo $total_sparepart; ?>],
            backgroundColor: ['#667eea', '#10b981', '#fbbf24', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
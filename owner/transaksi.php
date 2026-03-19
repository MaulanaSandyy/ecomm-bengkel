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

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-tie me-2"></i>Menu Owner
                </h5>
                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="transaksi.php" class="active"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="laporan.php"><i class="fas fa-chart-bar"></i>Laporan Keuangan</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold mb-4 text-dark">Profil Bengkel</h3>
            
            <?php if ($data['gambar_banner']): ?>
            <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden" data-aos="fade-down">
                <div class="card-body p-0 position-relative">
                    <img src="../uploads/profil/<?php echo $data['gambar_banner']; ?>" 
                         alt="Banner" class="w-100" style="height: 250px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                        <h4 class="text-white mb-0 fw-bold"><?php echo $data['nama_bengkel']; ?></h4>
                        <p class="text-white-50 mb-0 small"><i class="fas fa-map-marker-alt me-2"></i><?php echo $data['alamat']; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row g-4 mb-4">
                <div class="col-md-4 col-xl-3" data-aos="fade-right">
                    <div class="card text-center h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                            <?php if ($data['logo']): ?>
                                <img src="../uploads/profil/<?php echo $data['logo']; ?>" 
                                     alt="Logo" class="img-fluid rounded-circle shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid white;">
                            <?php else: ?>
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm bg-light" style="width: 120px; height: 120px;">
                                    <i class="fas fa-building fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="fw-bold text-dark mb-1"><?php echo $data['nama_bengkel']; ?></h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">ID: #B001</span>
                            <button class="btn btn-outline-primary btn-sm rounded-pill w-100">Edit Logo</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8 col-xl-9" data-aos="fade-left">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Informasi Lengkap</h5>
                        </div>
                        <div class="card-body px-4">
                            <table class="table table-borderless table-sm text-muted">
                                <tbody>
                                    <tr>
                                        <td width="30%" class="fw-medium text-dark pb-2">Nama Bengkel</td>
                                        <td class="pb-2">: <?php echo $data['nama_bengkel']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium text-dark pb-2">Alamat Lengkap</td>
                                        <td class="pb-2">: <?php echo $data['alamat']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium text-dark pb-2">No. Telepon</td>
                                        <td class="pb-2">: <?php echo $data['no_telp']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium text-dark pb-2">Email Official</td>
                                        <td class="pb-2">: <?php echo $data['email']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium text-dark pb-2">Jam Operasional</td>
                                        <td class="pb-2">: <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><?php echo $data['jam_operasional']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium text-dark pb-2">Deskripsi</td>
                                        <td class="pb-2">: <?php echo $data['deskripsi']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium text-dark pb-2">Terakhir Update</td>
                                        <td class="pb-2 fst-italic small">: <?php echo date('d M Y, H:i', strtotime($data['updated_at'])); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="text-end mt-2">
                                <button class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-edit me-2"></i>Edit Profil</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4" data-aos="fade-up">
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-success me-2"></i>Distribusi Data</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center p-4">
                            <?php
                            $total_pegawai = num_rows(query("SELECT * FROM users WHERE role_id = 3"));
                            $total_customer = num_rows(query("SELECT * FROM users WHERE role_id = 4"));
                            $total_jasa = num_rows(query("SELECT * FROM jasa"));
                            $total_sparepart = num_rows(query("SELECT * FROM sparepart"));
                            ?>
                            <div style="height: 250px; width: 100%;">
                                <canvas id="statistikChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-star text-warning me-2"></i>Informasi Ekstra</h5>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush gap-2">
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light rounded-3 px-3 py-2">
                                    <span class="text-muted fw-medium"><i class="fas fa-user-tie text-primary me-2"></i>Total Pegawai</span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $total_pegawai; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light rounded-3 px-3 py-2">
                                    <span class="text-muted fw-medium"><i class="fas fa-users text-success me-2"></i>Total Customer</span>
                                    <span class="badge bg-success rounded-pill"><?php echo $total_customer; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light rounded-3 px-3 py-2">
                                    <span class="text-muted fw-medium"><i class="fas fa-wrench text-warning me-2"></i>Layanan Jasa</span>
                                    <span class="badge bg-warning rounded-pill"><?php echo $total_jasa; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light rounded-3 px-3 py-2">
                                    <span class="text-muted fw-medium"><i class="fas fa-box-open text-info me-2"></i>Item Sparepart</span>
                                    <span class="badge bg-info rounded-pill"><?php echo $total_sparepart; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light rounded-3 px-3 py-2">
                                    <span class="text-muted fw-medium"><i class="fas fa-star text-danger me-2"></i>Rating Rata-rata</span>
                                    <span class="badge bg-danger rounded-pill">4.8 / 5.0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light rounded-3 px-3 py-2">
                                    <span class="text-muted fw-medium"><i class="fas fa-calendar text-secondary me-2"></i>Tahun Berdiri</span>
                                    <span class="badge bg-secondary rounded-pill">2015</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-map-marked-alt text-danger me-2"></i>Lokasi Google Maps</h5>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-21x9">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126907.03473145455!2d106.666324!3d-6.284206!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fabca8a3bf63%3A0xc5403e1e2d42bf!2sTangerang%20Selatan%2C%20Kota%20Tangerang%20Selatan%2C%20Banten!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <div class="p-3 bg-light text-center">
                        <p class="mb-0 fw-medium text-dark"><i class="fas fa-map-pin text-danger me-2"></i><?php echo $data['alamat']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statistikChart');
    if(ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pegawai', 'Customer', 'Jasa', 'Sparepart'],
                datasets: [{
                    data: [<?php echo $total_pegawai; ?>, <?php echo $total_customer; ?>, <?php echo $total_jasa; ?>, <?php echo $total_sparepart; ?>],
                    backgroundColor: [
                        '#4f46e5', // Primary
                        '#10b981', // Success
                        '#f59e0b', // Warning
                        '#0ea5e9'  // Info
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
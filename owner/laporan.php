<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(2); // Hanya owner

$title = "Laporan Keuangan";
include '../includes/header.php';

// Filter Periode
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Statistik berdasarkan periode
$where = "WHERE DATE(t.created_at) BETWEEN '$start_date' AND '$end_date'";

// Total Pendapatan Periode
$total_pendapatan = fetch_assoc(query("SELECT SUM(total_harga) as total FROM transaksi t 
                                      $where AND t.status = 'lunas'"))['total'];

// Total Transaksi Periode
$total_transaksi = num_rows(query("SELECT * FROM transaksi t $where"));

// Pendapatan Jasa
$pendapatan_jasa = fetch_assoc(query("SELECT SUM(d.harga * d.jumlah) as total 
                                     FROM detail_transaksi d 
                                     JOIN transaksi t ON d.transaksi_id = t.id 
                                     WHERE d.item_type = 'jasa' 
                                     AND DATE(t.created_at) BETWEEN '$start_date' AND '$end_date'
                                     AND t.status = 'lunas'"))['total'];

// Pendapatan Sparepart
$pendapatan_sparepart = fetch_assoc(query("SELECT SUM(d.harga * d.jumlah) as total 
                                          FROM detail_transaksi d 
                                          JOIN transaksi t ON d.transaksi_id = t.id 
                                          WHERE d.item_type = 'sparepart' 
                                          AND DATE(t.created_at) BETWEEN '$start_date' AND '$end_date'
                                          AND t.status = 'lunas'"))['total'];

// Transaksi per hari
$transaksi_per_hari = query("SELECT DATE(created_at) as tanggal, 
                             COUNT(*) as jumlah_transaksi,
                             SUM(CASE WHEN status = 'lunas' THEN total_harga ELSE 0 END) as pendapatan
                             FROM transaksi 
                             WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                             GROUP BY DATE(created_at)
                             ORDER BY tanggal DESC");

// Top 10 Produk Terlaris periode ini
$top_produk = query("SELECT 
                     CASE 
                        WHEN d.item_type = 'jasa' THEN (SELECT nama_jasa FROM jasa WHERE id = d.item_id)
                        ELSE (SELECT nama_sparepart FROM sparepart WHERE id = d.item_id)
                     END as nama_produk,
                     d.item_type,
                     COUNT(*) as total_terjual,
                     SUM(d.harga * d.jumlah) as pendapatan
                     FROM detail_transaksi d
                     JOIN transaksi t ON d.transaksi_id = t.id
                     WHERE DATE(t.created_at) BETWEEN '$start_date' AND '$end_date'
                     AND t.status = 'lunas'
                     GROUP BY d.item_type, d.item_id
                     ORDER BY total_terjual DESC
                     LIMIT 10");

// Rekap per metode pembayaran
$rekap_metode = query("SELECT metode_pembayaran, 
                       COUNT(*) as jumlah,
                       SUM(total_harga) as total
                       FROM transaksi 
                       WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                       AND status = 'lunas'
                       GROUP BY metode_pembayaran");
?>

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-tie me-2"></i>Menu Owner
                </h5>
                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="booking.php"><i class="fas fa-credit-card"></i>Data Booking</a>
                <a href="laporan.php" class="active"><i class="fas fa-chart-bar"></i>Laporan Keuangan</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <h3 class="fw-bold text-dark mb-0">Laporan Keuangan</h3>
                <div class="d-flex gap-2 mt-3 mt-md-0 d-print-none">
                    <button type="button" class="btn btn-outline-dark rounded-pill px-3 shadow-sm" onclick="printLaporan()">
                        <i class="fas fa-print me-2"></i>Cetak PDF
                    </button>
                    <a href="laporan.php?export=excel&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-success rounded-pill px-3 shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </a>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4 d-print-none" data-aos="fade-down">
                <div class="card-body p-4">
                    <form method="GET" action="" class="row align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">
                                <i class="fas fa-filter me-2"></i>Filter Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="d-none d-print-block mb-4 text-center">
                <h2>Laporan Keuangan Bengkel Jaya Abadi</h2>
                <p>Periode: <?php echo date('d M Y', strtotime($start_date)); ?> s/d <?php echo date('d M Y', strtotime($end_date)); ?></p>
                <hr>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm rounded-4" style="background: var(--primary-gradient);">
                        <div class="card-body p-4 text-white position-relative overflow-hidden">
                            <i class="fas fa-wallet fa-4x position-absolute opacity-25" style="right: -10px; bottom: -10px;"></i>
                            <h6 class="opacity-75 mb-1 fw-medium small">Total Pendapatan</h6>
                            <h3 class="fw-bold mb-0">Rp <?php echo number_format($total_pendapatan ?: 0, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-sm rounded-4 bg-white border-start border-4 border-success">
                        <div class="card-body p-4">
                            <h6 class="text-muted mb-1 fw-medium small">Total Transaksi Lunas</h6>
                            <h3 class="fw-bold text-dark mb-0"><?php echo $total_transaksi; ?> <span class="fs-6 text-muted fw-normal">Order</span></h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 shadow-sm rounded-4 bg-white border-start border-4 border-warning">
                        <div class="card-body p-4">
                            <h6 class="text-muted mb-1 fw-medium small">Pendapatan Jasa</h6>
                            <h3 class="fw-bold text-dark mb-0">Rp <?php echo number_format($pendapatan_jasa ?: 0, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 border-0 shadow-sm rounded-4 bg-white border-start border-4 border-info">
                        <div class="card-body p-4">
                            <h6 class="text-muted mb-1 fw-medium small">Pendapatan Sparepart</h6>
                            <h3 class="fw-bold text-dark mb-0">Rp <?php echo number_format($pendapatan_sparepart ?: 0, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-2 px-4 border-bottom-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-area text-primary me-2"></i>Tren Pendapatan Harian</h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px; width: 100%;">
                        <canvas id="harianChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-fire text-danger me-2"></i>Top 10 Terlaris</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Produk/Jasa</th>
                                            <th>Tipe</th>
                                            <th>Terjual</th>
                                            <th class="text-end pe-4">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <?php 
                                        if(num_rows($top_produk) > 0):
                                            while($row = fetch_assoc($top_produk)): 
                                        ?>
                                        <tr>
                                            <td class="ps-4 fw-medium text-dark"><?php echo $row['nama_produk']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $row['item_type'] == 'jasa' ? 'primary' : 'info'; ?> bg-opacity-10 text-<?php echo $row['item_type'] == 'jasa' ? 'primary' : 'info'; ?> rounded-pill px-2 py-1 small text-uppercase">
                                                    <?php echo $row['item_type']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $row['total_terjual']; ?>x</td>
                                            <td class="text-end pe-4 fw-bold text-success">Rp <?php echo number_format($row['pendapatan'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data penjualan di periode ini.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-wallet text-secondary me-2"></i>Metode Pembayaran</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Metode</th>
                                            <th>Frekuensi</th>
                                            <th class="text-end pe-4">Total Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <?php 
                                        if(num_rows($rekap_metode) > 0):
                                            while($row = fetch_assoc($rekap_metode)): 
                                        ?>
                                        <tr>
                                            <td class="ps-4 fw-medium text-dark">
                                                <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                                                <?php echo strtoupper($row['metode_pembayaran'] ?: 'TUNAI'); ?>
                                            </td>
                                            <td><?php echo $row['jumlah']; ?> transaksi</td>
                                            <td class="text-end pe-4 fw-bold">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada data pembayaran.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-list text-success me-2"></i>Rincian Harian</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Jumlah Transaksi</th>
                                    <th class="text-end pe-4">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php 
                                $total_pendapatan_harian = 0;
                                if(num_rows($transaksi_per_hari) > 0):
                                    while($row = fetch_assoc($transaksi_per_hari)): 
                                        $total_pendapatan_harian += $row['pendapatan'];
                                ?>
                                <tr>
                                    <td class="ps-4 fw-medium"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $row['jumlah_transaksi']; ?> TRX</span></td>
                                    <td class="text-end pe-4 fw-bold text-primary">Rp <?php echo number_format($row['pendapatan'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada transaksi di periode ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold text-uppercase py-3">Grand Total</td>
                                    <td class="text-end pe-4 fw-bold fs-5 text-success py-3">Rp <?php echo number_format($total_pendapatan_harian, 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data untuk grafik harian
<?php
$tanggal = [];
$pendapatan = [];
// Reset pointer result set untuk grafik
$transaksi_harian = query("SELECT DATE(created_at) as tanggal, 
                           SUM(total_harga) as total 
                           FROM transaksi 
                           WHERE status = 'lunas'
                           AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                           GROUP BY DATE(created_at) 
                           ORDER BY tanggal ASC");

while($row = fetch_assoc($transaksi_harian)) {
    $tanggal[] = date('d M', strtotime($row['tanggal']));
    $pendapatan[] = $row['total'];
}
?>

document.addEventListener('DOMContentLoaded', function() {
    const ctxHarian = document.getElementById('harianChart');
    if(ctxHarian) {
        // Gradient color for line chart
        const gradient = ctxHarian.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // Primary color with opacity
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        new Chart(ctxHarian, {
            type: 'line', // Diubah menjadi line chart (Area) agar lebih modern
            data: {
                labels: <?php echo json_encode($tanggal); ?>,
                datasets: [{
                    label: 'Pendapatan',
                    data: <?php echo json_encode($pendapatan); ?>,
                    backgroundColor: gradient,
                    borderColor: '#4f46e5', // Primary color
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Membuat kurva melengkung (smooth)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Sembunyikan legend karena hanya 1 dataset
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13 },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 14, weight: 'bold' },
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif" },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + 'K';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    }
});

function printLaporan() {
    window.print();
}
</script>

<style media="print">
    /* Styling khusus saat dokumen dicetak agar rapih di kertas */
    @page { size: landscape; margin: 1cm; }
    body { background: white !important; font-size: 11pt; }
    .sidebar, .navbar, footer, .d-print-none, .aos-init, .aos-animate { display: none !important; }
    .container-fluid { padding: 0 !important; margin: 0 !important; }
    .col-md-9, .col-lg-10 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; margin-bottom: 20px !important; break-inside: avoid; }
    .card-header { background: white !important; border-bottom: 2px solid #000 !important; padding: 0 0 10px 0 !important; }
    .table { border-collapse: collapse !important; width: 100% !important; }
    .table th, .table td { border: 1px solid #ddd !important; padding: 8px !important; }
    .bg-light { background-color: #f9f9f9 !important; -webkit-print-color-adjust: exact; }
    .shadow-sm { box-shadow: none !important; }
    canvas { max-height: 250px !important; }
</style>

<?php include '../includes/footer.php'; ?>
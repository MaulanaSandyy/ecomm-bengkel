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
            <h2 class="mb-4">Laporan Keuangan</h2>
            
            <!-- Filter Periode -->
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?php echo $start_date; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="<?php echo $end_date; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Tampilkan
                                </button>
                                <button type="button" class="btn btn-success" onclick="printLaporan()">
                                    <i class="fas fa-print me-2"></i>Print
                                </button>
                                <a href="laporan.php?export=excel&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                                   class="btn btn-warning">
                                    <i class="fas fa-file-excel me-2"></i>Export Excel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Ringkasan Laporan -->
            <div class="row mb-4">
                <div class="col-md-3" data-aos="fade-up">
                    <div class="card bg-primary text-white p-3">
                        <h6>Total Pendapatan</h6>
                        <h3>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                        <small>Periode: <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?></small>
                    </div>
                </div>
                
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card bg-success text-white p-3">
                        <h6>Total Transaksi</h6>
                        <h3><?php echo $total_transaksi; ?></h3>
                        <small>Transaksi sukses</small>
                    </div>
                </div>
                
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card bg-info text-white p-3">
                        <h6>Pendapatan Jasa</h6>
                        <h3>Rp <?php echo number_format($pendapatan_jasa, 0, ',', '.'); ?></h3>
                        <small>Dari layanan service</small>
                    </div>
                </div>
                
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card bg-warning text-white p-3">
                        <h6>Pendapatan Sparepart</h6>
                        <h3>Rp <?php echo number_format($pendapatan_sparepart, 0, ',', '.'); ?></h3>
                        <small>Dari penjualan sparepart</small>
                    </div>
                </div>
            </div>
            
            <!-- Grafik Pendapatan Harian -->
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Grafik Pendapatan Harian</h5>
                </div>
                <div class="card-body">
                    <canvas id="harianChart" height="80"></canvas>
                </div>
            </div>
            
            <!-- Tabel Transaksi Harian -->
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Rekap Transaksi Harian</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $total_pendapatan_harian = 0;
                                while($row = fetch_assoc($transaksi_per_hari)): 
                                    $total_pendapatan_harian += $row['pendapatan'];
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td><?php echo $row['jumlah_transaksi']; ?> transaksi</td>
                                    <td>Rp <?php echo number_format($row['pendapatan'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Total</td>
                                    <td>Rp <?php echo number_format($total_pendapatan_harian, 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Top Produk -->
            <div class="row mb-4">
                <div class="col-md-6" data-aos="fade-right">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-crown me-2"></i>Top 10 Produk Terlaris</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Produk</th>
                                            <th>Tipe</th>
                                            <th>Terjual</th>
                                            <th>Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while($row = fetch_assoc($top_produk)): 
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row['nama_produk']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $row['item_type'] == 'jasa' ? 'primary' : 'success'; ?>">
                                                    <?php echo $row['item_type']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $row['total_terjual']; ?>x</td>
                                            <td>Rp <?php echo number_format($row['pendapatan'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6" data-aos="fade-left">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Rekap Metode Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Metode Pembayaran</th>
                                            <th>Jumlah Transaksi</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while($row = fetch_assoc($rekap_metode)): 
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row['metode_pembayaran'] ?: 'Tidak diketahui'; ?></td>
                                            <td><?php echo $row['jumlah']; ?> transaksi</td>
                                            <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data untuk grafik harian
<?php
$tanggal = [];
$pendapatan = [];
$transaksi_harian = query("SELECT DATE(created_at) as tanggal, 
                           SUM(total_harga) as total 
                           FROM transaksi 
                           WHERE status = 'lunas'
                           AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                           GROUP BY DATE(created_at) 
                           ORDER BY tanggal ASC");
while($row = fetch_assoc($transaksi_harian)) {
    $tanggal[] = date('d/m', strtotime($row['tanggal']));
    $pendapatan[] = $row['total'];
}
?>

new Chart(document.getElementById('harianChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($tanggal); ?>,
        datasets: [{
            label: 'Pendapatan Harian',
            data: <?php echo json_encode($pendapatan); ?>,
            backgroundColor: 'rgba(102, 126, 234, 0.5)',
            borderColor: '#667eea',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

function printLaporan() {
    window.print();
}
</script>

<style media="print">
    .sidebar, .navbar, footer, .btn, form, .card-header {
        display: none !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
    .col-md-9 {
        width: 100% !important;
        flex: 0 0 100% !important;
    }
</style>

<?php include '../includes/footer.php'; ?>
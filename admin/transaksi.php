<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1); // Hanya admin

// Handle Update Status
if (isset($_GET['update_status'])) {
    $id = $_GET['update_status'];
    $status = $_GET['status'];
    
    $query = "UPDATE transaksi SET status = '$status' WHERE id = $id";
    
    if (query($query)) {
        $_SESSION['success'] = "Status pembayaran berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Status pembayaran gagal diupdate!";
    }
    header("Location: transaksi.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Hapus detail transaksi dulu
    query("DELETE FROM detail_transaksi WHERE transaksi_id = $id");
    
    if (query("DELETE FROM transaksi WHERE id = $id")) {
        $_SESSION['success'] = "Data transaksi berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Data transaksi gagal dihapus!";
    }
    header("Location: transaksi.php");
    exit();
}

// Handle Filter
$where = "WHERE 1=1";
if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $status = $_GET['filter_status'];
    $where .= " AND t.status = '$status'";
}

if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $start_date = $_GET['start_date'];
    $where .= " AND DATE(t.created_at) >= '$start_date'";
}

if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
    $where .= " AND DATE(t.created_at) <= '$end_date'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = escape_string($_GET['search']);
    $where .= " AND (t.kode_transaksi LIKE '%$search%' OR u.nama_lengkap LIKE '%$search%')";
}

// Get all transactions
$transaksi = query("SELECT t.*, u.nama_lengkap, u.no_hp,
                   (SELECT COUNT(*) FROM detail_transaksi WHERE transaksi_id = t.id) as total_item
                   FROM transaksi t 
                   JOIN users u ON t.user_id = u.id 
                   $where
                   ORDER BY t.created_at DESC");

// Statistik Transaksi
$total_transaksi = num_rows(query("SELECT * FROM transaksi"));
$total_pending = num_rows(query("SELECT * FROM transaksi WHERE status = 'pending'"));
$total_lunas = num_rows(query("SELECT * FROM transaksi WHERE status = 'lunas'"));
$total_batal = num_rows(query("SELECT * FROM transaksi WHERE status = 'batal'"));

$total_pendapatan = fetch_assoc(query("SELECT SUM(total_harga) as total FROM transaksi WHERE status = 'lunas'"))['total'];
$pendapatan_bulan_ini = fetch_assoc(query("SELECT SUM(total_harga) as total FROM transaksi 
                                          WHERE status = 'lunas' AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                                          AND YEAR(created_at) = YEAR(CURRENT_DATE())"))['total'];

$title = "Kelola Transaksi";
include '../includes/header.php';
?>

<div class="container-fluid px-0 px-lg-4 mt-3" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">
        
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-shield-alt me-2"></i>Menu Admin
                </h5>
                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="users.php"><i class="fas fa-users"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-box-open"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt"></i>Kelola Booking</a>
                <a href="transaksi.php" class="active"><i class="fas fa-cash-register"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode"></i>Upload QRIS</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold mb-4 text-dark">Data Transaksi & Pembayaran</h3>
            
            <div class="row g-3 mb-4">
                <div class="col-lg-7">
                    <div class="row g-3 h-100 row-cols-2 row-cols-md-4">
                        <div class="col" data-aos="fade-up">
                            <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-primary h-100">
                                <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Semua</h6>
                                    <h3 class="mb-0 fw-bold text-dark"><?php echo $total_transaksi; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-aos="fade-up" data-aos-delay="50">
                            <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-warning h-100">
                                <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Pending</h6>
                                    <h3 class="mb-0 fw-bold text-warning"><?php echo $total_pending; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-aos="fade-up" data-aos-delay="100">
                            <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-success h-100">
                                <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Lunas</h6>
                                    <h3 class="mb-0 fw-bold text-success"><?php echo $total_lunas; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-aos="fade-up" data-aos-delay="150">
                            <div class="card bg-white border-0 shadow-sm rounded-4 border-start border-4 border-danger h-100">
                                <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Batal</h6>
                                    <h3 class="mb-0 fw-bold text-danger"><?php echo $total_batal; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="row g-3 h-100 row-cols-1 row-cols-sm-2">
                        <div class="col" data-aos="fade-left" data-aos-delay="200">
                            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--success-gradient);">
                                <div class="card-body p-3 text-white">
                                    <p class="mb-1 small fw-medium opacity-75">Bulan Ini</p>
                                    <h4 class="fw-bold mb-0 text-truncate" title="Rp <?php echo number_format($pendapatan_bulan_ini ?: 0, 0, ',', '.'); ?>">Rp <?php echo number_format($pendapatan_bulan_ini ?: 0, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col" data-aos="fade-left" data-aos-delay="250">
                            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--primary-gradient);">
                                <div class="card-body p-3 text-white">
                                    <p class="mb-1 small fw-medium opacity-75">Total Revenue</p>
                                    <h4 class="fw-bold mb-0 text-truncate" title="Rp <?php echo number_format($total_pendapatan ?: 0, 0, ',', '.'); ?>">Rp <?php echo number_format($total_pendapatan ?: 0, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Daftar Invoice</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm" onclick="exportToExcel('transaksiTable', 'Laporan_Transaksi.xlsx')">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </button>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Cetak
                            </button>
                        </div>
                    </div>
                    
                    <form method="GET" action="" class="row align-items-end g-3 d-print-none">
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label text-muted small fw-bold">Status</label>
                            <select name="filter_status" class="form-select bg-light border-0">
                                <option value="">Semua</option>
                                <option value="pending" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="lunas" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                                <option value="batal" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label text-muted small fw-bold">Tgl Mulai</label>
                            <input type="date" name="start_date" class="form-control bg-light border-0" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label text-muted small fw-bold">Tgl Akhir</label>
                            <input type="date" name="end_date" class="form-control bg-light border-0" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label text-muted small fw-bold">Cari Data</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 px-3"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-0 ps-0" placeholder="Kode Invoice / Nama..." value="<?php echo $_GET['search'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill shadow-sm">Cari</button>
                            <a href="transaksi.php" class="btn btn-light border rounded-pill px-3" data-bs-toggle="tooltip" title="Reset">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="transaksiTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Invoice</th>
                                    <th>Customer Info</th>
                                    <th>Rincian</th>
                                    <th>Metode & Status</th>
                                    <th class="text-end pe-4 d-print-none">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php 
                                $total_table_pendapatan = 0;
                                while($row = fetch_assoc($transaksi)): 
                                    if($row['status'] == 'lunas') $total_table_pendapatan += $row['total_harga'];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-light text-dark border d-inline-block w-auto mb-1 align-self-start fw-bold fs-6">
                                                <i class="fas fa-file-invoice me-1"></i><?php echo $row['kode_transaksi']; ?>
                                            </span>
                                            <span class="small text-muted"><i class="far fa-calendar-alt me-1"></i><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-bold text-dark"><?php echo $row['nama_lengkap']; ?></h6>
                                        <small class="text-muted"><i class="fas fa-phone-alt me-1"></i><?php echo $row['no_hp']; ?></small>
                                    </td>
                                    <td>
                                        <span class="d-block text-muted small mb-1"><?php echo $row['total_item']; ?> Item Servis/Part</span>
                                        <h6 class="mb-0 fw-bold text-success">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></h6>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1 align-items-start">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-1 text-uppercase small" style="font-size: 0.65rem;">
                                                <i class="fas fa-wallet me-1"></i> <?php echo $row['metode_pembayaran'] ?: 'BELUM DIPILIH'; ?>
                                            </span>
                                            <?php
                                            $badge = ['pending' => 'warning', 'lunas' => 'success', 'batal' => 'danger'];
                                            ?>
                                            <span class="badge bg-<?php echo $badge[$row['status']]; ?> bg-opacity-10 text-<?php echo $badge[$row['status']]; ?> border border-<?php echo $badge[$row['status']]; ?> border-opacity-25 rounded-pill px-2 py-1 text-uppercase small" style="font-size: 0.65rem;">
                                                <i class="fas fa-circle me-1" style="font-size: 6px; vertical-align: middle;"></i> <?php echo $row['status']; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4 d-print-none">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm border" 
                                                    onclick="lihatDetail(<?php echo $row['id']; ?>)" data-bs-toggle="tooltip" title="Lihat Detail Nota">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border rounded-circle shadow-sm text-dark px-2" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                    <li><h6 class="dropdown-header">Update Pembayaran</h6></li>
                                                    <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=pending"><i class="fas fa-clock text-warning me-2 w-20px"></i>Set Pending</a></li>
                                                    <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=lunas"><i class="fas fa-check-circle text-success me-2 w-20px"></i>Pelunasan</a></li>
                                                    <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=batal"><i class="fas fa-times-circle text-danger me-2 w-20px"></i>Set Batal</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="return confirmDelete('?delete=<?php echo $row['id']; ?>', 'Hapus permanen invoice ini beserta detailnya?')"><i class="fas fa-trash me-2 w-20px"></i>Hapus Data</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if(num_rows($transaksi) == 0): ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Data transaksi kosong atau tidak sesuai kriteria filter.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-light d-none d-print-table-row">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold pt-3 pb-3">TOTAL LUNAS DARI TABEL INI:</td>
                                    <td colspan="3" class="fw-bold text-success fs-5 pt-3 pb-3">Rp <?php echo number_format($total_table_pendapatan, 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i>Rincian Invoice Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light" id="detailContent">
                <div class="p-5 text-center text-muted">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p>Memuat data nota...</p>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style media="print">
    /* Styling khusus saat dokumen dicetak agar rapih di kertas */
    @page { size: portrait; margin: 1cm; }
    body { background: white !important; font-size: 11pt; }
    .sidebar, .navbar, footer, .d-print-none, .aos-init, .aos-animate { display: none !important; }
    .container-fluid { padding: 0 !important; margin: 0 !important; }
    .col-md-9, .col-lg-10 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; margin-bottom: 20px !important; break-inside: avoid; }
    .card-header { background: white !important; border-bottom: 2px solid #000 !important; padding: 0 0 10px 0 !important; }
    .table { border-collapse: collapse !important; width: 100% !important; }
    .table th, .table td { border: 1px solid #ddd !important; padding: 8px !important; }
    .bg-light { background-color: #f9f9f9 !important; -webkit-print-color-adjust: exact; }
</style>

<script>
function lihatDetail(id) {
    // Show Modal with loading state first
    var modal = new bootstrap.Modal(document.getElementById('detailModal'));
    document.getElementById('detailContent').innerHTML = '<div class="p-5 text-center text-muted"><div class="spinner-border text-primary mb-3" role="status"></div><p>Memuat data nota...</p></div>';
    modal.show();

    // Load detail via AJAX
    fetch('get_detail_transaksi.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
        })
        .catch(err => {
            document.getElementById('detailContent').innerHTML = '<div class="p-5 text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Gagal memuat data. Silakan coba lagi.</p></div>';
        });
}

function exportToExcel(tableId, filename) {
    // Simple export function
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    rows.forEach(row => {
        // Skip header footer that might be problematic or hidden columns (Aksi)
        const cols = row.querySelectorAll('td:not(.d-print-none), th:not(.d-print-none)');
        if(cols.length > 0) {
            const rowData = [];
            cols.forEach(col => {
                rowData.push('"' + col.innerText.replace(/"/g, '""').replace(/\n/g, ' ') + '"');
            });
            csv.push(rowData.join(','));
        }
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename.replace('.xlsx', '.csv'); // Convert extension simply for CSV
    a.click();
}
</script>

<?php include '../includes/footer.php'; ?>
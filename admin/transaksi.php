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
        $_SESSION['success'] = "Status transaksi berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Status transaksi gagal diupdate!";
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
        $_SESSION['success'] = "Transaksi berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Transaksi gagal dihapus!";
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

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <h4 class="text-white mb-4"><i class="fas fa-dashboard me-2"></i>Menu Admin</h4>
                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="users.php"><i class="fas fa-users me-2"></i>Kelola Users</a>
                <a href="jasa.php"><i class="fas fa-wrench me-2"></i>Kelola Jasa</a>
                <a href="sparepart.php"><i class="fas fa-oil-can me-2"></i>Kelola Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-alt me-2"></i>Kelola Booking</a>
                <a href="transaksi.php" class="active"><i class="fas fa-credit-card me-2"></i>Kelola Transaksi</a>
                <a href="profil.php"><i class="fas fa-building me-2"></i>Profil Bengkel</a>
                <a href="qris.php"><i class="fas fa-qrcode me-2"></i>Upload QRIS</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">Kelola Transaksi</h2>
            
            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3" data-aos="fade-up">
                    <div class="card bg-primary text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Total Transaksi</h6>
                                <h3><?php echo $total_transaksi; ?></h3>
                            </div>
                            <i class="fas fa-credit-card fa-3x"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card bg-warning text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Pending</h6>
                                <h3><?php echo $total_pending; ?></h3>
                            </div>
                            <i class="fas fa-clock fa-3x"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card bg-success text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Lunas</h6>
                                <h3><?php echo $total_lunas; ?></h3>
                            </div>
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card bg-danger text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Batal</h6>
                                <h3><?php echo $total_batal; ?></h3>
                            </div>
                            <i class="fas fa-times-circle fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistik Pendapatan -->
            <div class="row mb-4">
                <div class="col-md-6" data-aos="fade-right">
                    <div class="card bg-info text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Total Pendapatan</h6>
                                <h3>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                            </div>
                            <i class="fas fa-money-bill-wave fa-3x"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6" data-aos="fade-left">
                    <div class="card bg-secondary text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Pendapatan Bulan Ini</h6>
                                <h3>Rp <?php echo number_format($pendapatan_bulan_ini, 0, ',', '.'); ?></h3>
                            </div>
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Form -->
            <div class="card mb-4" data-aos="fade-down">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-2">
                            <select name="filter_status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="pending" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="lunas" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                                <option value="batal" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?php echo $_GET['start_date'] ?? ''; ?>" placeholder="Start Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" class="form-control" 
                                   value="<?php echo $_GET['end_date'] ?? ''; ?>" placeholder="End Date">
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari kode transaksi atau customer..." 
                                       value="<?php echo $_GET['search'] ?? ''; ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="transaksi.php" class="btn btn-secondary w-100">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabel Transaksi -->
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Transaksi</h5>
                    <div>
                        <button class="btn btn-light btn-sm me-2" onclick="exportToExcel('transaksiTable', 'transaksi.xlsx')">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="btn btn-light btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="transaksiTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>No. HP</th>
                                    <th>Total Item</th>
                                    <th>Total Harga</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1; 
                                while($row = fetch_assoc($transaksi)): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <strong><?php echo $row['kode_transaksi']; ?></strong>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['no_hp']; ?></td>
                                    <td class="text-center"><?php echo $row['total_item']; ?></td>
                                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo $row['metode_pembayaran'] ?: 'Belum dipilih'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = [
                                            'pending' => 'warning',
                                            'lunas' => 'success',
                                            'batal' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $badge[$row['status']]; ?>">
                                            <?php echo strtoupper($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="lihatDetail(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=pending">
                                                        <i class="fas fa-clock text-warning me-2"></i>Set Pending
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=lunas">
                                                        <i class="fas fa-check-circle text-success me-2"></i>Set Lunas
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=batal">
                                                        <i class="fas fa-times-circle text-danger me-2"></i>Set Batal
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" 
                                                       onclick="return confirmDelete('?delete=<?php echo $row['id']; ?>')">
                                                        <i class="fas fa-trash me-2"></i>Hapus
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="6" class="text-end">Total:</td>
                                    <td>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function lihatDetail(id) {
    // Load detail via AJAX
    fetch('get_detail_transaksi.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
            var modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        });
}

function exportToExcel(tableId, filename) {
    // Simple export function
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename.replace('.xlsx', '.csv');
    a.click();
}
</script>

<?php include '../includes/footer.php'; ?>
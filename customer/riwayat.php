<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

$title = "Riwayat Transaksi";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

$transaksi = query("
SELECT t.*, 
       (SELECT COUNT(*) FROM detail_transaksi WHERE transaksi_id = t.id) as total_item
FROM transaksi t
WHERE t.user_id = $user_id
ORDER BY t.created_at DESC
");
?>

<div class="container-fluid px-0 px-lg-4" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">

        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-circle me-2"></i>Menu Pelanggan
                </h5>

                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="beli.php"><i class="fas fa-shopping-bag me-2"></i>Beli Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-check me-2"></i>Booking Service</a>
                <a href="checkout.php"><i class="fas fa-shopping-cart me-2"></i>Keranjang / Checkout</a>
                <a href="riwayat.php" class="active"><i class="fas fa-receipt me-2"></i>Riwayat Transaksi</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">

            <h3 class="fw-bold text-dark mb-4">Riwayat Transaksi Anda</h3>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-history"></i>
                    </div>
                    <h5 class="mb-0 fw-bold text-dark">Data Pemesanan & Servis</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Kode Invoice</th>
                                    <th class="py-3">Waktu Pemesanan</th>
                                    <th class="py-3">Metode Bayar</th>
                                    <th class="py-3">Detail Item</th>
                                    <th class="py-3 text-end">Total Biaya</th>
                                    <th class="py-3 text-center pe-4">Status</th>
                                    <th class="py-3 text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">

                                <?php if(num_rows($transaksi) > 0): ?>
                                    <?php while($row = fetch_assoc($transaksi)): 
                                        // Mapping status yang benar untuk transaksi (pending, lunas, batal)
                                        $status_color = [
                                            'pending' => 'warning',
                                            'lunas' => 'success',
                                            'batal' => 'danger'
                                        ];
                                        
                                        $status_icon = [
                                            'pending' => 'fa-clock',
                                            'lunas' => 'fa-check-circle',
                                            'batal' => 'fa-times-circle'
                                        ];
                                        
                                        $status_text = [
                                            'pending' => 'Menunggu Pembayaran',
                                            'lunas' => 'Lunas',
                                            'batal' => 'Dibatalkan'
                                        ];
                                        
                                        $warna = $status_color[$row['status']] ?? 'secondary';
                                        $icon = $status_icon[$row['status']] ?? 'fa-receipt';
                                        $text = $status_text[$row['status']] ?? ucfirst($row['status']);
                                    ?>
                                    <tr class="align-middle">
                                        <td class="ps-4">
                                            <span class="fw-bold text-primary d-block mb-1">
                                                <i class="fas fa-file-invoice me-1"></i><?php echo $row['kode_transaksi']; ?>
                                            </span>
                                            <small class="text-muted"><?php echo $row['total_item']; ?> item</small>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex flex-column text-muted small">
                                                <span class="text-dark fw-medium"><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                                                <span><?php echo date('H:i', strtotime($row['created_at'])); ?> WIB</span>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-normal">
                                                <i class="fas fa-wallet me-1"></i> <?php echo $row['metode_pembayaran'] ?: 'Belum dipilih'; ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill" 
                                                    onclick="lihatDetail(<?php echo $row['id']; ?>)"
                                                    data-bs-toggle="tooltip" title="Lihat Detail Item">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </button>
                                        </td>
                                        
                                        <td class="text-end fw-bold text-dark">
                                            Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?>
                                        </td>
                                        
                                        <td class="text-center">
                                            <span class="badge bg-<?php echo $warna; ?> bg-opacity-10 text-<?php echo $warna; ?> rounded-pill px-3 py-2 text-uppercase small">
                                                <i class="fas <?php echo $icon; ?> me-1"></i> <?php echo $text; ?>
                                            </span>
                                        </td>
                                        
                                        <td class="text-center pe-4">
                                            <?php if($row['status'] == 'pending'): ?>
                                                <a href="pembayaran.php?transaksi_id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-primary rounded-pill">
                                                    <i class="fas fa-credit-card me-1"></i> Bayar Sekarang
                                                </a>
                                            <?php elseif($row['status'] == 'lunas'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> Pembayaran Sukses
                                                </span>
                                            <?php elseif($row['status'] == 'batal'): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Dibatalkan
                                                </span>
                                            <?php endif; ?>
                                        <tr>
                                    </tr>
                                    <?php endwhile; ?>

                                <?php else: ?>
                                    <tr class="align-middle">
                                        <td colspan="7" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 120px; opacity: 0.5;" class="mb-3">
                                            <h5 class="text-dark fw-bold mb-1">Belum Ada Transaksi</h5>
                                            <p class="text-muted small mb-3">Wah, sepertinya Anda belum pernah melakukan pesanan atau booking.</p>
                                            <a href="beli.php" class="btn btn-primary rounded-pill px-4"><i class="fas fa-shopping-bag me-2"></i>Mulai Belanja</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Detail Item -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i>Detail Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill" onclick="printDetail()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function lihatDetail(id) {
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data...</p>
        </div>
    `;
    
    fetch('../admin/get_detail_transaksi.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
            var modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <p>Gagal memuat data. Silakan coba lagi.</p>
                </div>
            `;
        });
}

function printDetail() {
    const printContent = document.getElementById('detailContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Detail Transaksi</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { padding: 20px; }
                @media print {
                    .no-print { display: none; }
                    body { margin: 0; padding: 15px; }
                }
            </style>
        </head>
        <body>
            <div class="print-area">
                ${printContent}
            </div>
            <div class="text-center no-print mt-4">
                <button class="btn btn-primary" onclick="window.print()">Print</button>
                <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// Tooltip initialization
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>

<?php include '../includes/footer.php'; ?>
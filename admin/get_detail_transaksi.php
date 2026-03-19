<?php
session_start();
include '../includes/koneksi.php';
// File ini digunakan oleh admin, owner, dll via AJAX

if (!isset($_GET['id'])) {
    exit('<div class="p-4 text-center text-danger">ID Transaksi tidak ditemukan!</div>');
}

$id = $_GET['id'];

// Ambil data transaksi
$transaksi = fetch_assoc(query("SELECT t.*, u.nama_lengkap, u.no_hp, u.alamat, u.email 
                               FROM transaksi t 
                               JOIN users u ON t.user_id = u.id 
                               WHERE t.id = $id"));

if (!$transaksi) {
    exit('<div class="p-4 text-center text-danger">Data Transaksi tidak ditemukan!</div>');
}

// Ambil data profil bengkel untuk kop nota
$profil = fetch_assoc(query("SELECT * FROM profil_bengkel WHERE id = 1"));
$nama_bengkel = $profil ? $profil['nama_bengkel'] : 'Bengkel Mobil Jaya Abadi';
$alamat_bengkel = $profil ? $profil['alamat'] : 'Jl. Bengkel No. 1';
$telp_bengkel = $profil ? $profil['no_telp'] : '021-123456';

// Ambil detail transaksi
$detail = query("SELECT d.*, 
                 CASE 
                    WHEN d.item_type = 'jasa' THEN (SELECT nama_jasa FROM jasa WHERE id = d.item_id)
                    ELSE (SELECT nama_sparepart FROM sparepart WHERE id = d.item_id)
                 END as nama_item,
                 CASE 
                    WHEN d.item_type = 'jasa' THEN 'Layanan Jasa'
                    ELSE 'Sparepart'
                 END as tipe
                 FROM detail_transaksi d 
                 WHERE d.transaksi_id = $id");

// Ambil data payment
$payment = fetch_assoc(query("SELECT * FROM payment WHERE transaksi_id = $id ORDER BY id DESC LIMIT 1"));
?>

<div class="bg-white p-4 mt-3" id="invoice-printable-area">
    
    <div class="row align-items-center mb-4 border-bottom pb-3">
        <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
            <h4 class="fw-bold text-primary mb-1"><i class="fas fa-car-side me-2"></i><?php echo $nama_bengkel; ?></h4>
            <p class="small text-muted mb-0"><?php echo $alamat_bengkel; ?></p>
            <p class="small text-muted mb-0"><i class="fas fa-phone-alt me-1"></i> <?php echo $telp_bengkel; ?></p>
        </div>
        <div class="col-sm-6 text-center text-sm-end">
            <h2 class="fw-bold text-muted mb-1 text-uppercase letter-spacing-1">INVOICE</h2>
            <h5 class="fw-bold text-dark mb-0">#<?php echo $transaksi['kode_transaksi']; ?></h5>
            <span class="badge bg-<?php echo ($transaksi['status'] == 'lunas') ? 'success' : (($transaksi['status'] == 'batal') ? 'danger' : 'warning'); ?> mt-2 px-3 py-2 text-uppercase">
                STATUS: <?php echo $transaksi['status']; ?>
            </span>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-sm-6">
            <div class="bg-light rounded-3 p-3 h-100 border">
                <p class="small fw-bold text-muted text-uppercase mb-2">Tagihan Kepada:</p>
                <h6 class="fw-bold text-dark mb-1"><?php echo $transaksi['nama_lengkap']; ?></h6>
                <p class="small text-muted mb-0"><i class="fas fa-phone me-2 w-15px"></i><?php echo $transaksi['no_hp']; ?></p>
                <?php if($transaksi['email']): ?><p class="small text-muted mb-0"><i class="fas fa-envelope me-2 w-15px"></i><?php echo $transaksi['email']; ?></p><?php endif; ?>
                <p class="small text-muted mt-2 mb-0 border-top pt-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fas fa-map-marker-alt me-2 w-15px"></i><?php echo $transaksi['alamat']; ?></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="bg-light rounded-3 p-3 h-100 border d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small fw-bold text-muted">Tgl Transaksi:</span>
                    <span class="small text-dark fw-medium"><?php echo date('d M Y', strtotime($transaksi['created_at'])); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 border-top pt-2">
                    <span class="small fw-bold text-muted">Waktu Transaksi:</span>
                    <span class="small text-dark fw-medium"><?php echo date('H:i', strtotime($transaksi['created_at'])); ?> WIB</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span class="small fw-bold text-muted">Metode Bayar:</span>
                    <span class="small text-primary fw-bold text-uppercase"><?php echo $transaksi['metode_pembayaran'] ?: 'Belum Dipilih'; ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive mb-4">
        <table class="table table-bordered border-light mb-0">
            <thead class="bg-dark text-white text-uppercase small" style="font-size: 0.8rem;">
                <tr>
                    <th class="py-3 text-center" width="5%">No</th>
                    <th class="py-3" width="45%">Deskripsi Item / Jasa</th>
                    <th class="py-3 text-end" width="15%">Harga</th>
                    <th class="py-3 text-center" width="10%">Qty</th>
                    <th class="py-3 text-end" width="25%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $total = 0;
                while($item = fetch_assoc($detail)): 
                    $subtotal = $item['harga'] * $item['jumlah'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td class="text-center text-muted align-middle"><?php echo $no++; ?></td>
                    <td>
                        <div class="fw-bold text-dark"><?php echo $item['nama_item']; ?></div>
                        <span class="badge bg-<?php echo $item['item_type'] == 'jasa' ? 'primary' : 'success'; ?> bg-opacity-10 text-<?php echo $item['item_type'] == 'jasa' ? 'primary' : 'success'; ?> border-0 mt-1" style="font-size: 0.65rem;">
                            <?php echo $item['tipe']; ?>
                        </span>
                    </td>
                    <td class="text-end align-middle">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                    <td class="text-center align-middle"><?php echo $item['jumlah']; ?></td>
                    <td class="text-end fw-bold text-dark align-middle">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="row border-top pt-3">
        <div class="col-sm-6 mb-4 mb-sm-0 text-center text-sm-start d-flex flex-column justify-content-end">
            <?php if ($payment && $payment['status'] == 'sukses'): ?>
                <div class="d-inline-flex align-items-center bg-success bg-opacity-10 text-success rounded px-3 py-2 border border-success border-opacity-25 mb-3" style="width: fit-content;">
                    <i class="fas fa-check-circle fs-4 me-2"></i>
                    <div>
                        <span class="d-block small fw-bold">PEMBAYARAN TERVERIFIKASI</span>
                        <span style="font-size: 0.7rem;">Via <?php echo $payment['metode']; ?> pada <?php echo date('d/m/y', strtotime($payment['tanggal_payment'])); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <p class="small text-muted fst-italic mb-0 mt-auto">*Terima kasih telah mempercayakan kendaraan Anda pada kami.</p>
        </div>
        
        <div class="col-sm-6">
            <table class="table table-borderless table-sm mb-0">
                <tr>
                    <td class="text-end text-muted fw-bold">Subtotal:</td>
                    <td class="text-end w-30">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td class="text-end text-muted fw-bold border-bottom pb-2">Biaya Tambahan:</td>
                    <td class="text-end w-30 border-bottom pb-2">Rp 0</td>
                </tr>
                <tr>
                    <td class="text-end fw-bold text-dark pt-3 fs-5">TOTAL KESELURUHAN:</td>
                    <td class="text-end fw-bold text-success pt-3 fs-5">Rp <?php echo number_format($transaksi['total_harga'], 0, ',', '.'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="text-end mt-4 pt-3 border-top d-print-none px-4 pb-4">
    <button onclick="printInvoice()" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="fas fa-print me-2"></i>Cetak Invoice
    </button>
</div>

<script>
// Fungsi print spesifik hanya pada area modal/invoice
function printInvoice() {
    var printContents = document.getElementById('invoice-printable-area').innerHTML;
    var originalContents = document.body.innerHTML;

    // Bersihkan layar, masukkan content invoice saja
    document.body.innerHTML = printContents;
    
    // Panggil dialog print browser
    window.print();

    // Kembalikan layar ke semula setelah selesai/batal print
    document.body.innerHTML = originalContents;
    // Reload lokasi untuk mengembalikan fungsi JS/Event listener yang hilang krn DOM rewrite
    window.location.reload(); 
}
</script>
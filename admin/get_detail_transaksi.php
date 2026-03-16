<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(1);

if (!isset($_GET['id'])) {
    exit('ID tidak ditemukan');
}

$id = $_GET['id'];

// Ambil data transaksi
$transaksi = fetch_assoc(query("SELECT t.*, u.nama_lengkap, u.no_hp, u.alamat 
                               FROM transaksi t 
                               JOIN users u ON t.user_id = u.id 
                               WHERE t.id = $id"));

if (!$transaksi) {
    exit('Transaksi tidak ditemukan');
}

// Ambil detail transaksi
$detail = query("SELECT d.*, 
                 CASE 
                    WHEN d.item_type = 'jasa' THEN (SELECT nama_jasa FROM jasa WHERE id = d.item_id)
                    ELSE (SELECT nama_sparepart FROM sparepart WHERE id = d.item_id)
                 END as nama_item,
                 CASE 
                    WHEN d.item_type = 'jasa' THEN 'Jasa Service'
                    ELSE 'Sparepart'
                 END as tipe
                 FROM detail_transaksi d 
                 WHERE d.transaksi_id = $id");

// Ambil data payment
$payment = fetch_assoc(query("SELECT * FROM payment WHERE transaksi_id = $id ORDER BY id DESC LIMIT 1"));
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h6 class="fw-bold">Informasi Transaksi</h6>
            <table class="table table-sm">
                <tr>
                    <td>Kode Transaksi</td>
                    <td>: <strong><?php echo $transaksi['kode_transaksi']; ?></strong></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: <?php echo date('d/m/Y H:i', strtotime($transaksi['created_at'])); ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>: 
                        <?php
                        $badge = [
                            'pending' => 'warning',
                            'lunas' => 'success',
                            'batal' => 'danger'
                        ];
                        ?>
                        <span class="badge bg-<?php echo $badge[$transaksi['status']]; ?>">
                            <?php echo strtoupper($transaksi['status']); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td>: <?php echo $transaksi['metode_pembayaran'] ?: '-'; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold">Informasi Customer</h6>
            <table class="table table-sm">
                <tr>
                    <td>Nama</td>
                    <td>: <?php echo $transaksi['nama_lengkap']; ?></td>
                </tr>
                <tr>
                    <td>No. HP</td>
                    <td>: <?php echo $transaksi['no_hp']; ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: <?php echo $transaksi['alamat']; ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <hr>
    
    <h6 class="fw-bold">Detail Item</h6>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tipe</th>
                <th>Nama Item</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
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
                <td><?php echo $no++; ?></td>
                <td>
                    <span class="badge bg-<?php echo $item['item_type'] == 'jasa' ? 'primary' : 'success'; ?>">
                        <?php echo $item['tipe']; ?>
                    </span>
                </td>
                <td><?php echo $item['nama_item']; ?></td>
                <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                <td class="text-center"><?php echo $item['jumlah']; ?></td>
                <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="5" class="text-end">Total</td>
                <td>Rp <?php echo number_format($transaksi['total_harga'], 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>
    
    <?php if ($payment): ?>
    <hr>
    <h6 class="fw-bold">Informasi Pembayaran</h6>
    <table class="table table-sm">
        <tr>
            <td>Metode</td>
            <td>: <?php echo $payment['metode']; ?></td>
        </tr>
        <tr>
            <td>Status Payment</td>
            <td>: 
                <span class="badge bg-<?php echo $payment['status'] == 'sukses' ? 'success' : 'warning'; ?>">
                    <?php echo strtoupper($payment['status']); ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Tanggal Payment</td>
            <td>: <?php echo date('d/m/Y H:i', strtotime($payment['tanggal_payment'])); ?></td>
        </tr>
    </table>
    <?php endif; ?>
</div>
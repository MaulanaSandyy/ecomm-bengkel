<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../auth/login.php");
    exit();
}

include '../includes/koneksi.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$where = "WHERE DATE(t.created_at) BETWEEN '$start_date' AND '$end_date'";

$total_pendapatan = fetch_assoc(query("SELECT SUM(total_harga) as total FROM transaksi t $where AND t.status = 'lunas'"))['total'] ?: 0;
$total_transaksi = num_rows(query("SELECT * FROM transaksi t $where"));
$total_transaksi_lunas = num_rows(query("SELECT * FROM transaksi t $where AND t.status = 'lunas'"));

$pendapatan_jasa = fetch_assoc(query("SELECT SUM(d.harga * d.jumlah) as total FROM detail_transaksi d JOIN transaksi t ON d.transaksi_id = t.id WHERE d.item_type = 'jasa' AND DATE(t.created_at) BETWEEN '$start_date' AND '$end_date' AND t.status = 'lunas'"))['total'] ?: 0;

$pendapatan_sparepart = fetch_assoc(query("SELECT SUM(d.harga * d.jumlah) as total FROM detail_transaksi d JOIN transaksi t ON d.transaksi_id = t.id WHERE d.item_type = 'sparepart' AND DATE(t.created_at) BETWEEN '$start_date' AND '$end_date' AND t.status = 'lunas'"))['total'] ?: 0;

$transaksi_per_hari = query("SELECT DATE(created_at) as tanggal, 
                             COUNT(*) as jumlah_transaksi,
                             SUM(CASE WHEN status = 'lunas' THEN total_harga ELSE 0 END) as pendapatan
                             FROM transaksi 
                             WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                             GROUP BY DATE(created_at)
                             ORDER BY tanggal DESC");

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

$rekap_metode = query("SELECT metode_pembayaran, 
                       COUNT(*) as jumlah,
                       SUM(total_harga) as total
                       FROM transaksi 
                       WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                       AND status = 'lunas'
                       GROUP BY metode_pembayaran");

$profil = fetch_assoc(query("SELECT * FROM profil_bengkel WHERE id = 1"));
$nama_bengkel = $profil ? $profil['nama_bengkel'] : 'Bengkel Mobil Jaya Abadi';
$alamat_bengkel = $profil ? $profil['alamat'] : 'Jl. Bengkel No. 1';
$telp_bengkel = $profil ? $profil['no_telp'] : '021-123456';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan - <?php echo $nama_bengkel; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-size: 11px; }
        body { padding: 15px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .header-report { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header-report h3 { margin-bottom: 5px; }
        .header-report p { margin: 0; font-size: 10px; }
        .info-period { background: #f5f5f5; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .stat-box { background: #1e3a8a; color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .stat-box h5 { margin: 0; font-size: 12px; opacity: 0.8; }
        .stat-box h3 { margin: 5px 0 0 0; font-size: 18px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: middle; }
        th { background: #1e3a8a; color: white; text-align: center; font-weight: 600; font-size: 10px; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-light-row { background: #f8f9fa; }
        .total-row { background: #e8f5e9 !important; font-weight: bold; }
        .currency { mso-number-format:\#\,\#\#0; }
        .badge-jasa { background: #dbeafe; color: #1d4ed8; padding: 2px 6px; border-radius: 8px; font-size: 9px; }
        .badge-sparepart { background: #d1fae5; color: #047857; padding: 2px 6px; border-radius: 8px; font-size: 9px; }
        .footer-report { border-top: 1px solid #ccc; margin-top: 30px; padding-top: 10px; text-align: center; font-size: 9px; color: #666; }
        .no-print { margin-top: 20px; text-align: center; }
        @page { size: landscape; margin: 10mm; }
    </style>
</head>
<body>
    <div class="header-report">
        <h3 class="fw-bold mb-1"><i class="fas fa-car-side me-2"></i><?php echo $nama_bengkel; ?></h3>
        <p class="mb-1"><?php echo $alamat_bengkel; ?> | Telp: <?php echo $telp_bengkel; ?></p>
        <h4 class="fw-bold mt-2">LAPORAN KEUANGAN</h4>
    </div>

    <div class="info-period">
        <strong>PERIODE:</strong> <?php echo date('d/m/Y', strtotime($start_date)); ?> s/d <?php echo date('d/m/Y', strtotime($end_date)); ?>
    </div>

    <table width="100%" style="margin-bottom: 15px;">
        <tr>
            <td width="25%" style="vertical-align: top;">
                <div class="stat-box">
                    <h5>TOTAL PENDAPATAN</h5>
                    <h3 class="currency">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                </div>
            </td>
            <td width="25%" style="vertical-align: top; padding-left: 10px;">
                <div class="stat-box" style="background: #059669;">
                    <h5>TOTAL TRANSAKSI LUNAS</h5>
                    <h3><?php echo $total_transaksi_lunas; ?> Transaksi</h3>
                </div>
            </td>
            <td width="25%" style="vertical-align: top; padding-left: 10px;">
                <div class="stat-box" style="background: #d97706;">
                    <h5>PENDAPATAN JASA</h5>
                    <h3 class="currency">Rp <?php echo number_format($pendapatan_jasa, 0, ',', '.'); ?></h3>
                </div>
            </td>
            <td width="25%" style="vertical-align: top; padding-left: 10px;">
                <div class="stat-box" style="background: #0891b2;">
                    <h5>PENDAPATAN SPAREPART</h5>
                    <h3 class="currency">Rp <?php echo number_format($pendapatan_sparepart, 0, ',', '.'); ?></h3>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th colspan="4" class="text-center" style="background: #475569;">TOP 10 PRODUK/JASA TERLARIS</th>
            </tr>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Produk/Jasa</th>
                <th width="15%">Tipe</th>
                <th width="20%">Terjual</th>
                <th width="25%">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if(num_rows($top_produk) > 0):
                while($row = fetch_assoc($top_produk)): 
            ?>
            <tr>
                <td class="text-center"><?php echo $no++; ?></td>
                <td><?php echo $row['nama_produk']; ?></td>
                <td class="text-center">
                    <?php if($row['item_type'] == 'jasa'): ?>
                    <span class="badge-jasa">JASA</span>
                    <?php else: ?>
                    <span class="badge-sparepart">SPAREPART</span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?php echo $row['total_terjual']; ?>x</td>
                <td class="text-right currency">Rp <?php echo number_format($row['pendapatan'], 0, ',', '.'); ?></td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="5" class="text-center" style="color:#999;">Tidak ada data produk terlaris</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table style="margin-top: 15px;">
        <thead>
            <tr>
                <th colspan="3" class="text-center" style="background: #475569;">REKAP METODE PEMBAYARAN</th>
            </tr>
            <tr>
                <th width="40%">Metode Pembayaran</th>
                <th width="30%">Jumlah Transaksi</th>
                <th width="30%">Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(num_rows($rekap_metode) > 0):
                while($row = fetch_assoc($rekap_metode)): 
            ?>
            <tr>
                <td><strong><?php echo strtoupper($row['metode_pembayaran'] ?: 'TUNAI'); ?></strong></td>
                <td class="text-center"><?php echo $row['jumlah']; ?> transaksi</td>
                <td class="text-right currency">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="3" class="text-center" style="color:#999;">Tidak ada data pembayaran</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table style="margin-top: 15px;">
        <thead>
            <tr>
                <th colspan="3" class="text-center" style="background: #475569;">RINCIAN HARIAN</th>
            </tr>
            <tr>
                <th width="40%">Tanggal</th>
                <th width="30%">Jumlah Transaksi</th>
                <th width="30%">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_harian = 0;
            if(num_rows($transaksi_per_hari) > 0):
                while($row = fetch_assoc($transaksi_per_hari)): 
                    $total_harian += $row['pendapatan'];
            ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td class="text-center"><?php echo $row['jumlah_transaksi']; ?> transaksi</td>
                <td class="text-right currency">Rp <?php echo number_format($row['pendapatan'], 0, ',', '.'); ?></td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="3" class="text-center" style="color:#999;">Tidak ada transaksi harian</td>
            </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td class="font-bold">GRAND TOTAL</td>
                <td class="text-center font-bold"><?php echo $total_transaksi; ?> Transaksi</td>
                <td class="text-right font-bold currency" style="color:#059669;">Rp <?php echo number_format($total_harian, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer-report">
        <p class="mb-0">Dicetak pada: <?php echo date('d/m/Y H:i'); ?> WIB</p>
        <p class="mb-0"><?php echo $nama_bengkel; ?> - Laporan Keuangan</p>
    </div>

    <div class="no-print text-center">
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</button>
        <button class="btn btn-secondary" onclick="window.close()"><i class="fas fa-times me-2"></i>Tutup</button>
    </div>
</body>
</html>
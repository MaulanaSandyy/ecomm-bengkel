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

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Laporan_Keuangan_' . date('Ymd_His') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<title>Laporan Keuangan - <?php echo $nama_bengkel; ?></title>
<style>
    .header-title { font-size: 18px; font-weight: bold; color: #1e3a8a; text-align: center; }
    .header-info { font-size: 11px; color: #666; text-align: center; margin-bottom: 10px; }
    .filter-info { font-size: 11px; color: #888; margin-bottom: 15px; font-style: italic; text-align: center; }
    table { border-collapse: collapse; width: 100%; font-size: 11px; }
    th { background: #1e3a8a; color: white; padding: 8px; text-align: center; font-weight: bold; border: 1px solid #1e3a8a; }
    td { padding: 6px; border: 1px solid #ddd; vertical-align: middle; }
    tr:nth-child(even) { background: #f8f9fa; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .stat-box { background: #1e3a8a; color: white; padding: 10px; border-radius: 5px; text-align: center; }
    .badge-jasa { background: #dbeafe; color: #1d4ed8; padding: 2px 6px; border-radius: 8px; font-size: 9px; }
    .badge-sparepart { background: #d1fae5; color: #047857; padding: 2px 6px; border-radius: 8px; font-size: 9px; }
    .total-row { background: #e8f5e9 !important; font-weight: bold; }
    .currency { mso-number-format:\#\,\#\#0; }
    .section-header { background: #475569 !important; font-weight: bold; text-align: center; }
    .footer { font-size: 9px; color: #999; text-align: center; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
</style>
</head>
<body>
<div class="header-title"><?php echo $nama_bengkel; ?></div>
<div class="header-info"><?php echo $alamat_bengkel; ?> | Telp: <?php echo $telp_bengkel; ?></div>
<div class="header-info" style="font-weight:bold; font-size:14px;">LAPORAN KEUANGAN</div>
<div class="filter-info">Periode: <?php echo date('d/m/Y', strtotime($start_date)); ?> s/d <?php echo date('d/m/Y', strtotime($end_date)); ?></div>

<table>
    <tr>
        <td style="width:25%; vertical-align:top;">
            <div class="stat-box">
                <div style="font-size:10px; opacity:0.8;">TOTAL PENDAPATAN</div>
                <div class="currency" style="font-size:14px; font-weight:bold;">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
            </div>
        </td>
        <td style="width:25%; vertical-align:top; padding-left:8px;">
            <div class="stat-box" style="background:#059669;">
                <div style="font-size:10px; opacity:0.8;">TRANSAKSI LUNAS</div>
                <div style="font-size:14px; font-weight:bold;"><?php echo $total_transaksi_lunas; ?></div>
            </div>
        </td>
        <td style="width:25%; vertical-align:top; padding-left:8px;">
            <div class="stat-box" style="background:#d97706;">
                <div style="font-size:10px; opacity:0.8;">PENDAPATAN JASA</div>
                <div class="currency" style="font-size:14px; font-weight:bold;">Rp <?php echo number_format($pendapatan_jasa, 0, ',', '.'); ?></div>
            </div>
        </td>
        <td style="width:25%; vertical-align:top; padding-left:8px;">
            <div class="stat-box" style="background:#0891b2;">
                <div style="font-size:10px; opacity:0.8;">PENDAPATAN SPAREPART</div>
                <div class="currency" style="font-size:14px; font-weight:bold;">Rp <?php echo number_format($pendapatan_sparepart, 0, ',', '.'); ?></div>
            </div>
        </td>
    </tr>
</table>

<table style="margin-top:20px;">
    <thead>
        <tr>
            <th colspan="5" class="section-header">TOP 10 PRODUK/JASA TERLARIS</th>
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
            <td colspan="5" class="text-center" style="color:#999;">Tidak ada data</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<table style="margin-top:15px;">
    <thead>
        <tr>
            <th colspan="3" class="section-header">REKAP METODE PEMBAYARAN</th>
        </tr>
        <tr>
            <th width="40%">Metode</th>
            <th width="30%">Jumlah</th>
            <th width="30%">Total Nominal</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if(num_rows($rekap_metode) > 0):
            while($row = fetch_assoc($rekap_metode)): 
        ?>
        <tr>
            <td class="font-bold"><?php echo strtoupper($row['metode_pembayaran'] ?: 'TUNAI'); ?></td>
            <td class="text-center"><?php echo $row['jumlah']; ?> transaksi</td>
            <td class="text-right currency">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
        </tr>
        <?php 
            endwhile;
        else:
        ?>
        <tr>
            <td colspan="3" class="text-center" style="color:#999;">Tidak ada data</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<table style="margin-top:15px;">
    <thead>
        <tr>
            <th colspan="3" class="section-header">RINCIAN HARIAN</th>
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
            <td colspan="3" class="text-center" style="color:#999;">Tidak ada data</td>
        </tr>
        <?php endif; ?>
        <tr class="total-row">
            <td class="font-bold">GRAND TOTAL</td>
            <td class="text-center font-bold"><?php echo $total_transaksi; ?> Transaksi</td>
            <td class="text-right font-bold currency" style="color:#059669;">Rp <?php echo number_format($total_harian, 0, ',', '.'); ?></td>
        </tr>
    </tbody>
</table>

<div class="footer">
    Dicetak pada: <?php echo date('d/m/Y H:i'); ?> WIB | <?php echo $nama_bengkel; ?> - Laporan Keuangan
</div>
</body>
</html>
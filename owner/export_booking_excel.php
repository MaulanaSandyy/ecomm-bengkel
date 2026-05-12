<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../auth/login.php");
    exit();
}

include '../includes/koneksi.php';

$where = "WHERE 1=1";

if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $status = escape_string($_GET['filter_status']);
    $where .= " AND b.status = '$status'";
}

if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $start_date = escape_string($_GET['start_date']);
    $where .= " AND DATE(b.tanggal_booking) >= '$start_date'";
}

if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = escape_string($_GET['end_date']);
    $where .= " AND DATE(b.tanggal_booking) <= '$end_date'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = escape_string($_GET['search']);
    $where .= " AND (u.nama_lengkap LIKE '%$search%' OR u.no_hp LIKE '%$search%' OR j.nama_jasa LIKE '%$search%')";
}

$bookings = query("SELECT b.*, 
                   u.nama_lengkap as customer_name, 
                   u.no_hp, 
                   u.email,
                   j.nama_jasa, 
                   j.harga as jasa_harga,
                   s.status as service_status, 
                   s.catatan_service,
                   s.biaya_tambahan,
                   p.nama_lengkap as pegawai_name
                   FROM booking b 
                   JOIN users u ON b.user_id = u.id 
                   LEFT JOIN jasa j ON b.jasa_id = j.id 
                   LEFT JOIN service s ON b.id = s.booking_id
                   LEFT JOIN users p ON s.pegawai_id = p.id
                   $where
                   ORDER BY b.tanggal_booking DESC, b.jam_booking DESC");

$profil = fetch_assoc(query("SELECT * FROM profil_bengkel WHERE id = 1"));
$nama_bengkel = $profil ? $profil['nama_bengkel'] : 'Bengkel Mobil Jaya Abadi';
$alamat_bengkel = $profil ? $profil['alamat'] : 'Jl. Bengkel No. 1';
$telp_bengkel = $profil ? $profil['no_telp'] : '021-123456';

// Filter info
$filter_text = "Semua Data";
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $filter_text = date('d/m/Y', strtotime($_GET['start_date'])) . " - " . date('d/m/Y', strtotime($_GET['end_date']));
}
if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $status_labels = ['pending' => 'Pending', 'dikonfirmasi' => 'Dikonfirmasi', 'selesai' => 'Selesai', 'batal' => 'Batal'];
    $filter_text .= " | Status: " . ($status_labels[$_GET['filter_status']] ?? strtoupper($_GET['filter_status']));
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Laporan_Booking_' . date('Ymd_His') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<title>Laporan Booking - <?php echo $nama_bengkel; ?></title>
<style>
    .header-title { font-size: 18px; font-weight: bold; color: #1e3a8a; text-align: center; }
    .header-info { font-size: 11px; color: #666; text-align: center; margin-bottom: 10px; }
    .filter-info { font-size: 11px; color: #888; margin-bottom: 15px; font-style: italic; }
    table { border-collapse: collapse; width: 100%; font-size: 11px; }
    th { background: #1e3a8a; color: white; padding: 10px 8px; text-align: center; font-weight: bold; border: 1px solid #1e3a8a; }
    td { padding: 8px; border: 1px solid #ddd; vertical-align: middle; }
    tr:nth-child(even) { background: #f8f9fa; }
    tr:hover { background: #e9ecef; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .status-pending { color: #d97706; font-weight: 600; }
    .status-dikonfirmasi { color: #2563eb; font-weight: 600; }
    .status-selesai { color: #059669; font-weight: 600; }
    .status-batal { color: #dc2626; font-weight: 600; }
    .badge { padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
    .badge-pending { background: #fef3c7; color: #d97706; }
    .badge-dikonfirmasi { background: #dbeafe; color: #2563eb; }
    .badge-selesai { background: #d1fae5; color: #059669; }
    .badge-batal { background: #fee2e2; color: #dc2626; }
    .badge-antri { background: #fed7aa; color: #c2410c; }
    .badge-dikerjakan { background: #c7d2fe; color: #4338ca; }
    .footer { font-size: 9px; color: #999; text-align: center; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
    .total-row { background: #e8f5e9 !important; font-weight: bold; }
    .currency { mso-number-format:\#\,\#\#0; }
</style>
</head>
<body>
<div class="header-title"><?php echo $nama_bengkel; ?></div>
<div class="header-info"><?php echo $alamat_bengkel; ?> | Telp: <?php echo $telp_bengkel; ?></div>
<div class="header-info" style="font-weight:bold; font-size:14px;">LAPORAN DATA BOOKING & SERVICE</div>
<div class="filter-info">Filter: <?php echo $filter_text; ?></div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Kode Booking</th>
            <th>Nama Customer</th>
            <th>No. HP</th>
            <th>Jasa Service</th>
            <th>Harga Jasa</th>
            <th>Status Booking</th>
            <th>Status Service</th>
            <th>Mekanik</th>
            <th>Biaya Tambahan</th>
            <th>Keluhan</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $total_jasa = 0;
        $total_biaya_tambahan = 0;
        $total_keseluruhan = 0;
        
        if(num_rows($bookings) > 0):
        while($row = fetch_assoc($bookings)):
            $biaya_tambahan = $row['biaya_tambahan'] ?? 0;
            $subtotal = $row['jasa_harga'] + $biaya_tambahan;
            
            if($row['status'] == 'selesai') {
                $total_jasa += $row['jasa_harga'];
                $total_biaya_tambahan += $biaya_tambahan;
                $total_keseluruhan += $subtotal;
            }
        ?>
        <tr>
            <td class="text-center"><?php echo $no++; ?></td>
            <td class="text-center"><?php echo date('d/m/Y', strtotime($row['tanggal_booking'])); ?></td>
            <td class="text-center"><?php echo $row['jam_booking']; ?></td>
            <td class="text-center font-bold"><?php echo '#BKG-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
            <td><?php echo $row['customer_name']; ?></td>
            <td><?php echo $row['no_hp']; ?></td>
            <td><?php echo $row['nama_jasa'] ?: '<em>Konsultasi</em>'; ?></td>
            <td class="text-right currency"><?php echo number_format($row['jasa_harga'], 0, ',', '.'); ?></td>
            <td class="text-center">
                <span class="badge badge-<?php echo $row['status']; ?>">
                    <?php echo strtoupper($row['status']); ?>
                </span>
            </td>
            <td class="text-center">
                <?php if($row['service_status']): ?>
                <span class="badge badge-<?php echo $row['service_status']; ?>">
                    <?php echo strtoupper($row['service_status']); ?>
                </span>
                <?php else: ?>
                <span style="color:#999;">-</span>
                <?php endif; ?>
            </td>
            <td><?php echo $row['pegawai_name'] ?: '-'; ?></td>
            <td class="text-right currency"><?php echo number_format($biaya_tambahan, 0, ',', '.'); ?></td>
            <td><?php echo substr($row['keluhan'], 0, 100); ?><?php echo strlen($row['keluhan']) > 100 ? '...' : ''; ?></td>
        </tr>
        <?php 
        endwhile;
        else:
        ?>
        <tr>
            <td colspan="13" class="text-center" style="color:#999; padding:30px;">Tidak ada data booking ditemukan</td>
        </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="7" class="text-right font-bold">TOTAL PENDAPATAN (Booking Selesai):</td>
            <td class="text-right font-bold currency" style="color:#059669;"><?php echo number_format($total_jasa, 0, ',', '.'); ?></td>
            <td colspan="4"></td>
            <td class="text-right font-bold currency" style="color:#059669;"><?php echo number_format($total_biaya_tambahan, 0, ',', '.'); ?></td>
            <td></td>
        </tr>
        <tr class="total-row">
            <td colspan="11" class="text-right font-bold" style="font-size:12px;">GRAND TOTAL:</td>
            <td class="text-right font-bold currency" style="color:#059669; font-size:12px;"><?php echo number_format($total_keseluruhan, 0, ',', '.'); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    Dicetak pada: <?php echo date('d/m/Y H:i'); ?> WIB | <?php echo $nama_bengkel; ?> - Laporan Booking & Service
</div>
</body>
</html>
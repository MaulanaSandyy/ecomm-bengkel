<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(2);

// Filter params
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
                   j.nama_jasa, 
                   j.harga as jasa_harga,
                   s.status as service_status, 
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

$filter_info = "";
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $filter_info = "Periode: " . date('d/m/Y', strtotime($_GET['start_date'])) . " - " . date('d/m/Y', strtotime($_GET['end_date']));
}
if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $filter_info .= ($filter_info ? " | " : "") . "Status: " . strtoupper($_GET['filter_status']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Booking - <?php echo $nama_bengkel; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-size: 11px; }
        body { padding: 15px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .header-report { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header-report h4 { margin-bottom: 5px; }
        .header-report p { margin: 0; font-size: 10px; }
        .info-filter { background: #f5f5f5; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: middle; }
        th { background: #4f46e5; color: white; text-align: center; font-weight: 600; }
        td.center { text-align: center; }
        .status-badge { padding: 3px 8px; border-radius: 50px; font-size: 9px; font-weight: 600; display: inline-block; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-dikonfirmasi { background: #dbeafe; color: #2563eb; }
        .status-selesai { background: #d1fae5; color: #059669; }
        .status-batal { background: #fee2e2; color: #dc2626; }
        .service-antri { background: #fed7aa; color: #c2410c; }
        .service-dikerjakan { background: #c7d2fe; color: #4338ca; }
        .service-selesai { background: #d1fae5; color: #059669; }
        .footer-report { border-top: 1px solid #ccc; margin-top: 30px; padding-top: 10px; text-align: center; font-size: 9px; color: #666; }
        .no-print { margin-top: 20px; text-align: center; }
        @page { size: landscape; margin: 10mm; }
    </style>
</head>
<body>
    <div class="header-report">
        <h4 class="fw-bold mb-1"><i class="fas fa-car-side me-2"></i><?php echo $nama_bengkel; ?></h4>
        <p class="mb-1"><?php echo $alamat_bengkel; ?> | Telp: <?php echo $telp_bengkel; ?></p>
        <h5 class="fw-bold mt-2">LAPORAN DATA BOOKING & SERVICE</h5>
        <?php if($filter_info): ?>
        <p class="mb-0 mt-2"><em><?php echo $filter_info; ?></em></p>
        <?php endif; ?>
    </div>

    <?php if(num_rows($bookings) > 0): ?>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="8%">Tgl Booking</th>
                <th width="6%">Jam</th>
                <th width="12%">Customer</th>
                <th width="10%">No. HP</th>
                <th width="14%">Jasa Service</th>
                <th width="7%">Status</th>
                <th width="7%">Service</th>
                <th width="10%">Mekanik</th>
                <th width="8%">Biaya</th>
                <th width="14%">Keluhan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_pendapatan = 0;
            while($row = fetch_assoc($bookings)): 
                $biaya = $row['jasa_harga'] + ($row['biaya_tambahan'] ?? 0);
                if($row['status'] == 'selesai') $total_pendapatan += $biaya;
            ?>
            <tr>
                <td class="center"><?php echo $no++; ?></td>
                <td class="center"><?php echo date('d/m/Y', strtotime($row['tanggal_booking'])); ?></td>
                <td class="center"><?php echo $row['jam_booking']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['no_hp']; ?></td>
                <td><?php echo $row['nama_jasa'] ?: '<em>Konsultasi</em>'; ?></td>
                <td class="center">
                    <span class="status-badge status-<?php echo $row['status']; ?>">
                        <?php echo strtoupper($row['status']); ?>
                    </span>
                </td>
                <td class="center">
                    <?php if($row['service_status']): ?>
                    <span class="status-badge service-<?php echo $row['service_status']; ?>">
                        <?php echo strtoupper($row['service_status']); ?>
                    </span>
                    <?php else: ?>
                    <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $row['pegawai_name'] ?: '-'; ?></td>
                <td class="center">Rp <?php echo number_format($biaya, 0, ',', '.'); ?></td>
                <td><?php echo substr($row['keluhan'], 0, 50); ?><?php echo strlen($row['keluhan']) > 50 ? '...' : ''; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text-end fw-bold">Total Pendapatan (Booking Selesai):</td>
                <td class="fw-bold text-success">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <?php else: ?>
    <div class="text-center py-5">
        <p class="text-muted">Tidak ada data booking pada periode ini.</p>
    </div>
    <?php endif; ?>

    <div class="footer-report">
        <p class="mb-0">Dicetak pada: <?php echo date('d/m/Y H:i'); ?> WIB</p>
        <p class="mb-0"><?php echo $nama_bengkel; ?> - Laporan Booking & Service</p>
    </div>

    <div class="no-print text-center">
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</button>
        <button class="btn btn-secondary" onclick="window.close()"><i class="fas fa-times me-2"></i>Tutup</button>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../auth/login.php");
    exit();
}

include '../includes/koneksi.php';

if (!isset($_GET['id'])) {
    exit('ID tidak ditemukan');
}

$id = $_GET['id'];

$booking = fetch_assoc(query("SELECT b.*, 
                           u.nama_lengkap as customer_name, 
                           u.no_hp, 
                           u.email, 
                           u.alamat,
                           j.nama_jasa, 
                           j.harga as jasa_harga,
                           j.estimasi_waktu,
                           s.id as service_id,
                           s.status as service_status, 
                           s.catatan_service, 
                           s.biaya_tambahan,
                           s.tanggal_selesai,
                           p.nama_lengkap as pegawai_name
                           FROM booking b 
                           JOIN users u ON b.user_id = u.id 
                           LEFT JOIN jasa j ON b.jasa_id = j.id 
                           LEFT JOIN service s ON b.id = s.booking_id
                           LEFT JOIN users p ON s.pegawai_id = p.id
                           WHERE b.id = $id"));

$profil = fetch_assoc(query("SELECT * FROM profil_bengkel WHERE id = 1"));
$nama_bengkel = $profil ? $profil['nama_bengkel'] : 'Bengkel Mobil Jaya Abadi';
$alamat_bengkel = $profil ? $profil['alamat'] : 'Jl. Bengkel No. 1';
$telp_bengkel = $profil ? $profil['no_telp'] : '021-123456';
?>

<style>
.header-report { border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
.detail-section { background: #f9fafb; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
.detail-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 10px; font-weight: 600; }
.status-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
.status-pending { background: #fef3c7; color: #d97706; }
.status-dikonfirmasi { background: #dbeafe; color: #2563eb; }
.status-selesai { background: #d1fae5; color: #059669; }
.status-batal { background: #fee2e2; color: #dc2626; }
.service-status-antri { background: #fed7aa; color: #c2410c; }
.service-status-dikerjakan { background: #c7d2fe; color: #4338ca; }
.service-status-selesai { background: #d1fae5; color: #059669; }
.footer-report { border-top: 1px solid #ccc; margin-top: 30px; padding-top: 15px; text-align: center; font-size: 0.85rem; color: #666; }
.no-print { display: none; }
</style>

<div class="container-fluid">
    <div class="header-report">
        <div class="row">
            <div class="col-6">
                <h4 class="fw-bold text-primary mb-1"><i class="fas fa-car-side me-2"></i><?php echo $nama_bengkel; ?></h4>
                <p class="small text-muted mb-0"><?php echo $alamat_bengkel; ?></p>
                <p class="small text-muted mb-0"><i class="fas fa-phone-alt me-1"></i> <?php echo $telp_bengkel; ?></p>
            </div>
            <div class="col-6 text-end">
                <h5 class="fw-bold">BOOKING SERVICE</h5>
                <p class="mb-0">#BKG-<?php echo str_pad($id, 5, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="detail-section">
                <div class="detail-label">Informasi Booking</div>
                <table class="table table-sm mb-0">
                    <tr>
                        <td width="130"><strong>Tanggal Booking</strong></td>
                        <td>: <?php echo date('d/m/Y', strtotime($booking['tanggal_booking'])); ?> (<?php echo date('l', strtotime($booking['tanggal_booking'])); ?>)</td>
                    </tr>
                    <tr>
                        <td><strong>Jam Booking</strong></td>
                        <td>: <?php echo $booking['jam_booking']; ?> WIB</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>: 
                            <?php
                            $status_text = [
                                'pending' => 'Menunggu Konfirmasi',
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'selesai' => 'Selesai',
                                'batal' => 'Dibatalkan'
                            ];
                            $status_class = [
                                'pending' => 'status-pending',
                                'dikonfirmasi' => 'status-dikonfirmasi',
                                'selesai' => 'status-selesai',
                                'batal' => 'status-batal'
                            ];
                            ?>
                            <span class="status-badge <?php echo $status_class[$booking['status']]; ?>">
                                <?php echo $status_text[$booking['status']]; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="detail-section">
                <div class="detail-label">Informasi Customer</div>
                <table class="table table-sm mb-0">
                    <tr>
                        <td width="130"><strong>Nama</strong></td>
                        <td>: <?php echo $booking['customer_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>No. HP</strong></td>
                        <td>: <?php echo $booking['no_hp']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>: <?php echo $booking['alamat']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="detail-section">
                <div class="detail-label">Detail Service</div>
                <table class="table table-sm mb-0">
                    <?php if($booking['nama_jasa']): ?>
                    <tr>
                        <td width="130"><strong>Jasa Service</strong></td>
                        <td>: <?php echo $booking['nama_jasa']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Estimasi Waktu</strong></td>
                        <td>: <?php echo $booking['estimasi_waktu']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Harga</strong></td>
                        <td>: Rp <?php echo number_format($booking['jasa_harga'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="2">Konsultasi (Tanpa Jasa)</td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>Keluhan</strong></td>
                        <td>: <?php echo nl2br($booking['keluhan']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="col-md-6">
            <?php if($booking['service_id']): ?>
            <div class="detail-section">
                <div class="detail-label">Informasi Service</div>
                <table class="table table-sm mb-0">
                    <tr>
                        <td width="130"><strong>Status Service</strong></td>
                        <td>: 
                            <?php
                            $service_text = [
                                'antri' => 'Dalam Antrian',
                                'dikerjakan' => 'Sedang Dikerjakan',
                                'selesai' => 'Service Selesai'
                            ];
                            $service_class = [
                                'antri' => 'service-status-antri',
                                'dikerjakan' => 'service-status-dikerjakan',
                                'selesai' => 'service-status-selesai'
                            ];
                            ?>
                            <span class="badge <?php echo $service_class[$booking['service_status']]; ?>">
                                <?php echo $service_text[$booking['service_status']]; ?>
                            </span>
                        </td>
                    </tr>
                    <?php if($booking['pegawai_name']): ?>
                    <tr>
                        <td><strong>Mekanik</strong></td>
                        <td>: <?php echo $booking['pegawai_name']; ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($booking['biaya_tambahan'] > 0): ?>
                    <tr>
                        <td><strong>Biaya Tambahan</strong></td>
                        <td>: Rp <?php echo number_format($booking['biaya_tambahan'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($booking['catatan_service']): ?>
                    <tr>
                        <td><strong>Catatan</strong></td>
                        <td>: <?php echo nl2br($booking['catatan_service']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-report">
        <p class="mb-0">Dicetak pada: <?php echo date('d/m/Y H:i'); ?> WIB</p>
        <p class="mb-0"><?php echo $nama_bengkel; ?> - <?php echo $alamat_bengkel; ?></p>
    </div>
</div>
<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(2);

if (!isset($_GET['id'])) {
    exit('ID tidak ditemukan');
}

$id = $_GET['id'];

// Ambil data booking lengkap
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
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="detail-section">
                <div class="detail-label">Informasi Booking</div>
                <table class="table table-sm">
                    32
                        <td width="120"><strong>Kode Booking</strong>32
                        <td>: #BKG-<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?>32
                    </tr>
                    32
                        <td><strong>Tanggal Booking</strong>32
                        <td>: <?php echo date('d/m/Y', strtotime($booking['tanggal_booking'])); ?> (<?php echo date('l', strtotime($booking['tanggal_booking'])); ?>)32
                    </tr>
                    32
                        <td><strong>Jam Booking</strong>32
                        <td>: <?php echo $booking['jam_booking']; ?> WIB32
                    </tr>
                    32
                        <td><strong>Status Booking</strong>32
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
                        32
                    </tr>
                    <?php if($booking['created_at']): ?>
                    <tr>
                        <td><strong>Dibuat Pada</strong>32
                        <td>: <?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?>32
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="detail-section">
                <div class="detail-label">Informasi Customer</div>
                <table class="table table-sm">
                    32
                        <td width="120"><strong>Nama Lengkap</strong>32
                        <td>: <?php echo $booking['customer_name']; ?>32
                    </tr>
                    32
                        <td><strong>No. HP</strong>32
                        <td>: <?php echo $booking['no_hp']; ?>32
                    </tr>
                    32
                        <td><strong>Email</strong>32
                        <td>: <?php echo $booking['email']; ?>32
                    </tr>
                    32
                        <td><strong>Alamat</strong>32
                        <td>: <?php echo $booking['alamat']; ?>32
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="detail-section">
                <div class="detail-label">Detail Service</div>
                <table class="table table-sm">
                    <?php if($booking['nama_jasa']): ?>
                    <tr>
                        <td width="120"><strong>Jasa Service</strong>32
                        <td>: <?php echo $booking['nama_jasa']; ?>32
                    </tr>
                    <tr>
                        <td><strong>Estimasi Waktu</strong>32
                        <td>: <?php echo $booking['estimasi_waktu']; ?>32
                    </tr>
                    <tr>
                        <td><strong>Harga Jasa</strong>32
                        <td>: Rp <?php echo number_format($booking['jasa_harga'], 0, ',', '.'); ?>32
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="2">Tidak memilih jasa (Konsultasi)32
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>Keluhan</strong>32
                        <td>: <?php echo nl2br($booking['keluhan']); ?>32
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="col-md-6">
            <?php if($booking['service_id']): ?>
            <div class="detail-section">
                <div class="detail-label">Informasi Service</div>
                <table class="table table-sm">
                    <tr>
                        <td width="120"><strong>Status Service</strong>32
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
                        32
                    </tr>
                    <?php if($booking['pegawai_name']): ?>
                    <tr>
                        <td><strong>Mekanik</strong>32
                        <td>: <?php echo $booking['pegawai_name']; ?>32
                    </tr>
                    <?php endif; ?>
                    <?php if($booking['biaya_tambahan'] > 0): ?>
                    <tr>
                        <td><strong>Biaya Tambahan</strong>32
                        <td>: Rp <?php echo number_format($booking['biaya_tambahan'], 0, ',', '.'); ?>32
                    </tr>
                    <?php endif; ?>
                    <?php if($booking['catatan_service']): ?>
                    <tr>
                        <td><strong>Catatan Service</strong>32
                        <td>: <?php echo nl2br($booking['catatan_service']); ?>32
                    </tr>
                    <?php endif; ?>
                    <?php if($booking['tanggal_selesai']): ?>
                    <tr>
                        <td><strong>Tanggal Selesai</strong>32
                        <td>: <?php echo date('d/m/Y', strtotime($booking['tanggal_selesai'])); ?>32
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.detail-section {
    background: #f9fafb;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
}
.detail-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    margin-bottom: 10px;
    font-weight: 600;
}
.status-badge {
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}
.status-pending { background: #fef3c7; color: #d97706; }
.status-dikonfirmasi { background: #dbeafe; color: #2563eb; }
.status-selesai { background: #d1fae5; color: #059669; }
.status-batal { background: #fee2e2; color: #dc2626; }
.service-status-antri { background: #fed7aa; color: #c2410c; }
.service-status-dikerjakan { background: #c7d2fe; color: #4338ca; }
.service-status-selesai { background: #d1fae5; color: #059669; }
</style>
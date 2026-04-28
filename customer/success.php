<?php
include '../includes/koneksi.php';

// ambil kode dari URL
$kode = $_GET['kode'] ?? null;

// kalau kosong, redirect
if (!$kode) {
    header("Location: riwayat.php");
    exit();
}

// ambil data transaksi
$data = fetch_assoc(query("SELECT * FROM transaksi WHERE kode_transaksi='$kode'"));

// kalau data tidak ditemukan
if (!$data) {
    echo "<h3>Transaksi tidak ditemukan</h3>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        
        .success-card {
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="card border-0 shadow-sm rounded-4">
        
        <!-- Header - pakai bg-success sesuai standar Bootstrap -->
        <div class="bg-success text-white p-4 text-center rounded-top-4">
            <div class="mb-3">
                <i class="fas fa-check-circle fa-4x"></i>
            </div>
            <h4 class="fw-bold mb-1">Pembayaran Berhasil!</h4>
            <p class="mb-0 opacity-75">Terima kasih telah melakukan transaksi</p>
        </div>
        
        <div class="card-body p-4">
            
            <!-- Kode Transaksi -->
            <div class="text-center mb-4 pb-2 border-bottom">
                <small class="text-muted d-block mb-1">KODE TRANSAKSI</small>
                <h5 class="fw-bold text-primary mb-0">
                    <i class="fas fa-file-invoice me-2"></i><?php echo $kode; ?>
                </h5>
            </div>
            
            <!-- Status & Metode -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3">
                        <small class="text-muted d-block mb-2">
                            <i class="fas fa-tag me-1"></i> Status Pesanan
                        </small>
                        <?php
                            $status = $data['status'] ?? 'dikemas';

                            $status_color = [
                                'dikemas' => 'warning',
                                'dikirim' => 'primary',
                                'selesai' => 'success'
                            ];

                            $status_icon = [
                                'dikemas' => 'fa-box',
                                'dikirim' => 'fa-truck',
                                'selesai' => 'fa-check-circle'
                            ];

                            $warna = $status_color[$status] ?? 'secondary';
                            $icon = $status_icon[$status] ?? 'fa-question-circle';
                            ?>
                        <span class="badge bg-<?php echo $warna; ?> bg-opacity-10 text-<?php echo $warna; ?> rounded-pill px-3 py-2">
                        <i class="fas <?php echo $icon; ?> me-1"></i>
                        <?php echo ucfirst($status); ?>
                        </span> 
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3">
                        <small class="text-muted d-block mb-2">
                            <i class="fas fa-wallet me-1"></i> Metode Bayar
                        </small>
                        <span class="fw-medium">
                            <i class="fas fa-qrcode me-2 text-secondary"></i>
                            <?php echo $data['metode_pembayaran'] ?: 'QRIS'; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Total -->
            <div class="bg-success bg-opacity-10 rounded-3 p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Pembayaran</small>
                        <h4 class="fw-bold text-success mb-0">Rp <?php echo number_format($data['total_harga'], 0, ',', '.'); ?></h4>
                    </div>
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
            
            <!-- Info -->
            <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-3 mb-4 py-2">
                <small class="text-secondary">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Simpan kode transaksi ini untuk keperluan informasi lebih lanjut.
                </small>
            </div>
            
            <!-- Tombol -->
            <div class="d-flex gap-2">
                <a href="riwayat.php" class="btn btn-primary rounded-pill flex-fill py-2">
                    <i class="fas fa-history me-2"></i>Riwayat
                </a>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill flex-fill py-2">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top-0 text-center p-3">
            <small class="text-muted">
                <i class="far fa-calendar-alt me-1"></i>
                <?php echo date('d F Y H:i', strtotime($data['created_at'])); ?>
            </small>
        </div>
    </div>
</div>

</body>
</html>
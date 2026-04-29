<?php
include '../includes/koneksi.php';

// ambil kode dari URL
$kode = $_GET['kode'] ?? null;

// kalau kosong, redirect
if (!$kode) {
    header("Location: riwayat.php");
    exit();
}

// ambil data transaksi (update status jika perlu)
$data = fetch_assoc(query("SELECT * FROM transaksi WHERE kode_transaksi='$kode'"));

// Jika status masih pending, coba cek ke Xendit atau update manual
if ($data && $data['status'] == 'pending') {
    // Update status menjadi lunas karena sukses redirect
    query("UPDATE transaksi SET status = 'lunas' WHERE kode_transaksi='$kode'");
    $data['status'] = 'lunas';
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .checkmark-circle {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        
        <!-- Header - pakai bg-success -->
        <div class="bg-success text-white p-4 text-center">
            <div class="checkmark-circle">
                <i class="fas fa-check fa-3x text-white"></i>
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
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i> LUNAS
                        </span>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="bg-light rounded-3 p-3">
                        <small class="text-muted d-block mb-2">
                            <i class="fas fa-wallet me-1"></i> Metode Bayar
                        </small>
                        <span class="fw-medium">
                            <i class="fas fa-credit-card me-2 text-secondary"></i>
                            <?php echo $data['metode_pembayaran'] ?: 'Virtual Account'; ?>
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
                    <i class="fas fa-history me-2"></i>Lihat Riwayat
                </a>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill flex-fill py-2">
                    <i class="fas fa-home me-2"></i>Ke Dashboard
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
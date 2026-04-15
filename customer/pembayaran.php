<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

// Ambil data transaksi
if (!isset($_GET['transaksi_id'])) {
    header("Location: riwayat.php");
    exit();
}

$transaksi_id = $_GET['transaksi_id'];
$user_id = $_SESSION['user_id'];

// Cek transaksi
$transaksi = query("SELECT t.*, u.nama_lengkap FROM transaksi t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.id = $transaksi_id AND t.user_id = $user_id");
if (num_rows($transaksi) == 0) {
    header("Location: riwayat.php");
    exit();
}

$data_transaksi = fetch_assoc($transaksi);

// Ambil detail transaksi
$detail = query("SELECT d.*, 
                 CASE 
                    WHEN d.item_type = 'jasa' THEN (SELECT nama_jasa FROM jasa WHERE id = d.item_id)
                    ELSE (SELECT nama_sparepart FROM sparepart WHERE id = d.item_id)
                 END as nama_item
                 FROM detail_transaksi d 
                 WHERE d.transaksi_id = $transaksi_id");

// Ambil QRIS
$qris = query("SELECT * FROM qris WHERE id = 1");
$data_qris = num_rows($qris) > 0 ? fetch_assoc($qris) : null;

// Handle Payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bayar'])) {
    // Simulasi pembayaran
    $metode = $_POST['metode'];
    
    // Insert ke payment
    query("INSERT INTO payment (user_id, transaksi_id, metode, status) 
           VALUES ($user_id, $transaksi_id, '$metode', 'pending')");
    
    $payment_id = mysqli_insert_id($conn);
    
    echo json_encode(['status' => 'success', 'payment_id' => $payment_id]);
    exit();
}

// Handle Confirm Payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    $payment_id = $_POST['payment_id'];
    
    // Update status payment dan transaksi
    query("UPDATE payment SET status = 'sukses' WHERE id = $payment_id");
    query("UPDATE transaksi SET status = 'lunas' WHERE id = $transaksi_id");
    
    echo json_encode(['status' => 'success']);
    exit();
}

$title = "Pembayaran";
include '../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" data-aos="fade-up">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Pembayaran</h4>
                </div>
                <div class="card-body">
                    <!-- Info Transaksi -->
                    <div class="alert alert-info">
                        <h5>Detail Transaksi #<?php echo $data_transaksi['kode_transaksi']; ?></h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Customer</td>
                                <td>: <?php echo $data_transaksi['nama_lengkap']; ?></td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td>: <strong>Rp <?php echo number_format($data_transaksi['total_harga'], 0, ',', '.'); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- List Item -->
                    <h5 class="mb-3">Item yang dibeli:</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = fetch_assoc($detail)): ?>
                                <tr>
                                    <td><?php echo $item['nama_item']; ?></td>
                                    <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $item['jumlah']; ?></td>
                                    <td>Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pilihan Pembayaran -->
                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-lg" onclick="bayarXendit()">
                            <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                        </button>
                    </div>
                    
                    <!-- <?php if ($data_qris): ?>
                    <div class="card mb-3 border-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="../uploads/qris/<?php echo $data_qris['gambar']; ?>" 
                                         alt="QRIS" class="img-fluid" style="max-width: 200px;">
                                </div>
                                <div class="col-md-8">
                                    <h5>QRIS Pembayaran</h5>
                                    <p><strong>Bank:</strong> <?php echo $data_qris['nama_bank']; ?></p>
                                    <p><strong>Atas Nama:</strong> <?php echo $data_qris['atas_nama']; ?></p>
                                    <p><strong>Total:</strong> Rp <?php echo number_format($data_transaksi['total_harga'], 0, ',', '.'); ?></p>
                                    
                                    <button class="btn btn-primary btn-lg" onclick="prosesPembayaran('QRIS')">
                                        <i class="fas fa-qrcode me-2"></i>Bayar dengan QRIS
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?> -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Loading -->
<div class="modal fade" id="paymentModal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="loading-spinner" id="modalSpinner" style="display: block; position: relative; background: transparent;">
                    <div class="spinner-content" style="background: transparent;">
                        <div class="spinner-border text-primary" style="width: 5rem; height: 5rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h4 class="mt-4" id="paymentStatus">Memproses pembayaran...</h4>
                        <p class="text-muted" id="paymentDetail">Mohon tunggu sebentar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let paymentId = null;

function prosesPembayaran(metode) {
    // Tampilkan modal loading
    var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
    
    // Kirim request pembayaran
    fetch('pembayaran.php?transaksi_id=<?php echo $transaksi_id; ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'bayar=1&metode=' + metode
    })
    .then(response => response.json())
    .then(data => {
        if (data.status == 'success') {
            paymentId = data.payment_id;
            
            // Animasi loading 2 detik
            setTimeout(() => {
                document.getElementById('paymentStatus').innerHTML = 'Memverifikasi pembayaran...';
                document.getElementById('paymentDetail').innerHTML = 'Mohon tunggu sebentar';
                
                setTimeout(() => {
                    konfirmasiPembayaran();
                }, 2000);
            }, 2000);
        }
    });
}

function konfirmasiPembayaran() {
    fetch('pembayaran.php?transaksi_id=<?php echo $transaksi_id; ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'confirm=1&payment_id=' + paymentId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status == 'success') {
            // Update status sukses
            document.getElementById('paymentStatus').innerHTML = '✓ Pembayaran Berhasil!';
            document.getElementById('paymentDetail').innerHTML = 'Terima kasih, pembayaran Anda telah dikonfirmasi.';
            
            // Redirect ke halaman sukses setelah 2 detik
            setTimeout(() => {
                window.location.href = 'riwayat.php';
            }, 2000);
        }
    });
}

function bayarXendit() {
    window.location.href = 'xendit_create.php?transaksi_id=<?php echo $transaksi_id; ?>';
}

</script>

<?php include '../includes/footer.php'; ?>
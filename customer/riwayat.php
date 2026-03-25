<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

$title = "Riwayat Transaksi";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

$transaksi = query("
SELECT * FROM transaksi
WHERE user_id = $user_id
ORDER BY created_at DESC
");
?>

<div class="container-fluid px-0 px-lg-4" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">

        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-circle me-2"></i>Menu Pelanggan
                </h5>

                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="beli.php"><i class="fas fa-shopping-bag"></i>Beli Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-check"></i>Booking Service</a>
                <a href="checkout.php"><i class="fas fa-shopping-cart"></i>Keranjang / Checkout</a>
                <a href="riwayat.php" class="active"><i class="fas fa-receipt"></i>Riwayat Transaksi</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">

            <h3 class="fw-bold text-dark mb-4">Riwayat Transaksi Anda</h3>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-history"></i>
                    </div>
                    <h5 class="mb-0 fw-bold text-dark">Data Pemesanan & Servis</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Kode Invoice</th>
                                    <th class="py-3">Waktu Pemesanan</th>
                                    <th class="py-3">Metode Bayar</th>
                                    <th class="py-3 text-end">Total Biaya</th>
                                    <th class="py-3 text-center pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">

                                <?php if(num_rows($transaksi) > 0): ?>
                                    <?php while($row = fetch_assoc($transaksi)): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-bold text-primary d-block mb-1">
                                                <i class="fas fa-file-invoice me-1"></i><?php echo $row['kode_transaksi']; ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex flex-column text-muted small">
                                                <span class="text-dark fw-medium"><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                                                <span><?php echo date('H:i', strtotime($row['created_at'])); ?> WIB</span>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-normal">
                                                <i class="fas fa-wallet me-1"></i> <?php echo $row['metode_pembayaran'] ?: 'QRIS'; ?>
                                            </span>
                                        </td>
                                        
                                        <td class="text-end fw-bold text-dark">
                                            Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?>
                                        </td>
                                        
                                        <td class="text-center pe-4">
                                            <?php
                                            $status_color = ['pending' => 'warning', 'lunas' => 'success', 'batal' => 'danger'];
                                            $status_icon = ['pending' => 'fa-clock', 'lunas' => 'fa-check-circle', 'batal' => 'fa-times-circle'];
                                            $warna = $status_color[$row['status']];
                                            $icon = $status_icon[$row['status']];
                                            ?>
                                            <span class="badge bg-<?php echo $warna; ?> bg-opacity-10 text-<?php echo $warna; ?> rounded-pill px-3 py-2 text-uppercase small w-100">
                                                <i class="fas <?php echo $icon; ?> me-1"></i> <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 120px; opacity: 0.5;" class="mb-3">
                                            <h5 class="text-dark fw-bold mb-1">Belum Ada Transaksi</h5>
                                            <p class="text-muted small mb-3">Wah, sepertinya Anda belum pernah melakukan pesanan atau booking.</p>
                                            <a href="beli.php" class="btn btn-primary rounded-pill px-4"><i class="fas fa-shopping-bag me-2"></i>Mulai Belanja</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
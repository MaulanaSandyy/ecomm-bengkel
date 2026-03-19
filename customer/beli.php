<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

$title = "Beli Sparepart";
include '../includes/header.php';

// Fitur pencarian sederhana untuk customer (Opsional tapi berguna)
$search = isset($_GET['q']) ? escape_string($_GET['q']) : '';
$where = $search ? "WHERE nama_sparepart LIKE '%$search%' OR merek LIKE '%$search%'" : "";
$sparepart = query("SELECT * FROM sparepart $where ORDER BY id DESC");
?>

<div class="container-fluid px-0 px-lg-4" style="margin-top: -20px;">
    <div class="row g-0 g-lg-4">

        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-circle me-2"></i>Menu Pelanggan
                </h5>

                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="beli.php" class="active"><i class="fas fa-shopping-bag"></i>Beli Sparepart</a>
                <a href="booking.php"><i class="fas fa-calendar-check"></i>Booking Service</a>
                <a href="checkout.php"><i class="fas fa-shopping-cart"></i>Keranjang / Checkout</a>
                <a href="pembayaran.php"><i class="fas fa-wallet"></i>Pembayaran</a>
                <a href="riwayat.php"><i class="fas fa-receipt"></i>Riwayat Transaksi</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Katalog Sparepart</h3>
                    <p class="text-muted small mb-0">Temukan suku cadang berkualitas untuk kendaraan Anda.</p>
                </div>
                
                <div class="mt-3 mt-md-0">
                    <form action="" method="GET" class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control bg-white border-0 shadow-sm rounded-start-pill ps-4" name="q" placeholder="Cari nama atau merek..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary border-0 shadow-sm rounded-end-pill px-3" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                <?php while($row = fetch_assoc($sparepart)): ?>
                <div class="col" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card transition-all">
                        
                        <div class="position-relative bg-light" style="padding-top: 75%;"> <?php if ($row['gambar'] && file_exists("../uploads/sparepart/" . $row['gambar'])): ?>
                                <img src="../uploads/sparepart/<?php echo $row['gambar']; ?>" 
                                     class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" alt="<?php echo $row['nama_sparepart']; ?>">
                            <?php else: ?>
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="fas fa-box-open fa-3x mb-2 opacity-50"></i>
                                    <small>No Image</small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 rounded-pill fw-normal px-2 py-1"><i class="fas fa-tag me-1"></i><?php echo $row['merek']; ?></span>
                            </div>
                        </div>

                        <div class="card-body p-3 d-flex flex-column">
                            <h6 class="card-title fw-bold text-dark text-truncate mb-1" title="<?php echo $row['nama_sparepart']; ?>">
                                <?php echo $row['nama_sparepart']; ?>
                            </h6>
                            
                            <p class="card-text text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.8rem;">
                                <?php echo $row['deskripsi']; ?>
                            </p>

                            <div class="mt-auto">
                                <h5 class="text-primary fw-bold mb-2">Rp <?php echo number_format($row['harga'],0,',','.'); ?></h5>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <?php if ($row['stok'] > 5): ?>
                                        <span class="small text-success fw-medium"><i class="fas fa-check-circle me-1"></i>Stok <?php echo $row['stok']; ?></span>
                                    <?php elseif ($row['stok'] > 0): ?>
                                        <span class="small text-warning fw-medium"><i class="fas fa-exclamation-circle me-1"></i>Sisa <?php echo $row['stok']; ?></span>
                                    <?php else: ?>
                                        <span class="small text-danger fw-medium"><i class="fas fa-times-circle me-1"></i>Habis</span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($row['stok'] > 0): ?>
                                    <a href="checkout.php?id=<?php echo $row['id']; ?>" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm" style="font-size: 0.9rem;">
                                        <i class="fas fa-cart-plus me-2"></i>Beli Sekarang
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 rounded-pill fw-bold shadow-sm" style="font-size: 0.9rem;" disabled>
                                        <i class="fas fa-ban me-2"></i>Stok Habis
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

                <?php if(num_rows($sparepart) == 0): ?>
                    <div class="col-12 text-center py-5" data-aos="fade-in">
                        <i class="fas fa-search-minus fa-4x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-muted fw-bold">Sparepart Tidak Ditemukan</h5>
                        <p class="text-muted small">Coba gunakan kata kunci pencarian yang lain.</p>
                        <a href="beli.php" class="btn btn-outline-primary rounded-pill mt-2">Tampilkan Semua</a>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<style>
/* Styling khusus card produk */
.object-fit-cover { object-fit: cover; }
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    border-color: var(--primary-color)!important;
}
.transition-all { transition: all 0.3s ease; }
</style>

<?php include '../includes/footer.php'; ?>
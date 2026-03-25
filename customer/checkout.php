<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4); // Hanya customer

$user_id = $_SESSION['user_id'];

// Handle Add to Cart from URL (untuk tombol "Beli" di beli.php)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sparepart = fetch_assoc(query("SELECT * FROM sparepart WHERE id = $id"));
    
    if ($sparepart && $sparepart['stok'] > 0) {
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add to cart
        if (isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['qty'] + 1 <= $sparepart['stok']) {
                $_SESSION['cart'][$id]['qty']++;
                $_SESSION['success'] = "Jumlah sparepart ditambah!";
            } else {
                $_SESSION['error'] = "Stok tidak mencukupi!";
            }
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $sparepart['id'],
                'nama' => $sparepart['nama_sparepart'],
                'harga' => $sparepart['harga'],
                'merek' => $sparepart['merek'],
                'gambar' => $sparepart['gambar'],
                'stok' => $sparepart['stok'],
                'qty' => 1
            ];
            $_SESSION['success'] = "Sparepart berhasil ditambahkan ke keranjang!";
        }
    } else {
        $_SESSION['error'] = "Stok sparepart habis!";
    }
    header("Location: checkout.php");
    exit();
}

// Handle Clear Cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    $_SESSION['success'] = "Keranjang berhasil dikosongkan!";
    header("Location: beli.php");
    exit();
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        $_SESSION['success'] = "Item berhasil dihapus dari keranjang!";
    }
    header("Location: checkout.php");
    exit();
}

// Handle Update Quantity
if (isset($_POST['update_qty'])) {
    $id = $_POST['id'];
    $qty = $_POST['qty'];
    if (isset($_SESSION['cart'][$id]) && $qty > 0) {
        // Cek stok
        $sparepart = fetch_assoc(query("SELECT stok FROM sparepart WHERE id = $id"));
        if ($qty <= $sparepart['stok']) {
            $_SESSION['cart'][$id]['qty'] = $qty;
            $_SESSION['success'] = "Jumlah berhasil diupdate!";
        } else {
            $_SESSION['error'] = "Stok tidak mencukupi! Maksimal " . $sparepart['stok'];
        }
    }
    header("Location: checkout.php");
    exit();
}

// Handle Checkout (Proses Pembayaran)
if (isset($_POST['checkout'])) {
    if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
        $_SESSION['error'] = "Keranjang belanja kosong!";
        header("Location: beli.php");
        exit();
    }
    
    // Hitung total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['harga'] * $item['qty'];
    }
    
    // Generate kode transaksi unik
    $kode_transaksi = "INV-" . date('Ymd') . "-" . strtoupper(substr(uniqid(), -6));
    
    // Insert ke tabel transaksi
    $query = "INSERT INTO transaksi (kode_transaksi, user_id, total_harga, status) 
              VALUES ('$kode_transaksi', $user_id, $total, 'pending')";
    
    if (query($query)) {
        $transaksi_id = mysqli_insert_id($conn);
        
        // Insert detail transaksi
        foreach ($_SESSION['cart'] as $item) {
            $item_id = $item['id'];
            $item_type = 'sparepart';
            $harga = $item['harga'];
            $qty = $item['qty'];
            
            $detail_query = "INSERT INTO detail_transaksi (transaksi_id, item_type, item_id, harga, jumlah) 
                             VALUES ($transaksi_id, '$item_type', $item_id, $harga, $qty)";
            query($detail_query);
            
            // Kurangi stok sparepart
            query("UPDATE sparepart SET stok = stok - $qty WHERE id = $item_id");
        }
        
        // Kosongkan keranjang
        unset($_SESSION['cart']);
        
        $_SESSION['success'] = "Checkout berhasil! Silakan lakukan pembayaran.";
        header("Location: pembayaran.php?transaksi_id=$transaksi_id");
        exit();
    } else {
        $_SESSION['error'] = "Checkout gagal! Silakan coba lagi.";
        header("Location: checkout.php");
        exit();
    }
}

// Ambil data keranjang
$cart_items = [];
$total = 0;
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $id => $item) {
        $cart_items[] = $item;
        $total += $item['harga'] * $item['qty'];
    }
}

$title = "Keranjang Belanja";
include '../includes/header.php';
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
                <a href="checkout.php" class="active"><i class="fas fa-shopping-cart"></i>Keranjang / Checkout</a>
                <a href="riwayat.php"><i class="fas fa-receipt"></i>Riwayat Transaksi</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <h3 class="fw-bold text-dark mb-4">Keranjang Belanja Anda</h3>

            <div class="row g-4">
                
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-shopping-cart text-primary me-2"></i>Item Terpilih
                            </h5>
                            <?php if (count($cart_items) > 0): ?>
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    <?php echo count($cart_items); ?> Produk
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body p-0">
                            <?php if (count($cart_items) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($cart_items as $item): ?>
                                    <div class="list-group-item p-4 border-bottom">
                                        <div class="row align-items-center g-3">
                                            <div class="col-auto">
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center overflow-hidden border" 
                                                     style="width: 80px; height: 80px;">
                                                    <img src="../uploads/sparepart/<?php echo $item['gambar'] ?: 'default.jpg'; ?>" 
                                                         class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;" 
                                                         alt="<?php echo $item['nama']; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 col-sm-12">
                                                <h6 class="fw-bold text-dark mb-1"><?php echo $item['nama']; ?></h6>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-1 small mb-2 d-inline-block">
                                                    Merek: <?php echo $item['merek']; ?>
                                                </span>
                                                <h6 class="text-primary fw-bold mb-0">
                                                    Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?> 
                                                    <span class="text-muted fw-normal small">/ pcs</span>
                                                </h6>
                                            </div>
                                            
                                            <div class="col-md-3 col-sm-6 text-md-center">
                                                <form method="POST" action="" class="d-inline-flex align-items-center bg-light rounded-pill p-1 border">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <input type="number" name="qty" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold" 
                                                           style="width: 60px; box-shadow: none;" value="<?php echo $item['qty']; ?>" 
                                                           min="1" max="<?php echo $item['stok']; ?>" onchange="this.form.submit()">
                                                    <button type="submit" name="update_qty" class="btn btn-sm btn-white text-primary rounded-circle" 
                                                            data-bs-toggle="tooltip" title="Update">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </form>
                                                <div class="small text-muted mt-1 text-center">
                                                    Sisa Stok: <?php echo $item['stok']; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 col-sm-6 text-md-end text-sm-start d-flex flex-md-column justify-content-between align-items-md-end">
                                                <div class="text-end">
                                                    <span class="d-block small text-muted mb-1">Subtotal</span>
                                                    <h6 class="fw-bold text-dark mb-0">
                                                        Rp <?php echo number_format($item['harga'] * $item['qty'], 0, ',', '.'); ?>
                                                    </h6>
                                                </div>
                                                <a href="?remove=<?php echo $item['id']; ?>" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm border mt-md-2" 
                                                   onclick="return confirm('Hapus produk ini dari keranjang?')" data-bs-toggle="tooltip" title="Hapus Item">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="p-4 bg-light d-flex justify-content-between align-items-center">
                                    <a href="beli.php" class="btn btn-outline-primary rounded-pill px-4">
                                        <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                                    </a>
                                    <a href="?clear=1" class="btn btn-link text-danger text-decoration-none px-4" 
                                       onclick="return confirm('Anda yakin ingin mengosongkan keranjang?')">
                                        <i class="fas fa-trash-alt me-2"></i>Kosongkan Keranjang
                                    </a>
                                </div>
                                
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                                         style="width: 100px; height: 100px;">
                                        <i class="fas fa-shopping-cart fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Keranjang Masih Kosong</h5>
                                    <p class="text-muted mb-4">Sepertinya Anda belum menambahkan barang apapun.</p>
                                    <a href="beli.php" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                        <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 100px; z-index: 10;">
                        <div class="card-header bg-primary text-white pt-4 pb-3 px-4 rounded-top-4 border-0">
                            <h5 class="fw-bold mb-0"><i class="fas fa-receipt me-2"></i>Ringkasan Pesanan</h5>
                        </div>
                        
                        <div class="card-body p-4 bg-white rounded-bottom-4">
                            <?php if (count($cart_items) > 0): ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between text-muted mb-2">
                                        <span>Total Item</span>
                                        <span class="fw-bold text-dark"><?php echo count($cart_items); ?> Produk</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted mb-3">
                                        <span>Kuantitas Keseluruhan</span>
                                        <span class="fw-bold text-dark">
                                            <?php 
                                            $total_qty = 0;
                                            foreach($cart_items as $item) { $total_qty += $item['qty']; }
                                            echo $total_qty; 
                                            ?> Pcs
                                        </span>
                                    </div>
                                    
                                    <hr class="border-secondary opacity-25">
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3 mb-1">
                                        <span class="h6 text-muted mb-0">Total Tagihan</span>
                                        <h4 class="fw-bold text-primary mb-0">
                                            Rp <?php echo number_format($total, 0, ',', '.'); ?>
                                        </h4>
                                    </div>
                                </div>
                                
                                <form id="formCheckout" method="POST" action="">
                                    <div class="alert bg-info bg-opacity-10 border border-info border-opacity-25 rounded-3 mb-4 p-3">
                                        <div class="d-flex">
                                            <i class="fas fa-info-circle text-info fs-5 me-2 mt-1"></i>
                                            <div>
                                                <strong class="d-block text-dark mb-1 small">Metode Pengambilan Barang</strong>
                                                <p class="mb-0 small text-muted" style="line-height: 1.4;">
                                                    Harap bawa nota/kode transaksi ke bengkel untuk mengambil pesanan Anda. <br>
                                                    📍 <strong>Jl. Raya Otomotif No. 123, Jakarta</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" onclick="confirmCheckout()" class="btn btn-success w-100 py-3 rounded-pill shadow-sm fw-bold mb-3">
                                        <i class="fas fa-check-circle me-2"></i>Proses Checkout Sekarang
                                    </button>
                                    
                                    <div class="text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt text-success me-1"></i> Checkout Aman & Terenkripsi
                                        </small>
                                    </div>
                                    
                                    <input type="hidden" name="checkout" value="1">
                                </form>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-3x text-muted opacity-50 mb-3"></i>
                                    <p class="text-muted small mb-0">Tambahkan produk ke keranjang untuk melihat ringkasan tagihan.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmCheckout() {
    Swal.fire({
        title: 'Konfirmasi Pesanan',
        text: 'Anda akan dialihkan ke halaman pembayaran. Lanjutkan?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Proses Sekarang!',
        cancelButtonText: 'Batal',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCheckout').submit();
        }
    });
}

// Tooltip initialization
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<?php include '../includes/footer.php'; ?>
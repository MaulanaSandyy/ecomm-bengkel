<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

// Handle Add to Cart (Fallback jika tidak menggunakan AJAX)
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    $sparepart = fetch_assoc(query("SELECT * FROM sparepart WHERE id = $id"));
    
    if ($sparepart && $sparepart['stok'] > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['qty'] + $qty <= $sparepart['stok']) {
                $_SESSION['cart'][$id]['qty'] += $qty;
                $_SESSION['success'] = "Jumlah sparepart ditambah!";
            } else {
                $_SESSION['error'] = "Stok tidak mencukupi! Stok tersedia: " . $sparepart['stok'];
            }
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $sparepart['id'],
                'nama' => $sparepart['nama_sparepart'],
                'harga' => $sparepart['harga'],
                'merek' => $sparepart['merek'],
                'gambar' => $sparepart['gambar'],
                'stok' => $sparepart['stok'],
                'qty' => $qty
            ];
            $_SESSION['success'] = "Sparepart berhasil ditambahkan ke keranjang!";
        }
    } else {
        $_SESSION['error'] = "Stok sparepart habis!";
    }
    header("Location: beli.php");
    exit();
}

// Handle Buy Now (langsung checkout)
if (isset($_GET['buy'])) {
    $id = (int)$_GET['buy'];
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    $sparepart = fetch_assoc(query("SELECT * FROM sparepart WHERE id = $id"));
    
    if ($sparepart && $sparepart['stok'] >= $qty) {
        // Kosongkan cart terlebih dahulu
        $_SESSION['cart'] = [];
        
        // Tambahkan item ke cart
        $_SESSION['cart'][$id] = [
            'id' => $sparepart['id'],
            'nama' => $sparepart['nama_sparepart'],
            'harga' => $sparepart['harga'],
            'merek' => $sparepart['merek'],
            'gambar' => $sparepart['gambar'],
            'stok' => $sparepart['stok'],
            'qty' => $qty
        ];
        
        header("Location: checkout.php");
        exit();
    } else {
        $_SESSION['error'] = "Stok sparepart tidak mencukupi atau habis!";
        header("Location: beli.php");
        exit();
    }
}

$title = "Beli Sparepart";
include '../includes/header.php';

// Fitur pencarian
$search = isset($_GET['q']) ? escape_string($_GET['q']) : '';
$where = $search ? "WHERE nama_sparepart LIKE '%$search%' OR merek LIKE '%$search%' OR deskripsi LIKE '%$search%'" : "";
$sparepart = query("SELECT * FROM sparepart $where ORDER BY id DESC");

// Hitung jumlah item di keranjang (total quantity)
$cart_count = 0;
$cart_total = 0;
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['qty'];
        $cart_total += $item['harga'] * $item['qty'];
    }
}
?>

<style>
/* Product Card Styles */
.product-card {
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    height: 100%;
    position: relative;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 30px rgba(0,0,0,0.15);
}

.product-image {
    height: 200px;
    object-fit: cover;
    transition: transform 0.5s ease;
    width: 100%;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-price {
    font-size: 1.25rem;
    font-weight: bold;
    color: #667eea;
}

/* Quantity Input Styles */
.qty-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 10px 0;
}

.qty-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1px solid #e0e0e0;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.qty-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.qty-input {
    width: 50px;
    text-align: center;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 6px;
    font-size: 14px;
}

/* Cart Bubble Styles */
.cart-bubble {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
    cursor: pointer;
    transition: all 0.3s ease;
}

.cart-bubble .btn-cart {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 10px 25px rgba(102,126,234,0.4);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    text-decoration: none;
}

.cart-bubble .btn-cart:hover {
    transform: scale(1.1);
    box-shadow: 0 15px 35px rgba(102,126,234,0.5);
}

.cart-bubble .btn-cart i {
    font-size: 28px;
}

.cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    min-width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    padding: 0 6px;
    animation: pulse 1s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
}

/* Toast Notification */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    min-width: 300px;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<div class="container-fluid px-0 px-lg-4" >
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
                <a href="riwayat.php"><i class="fas fa-receipt"></i>Riwayat Transaksi</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0" data-aos="fade-left">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Katalog Sparepart</h3>
                    <p class="text-muted small mb-0">Temukan suku cadang berkualitas untuk kendaraan Anda</p>
                </div>
                
                <div class="mt-3 mt-md-0">
                    <form action="" method="GET" class="input-group" style="max-width: 300px;" id="searchForm">
                        <input type="text" class="form-control bg-white border-0 shadow-sm rounded-start-pill ps-4" 
                               name="q" id="searchInput" placeholder="Cari nama atau merek..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary border-0 shadow-sm rounded-end-pill px-3" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4" id="productContainer">
                <?php while($row = fetch_assoc($sparepart)): ?>
                <div class="col" data-aos="fade-up" data-product-id="<?php echo $row['id']; ?>">
                    <div class="product-card">
                        <div class="position-relative bg-light overflow-hidden" style="height: 200px;">
                            <?php if ($row['gambar'] && file_exists("../uploads/sparepart/" . $row['gambar'])): ?>
                                <img src="../uploads/sparepart/<?php echo $row['gambar']; ?>" 
                                     class="product-image" alt="<?php echo $row['nama_sparepart']; ?>">
                            <?php else: ?>
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-2 opacity-50"></i>
                                    <small>No Image</small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 rounded-pill fw-normal px-2 py-1">
                                    <i class="fas fa-tag me-1"></i><?php echo $row['merek']; ?>
                                </span>
                            </div>
                            
                            <?php if($row['stok'] <= 5 && $row['stok'] > 0): ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-warning rounded-pill">Stok Terbatas!</span>
                            </div>
                            <?php elseif($row['stok'] <= 0): ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-danger rounded-pill">Habis</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body p-3 d-flex flex-column">
                            <h6 class="card-title fw-bold text-dark mb-1 text-truncate" title="<?php echo $row['nama_sparepart']; ?>">
                                <?php echo $row['nama_sparepart']; ?>
                            </h6>
                            
                            <p class="card-text text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo $row['deskripsi']; ?>
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="product-price">Rp <?php echo number_format($row['harga'],0,',','.'); ?></span>
                                    <?php if ($row['stok'] > 5): ?>
                                        <span class="small text-success"><i class="fas fa-check-circle me-1"></i>Stok <?php echo $row['stok']; ?></span>
                                    <?php elseif ($row['stok'] > 0): ?>
                                        <span class="small text-warning"><i class="fas fa-exclamation-circle me-1"></i>Sisa <?php echo $row['stok']; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($row['stok'] > 0): ?>
                                <div class="qty-wrapper">
                                    <button type="button" class="qty-btn" onclick="changeQty(<?php echo $row['id']; ?>, -1, <?php echo $row['stok']; ?>)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="qty_<?php echo $row['id']; ?>" class="qty-input" value="1" min="1" max="<?php echo $row['stok']; ?>">
                                    <button type="button" class="qty-btn" onclick="changeQty(<?php echo $row['id']; ?>, 1, <?php echo $row['stok']; ?>)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                
                                <div class="d-flex gap-2 mt-2">
                                    <button onclick="addToCart(<?php echo $row['id']; ?>)" class="btn btn-outline-primary rounded-pill flex-grow-1 add-to-cart-btn" style="font-size: 0.85rem;" data-id="<?php echo $row['id']; ?>">
                                        <i class="fas fa-cart-plus me-1"></i>Keranjang
                                    </button>
                                    <button onclick="buyNow(<?php echo $row['id']; ?>)" class="btn btn-primary rounded-pill flex-grow-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-bolt me-1"></i>Beli
                                    </button>
                                </div>
                                <?php else: ?>
                                <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                    <i class="fas fa-ban me-2"></i>Stok Habis
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                
                <?php if(num_rows($sparepart) == 0): ?>
                <div class="col-12 text-center py-5">
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

<div class="cart-bubble" id="cartBubble" style="<?php echo $cart_count > 0 ? 'display: block;' : 'display: none;'; ?>">
    <a href="checkout.php" class="btn-cart">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge" id="cartBadge"><?php echo $cart_count; ?></span>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fungsi untuk mengubah quantity
function changeQty(id, delta, maxStok) {
    const qtyInput = document.getElementById('qty_' + id);
    let currentVal = parseInt(qtyInput.value) || 1;
    let newVal = currentVal + delta;
    
    if (newVal < 1) newVal = 1;
    if (newVal > maxStok) newVal = maxStok;
    
    qtyInput.value = newVal;
}

// Fungsi untuk mendapatkan quantity
function getQty(id) {
    const qtyInput = document.getElementById('qty_' + id);
    return parseInt(qtyInput.value) || 1;
}

// Fungsi untuk update cart badge tanpa reload
function updateCartBadge(newCount) {
    const cartBubble = document.getElementById('cartBubble');
    const cartBadge = document.getElementById('cartBadge');
    
    if (newCount > 0) {
        cartBadge.textContent = newCount;
        cartBubble.style.display = 'block';
        
        // Animasi bounce
        cartBubble.style.animation = 'bounce 0.5s ease';
        setTimeout(() => {
            cartBubble.style.animation = '';
        }, 500);
    } else {
        cartBubble.style.display = 'none';
    }
}

// Fungsi untuk menampilkan toast notification
function showToast(message, type = 'success') {
    // Hapus toast lama jika ada
    const oldToast = document.querySelector('.toast-notification');
    if (oldToast) {
        oldToast.remove();
    }
    
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `
        <div class="alert alert-${type === 'success' ? 'success' : 'danger'} shadow-lg border-0 rounded-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} fa-lg"></i>
                <div>
                    <strong>${type === 'success' ? 'Berhasil!' : 'Gagal!'}</strong>
                    <p class="mb-0 small">${message}</p>
                </div>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove setelah 3 detik
    setTimeout(() => {
        if (toast) toast.remove();
    }, 3000);
}

// Fungsi Add to Cart
function addToCart(id) {
    const qty = getQty(id);
    const btn = document.querySelector(`.add-to-cart-btn[data-id="${id}"]`);
    const originalText = btn.innerHTML;
    
    // Disable button dan tampilkan loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menambah...';
    
    // Gunakan parameter GET agar sinkron dengan fungsi fetch Anda yang lama, namun dikirim via form-urlencoded untuk POST
    fetch('ajax_add_to_cart.php?id=' + id + '&qty=' + qty, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id + '&qty=' + qty
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update cart badge
            updateCartBadge(data.cart_count);
            
            // Tampilkan toast notification
            showToast(data.message, 'success');
            
            // Animasi pada card
            const card = btn.closest('.product-card');
            card.style.transform = 'scale(0.98)';
            setTimeout(() => {
                card.style.transform = '';
            }, 200);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan, silakan coba lagi.', 'error');
    })
    .finally(() => {
        // Restore button
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Fungsi Buy Now
function buyNow(id) {
    const qty = getQty(id);
    // Diarahkan ke beli.php agar logika PHP $_GET['buy'] di bagian atas dieksekusi terlebih dahulu sebelum dilempar ke checkout
    window.location.href = 'beli.php?buy=' + id + '&qty=' + qty;
}

// Pencarian tanpa refresh (opsional)
document.getElementById('searchForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const searchValue = document.getElementById('searchInput').value;
    window.location.href = 'beli.php?q=' + encodeURIComponent(searchValue);
});

// ============================================
// NOTIFIKASI SWEETALERT DARI SESSION (KODE 1)
// ============================================
<?php if(isset($_SESSION['success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?php echo $_SESSION['success']; ?>',
    timer: 2000,
    showConfirmButton: false,
    background: '#fff',
    backdrop: true
});
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?php echo $_SESSION['error']; ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
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
$transaksi = query("SELECT t.*, u.nama_lengkap, u.email, u.no_hp FROM transaksi t 
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

$title = "Pembayaran";
include '../includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ============================================
   PREMIUM PAYMENT PAGE - MODERN & ANIMATED
   ============================================ */
:root {
    --primary: #4F46E5;
    --primary-dark: #4338CA;
    --primary-light: #818CF8;
    --secondary: #10B981;
    --dark: #0F172A;
    --dark-light: #1E293B;
    --gray: #64748B;
    --gray-light: #94A3B8;
    --bg-light: #F8FAFC;
    --white: #FFFFFF;
    --shadow-sm: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 30px 60px -15px rgba(79, 70, 229, 0.2);
    --shadow-glow: 0 0 40px rgba(79, 70, 229, 0.3);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
    min-height: 100vh;
    overflow-x: hidden;
}

/* ============================================
   ANIMATED BACKGROUND PARTICLES
   ============================================ */
.particles-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
    pointer-events: none;
}

.particle {
    position: absolute;
    background: linear-gradient(135deg, var(--primary-light), var(--primary));
    border-radius: 50%;
    opacity: 0.1;
    animation: floatParticle linear infinite;
}

@keyframes floatParticle {
    0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 0.1;
    }
    90% {
        opacity: 0.1;
    }
    100% {
        transform: translateY(-100vh) rotate(360deg);
        opacity: 0;
    }
}

/* ============================================
   MAIN CONTAINER
   ============================================ */
.payment-wrapper {
    position: relative;
    z-index: 10;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

.payment-card {
    max-width: 550px;
    width: 100%;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    border-radius: 48px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid rgba(255, 255, 255, 0.5);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ============================================
   HEADER SECTION
   ============================================ */
.payment-header-gradient {
    background: linear-gradient(135deg, var(--primary) 0%, #8B5CF6 50%, #EC4899 100%);
    padding: 40px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.payment-header-gradient::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotateGlow 20s linear infinite;
}

@keyframes rotateGlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.success-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    animation: pulseIcon 2s ease-in-out infinite;
}

@keyframes pulseIcon {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 20px rgba(255, 255, 255, 0);
    }
}

.success-icon i {
    font-size: 40px;
    color: white;
}

.payment-header-gradient h2 {
    font-size: 28px;
    font-weight: 800;
    color: white;
    margin-bottom: 8px;
}

.payment-header-gradient p {
    color: rgba(255, 255, 255, 0.85);
    font-size: 14px;
}

/* ============================================
   TRANSACTION INFO
   ============================================ */
.transaction-info {
    padding: 20px 30px;
    background: #F8FAFC;
    border-bottom: 1px solid #E2E8F0;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
}

.info-row:not(:last-child) {
    border-bottom: 1px dashed #E2E8F0;
}

.info-label {
    color: var(--gray);
    font-size: 14px;
    font-weight: 500;
}

.info-value {
    font-weight: 700;
    color: var(--dark);
}

.info-value.code {
    font-family: monospace;
    background: #E2E8F0;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
}

/* ============================================
   ORDER ITEMS
   ============================================ */
.order-items-container {
    padding: 20px 30px;
    max-height: 300px;
    overflow-y: auto;
    scrollbar-width: thin;
}

.order-items-container::-webkit-scrollbar {
    width: 4px;
}

.order-items-container::-webkit-scrollbar-track {
    background: #E2E8F0;
    border-radius: 10px;
}

.order-items-container::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.order-item-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #F1F5F9;
    transition: all 0.3s ease;
    animation: fadeInItem 0.5s ease forwards;
    opacity: 0;
    transform: translateX(-20px);
}

@keyframes fadeInItem {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.order-item-card:hover {
    background: #F8FAFC;
    padding-left: 10px;
    transform: translateX(5px);
}

.item-name {
    font-weight: 600;
    color: var(--dark);
}

.item-qty {
    font-size: 12px;
    color: var(--gray);
    margin-top: 4px;
}

.item-price {
    font-weight: 700;
    color: var(--dark);
}

/* ============================================
   TOTAL AMOUNT SECTION
   ============================================ */
.total-section {
    background: linear-gradient(135deg, #F8FAFC 0%, #FFFFFF 100%);
    padding: 25px 30px;
    border-top: 2px solid #E2E8F0;
    border-bottom: 2px solid #E2E8F0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 15px;
}

.total-label {
    font-size: 14px;
    color: var(--gray);
    font-weight: 500;
}

.total-amount {
    font-size: 36px;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary) 0%, #8B5CF6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -1px;
}

/* ============================================
   PAYMENT BUTTON
   ============================================ */
.payment-actions {
    padding: 30px;
}

.pay-now-btn {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, var(--primary) 0%, #8B5CF6 100%);
    border: none;
    border-radius: 60px;
    color: white;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
}

.pay-now-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.pay-now-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 40px rgba(79, 70, 229, 0.4);
}

.pay-now-btn:hover::before {
    left: 100%;
}

.pay-now-btn:active {
    transform: translateY(0);
}

/* ============================================
   SECURITY BADGES
   ============================================ */
.security-badges {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #E2E8F0;
}

.security-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--gray);
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.security-badge i {
    font-size: 16px;
}

.security-badge i.fa-shield-alt { color: #10B981; }
.security-badge i.fa-lock { color: var(--primary); }
.security-badge i.fa-clock { color: #F59E0B; }

/* ============================================
   COUNTDOWN TIMER
   ============================================ */
.countdown-timer {
    background: #FEF3C7;
    border-radius: 60px;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    animation: pulseBg 2s infinite;
}

@keyframes pulseBg {
    0%, 100% { background: #FEF3C7; }
    50% { background: #FDE68A; }
}

.countdown-timer i {
    color: #D97706;
    font-size: 18px;
}

.countdown-text {
    font-size: 13px;
    color: #92400E;
    font-weight: 500;
}

#timer {
    font-weight: 800;
    font-size: 16px;
    color: #D97706;
    font-family: monospace;
}

/* ============================================
   LOADING OVERLAY
   ============================================ */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.9);
    backdrop-filter: blur(12px);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 20px;
}

.loading-spinner {
    width: 70px;
    height: 70px;
    border: 3px solid rgba(79, 70, 229, 0.2);
    border-top: 3px solid var(--primary);
    border-right: 3px solid #8B5CF6;
    border-radius: 50%;
    animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    color: white;
    font-weight: 600;
    font-size: 18px;
    letter-spacing: 1px;
}

.loading-dots {
    display: flex;
    gap: 8px;
}

.loading-dots span {
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    animation: bounce 0.6s infinite alternate;
}

.loading-dots span:nth-child(2) { animation-delay: 0.2s; }
.loading-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
    to { transform: translateY(-10px); opacity: 0.5; }
}

/* ============================================
   SUCCESS CHECKMARK ANIMATION
   ============================================ */
.checkmark-animation {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10000;
    display: none;
}

.checkmark-circle {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #10B981, #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.checkmark-circle i {
    font-size: 50px;
    color: white;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 550px) {
    .payment-card {
        border-radius: 32px;
    }
    
    .payment-header-gradient {
        padding: 30px 20px;
    }
    
    .transaction-info,
    .order-items-container,
    .total-section,
    .payment-actions {
        padding-left: 20px;
        padding-right: 20px;
    }
    
    .total-amount {
        font-size: 28px;
    }
    
    .pay-now-btn {
        padding: 15px;
        font-size: 16px;
    }
}

/* Floating decorative elements */
.floating-element {
    position: absolute;
    pointer-events: none;
    z-index: 0;
}

.floating-1 {
    top: 10%;
    right: 5%;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(79,70,229,0.1) 0%, transparent 70%);
    border-radius: 50%;
    animation: floatAround 12s ease-in-out infinite;
}

.floating-2 {
    bottom: 15%;
    left: 2%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(139,92,246,0.08) 0%, transparent 70%);
    border-radius: 50%;
    animation: floatAround 15s ease-in-out infinite reverse;
}

@keyframes floatAround {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(30px, -30px); }
}
</style>

<div class="particles-container" id="particles"></div>
<div class="floating-element floating-1"></div>
<div class="floating-element floating-2"></div>

<div class="payment-wrapper">
    <div class="payment-card">
        <!-- Header -->
        <div class="payment-header-gradient">
            <div class="success-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <h2>Selesaikan Pembayaran</h2>
            <p>Lengkapi pembayaran Anda untuk menyelesaikan transaksi</p>
        </div>
        
        <!-- Transaction Info -->
        <div class="transaction-info">
            <div class="info-row">
                <span class="info-label">Kode Transaksi</span>
                <span class="info-value code"><?php echo $data_transaksi['kode_transaksi']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer</span>
                <span class="info-value"><?php echo $data_transaksi['nama_lengkap']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Order</span>
                <span class="info-value"><?php echo date('d F Y, H:i', strtotime($data_transaksi['created_at'])); ?></span>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="order-items-container">
            <div style="margin-bottom: 15px;">
                <span class="info-label"><i class="fas fa-shopping-bag me-2"></i>Detail Pesanan</span>
            </div>
            <?php 
            $item_no = 1;
            while($item = fetch_assoc($detail)): 
            ?>
            <div class="order-item-card" style="animation-delay: <?php echo $item_no * 0.05; ?>s">
                <div>
                    <div class="item-name"><?php echo $item['nama_item']; ?></div>
                    <div class="item-qty">Jumlah: <?php echo $item['jumlah']; ?> x Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></div>
                </div>
                <div class="item-price">Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?></div>
            </div>
            <?php 
            $item_no++;
            endwhile; 
            ?>
        </div>
        
        <!-- Total Amount -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span>Rp <?php echo number_format($data_transaksi['total_harga'], 0, ',', '.'); ?></span>
            </div>
            <div class="total-row">
                <span class="total-label">Biaya Admin</span>
                <span class="text-success">Rp 0</span>
            </div>
            <div class="total-row" style="margin-top: 15px; padding-top: 15px; border-top: 2px dashed #E2E8F0;">
                <span class="total-label fw-bold">Total Pembayaran</span>
                <span class="total-amount">Rp <?php echo number_format($data_transaksi['total_harga'], 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <!-- Payment Button -->
        <div class="payment-actions">
            <button class="pay-now-btn" id="payNowBtn" onclick="processPayment()">
                <i class="fas fa-bolt me-2"></i> Lanjutkan ke Pembayaran
                <i class="fas fa-arrow-right ms-2"></i>
            </button>
            
            <div class="countdown-timer">
                <i class="fas fa-hourglass-half"></i>
                <span class="countdown-text">Selesaikan pembayaran sebelum</span>
                <span id="timer">23:59</span>
            </div>
            
            <div class="security-badges">
                <div class="security-badge" style="animation-delay: 0.1s">
                    <i class="fas fa-shield-alt"></i>
                    <span>Enkripsi 256-bit</span>
                </div>
                <div class="security-badge" style="animation-delay: 0.2s">
                    <i class="fas fa-lock"></i>
                    <span>Transaksi Aman</span>
                </div>
                <div class="security-badge" style="animation-delay: 0.3s">
                    <i class="fas fa-clock"></i>
                    <span>24 Jam Support</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Memproses Pembayaran</div>
    <div class="loading-dots">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<!-- Success Checkmark Animation -->
<div class="checkmark-animation" id="checkmarkAnim">
    <div class="checkmark-circle">
        <i class="fas fa-check"></i>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ============================================
// PREMIUM PAYMENT PAGE - FULL ANIMATIONS
// ============================================

// Generate particles
const particlesContainer = document.getElementById('particles');
for (let i = 0; i < 50; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    const size = Math.random() * 6 + 2;
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDuration = Math.random() * 15 + 5 + 's';
    particle.style.animationDelay = Math.random() * 10 + 's';
    particle.style.opacity = Math.random() * 0.3 + 0.05;
    particlesContainer.appendChild(particle);
}

// Countdown Timer (24 hours)
function startCountdown() {
    let timeLeft = 24 * 60 * 60; // 24 hours in seconds
    
    const timerElement = document.getElementById('timer');
    
    const interval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(interval);
            timerElement.innerHTML = '00:00';
            timerElement.style.color = '#EF4444';
            
            Swal.fire({
                icon: 'warning',
                title: 'Waktu Habis!',
                text: 'Waktu pembayaran telah habis. Silakan lakukan transaksi ulang.',
                confirmButtonColor: '#4F46E5',
                background: '#fff',
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                }
            });
            return;
        }
        
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        
        timerElement.innerHTML = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        // Warning when time is running low
        if (timeLeft < 3600) { // Less than 1 hour
            timerElement.style.color = '#EF4444';
            timerElement.style.animation = 'pulseBg 1s infinite';
        }
        
        timeLeft--;
    }, 1000);
}

startCountdown();

// Process payment function
function processPayment() {
    const btn = document.getElementById('payNowBtn');
    const originalText = btn.innerHTML;
    
    // Button loading animation
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengarahkan ke Pembayaran...';
    btn.disabled = true;
    
    // Show loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'flex';
    
    // Simulate processing (with smooth transition)
    setTimeout(() => {
        // Redirect to Xendit
        window.location.href = `xendit_create.php?transaksi_id=<?php echo $transaksi_id; ?>`;
    }, 1500);
}

// Add ripple effect to button
document.querySelectorAll('.pay-now-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const ripple = document.createElement('div');
        ripple.style.position = 'absolute';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.width = '0px';
        ripple.style.height = '0px';
        ripple.style.background = 'rgba(255, 255, 255, 0.4)';
        ripple.style.borderRadius = '50%';
        ripple.style.transform = 'translate(-50%, -50%)';
        ripple.style.transition = 'width 0.6s, height 0.6s';
        ripple.style.pointerEvents = 'none';
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.style.width = '300px';
            ripple.style.height = '300px';
        }, 10);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Animate order items on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateX(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.order-item-card').forEach(el => {
    observer.observe(el);
});

// Add floating animation to logo
const successIcon = document.querySelector('.success-icon');
if (successIcon) {
    setInterval(() => {
        successIcon.style.transform = 'scale(1.05)';
        setTimeout(() => {
            successIcon.style.transform = 'scale(1)';
        }, 300);
    }, 3000);
}

// Add gradient animation to total amount
const totalAmount = document.querySelector('.total-amount');
if (totalAmount) {
    setInterval(() => {
        totalAmount.style.opacity = '0.8';
        setTimeout(() => {
            totalAmount.style.opacity = '1';
        }, 300);
    }, 4000);
}

// Prevent accidental page refresh
window.addEventListener('beforeunload', function(e) {
    // Only show warning if payment is in progress
    const btn = document.getElementById('payNowBtn');
    if (btn && btn.disabled === true) {
        e.preventDefault();
        e.returnValue = 'Pembayaran sedang diproses. Yakin ingin meninggalkan halaman?';
        return e.returnValue;
    }
});

// Parallax effect for floating elements
document.addEventListener('mousemove', function(e) {
    const floating1 = document.querySelector('.floating-1');
    const floating2 = document.querySelector('.floating-2');
    
    if (floating1 && floating2) {
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;
        
        floating1.style.transform = `translate(${mouseX * 20}px, ${mouseY * 20}px)`;
        floating2.style.transform = `translate(${mouseX * -15}px, ${mouseY * -15}px)`;
    }
});
</script>

<?php include '../includes/footer.php'; ?>
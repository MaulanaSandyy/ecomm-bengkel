<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4); // Hanya customer

$user_id = $_SESSION['user_id'];

// Handle Cancel Booking
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];
    $booking = fetch_assoc(query("SELECT * FROM booking WHERE id = $id AND user_id = $user_id"));
    if ($booking && $booking['status'] == 'pending') {
        query("UPDATE booking SET status = 'batal' WHERE id = $id");
        $_SESSION['success'] = "Booking berhasil dibatalkan!";
    } else {
        $_SESSION['error'] = "Booking tidak dapat dibatalkan!";
    }
    header("Location: booking.php");
    exit();
}

// Handle Booking Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking'])) {
    $jasa_id = !empty($_POST['jasa_id']) ? $_POST['jasa_id'] : 'NULL';
    $tanggal_booking = escape_string($_POST['tanggal_booking']);
    $jam_booking = escape_string($_POST['jam_booking']);
    $keluhan = escape_string($_POST['keluhan']);
    
    // Validasi tanggal tidak boleh kurang dari hari ini
    if ($tanggal_booking < date('Y-m-d')) {
        $_SESSION['error'] = "Tanggal booking tidak boleh kurang dari hari ini!";
        header("Location: booking.php");
        exit();
    }
    
    // Cek apakah tanggal dan jam sudah dibooking
    $cek = query("SELECT * FROM booking WHERE tanggal_booking = '$tanggal_booking' AND jam_booking = '$jam_booking' AND status != 'batal'");
    if (num_rows($cek) > 0) {
        $_SESSION['error'] = "Maaf, jam tersebut sudah dibooking. Silakan pilih jam lain.";
        header("Location: booking.php");
        exit();
    }
    
    $query = "INSERT INTO booking (user_id, jasa_id, tanggal_booking, jam_booking, keluhan) 
              VALUES ($user_id, $jasa_id, '$tanggal_booking', '$jam_booking', '$keluhan')";
    
    if (query($query)) {
        $_SESSION['success'] = "Booking berhasil! Silakan tunggu konfirmasi dari admin.";
        header("Location: booking.php");
        exit();
    } else {
        $_SESSION['error'] = "Booking gagal! Silakan coba lagi.";
        header("Location: booking.php");
        exit();
    }
}

// Ambil data jasa untuk dropdown
$jasa_list = query("SELECT * FROM jasa ORDER BY nama_jasa ASC");

// Ambil booking customer dengan join ke service
$bookings = query("SELECT b.*, j.nama_jasa, j.harga, j.estimasi_waktu,
                   s.status as service_status, s.catatan_service, s.pegawai_id,
                   p.nama_lengkap as pegawai_name
                   FROM booking b 
                   LEFT JOIN jasa j ON b.jasa_id = j.id 
                   LEFT JOIN service s ON b.id = s.booking_id
                   LEFT JOIN users p ON s.pegawai_id = p.id
                   WHERE b.user_id = $user_id 
                   ORDER BY 
                       CASE b.status 
                           WHEN 'pending' THEN 1
                           WHEN 'dikonfirmasi' THEN 2
                           WHEN 'selesai' THEN 3
                           WHEN 'batal' THEN 4
                       END,
                       b.tanggal_booking DESC,
                       b.jam_booking DESC");

// Ambil jam yang sudah dibooking untuk tanggal tertentu (untuk AJAX)
if (isset($_GET['get_jam']) && isset($_GET['tanggal'])) {
    $tanggal = escape_string($_GET['tanggal']);
    $booked = query("SELECT jam_booking FROM booking WHERE tanggal_booking = '$tanggal' AND status != 'batal'");
    $booked_jam = [];
    while($row = fetch_assoc($booked)) {
        $booked_jam[] = $row['jam_booking'];
    }
    header('Content-Type: application/json');
    echo json_encode($booked_jam);
    exit();
}

$title = "Booking Service";
include '../includes/header.php';
?>

<style>
.booking-card {
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.booking-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.booking-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    color: white;
}

.jam-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.jam-card:hover:not(.disabled) {
    border-color: #667eea;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(102,126,234,0.2);
}

.jam-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.jam-card.selected .text-muted {
    color: rgba(255,255,255,0.8) !important;
}

.jam-card.disabled {
    background: #f3f4f6;
    border-color: #e5e7eb;
    cursor: not-allowed;
    opacity: 0.6;
}

.jam-card.disabled .text-muted {
    color: #9ca3af !important;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 50px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}
.status-pending { background: #fef3c7; color: #d97706; }
.status-dikonfirmasi { background: #dbeafe; color: #2563eb; }
.status-selesai { background: #d1fae5; color: #059669; }
.status-batal { background: #fee2e2; color: #dc2626; }

.service-status-antri { background: #fed7aa; color: #c2410c; }
.service-status-dikerjakan { background: #c7d2fe; color: #4338ca; }
.service-status-selesai { background: #d1fae5; color: #059669; }

.booking-detail-card {
    background: #f9fafb;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
}

.booking-detail-card:last-child {
    margin-bottom: 0;
}

.riwayat-item {
    transition: all 0.2s ease;
    border-left: 4px solid transparent;
}

.riwayat-item:hover {
    background: #f9fafb;
    border-left-color: #667eea;
}

.jam-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

@media (max-width: 768px) {
    .jam-list {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 480px) {
    .jam-list {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="container-fluid px-0 px-lg-4">
    <div class="row g-0 g-lg-4">
        <div class="col-md-3 col-lg-2 d-none d-md-block" data-aos="fade-right">
            <div class="sidebar rounded-4 shadow-sm" style="top: 100px;">
                <h5 class="fw-bold px-3 mb-4 text-uppercase" style="color: var(--primary-color); font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="fas fa-user-circle me-2"></i>Menu Pelanggan
                </h5>
                <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                <a href="beli.php"><i class="fas fa-shopping-bag"></i>Beli Sparepart</a>
                <a href="booking.php"  class="active"><i class="fas fa-calendar-check"></i>Booking Service</a>
                <a href="checkout.php"><i class="fas fa-shopping-cart"></i>Keranjang / Checkout</a>
                <a href="riwayat.php"><i class="fas fa-receipt"></i>Riwayat Transaksi</a>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4 p-lg-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Booking Service</h3>
                    <p class="text-muted small mb-0">Booking layanan service mobil Anda dengan mudah dan cepat</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="booking-card" data-aos="fade-right">
                        <div class="booking-header">
                            <h4 class="mb-2"><i class="fas fa-calendar-plus me-2"></i>Booking Service</h4>
                            <p class="mb-0 opacity-75">Isi form berikut untuk melakukan booking service</p>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="" id="bookingForm">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-wrench text-primary me-1"></i> Pilih Jasa Service
                                    </label>
                                    <select name="jasa_id" class="form-control" id="jasaSelect">
                                        <option value="">-- Konsultasi / Tidak Pilih Jasa --</option>
                                        <?php while($jasa = fetch_assoc($jasa_list)): ?>
                                        <option value="<?php echo $jasa['id']; ?>" 
                                                data-harga="<?php echo $jasa['harga']; ?>"
                                                data-estimasi="<?php echo $jasa['estimasi_waktu']; ?>">
                                            <?php echo $jasa['nama_jasa']; ?> - Rp <?php echo number_format($jasa['harga'], 0, ',', '.'); ?> (<?php echo $jasa['estimasi_waktu']; ?>)
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <small class="text-muted">*Tidak wajib, Anda bisa langsung konsultasi dengan mekanik</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-calendar-day text-primary me-1"></i> Tanggal Booking
                                    </label>
                                    <input type="date" name="tanggal_booking" class="form-control" 
                                           id="tanggalBooking"
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                           max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" 
                                           required>
                                    <small class="text-muted">*Minimal H+1, maksimal 30 hari ke depan</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-clock text-primary me-1"></i> Jam Booking
                                    </label>
                                    <div class="jam-list" id="jamContainer">
                                        <?php
                                        $jam_list = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];
                                        foreach($jam_list as $jam):
                                        ?>
                                        <div class="jam-card" data-jam="<?php echo $jam; ?>" onclick="pilihJam(this)">
                                            <i class="fas fa-clock fa-lg mb-1"></i>
                                            <div class="fw-bold"><?php echo $jam; ?></div>
                                            <small class="text-muted">WIB</small>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="jam_booking" id="jam_booking" required>
                                    <div id="jamError" class="text-danger small mt-1" style="display: none;">Silakan pilih jam booking</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-comment-dots text-primary me-1"></i> Keluhan / Deskripsi Masalah
                                    </label>
                                    <textarea name="keluhan" class="form-control" rows="4" 
                                              placeholder="Jelaskan masalah yang dialami kendaraan Anda (contoh: mesin sulit dinyalakan, suara berisik, dll)" 
                                              required></textarea>
                                </div>
                                
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Info Penting:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li>Booking akan dikonfirmasi oleh admin maksimal 1x24 jam</li>
                                        <li>Mohon datang tepat waktu sesuai jadwal yang dipilih</li>
                                        <li>Jika ingin membatalkan, lakukan H-1 sebelum jadwal</li>
                                        <li>Untuk perubahan jadwal, hubungi admin via WhatsApp</li>
                                    </ul>
                                </div>
                                
                                <button type="submit" name="booking" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                                    <i class="fas fa-paper-plane me-2"></i>Booking Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <div class="booking-card" data-aos="fade-left">
                        <div class="booking-header">
                            <h4 class="mb-2"><i class="fas fa-history me-2"></i>Riwayat Booking Saya</h4>
                            <p class="mb-0 opacity-75">Daftar booking service yang telah Anda lakukan</p>
                        </div>
                        <div class="card-body p-0">
                            <?php if (num_rows($bookings) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php 
                                    $no = 1;
                                    while($row = fetch_assoc($bookings)): 
                                    ?>
                                    <div class="list-group-item p-4 riwayat-item">
                                        <div class="row align-items-start">
                                            <div class="col-md-3 mb-2 mb-md-0">
                                                <div class="bg-light rounded-3 p-2 text-center">
                                                    <h5 class="mb-0 text-primary fw-bold">
                                                        <?php echo date('d', strtotime($row['tanggal_booking'])); ?>
                                                    </h5>
                                                    <small class="text-muted">
                                                        <?php echo date('M Y', strtotime($row['tanggal_booking'])); ?>
                                                    </small>
                                                    <hr class="my-2">
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-clock me-1"></i><?php echo $row['jam_booking']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-5 mb-2 mb-md-0">
                                                <div class="booking-detail-card">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <i class="fas fa-wrench text-primary"></i>
                                                        <strong class="small text-muted">Jasa Service</strong>
                                                    </div>
                                                    <p class="mb-1 fw-semibold">
                                                        <?php if ($row['nama_jasa']): ?>
                                                            <?php echo $row['nama_jasa']; ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?> 
                                                                (<?php echo $row['estimasi_waktu']; ?>)
                                                            </small>
                                                        <?php else: ?>
                                                            <span class="text-muted">Konsultasi / Tidak memilih jasa</span>
                                                        <?php endif; ?>
                                                    </p>
                                                    
                                                    <div class="d-flex align-items-center gap-2 mt-2">
                                                        <i class="fas fa-comment text-info"></i>
                                                        <small class="text-muted">Keluhan:</small>
                                                    </div>
                                                    <p class="small mb-0 text-secondary">
                                                        <?php echo substr($row['keluhan'], 0, 100); ?>
                                                        <?php if(strlen($row['keluhan']) > 100): ?>...<?php endif; ?>
                                                        <?php if($row['keluhan']): ?>
                                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#keluhanModal"
                                                                onclick="showKeluhan('<?php echo addslashes($row['keluhan']); ?>')">
                                                            <i class="fas fa-eye"></i> Lihat
                                                        </button>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="booking-detail-card">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="small text-muted">Status Booking</span>
                                                        <?php
                                                        $status_class = [
                                                            'pending' => 'status-pending',
                                                            'dikonfirmasi' => 'status-dikonfirmasi',
                                                            'selesai' => 'status-selesai',
                                                            'batal' => 'status-batal'
                                                        ];
                                                        ?>
                                                        <span class="status-badge <?php echo $status_class[$row['status']]; ?>">
                                                            <?php 
                                                            $status_text = [
                                                                'pending' => 'Menunggu Konfirmasi',
                                                                'dikonfirmasi' => 'Dikonfirmasi',
                                                                'selesai' => 'Selesai',
                                                                'batal' => 'Dibatalkan'
                                                            ];
                                                            echo $status_text[$row['status']];
                                                            ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <?php if ($row['service_status']): ?>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="small text-muted">Status Service</span>
                                                        <span class="badge service-status-<?php echo $row['service_status']; ?>">
                                                            <?php 
                                                            $service_text = [
                                                                'antri' => 'Dalam Antrian',
                                                                'dikerjakan' => 'Sedang Dikerjakan',
                                                                'selesai' => 'Service Selesai'
                                                            ];
                                                            echo $service_text[$row['service_status']];
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <?php if ($row['pegawai_name']): ?>
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <i class="fas fa-user-cog text-muted small"></i>
                                                        <small class="text-muted">Mekanik: <?php echo $row['pegawai_name']; ?></small>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($row['catatan_service']): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-info w-100 mt-2" 
                                                            data-bs-toggle="popover" 
                                                            title="Catatan Service" 
                                                            data-bs-content="<?php echo htmlspecialchars($row['catatan_service']); ?>">
                                                        <i class="fas fa-sticky-note me-1"></i>Lihat Catatan Service
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($row['status'] == 'pending'): ?>
                                                    <div class="mt-3">
                                                        <a href="?cancel=<?php echo $row['id']; ?>" 
                                                           class="btn btn-outline-danger btn-sm w-100 rounded-pill"
                                                           onclick="return confirm('Yakin ingin membatalkan booking ini?')">
                                                            <i class="fas fa-times me-1"></i>Batalkan Booking
                                                        </a>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($row['status'] == 'dikonfirmasi' && $row['service_status'] != 'selesai'): ?>
                                                    <div class="mt-3 text-center">
                                                        <span class="badge bg-success bg-opacity-10 text-success">
                                                            <i class="fas fa-check-circle me-1"></i>Booking Terkonfirmasi
                                                        </span>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                                         style="width: 100px; height: 100px;">
                                        <i class="fas fa-calendar-alt fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Belum Ada Booking</h5>
                                    <p class="text-muted mb-4">Anda belum melakukan booking service. Yuk booking sekarang!</p>
                                    <button onclick="document.getElementById('bookingForm').scrollIntoView({behavior: 'smooth'})" 
                                            class="btn btn-primary rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0 rounded-4" data-aos="fade-up">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                            <i class="fas fa-clock text-primary fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">Jam Operasional</h6>
                                            <p class="mb-0 small text-muted">
                                                Senin - Jumat: 08:00 - 20:00<br>
                                                Sabtu: 08:00 - 18:00<br>
                                                Minggu: 09:00 - 15:00
                                            </p>
                                            <small class="text-muted">*Istirahat 12:00 - 13:00</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0 rounded-4" data-aos="fade-up" data-aos-delay="100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                            <i class="fas fa-phone-alt text-success fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">Butuh Bantuan?</h6>
                                            <p class="mb-1 small">Hubungi kami untuk informasi lebih lanjut:</p>
                                            <p class="mb-0 small">
                                                <i class="fab fa-whatsapp text-success me-1"></i> 0812-3456-7890<br>
                                                <i class="fas fa-phone-alt me-1"></i> 021-555-1234
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="keluhanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-comment-dots me-2"></i>Detail Keluhan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="keluhanText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let selectedJam = null;

// Pilih jam
function pilihJam(element) {
    if (element.classList.contains('disabled')) {
        return;
    }
    
    // Hapus class selected dari semua jam
    document.querySelectorAll('.jam-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Tambah class selected ke jam yang dipilih
    element.classList.add('selected');
    selectedJam = element.getAttribute('data-jam');
    document.getElementById('jam_booking').value = selectedJam;
    document.getElementById('jamError').style.display = 'none';
}

// Cek ketersediaan jam berdasarkan tanggal
async function cekKetersediaanJam(tanggal) {
    if (!tanggal) return;
    
    try {
        const response = await fetch(`booking.php?get_jam=1&tanggal=${tanggal}`);
        const bookedJams = await response.json();
        
        // Reset semua jam
        document.querySelectorAll('.jam-card').forEach(card => {
            card.classList.remove('disabled', 'selected');
            const jam = card.getAttribute('data-jam');
            if (bookedJams.includes(jam)) {
                card.classList.add('disabled');
            }
        });
        
        // Reset selected jam
        selectedJam = null;
        document.getElementById('jam_booking').value = '';
        
    } catch (error) {
        console.error('Error:', error);
    }
}

// Event listener untuk perubahan tanggal
document.getElementById('tanggalBooking').addEventListener('change', function() {
    cekKetersediaanJam(this.value);
});

// Validasi form sebelum submit
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    if (!document.getElementById('jam_booking').value) {
        e.preventDefault();
        document.getElementById('jamError').style.display = 'block';
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Silakan pilih jam booking terlebih dahulu!',
            confirmButtonColor: '#667eea'
        });
        return false;
    }
    
    const tanggal = document.querySelector('input[name="tanggal_booking"]').value;
    if (!tanggal) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Silakan pilih tanggal booking!',
            confirmButtonColor: '#667eea'
        });
        return false;
    }
    
    const keluhan = document.querySelector('textarea[name="keluhan"]').value;
    if (!keluhan.trim()) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Silakan isi keluhan/deskripsi masalah!',
            confirmButtonColor: '#667eea'
        });
        return false;
    }
    
    // Tampilkan loading
    Swal.fire({
        title: 'Memproses Booking...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});

// Fungsi untuk menampilkan keluhan di modal
function showKeluhan(keluhan) {
    document.getElementById('keluhanText').innerHTML = keluhan;
}

// Inisialisasi tooltip dan popover
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            html: true
        })
    })
});

// Notifikasi dari session
<?php if(isset($_SESSION['success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?php echo $_SESSION['success']; ?>',
    timer: 3000,
    showConfirmButton: true,
    confirmButtonColor: '#667eea',
    background: '#fff'
});
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?php echo $_SESSION['error']; ?>',
    timer: 3000,
    showConfirmButton: true,
    confirmButtonColor: '#667eea'
});
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
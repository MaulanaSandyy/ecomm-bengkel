<?php
session_start();
include '../includes/koneksi.php';
cek_login();
cek_role(4);

$user_id = $_SESSION['user_id'];

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking'])) {
    $jasa_id = !empty($_POST['jasa_id']) ? $_POST['jasa_id'] : 'NULL';
    $tanggal_booking = escape_string($_POST['tanggal_booking']);
    $jam_booking = escape_string($_POST['jam_booking']);
    $keluhan = escape_string($_POST['keluhan']);
    
    if ($tanggal_booking < date('Y-m-d')) {
        $_SESSION['error'] = "Tanggal booking tidak boleh kurang dari hari ini!";
        header("Location: booking.php");
        exit();
    }
    
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

$jasa_list = query("SELECT * FROM jasa ORDER BY nama_jasa ASC");

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

$booked_jam = [];
if (isset($_GET['get_jam']) && isset($_GET['tanggal'])) {
    $tanggal = escape_string($_GET['tanggal']);
    $booked = query("SELECT jam_booking FROM booking WHERE tanggal_booking = '$tanggal' AND status != 'batal'");
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
.booking-wrapper {
    animation: fadeUp 0.6s ease-out forwards;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.booking-wrapper .glass-card {
    background: #ffffff;
    border-radius: 1.5rem;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.booking-wrapper .glass-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px -10px rgba(0,0,0,0.12);
}

.booking-wrapper .booking-header-modern {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    padding: 30px;
    color: white;
    position: relative;
    overflow: hidden;
}

.booking-wrapper .booking-header-modern::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    filter: blur(20px);
}

.booking-wrapper .form-control, 
.booking-wrapper .form-select {
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    background-color: #f8fafc;
    transition: all 0.3s ease;
}

.booking-wrapper .form-control:focus, 
.booking-wrapper .form-select:focus {
    background-color: #ffffff;
    border-color: #0d6efd;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
}

.booking-wrapper .jam-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 12px;
}

.booking-wrapper .jam-card {
    background: #f8fafc;
    border: 2px solid transparent;
    border-radius: 1rem;
    padding: 15px 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.booking-wrapper .jam-card:hover:not(.disabled) {
    background: #eff6ff;
    border-color: #bfdbfe;
    transform: scale(1.05);
}

.booking-wrapper .jam-card.selected {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 8px 16px rgba(13, 110, 253, 0.2);
    transform: scale(1.05);
}

.booking-wrapper .jam-card.selected .text-muted {
    color: rgba(255,255,255,0.8) !important;
}

.booking-wrapper .jam-card.disabled {
    background: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
    opacity: 0.5;
}

.booking-wrapper .btn-primary-custom {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    border: none;
    border-radius: 1rem;
    padding: 12px 24px;
    color: white;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.booking-wrapper .btn-primary-custom:hover {
    box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
    transform: translateY(-2px);
    color: white;
}

.booking-wrapper .riwayat-item {
    border-left: 4px solid transparent;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.booking-wrapper .riwayat-item:hover {
    background: #f8fafc;
    border-left-color: #0d6efd;
}

.booking-wrapper .status-badge {
    padding: 8px 16px;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.booking-wrapper .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.booking-wrapper .status-dikonfirmasi { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.booking-wrapper .status-selesai { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.booking-wrapper .status-batal { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

.booking-wrapper .service-status-antri { background: #fff7ed; color: #c2410c; }
.booking-wrapper .service-status-dikerjakan { background: #eef2ff; color: #4338ca; }
.booking-wrapper .service-status-selesai { background: #ecfdf5; color: #047857; }

.booking-wrapper .date-box {
    background: #f8fafc;
    border-radius: 1rem;
    padding: 15px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}
</style>

<div class="container py-4 booking-wrapper">
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="glass-card" data-aos="fade-right">
                <div class="booking-header-modern">
                    <h4 class="mb-2"><i class="fas fa-calendar-plus me-2"></i>Booking Service</h4>
                    <p class="mb-0 opacity-75">Jadwalkan perawatan kendaraan Anda</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="" id="bookingForm">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary mb-2">
                                <i class="fas fa-wrench text-primary me-1"></i> Pilih Layanan
                            </label>
                            <select name="jasa_id" class="form-select" id="jasaSelect">
                                <option value="">-- Konsultasi / Cek Fisik --</option>
                                <?php while($jasa = fetch_assoc($jasa_list)): ?>
                                <option value="<?php echo $jasa['id']; ?>" 
                                        data-harga="<?php echo $jasa['harga']; ?>"
                                        data-estimasi="<?php echo $jasa['estimasi_waktu']; ?>">
                                    <?php echo $jasa['nama_jasa']; ?> - Rp <?php echo number_format($jasa['harga'], 0, ',', '.'); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary mb-2">
                                <i class="fas fa-calendar-day text-primary me-1"></i> Pilih Tanggal
                            </label>
                            <input type="date" name="tanggal_booking" class="form-control text-primary fw-medium" 
                                   id="tanggalBooking"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                   max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary mb-3">
                                <i class="fas fa-clock text-primary me-1"></i> Pilih Waktu
                            </label>
                            <div class="jam-list" id="jamContainer">
                                <?php
                                $jam_list = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];
                                foreach($jam_list as $jam):
                                ?>
                                <div class="jam-card shadow-sm" data-jam="<?php echo $jam; ?>" onclick="pilihJam(this)">
                                    <div class="fw-bold fs-5"><?php echo $jam; ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">WIB</small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="jam_booking" id="jam_booking" required>
                            <div id="jamError" class="text-danger small mt-2" style="display: none;">
                                Silakan pilih jam layanan
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary mb-2">
                                <i class="fas fa-comment-dots text-primary me-1"></i> Detail Keluhan
                            </label>
                            <textarea name="keluhan" class="form-control" rows="4" 
                                      placeholder="Deskripsikan masalah pada kendaraan Anda..." 
                                      required></textarea>
                        </div>
                        
                        <button type="submit" name="booking" class="btn btn-primary-custom w-100 fs-5 mt-2">
                            <i class="fas fa-paper-plane me-2"></i> Konfirmasi Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="glass-card" data-aos="fade-left">
                <div class="booking-header-modern">
                    <h4 class="mb-2"><i class="fas fa-history me-2"></i>Riwayat Perawatan</h4>
                    <p class="mb-0 opacity-75">Daftar booking service Anda</p>
                </div>
                <div class="card-body p-0">
                    <?php if (num_rows($bookings) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php 
                            while($row = fetch_assoc($bookings)): 
                            ?>
                            <div class="list-group-item p-4 riwayat-item border-0 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-md-3 mb-3 mb-md-0 text-center">
                                        <div class="date-box">
                                            <h3 class="mb-0 text-primary fw-bold">
                                                <?php echo date('d', strtotime($row['tanggal_booking'])); ?>
                                            </h3>
                                            <span class="text-secondary fw-medium d-block text-uppercase" style="font-size: 0.8rem;">
                                                <?php echo date('M Y', strtotime($row['tanggal_booking'])); ?>
                                            </span>
                                            <div class="mt-2 pt-2 border-top">
                                                <span class="badge bg-dark rounded-pill">
                                                    <i class="fas fa-clock me-1"></i><?php echo $row['jam_booking']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <h5 class="fw-bold mb-1">
                                            <?php echo $row['nama_jasa'] ? $row['nama_jasa'] : 'Cek Fisik / Konsultasi'; ?>
                                        </h5>
                                        <?php if ($row['nama_jasa']): ?>
                                            <p class="text-primary fw-semibold mb-2">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                        <?php endif; ?>
                                        
                                        <p class="mb-0 text-secondary" style="font-size: 0.9rem;">
                                            <i class="fas fa-comment me-1"></i> 
                                            <?php echo substr($row['keluhan'], 0, 60); ?><?php if(strlen($row['keluhan']) > 60): ?>...<?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <?php
                                        $status_class = [
                                            'pending' => 'status-pending',
                                            'dikonfirmasi' => 'status-dikonfirmasi',
                                            'selesai' => 'status-selesai',
                                            'batal' => 'status-batal'
                                        ];
                                        $status_text = [
                                            'pending' => 'Menunggu',
                                            'dikonfirmasi' => 'Dikonfirmasi',
                                            'selesai' => 'Selesai',
                                            'batal' => 'Dibatalkan'
                                        ];
                                        ?>
                                        <span class="status-badge <?php echo $status_class[$row['status']]; ?> mb-3 d-inline-block">
                                            <?php echo $status_text[$row['status']]; ?>
                                        </span>

                                        <?php if ($row['status'] == 'pending'): ?>
                                            <a href="?cancel=<?php echo $row['id']; ?>" 
                                               class="btn btn-outline-danger btn-sm w-100 rounded-pill"
                                               onclick="return confirm('Batalkan jadwal ini?')">
                                                <i class="fas fa-times me-1"></i> Batalkan
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($row['service_status']): ?>
                                            <div class="mt-2 text-start text-md-end">
                                                <span class="badge service-status-<?php echo $row['service_status']; ?> rounded-pill">
                                                    <i class="fas fa-wrench"></i> <?php echo ucfirst($row['service_status']); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-4x text-muted opacity-25 mb-3"></i>
                            <h5 class="fw-bold text-dark">Belum Ada Aktivitas</h5>
                            <p class="text-muted">Jadwalkan perawatan pertama kendaraan Anda sekarang.</p>
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
                                        Sabtu: 08:00 - 18:00
                                    </p>
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
                                    <p class="mb-0 small text-muted">
                                        WA: 0812-3456-7890<br>
                                        Telp: 021-555-1234
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let selectedJam = null;

function pilihJam(element) {
    if (element.classList.contains('disabled')) return;
    
    document.querySelectorAll('.jam-card').forEach(card => card.classList.remove('selected'));
    
    element.classList.add('selected');
    selectedJam = element.getAttribute('data-jam');
    document.getElementById('jam_booking').value = selectedJam;
    document.getElementById('jamError').style.display = 'none';
}

async function cekKetersediaanJam(tanggal) {
    if (!tanggal) return;
    
    try {
        const response = await fetch(`booking.php?get_jam=1&tanggal=${tanggal}`);
        const bookedJams = await response.json();
        
        document.querySelectorAll('.jam-card').forEach(card => {
            card.classList.remove('disabled', 'selected');
            const jam = card.getAttribute('data-jam');
            if (bookedJams.includes(jam)) {
                card.classList.add('disabled');
            }
        });
        
        selectedJam = null;
        document.getElementById('jam_booking').value = '';
        
    } catch (error) {
        console.error('Error:', error);
    }
}

document.getElementById('tanggalBooking').addEventListener('change', function() {
    cekKetersediaanJam(this.value);
});

document.getElementById('bookingForm').addEventListener('submit', function(e) {
    if (!document.getElementById('jam_booking').value) {
        e.preventDefault();
        document.getElementById('jamError').style.display = 'block';
        return false;
    }
    
    Swal.fire({
        title: 'Memproses...',
        text: 'Menyimpan jadwal booking Anda',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});

<?php if(isset($_SESSION['success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Sukses!',
    text: '<?php echo $_SESSION['success']; ?>',
    timer: 3000,
    showConfirmButton: false
});
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: '<?php echo $_SESSION['error']; ?>',
});
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
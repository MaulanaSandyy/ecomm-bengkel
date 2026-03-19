</div> <footer class="mt-auto">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="fas fa-car-side fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0 text-white">Jaya Abadi</h4>
                </div>
                <p class="text-white-50 lh-lg mb-4 pe-lg-4">Bengkel mobil profesional dengan teknisi berpengalaman. Melayani service rutin, perbaikan mesin, dan jual sparepart original dengan standar kualitas terbaik.</p>
                <div class="social-links d-flex gap-2">
                    <a href="#" class="text-white text-decoration-none"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white text-decoration-none"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white text-decoration-none"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="text-white text-decoration-none"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <h5 class="fw-bold text-white mb-4">Jam Operasional</h5>
                <ul class="list-unstyled text-white-50 d-flex flex-column gap-3">
                    <li class="d-flex justify-content-between border-bottom border-secondary border-opacity-25 pb-2">
                        <span><i class="far fa-calendar-alt me-2 text-primary"></i>Senin - Jumat</span>
                        <span class="fw-medium text-white">08:00 - 20:00</span>
                    </li>
                    <li class="d-flex justify-content-between border-bottom border-secondary border-opacity-25 pb-2">
                        <span><i class="far fa-calendar-check me-2 text-primary"></i>Sabtu</span>
                        <span class="fw-medium text-white">08:00 - 18:00</span>
                    </li>
                    <li class="d-flex justify-content-between border-bottom border-secondary border-opacity-25 pb-2">
                        <span><i class="far fa-calendar-minus me-2 text-warning"></i>Minggu</span>
                        <span class="fw-medium text-white">09:00 - 15:00</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span><i class="far fa-calendar-times me-2 text-danger"></i>Libur Nasional</span>
                        <span class="fw-medium text-danger">Tutup</span>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-12" data-aos="fade-up" data-aos-delay="200">
                <h5 class="fw-bold text-white mb-4">Informasi Kontak</h5>
                <ul class="list-unstyled text-white-50 d-flex flex-column gap-3">
                    <li class="d-flex align-items-start gap-3">
                        <div class="bg-white bg-opacity-10 p-2 rounded text-primary">
                            <i class="fas fa-phone-alt px-1"></i>
                        </div>
                        <div>
                            <span class="d-block small text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">Telepon</span>
                            <span class="fw-medium text-white">021-231-1234</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <div class="bg-white bg-opacity-10 p-2 rounded text-success">
                            <i class="fab fa-whatsapp px-1"></i>
                        </div>
                        <div>
                            <span class="d-block small text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">WhatsApp</span>
                            <span class="fw-medium text-white">0812-3456-7890</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <div class="bg-white bg-opacity-10 p-2 rounded text-info">
                            <i class="fas fa-envelope px-1"></i>
                        </div>
                        <div>
                            <span class="d-block small text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">Email</span>
                            <span class="fw-medium text-white">info@jayabadi.com</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <div class="bg-white bg-opacity-10 p-2 rounded text-danger">
                            <i class="fas fa-map-marker-alt px-1"></i>
                        </div>
                        <div>
                            <span class="d-block small text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">Lokasi</span>
                            <span class="fw-medium text-white">Jl. Puspitek Unpam Viktor No. 123, Tangsel</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="border-secondary border-opacity-50 my-5">
        
        <div class="row align-items-center pb-4">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 text-white-50 small">&copy; 2024 Bengkel Mobil Jaya Abadi. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-white-50 small">Designed for better performance</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="/ecomm-bengkel/assets/js/script.js"></script>

<script>
// Sebagian fungsi script kita pertahankan di sini agar logic spesifik file tetap berjalan

// Inisialisasi AOS
AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true
});

// Fungsi loading
function showLoading(text = 'Memproses...') {
    document.getElementById('loadingText').innerText = text;
    document.getElementById('loadingSpinner').classList.add('show');
}

function hideLoading() {
    document.getElementById('loadingSpinner').classList.remove('show');
}

// Konfirmasi hapus dengan SweetAlert (Dipercantik)
function confirmDelete(url, message = 'Data akan dihapus permanen!') {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444', 
        cancelButtonColor: '#64748b',  
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        background: '#ffffff',
        borderRadius: '24px'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}

// Notifikasi Sukses / Error dengan model TOAST (Muncul elegan di pojok atas)
<?php if (isset($_SESSION['success'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?php echo $_SESSION['success']; ?>',
        timer: 3000,
        showConfirmButton: false,
        background: '#ffffff',
        borderRadius: '16px',
        toast: true,
        position: 'top-end'
    });
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?php echo $_SESSION['error']; ?>',
        timer: 3000,
        showConfirmButton: false,
        background: '#ffffff',
        borderRadius: '16px',
        toast: true,
        position: 'top-end'
    });
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

</body>
</html>
</div> <!-- Penutup container-fluid dari header.php -->

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up">
                <h5 class="mb-4"><i class="fas fa-car me-2"></i>Bengkel Jaya Abadi</h5>
                <p>Bengkel mobil profesional dengan teknisi berpengalaman. Melayani service rutin, perbaikan mesin, dan jual sparepart original.</p>
                <div class="social-links mt-3">
                    <a href="#" class="me-3 text-white"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="me-3 text-white"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="me-3 text-white"><i class="fab fa-whatsapp fa-2x"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-youtube fa-2x"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <h5 class="mb-4"><i class="fas fa-clock me-2"></i>Jam Operasional</h5>
                <ul class="list-unstyled">
                    <li>Senin - Jumat: 08:00 - 20:00</li>
                    <li>Sabtu: 08:00 - 18:00</li>
                    <li>Minggu: 09:00 - 15:00</li>
                    <li>Libur Nasional: Tutup</li>
                </ul>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <h5 class="mb-4"><i class="fas fa-map-marker-alt me-2"></i>Kontak Kami</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-phone me-2"></i> 021-231-1234</li>
                    <li><i class="fab fa-whatsapp me-2"></i> 0812-3456-7890</li>
                    <li><i class="fas fa-envelope me-2"></i> info@jayabadi.com</li>
                    <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Puspitek Unpam Viktor No. 123, Tangsel</li>
                </ul>
            </div>
        </div>
        <hr class="bg-light">
        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0">&copy; 2024 Bengkel Mobil Jaya Abadi. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom JS -->
<script src="../assets/js/script.js"></script>

<script>
// Inisialisasi AOS
AOS.init({
    duration: 1000,
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

// Konfirmasi hapus dengan SweetAlert
function confirmDelete(url, message = 'Data akan dihapus permanen!') {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}

// Notifikasi
<?php if (isset($_SESSION['success'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?php echo $_SESSION['success']; ?>',
        timer: 3000,
        showConfirmButton: false
    });
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?php echo $_SESSION['error']; ?>',
        timer: 3000,
        showConfirmButton: false
    });
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

</body>
</html>
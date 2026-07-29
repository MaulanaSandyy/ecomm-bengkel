<?php if (isset($title) && $title === 'Beranda'): ?>
</main>
<?php else: ?>
</div>
<?php endif; ?>

<footer class="mt-auto">
  <div class="container">
    <div class="row gy-5">
      <div class="col-lg-4 col-md-6 reveal">
        <div class="d-flex align-items-center gap-3 mb-4">
          <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--c-accent),#7c3aed);color:#fff;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-car-side"></i>
          </div>
          <h4 class="fw-bold mb-0" style="color:#fff;letter-spacing:-0.03em;">Jaya Abadi</h4>
        </div>
        <p style="font-size:0.88rem;line-height:1.7;max-width:320px;">
          Bengkel mobil profesional dengan teknisi berpengalaman. Melayani service rutin, perbaikan mesin, dan sparepart original.
        </p>
        <div class="d-flex gap-2 mt-4">
          <a href="#" class="slink"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="slink"><i class="fab fa-instagram"></i></a>
          <a href="#" class="slink"><i class="fab fa-whatsapp"></i></a>
          <a href="#" class="slink"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 reveal reveal-d1">
        <h5 class="fw-bold mb-4" style="color:#fff;">Jam Operasional</h5>
        <ul class="list-unstyled d-flex flex-column gap-3" style="font-size:0.85rem;">
          <li class="d-flex justify-content-between" style="padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.05);">
            <span><i class="far fa-calendar-alt me-2" style="color:var(--c-accent-l);"></i>Senin — Jumat</span>
            <span class="fw-medium" style="color:#fff;">08:00 — 20:00</span>
          </li>
          <li class="d-flex justify-content-between" style="padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.05);">
            <span><i class="far fa-calendar-check me-2" style="color:var(--c-accent-l);"></i>Sabtu</span>
            <span class="fw-medium" style="color:#fff;">08:00 — 18:00</span>
          </li>
          <li class="d-flex justify-content-between" style="padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.05);">
            <span><i class="far fa-calendar-minus me-2" style="color:var(--c-amber);"></i>Minggu</span>
            <span class="fw-medium" style="color:#fff;">09:00 — 15:00</span>
          </li>
          <li class="d-flex justify-content-between">
            <span><i class="far fa-calendar-xmark me-2" style="color:var(--c-rose);"></i>Libur Nasional</span>
            <span class="fw-medium" style="color:var(--c-rose);">Tutup</span>
          </li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-12 reveal reveal-d2">
        <h5 class="fw-bold mb-4" style="color:#fff;">Kontak</h5>
        <ul class="list-unstyled d-flex flex-column gap-3" style="font-size:0.85rem;">
          <li class="d-flex align-items-start gap-3">
            <div style="width:36px;height:36px;border-radius:var(--r-xs);background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:var(--c-accent-l);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="fas fa-phone" style="font-size:0.82rem;"></i>
            </div>
            <div>
              <span class="d-block" style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.1em;opacity:0.45;">Telepon</span>
              <span class="fw-medium" style="color:#fff;">021-231-1234</span>
            </div>
          </li>
          <li class="d-flex align-items-start gap-3">
            <div style="width:36px;height:36px;border-radius:var(--r-xs);background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:var(--c-green);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="fab fa-whatsapp" style="font-size:0.82rem;"></i>
            </div>
            <div>
              <span class="d-block" style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.1em;opacity:0.45;">WhatsApp</span>
              <span class="fw-medium" style="color:#fff;">0812-3456-7890</span>
            </div>
          </li>
          <li class="d-flex align-items-start gap-3">
            <div style="width:36px;height:36px;border-radius:var(--r-xs);background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:var(--c-sky);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="fas fa-envelope" style="font-size:0.82rem;"></i>
            </div>
            <div>
              <span class="d-block" style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.1em;opacity:0.45;">Email</span>
              <span class="fw-medium" style="color:#fff;">info@jayabadi.com</span>
            </div>
          </li>
          <li class="d-flex align-items-start gap-3">
            <div style="width:36px;height:36px;border-radius:var(--r-xs);background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:var(--c-rose);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="fas fa-location-dot" style="font-size:0.82rem;"></i>
            </div>
            <div>
              <span class="d-block" style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.1em;opacity:0.45;">Lokasi</span>
              <span class="fw-medium" style="color:#fff;">Jl. Puspitek Unpam Viktor No. 123, Tangsel</span>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <hr class="footer-line">

    <div class="row align-items-center pb-4">
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <p class="mb-0 small" style="opacity:0.35;">&copy; 2024 Bengkel Mobil Jaya Abadi. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <p class="mb-0 small" style="opacity:0.35;">Designed for better performance</p>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>

<script>
function showLoading(t){var e=document.getElementById('loadingSpinner'),n=document.getElementById('loadingText');if(e&&n){n.textContent=t||'Memproses...';e.classList.add('show')}}
function hideLoading(){var e=document.getElementById('loadingSpinner');if(e)e.classList.remove('show')}

function confirmDelete(u,m){Swal.fire({title:'Apakah Anda yakin?',text:m||'Data akan dihapus permanen!',icon:'warning',showCancelButton:true,confirmButtonColor:'#e11d48',cancelButtonColor:'#6b6b7b',confirmButtonText:'Ya, hapus!',cancelButtonText:'Batal',background:'#fff',borderRadius:'18px'}).then(function(r){if(r.isConfirmed)window.location.href=u});return false}

<?php if(isset($_SESSION['success'])): ?>
Swal.fire({icon:'success',title:'Berhasil!',text:'<?php echo addslashes($_SESSION['success']); ?>',timer:3000,showConfirmButton:false,background:'#fff',borderRadius:'18px',toast:true,position:'top-end'});
<?php unset($_SESSION['success']); endif; ?>
<?php if(isset($_SESSION['error'])): ?>
Swal.fire({icon:'error',title:'Gagal!',text:'<?php echo addslashes($_SESSION['error']); ?>',timer:3000,showConfirmButton:false,background:'#fff',borderRadius:'18px',toast:true,position:'top-end'});
<?php unset($_SESSION['error']); endif; ?>

// Scroll progress
(function(){var b=document.createElement('div');b.className='scroll-progress';document.body.appendChild(b);window.addEventListener('scroll',function(){var h=document.documentElement.scrollHeight-document.documentElement.clientHeight;b.style.width=h>0?(window.scrollY/h*100)+'%':'0%'},{passive:true})})();

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(function(a){a.addEventListener('click',function(e){var id=this.getAttribute('href');if(!id||id==='#')return;var t=document.querySelector(id);if(t){e.preventDefault();var n=document.querySelector('.navbar'),o=n?n.offsetHeight:80;top=t.getBoundingClientRect().top+window.pageYOffset-o-16;window.scrollTo({top:top,behavior:'smooth'});history.pushState(null,null,id)}})});

// Close mobile nav
document.querySelectorAll('.navbar-nav .nav-link').forEach(function(l){l.addEventListener('click',function(){var c=document.querySelector('.navbar-collapse'),t=document.querySelector('.navbar-toggler');if(window.innerWidth<=991.98&&c&&c.classList.contains('show'))setTimeout(function(){if(t)t.click()},80)})});
</script>

</body>
</html>

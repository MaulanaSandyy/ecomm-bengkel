/* ═══════════════════════════════════════════
   GLOBAL JS — Agency-tier interactions
   ═══════════════════════════════════════════ */

// ── Scroll Reveal (IntersectionObserver) ──
(function () {
  var els = document.querySelectorAll('.reveal');
  if (!els.length) return;

  if (!('IntersectionObserver' in window)) {
    // Fallback: show everything
    els.forEach(function (el) { el.classList.add('vis'); });
    return;
  }

  var obs = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('vis');
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });

  els.forEach(function (el) { obs.observe(el); });
})();

// ── Navbar Scroll ──
(function () {
  var nav = document.querySelector('.navbar');
  if (!nav) return;
  var onScroll = function () {
    if (window.scrollY > 50) nav.classList.add('navbar-scrolled');
    else nav.classList.remove('navbar-scrolled');
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

// ── Button Active Scale ──
document.querySelectorAll('.btn').forEach(function (b) {
  b.addEventListener('mousedown', function () { this.style.transform = 'scale(0.97)'; });
  b.addEventListener('mouseup', function () { this.style.transform = ''; });
  b.addEventListener('mouseleave', function () { this.style.transform = ''; });
});

// ── Counter Animation ──
function animateCounter(id, target, dur) {
  dur = dur || 1600;
  var el = document.getElementById(id);
  if (!el) return;
  var step = target / (dur / 16);
  var cur = 0;
  var t = setInterval(function () {
    cur += step;
    if (cur >= target) { el.textContent = target; clearInterval(t); }
    else el.textContent = Math.floor(cur);
  }, 16);
}

// ── Auto-hide Alerts ──
document.querySelectorAll('.alert').forEach(function (a) {
  setTimeout(function () {
    a.style.transition = 'all 0.35s cubic-bezier(0.16,1,0.3,1)';
    a.style.opacity = '0';
    a.style.transform = 'translateY(-8px)';
    setTimeout(function () { a.style.display = 'none'; }, 350);
  }, 5000);
});

// ── Search Table ──
function searchTable(inId, tblId) {
  var inp = document.getElementById(inId);
  var tbl = document.getElementById(tblId);
  if (!inp || !tbl) return;
  var f = inp.value.toUpperCase();
  tbl.querySelectorAll('tbody tr').forEach(function (r) {
    r.style.display = (r.textContent || '').toUpperCase().indexOf(f) > -1 ? '' : 'none';
  });
}

// ── Print ──
function printTable(id) {
  var el = document.getElementById(id);
  if (!el) return;
  var orig = document.body.innerHTML;
  document.body.innerHTML = el.outerHTML;
  window.print();
  document.body.innerHTML = orig;
  location.reload();
}

// ── Image Preview ──
function previewImage(input, pid) {
  if (input.files && input.files[0]) {
    var r = new FileReader();
    r.onload = function (e) { var img = document.getElementById(pid); if (img) img.src = e.target.result; };
    r.readAsDataURL(input.files[0]);
  }
}

// ── Rupiah Format ──
function formatRupiah(n) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
}

// ── Bootstrap Init ──
document.addEventListener('DOMContentLoaded', function () {
  if (typeof bootstrap !== 'undefined') {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) { new bootstrap.Tooltip(el); });
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) { new bootstrap.Popover(el); });
  }
});

// ── Form Validation ──
document.querySelectorAll('form.needs-validation').forEach(function (f) {
  f.addEventListener('submit', function (e) {
    if (!f.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    f.classList.add('was-validated');
  });
});

// ── Password Toggle ──
document.querySelectorAll('.toggle-password').forEach(function (b) {
  b.addEventListener('click', function () {
    var inp = this.previousElementSibling;
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
    var i = this.querySelector('i');
    if (i) { i.classList.toggle('fa-eye'); i.classList.toggle('fa-eye-slash'); }
  });
});

// ── Back to Top ──
(function () {
  var b = document.createElement('button');
  b.innerHTML = '<i class="fas fa-arrow-up"></i>';
  b.setAttribute('aria-label', 'Back to top');
  b.style.cssText = 'position:fixed;bottom:28px;right:28px;z-index:99;width:44px;height:44px;padding:0;border-radius:50%;background:var(--c-accent);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:var(--sh-md);opacity:0;transform:translateY(12px);transition:all 0.3s cubic-bezier(0.16,1,0.3,1);cursor:pointer;border:none;font-size:0.85rem;';
  document.body.appendChild(b);
  window.addEventListener('scroll', function () {
    if (window.scrollY > 350) { b.style.opacity = '1'; b.style.transform = 'translateY(0)'; }
    else { b.style.opacity = '0'; b.style.transform = 'translateY(12px)'; }
  }, { passive: true });
  b.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
})();

// JavaScript untuk website bengkel - Logika tetap sama, hanya dirapikan

// Inisialisasi AOS
AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
    offset: 50
});

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Navbar Scroll Effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    }
});

// Loading Function
function showLoading(text = 'Memproses...') {
    const spinner = document.getElementById('loadingSpinner');
    const loadingText = document.getElementById('loadingText');
    if (spinner && loadingText) {
        loadingText.innerText = text;
        spinner.classList.add('show');
    }
}

function hideLoading() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.remove('show');
    }
}

// Konfirmasi Delete dengan SweetAlert (Disesuaikan style-nya sedikit)
function confirmDelete(url, message = 'Data akan dihapus permanen!') {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444', // Tailwind red-500
        cancelButtonColor: '#64748b',  // Tailwind slate-500
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        background: '#ffffff',
        borderRadius: '24px',
        backdrop: `
            rgba(15, 23, 42, 0.4)
            url("https://sweetalert2.github.io/images/nyan-cat.gif")
            left top
            no-repeat
        `
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}

// Format Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

// Preview Image sebelum upload
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto hide alerts
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 500);
    }, 5000);
});

// Search functionality
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    if(!table) return;
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const tdArray = tr[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < tdArray.length; j++) {
            const td = tdArray[j];
            if (td) {
                const textValue = td.textContent || td.innerText;
                if (textValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        if (found) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}

// Print function
function printTable(tableId) {
    const printElement = document.getElementById(tableId);
    if(!printElement) return;
    const printContent = printElement.outerHTML;
    const originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

// Export to Excel
function exportToExcel(tableId, filename = 'data.xlsx') {
    const table = document.getElementById(tableId);
    if(table && typeof XLSX !== 'undefined') {
        const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, filename);
    }
}

// Counter Animation
function animateCounter(elementId, target, duration = 2000) {
    const element = document.getElementById(elementId);
    if(!element) return;
    const start = 0;
    const increment = target / (duration / 10);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        element.innerText = Math.floor(current);
        
        if (current >= target) {
            element.innerText = target;
            clearInterval(timer);
        }
    }, 10);
}

// Tooltip & Popover initialization (Bootstrap 5)
document.addEventListener('DOMContentLoaded', function() {
    if(typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
});

// Dynamic form validation
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});

// Password visibility toggle
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});

// Back to top button
const backToTopButton = document.createElement('button');
backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
backToTopButton.className = 'btn btn-primary back-to-top shadow-lg';
backToTopButton.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    display: none;
    z-index: 99;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
    transform: translateY(20px);
`;

document.body.appendChild(backToTopButton);

window.addEventListener('scroll', function() {
    if (window.scrollY > 300) {
        backToTopButton.style.display = 'flex';
        // Small delay for transition to work properly after display block
        setTimeout(() => {
            backToTopButton.style.opacity = '1';
            backToTopButton.style.transform = 'translateY(0)';
        }, 10);
    } else {
        backToTopButton.style.opacity = '0';
        backToTopButton.style.transform = 'translateY(20px)';
        setTimeout(() => {
            if(window.scrollY <= 300) backToTopButton.style.display = 'none';
        }, 300);
    }
});

backToTopButton.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
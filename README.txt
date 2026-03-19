<div align="center">
  
  <img src="https://cdn-icons-png.flaticon.com/512/1995/1995470.png" alt="AutoGarage Logo" width="120">

  # 🚗 AutoGarage - Modern Workshop Management System

  Sebuah sistem informasi manajemen bengkel dan *e-commerce* suku cadang berbasis Web. Dibangun dengan pendekatan UI/UX yang modern, responsif, dan interaktif untuk memberikan pengalaman terbaik bagi Admin maupun Pelanggan.

  [![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net/)
  [![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
  [![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
  [![Status](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge)](#)

</div>

---

## 📖 Tentang Projek

**AutoGarage** dirancang untuk mendigitalisasi proses bisnis bengkel otomotif. Sistem ini menggabungkan fitur **Booking Service** antrean secara *real-time* dengan **Marketplace Sparepart** dalam satu platform terpadu. Dilengkapi dengan *dashboard* analitik cerdas, nota digital (*printable*), dan integrasi pembayaran QRIS.

## ✨ Fitur Unggulan

Sistem ini membagi hak akses menjadi 2 *Role* utama dengan ekosistem antarmuka yang disesuaikan:

### 👨‍🔧 Panel Admin (Manajemen Inti)
- **Dashboard Analytics:** Pantauan statistik pendapatan, status booking, dan transaksi secara dinamis.
- **Manajemen Inventaris (Split UX):** Kelola katalog suku cadang dengan layout *split-screen* untuk *editing* cepat tanpa berpindah halaman.
- **Manajemen Layanan Jasa:** Pengaturan harga dan jenis *service* bengkel.
- **Smart Booking System:** Kontrol alur antrean (Pending ➔ Konfirmasi ➔ Selesai) terintegrasi dengan tabel teknisi.
- **Digital Invoicing:** *Generate* struk/nota transaksi ala kasir profesional yang siap dicetak (*Printable*).
- **Pengaturan Profil & QRIS:** Fleksibilitas mengubah informasi bengkel, logo, *banner*, dan *barcode* QRIS dinamis untuk pembayaran.

### 👤 Panel Customer (E-Commerce Style)
- **Interactive Dashboard:** Sapaan *user* dengan *quick-action cards* berdesain *hover-lift*.
- **Katalog Sparepart:** Tampilan *grid card* produk ala *marketplace* modern dengan indikator stok otomatis.
- **Seamless Booking:** Jadwalkan perbaikan kendaraan dari rumah tanpa antre fisik.
- **Sistem Keranjang & Checkout:** Penggabungan pembelian *sparepart* dan layanan jasa dalam satu tagihan pembayaran.
- **Riwayat Transaksi:** Lacak progres status pemesanan dan riwayat *service* kendaraan.

---

## 🛠️ Tech Stack & Library

- **Backend:** PHP Native (Custom Query Wrapper)
- **Database:** MySQL (Relational Mapping)
- **Frontend:** HTML5, CSS3, JavaScript
- **CSS Framework:** Bootstrap 5
- **Icons:** FontAwesome 5/6
- **Animations:** AOS (Animate On Scroll)

---

## 🚀 Panduan Instalasi (Local Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan *project* ini di mesin lokal Anda (menggunakan XAMPP / Laragon / MAMP):

1. **Clone Repository**
   Buka terminal/CMD Anda dan jalankan perintah berikut:
   `git clone https://github.com/username/autogarage.git`

2. **Pindahkan Folder**
   Pindahkan folder hasil *clone* (`autogarage`) ke dalam direktori *server* lokal Anda:
   - Pengguna XAMPP: Masukkan ke `C:/xampp/htdocs/autogarage`
   - Pengguna Laragon: Masukkan ke `C:/laragon/www/autogarage`

3. **Konfigurasi Database**
   - Buka *browser* dan akses `http://localhost/phpmyadmin`
   - Buat *database* baru dengan nama `db_bengkel` (atau sesuai nama *database* Anda).
   - *Import* file `db_bengkel.sql` yang terdapat di dalam folder `database/` atau *root folder* ke dalam *database* yang baru dibuat.

4. **Konfigurasi Koneksi PHP**
   Buka file `includes/koneksi.php` menggunakan *Code Editor* (VS Code) dan sesuaikan kredensial *database* Anda jika memakai *password*:
   `$host = "localhost";`
   `$user = "root";`
   `$pass = "";`
   `$db   = "db_bengkel";`

5. **Jalankan Aplikasi**
   Nyalakan modul Apache dan MySQL di XAMPP/Laragon Anda. Buka *browser* dan akses URL berikut:
   `http://localhost/autogarage`

---

## 🔐 Akun Demo (Testing)

Gunakan kredensial berikut untuk menguji coba sistem (Pastikan data ini ada di tabel `users` Anda):

| Role | Username / Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@bengkel.com` | `admin123` |
| **Customer** | `customer@gmail.com` | `customer123` |

---

## 📂 Struktur Direktori

    📦 autogarage
     ┣ 📂 admin               # Panel khusus Administrator
     ┃ ┣ 📜 index.php
     ┃ ┣ 📜 transaksi.php
     ┃ ┗ 📜 ...
     ┣ 📂 customer            # Antarmuka Pelanggan (B2C)
     ┃ ┣ 📜 index.php
     ┃ ┣ 📜 beli.php
     ┃ ┗ 📜 ...
     ┣ 📂 includes            # File konfigurasi & template parsial
     ┃ ┣ 📜 header.php
     ┃ ┣ 📜 footer.php
     ┃ ┗ 📜 koneksi.php
     ┣ 📂 uploads             # Direktori penyimpanan file dinamis (Image/QRIS)
     ┗ 📜 index.php           # Landing page utama / Login

---

## 💡 Pro Tips untuk Portofolio

- Ganti URL gambar logo di paling atas dokumen ini dengan URL *screenshot dashboard* asli buatan Anda.
- Ganti `https://github.com/username/autogarage.git` dengan *link repository* Anda yang sebenarnya.
- Jangan lupa tambahkan *link* LinkedIn atau website pribadi Anda di bagian pengembang di bawah!

---

## 👨‍💻 Pengembang

Dikembangkan dan dirancang oleh **Maulana Sandy**.
- **Role:** Web Developer / Informatics Engineering Student
- **GitHub:** [https://github.com/maulanasandy](https://github.com) *(Ganti dengan link asli)*
- **LinkedIn:** [Profil LinkedIn Anda](https://linkedin.com)

---

<div align="center">
  <p>Dibuat dengan 💻 dan ☕ untuk menyelesaikan permasalahan manajemen operasional bengkel.</p>
  <p>&copy; 2026 AutoGarage System. All Rights Reserved.</p>
</div>
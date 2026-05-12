# 🚗 Bengkel Mobil E-Commerce Platform

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Xendit](https://img.shields.io/badge/Xendit-00AAFF?style=for-the-badge&logo=credit-card&logoColor=white)

<!-- Animated Banner -->
<div align="center">

![Banner Animation](https://raw.githubusercontent.com/sindresorhus/pacursors/main/pacursor-33.gif)

# 🔥 SISTEM MANAJEMEN BENGKEL MOBIL MODERN 🔥

[![GitHub stars](https://img.shields.io/github/stars/MaulanaSandyy/ecomm-bengkel?style=social)](https://github.com/MaulanaSandyy/ecomm-bengkel/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/MaulanaSandyy/ecomm-bengkel?style=social)](https://github.com/MaulanaSandyy/ecomm-bengkel/network/members)
[![License](https://img.shields.io/github/license/MaulanaSandyy/ecomm-bengkel?style=social)](https://github.com/MaulanaSandyy/ecomm-bengkel/blob/main/LICENSE)
[![GitHub commit activity](https://img.shields.io/github/commit-activity/m/MaulanaSandyy/ecomm-bengkel?style=social)](https://github.com/MaulanaSandyy/ecomm-bengkel/commits/main)

<br>

> ⚡ **Full-stack e-commerce platform untuk bisnis bengkel mobil dengan fitur lengkap: manajemen sparepart, booking service, dan pembayaran digital.** ⚡

</div>

---

## ✨ Features / Fitur Unggulan

### 🛒 E-Commerce Features
| Fitur | Deskripsi |
|-------|-----------|
| 🛍️ **Katalog Sparepart** | Display produk dengan gambar, harga, dan stok real-time |
| 🛒 **Keranjang Belanja** | Smart cart dengan quantity control dan live update |
| 💳 **Checkout & Pembayaran** | Integrasi Payment Gateway Xendit (VA, QRIS, E-Wallet) |
| 📜 **Invoice Otomatis** | Generate invoice profesional dengan format PDF-ready |

### 🔧 Booking & Service Management
| Fitur | Deskripsi |
|-------|-----------|
| 📅 **Booking Service Online** | Customer bisa booking service via website |
| ⚙️ **Manajemen Antrian** | Status booking: Pending → Dikonfirmasi → Dikerjakan → Selesai |
| 👨‍🔧 **Mekanik** | Owner bisa assign mekanik ke setiap job |
| 📝 **Catatan Service** | Tracking detail pekerjaan dan biaya tambahan |

### 📊 Dashboard & Laporan
| Fitur | Deskripsi |
|-------|-----------|
| 📈 **Dashboard Owner** | Statistik penjualan, booking, dan pendapatan |
| 📊 **Laporan Keuangan** | Export Excel & Print laporan harian |
| 📋 **Laporan Booking** | Export & print data booking service |
| 🏆 **Top Products** | Analisis produk terlaris per periode |

### 🔐 Sistem Role-Based Access
| Role | Akses |
|------|-------|
| 👑 **Admin** | CRUD sparepart, jasa, users, verifikasi pembayaran |
| 👔 **Owner** | Dashboard, laporan keuangan, laporan booking |
| 🔧 **Pegawai** | Management service & status pengerjaan |
| 👤 **Customer** | Belanja, booking service, tracking order |

---

## 🛠️ Tech Stack

```
┌─────────────────────────────────────────────────────────────────┐
│                        FRONTEND                                  │
├─────────────────────────────────────────────────────────────────┤
│  🎨 HTML5          🚀 CSS3 (Modern + Responsive)                │
│  ⚡ JavaScript     🎭 Bootstrap 5.3                             │
│  ✨ Font Awesome   💫 AOS Animation                              │
│  📊 Chart.js      🍭 SweetAlert2                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                         BACKEND                                  │
├─────────────────────────────────────────────────────────────────┤
│  🐘 PHP 7.4+          📦 MySQL Database                        │
│  🔌 Xendit API        💰 Payment Gateway                       │
│  🛡️ Session Auth      🔒 Environment Variables (.env)           │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📦 Installation / Instalasi

### ⚡ Quick Start

```bash
# 1. Clone repository
git clone https://github.com/MaulanaSandyy/ecomm-bengkel.git
cd ecomm-bengkel

# 2. Setup Database
mysql -u root -p
CREATE DATABASE bengkel_mobil;
USE bengkel_mobil;
SOURCE bengkel_mobil.sql;

# 3. Konfigurasi Environment
cp .env.example .env
# Edit .env sesuaikan dengan konfigurasi Anda

# 4. Jalankan XAMPP/WAMP
# Letakkan project di htdocs
# Akses via browser: http://localhost/ecomm-bengkel

# 5. Login Default
# Admin   → username: admin    | password: 123
# Owner   → username: owner   | password: 123
# Pegawai → username: pegawai | password: 123
# Customer→ register via halaman register
```

### 📋 Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 7.4+ |
| MySQL | 5.7+ |
| Apache/Nginx | Latest |
| XAMPP/WAMP | Latest |

---

## 📂 Project Structure

```
ecomm-bengkel/
├── 📁 admin/               # Modul Admin
│   ├── index.php          # Dashboard admin
│   ├── sparepart.php      # Manajemen sparepart
│   ├── jasa.php          # Manajemen jasa service
│   ├── transaksi.php     # Kelola transaksi
│   ├── booking.php       # Kelola booking
│   ├── users.php         # Manajemen user
│   ├── qris.php          # Konfigurasi QRIS
│   ├── profil.php        # Profil bengkel
│   └── get_detail_transaksi.php  # Detail invoice
│
├── 📁 owner/              # Modul Owner
│   ├── index.php         # Dashboard owner
│   ├── booking.php       # Laporan & kelola booking
│   ├── laporan.php      # Laporan keuangan
│   └── print_*.php       # Print laporan
│
├── 📁 pegawai/            # Modul Pegawai
│   ├── index.php         # Dashboard pegawai
│   ├── booking.php      # Daftar booking
│   └── service.php       # Kelola service
│
├── 📁 customer/           # Modul Customer
│   ├── index.php         # Dashboard customer
│   ├── beli.php          # Belanja sparepart
│   ├── booking.php       # Booking service
│   ├── checkout.php      # Checkout
│   ├── pembayaran.php    # Pembayaran
│   └── riwayat.php       # Riwayat transaksi
│
├── 📁 includes/           # Shared Components
│   ├── koneksi.php       # Koneksi database
│   ├── env.php           # Environment loader
│   ├── header.php        # Header template
│   ├── navbar.php        # Navigation
│   └── footer.php        # Footer template
│
├── 📁 auth/               # Authentication
│   ├── login.php         # Halaman login
│   └── register.php      # Halaman register
│
├── 📁 callback/          # Payment Callbacks
│   └── xendit.php       # Xendit callback handler
│
├── 📁 uploads/           # Uploaded files
│   ├── sparepart/
│   └── jasa/
│
├── .env                  # Environment variables
├── .env.example         # Template .env
├── .gitignore          # Git ignore file
├── bengkel_mobil.sql    # Database schema
└── progress.todo        # Development progress
```

---

## 💰 Payment Integration (Xendit)

### Metode Pembayaran yang Didukung

| Metode | Status |
|--------|--------|
| 🏦 Bank BCA | ✅ Active |
| 🏦 Bank Mandiri | ✅ Active |
| 🏦 Bank BNI | ✅ Active |
| 🏦 Bank BRI | ✅ Active |
| 🏦 Bank Permata | ✅ Active |
| 📱 QRIS | ✅ Active |
| 📱 GoPay | ✅ Active |
| 📱 OVO | ✅ Active |
| 📱 Dana | ✅ Active |

---

## 🎯 User Flow

```
👤 CUSTOMER
   │
   ├─→ 🛒 Beli Sparepart
   │     └─→ 🛍️ Keranjang → 💳 Checkout → ✅ Pembayaran → 📦 Pesanan
   │
   └─→ 🔧 Booking Service
         └─→ 📅 Pilih Jadwal → 💳 Bayar → ⏳ Pending → ✅ Dikonfirmasi → 🔨 Dikerjakan → ✅ Selesai

👑 ADMIN
   └─→ ✅ Verifikasi Pembayaran → 📦 Input Resi → 🚚 Kirim

👔 OWNER
   └─→ 📊 Dashboard → 📋 Laporan → 📈 Analisis

🔧 PEGAWAI
   └─→ 🔨 Terima Job → 📝 Kerjakan → ✅ Selesai
```

---

## 🎨 Screenshots

<div align="center">

### 🏠 Homepage
![Homepage](https://via.placeholder.com/800x400?text=🏠+Homepage+Bengkel+Mobil)

### 🛒 Catalog
![Catalog](https://via.placeholder.com/800x400?text=🛒+Catalog+Sparepart)

### 📊 Dashboard Owner
![Dashboard](https://via.placeholder.com/800x400?text=📊+Dashboard+Owner)

### 📅 Booking Service
![Booking](https://via.placeholder.com/800x400?text=📅+Booking+Service)

</div>

---

## 🤝 Contributing

Kontribusi sangat diterima! Langkah:

1. 🍴 Fork repository ini
2. 🔱 Buat branch baru (`git checkout -b feature/amazing-feature`)
3. ✅ Commit perubahan (`git commit -m 'Add amazing feature'`)
4. 📤 Push ke branch (`git push origin feature/amazing-feature`)
5. 🔍 Buat Pull Request

---

## 📜 License

Distributed under the MIT License. See `LICENSE` file for more information.

---

## 🙏 Credits

| Library/Tools | Description |
|---------------|-------------|
| [Bootstrap](https://getbootstrap.com/) | CSS Framework |
| [Font Awesome](https://fontawesome.com/) | Icon Library |
| [AOS](https://michalsnik.github.io/aos/) | Scroll Animation |
| [SweetAlert2](https://sweetalert2.github.io/) | Alert Library |
| [Chart.js](https://www.chartjs.org/) | Charts Library |
| [Xendit](https://xendit.co/) | Payment Gateway |

---

## 📞 Contact / Kontak

<div align="center">

[![GitHub](https://img.shields.io/badge/GitHub-MaulanaSandyy-181717?style=for-the-badge&logo=github)](https://github.com/MaulanaSandyy)
[![Email](https://img.shields.io/badge/Email-maulanasandy.official-D14836?style=for-the-badge&logo=gmail)](mailto:maulanasandy.official@gmail.com)
[![Instagram](https://img.shields.io/badge/Instagram-@maulanasandyy-E4405F?style=for-the-badge&logo=instagram)](https://instagram.com/maulanasandyy)

</div>

---

<div align="center">

⭐ Star repo ini jika bermanfaat! ⭐

Made with ❤️ by [MaulanaSandyy](https://github.com/MaulanaSandyy)

</div>

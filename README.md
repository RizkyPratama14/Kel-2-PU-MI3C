<div align="center">

# 📸 Web Digicam — Platform Sewa Kamera Digital Modern

[![Laravel Framework](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Status Proyek](https://img.shields.io/badge/Status-Active_Development-success?style=for-the-badge)]()

*Solusi digital terpusat untuk katalog produk interaktif, pengecekan ketersediaan unit real-time, dan manajemen pemesanan sewa kamera di Digicam Padang.*

</div>

---

## 📋 Tentang Proyek

**Web Digicam** adalah sistem informasi berbasis web yang dikembangkan untuk mendigitalisasi operasional penyewaan kamera di **Digicam Padang**. Proyek ini dibuat untuk memenuhi tugas mata kuliah **Proyek Utama (ISY3215)** pada Program Studi D3 Manajemen Informatika, Jurusan Teknologi Informasi, Politeknik Negeri Padang.

Sebelum sistem ini dibangun, seluruh proses pengecekan jadwal, pencatatan transaksi, hingga rekapitulasi laporan dilakukan secara manual, yang sering kali memicu risiko bentrok jadwal (*overbooking*) dan lambatnya proses verifikasi. Dengan platform ini, seluruh alur pemesanan menjadi lebih terstruktur, transparan, dan cepat.

---

## 👥 Tim Pengembang (Kelompok 2 - MI 3C)

| No | Nama Anggota | NIM | Peran Utama |
| :---: | :--- | :---: | :--- |
| 1. | **Alivia Ananda** | `2401091022` | Database Architecture & Backend |
| 2. | **Rahmad Irwanda** | `2401091026` | UI/UX Design & Frontend |
| 3. | **Rizky Pratama** | `2401091028` | Product Owner & Backend |
| 4. | **Ririn Alifia** | `2401092025` | Frontend Slicing & Integration |
| 5. | **Tri Wulan Dari Septia Anantha** | `2401093014` | Frontend Slicing & Quality Control |

* **Client / Project Sponsor:** Cinta Gusnaini (Owner Digicam Padang)
* **Institusi:** Politeknik Negeri Padang

---

## 🚀 Fitur Utama Sistem

### 👤 Untuk Penyewa / Pelanggan
* **Katalog & Price List Interaktif:** Menampilkan daftar kamera lengkap dengan spesifikasi, foto, dan harga sewa secara transparan.
* **Cek Ketersediaan Real-time:** Memeriksa ketersediaan unit kamera berdasarkan jam, hari, bulan, dan tahun secara akurat untuk menghindari jadwal bentrok.
* **Sistem Booking & Integrasi WhatsApp:** Formulir pemesanan terstruktur yang langsung terhubung ke konfirmasi WhatsApp admin.
* **Informasi & Aturan Sewa:** Panduan syarat ketentuan sewa, profil toko, dan tautan media sosial resmi.
* **Testimoni & Ulasan:** Halaman testimoni serta kotak saran/kritik untuk meningkatkan *social proof*.

### 🛠️ Untuk Admin & Owner
* **Manajemen Unit & Ketersediaan:** Panel admin untuk menambah foto kamera, memperbarui detail, dan mengatur status ketersediaan unit.
* **Dashboard Grafik Bulanan:** Visualisasi data penyewaan untuk analisis performa bisnis oleh Owner.
* **Rekapitulasi Laporan Otomatis:** Ekspor data transaksi dan penyewaan ke dalam format **PDF** atau **Excel** hanya dalam sekali klik.

---

## 📈 Business Value (Manfaat Bisnis)

* **Tangible (Terukur):**
  * Memangkas waktu pemesanan dan verifikasi data penyewa dari rata-rata **15 menit menjadi kurang dari 3 menit**.
  * Mempercepat pembuatan rekapan laporan bulanan dari **2 hari kerja menjadi sekali klik (unduh PDF/Excel)**.
* **Intangible (Kualitatif):**
  * Meningkatkan transparansi aturan sewa, harga, dan kepercayaan pelanggan melalui fitur testimoni.
  * Menghilangkan risiko kesalahan manusia (*human error*) berupa *overbooking* atau jadwal bentrok.

---

## ⚙️ Kebutuhan Non-Fungsional

* **Operational:** Responsif dan kompatibel di berbagai *browser* modern (Chrome, Safari, Edge) baik di perangkat desktop maupun *smartphone*.
* **Performance:** Waktu muat halaman kurang dari 3 detik dengan dukungan hingga 50 pengguna bersamaan.
* **Security:** Halaman panel admin dilindungi autentikasi kata sandi terenkripsi serta validasi input untuk mencegah injeksi data.
* **Cultural / Political:** Menggunakan Bahasa Indonesia yang ramah pengguna (*user-friendly*) dengan komitmen penuh menjaga kerahasiaan data pribadi penyewa.

---

## 🛠️ Tech Stack

Proyek ini dibangun menggunakan teknologi pilihan yang handal dan modular:
* **Backend:** PHP / Laravel Framework
* **Frontend:** Blade Templates / Tailwind CSS / JavaScript
* **Database:** MySQL
* **Version Control:** Git & GitHub

---

## 📦 Ruang Lingkup Proyek (Scope)

| Termasuk Dalam Proyek (In-Scope) | Tidak Termasuk (Out-of-Scope) |
| :--- | :--- |
| ✅ System Login dan Register Untuk Penyewa | |
| ✅ Katalog Kamera, Price List, & Jadwal Ketersediaan | ❌ Aplikasi Mobile Native (Android / iOS) |
| ✅ Pemesanan Digital & Integrasi WhatsApp | ❌ Pelacakan Lokasi GPS Unit Kamera Sewa |
| ✅ Dashboard Admin, Ekspor PDF/Excel, & Grafik Bulanan | |
| ✅ Halaman Testimoni & Kritik/Saran | |
| ✅ Sistem Booking & Manajemen Unit | |

---

## 💻 Panduan Instalasi Lokal (Developer Guide)

Jika Anda ingin menjalankan proyek ini di komputer lokal, ikuti langkah-langkah berikut:

1. **Clone Repository ini:**
   ```bash
   git clone https://github.com/username/web-digicam.git
   cd web-digicam
   ```

2. **Install Dependensi PHP:**
   ```bash
   composer install
   ```

3. **Install Dependensi Node.js:**
   ```bash
   npm install
   npm run dev
   ```

4. **Konfigurasi Environment:**
   Salin file konfigurasi environment dan sesuaikan pengaturan database Anda:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Jalankan Migrasi Database:**
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Akses aplikasi melalui browser di `http://127.0.0.1:8000`.

---

<div align="center">
  <p>Dibuat dengan ❤️ oleh Tim NEXUS — D3 Manajemen Informatika Politeknik Negeri Padang</p>
</div>

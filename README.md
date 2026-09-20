# Sistem Peminjaman & Inventaris Lab TKJ

Platform tata kelola dan peminjaman alat laboratorium terpadu berbasis Laravel, Tailwind CSS, Alpine.js, dan Vite.

---

## Fitur Utama

- **Peminjaman Mandiri (Self-Service)**: Pengajuan izin peminjaman alat praktikum secara langsung melalui katalog online.
- **Pemindaian QR Code Cepat**: Fitur pemindai QR Code untuk identifikasi instan spesifikasi, kondisi, dan status inventaris alat di meja lab.
- **Manajemen Inventaris & Kondisi Unit**: Pencatatan unit barang secara detail (baik, rusak ringan, rusak berat), pemisahan unit, dan riwayat pemeliharaan (*Maintenance*).
- **Multi-Role User Access**: Hak akses berbasis peran (Administrator, Guru, Siswa, Kepala Lab, dan Koordinator Lab).
- **Laporan & Ekspor Data**: Rekapitulasi peminjaman dan log pemeliharaan dalam format PDF dan JSON.
- **Asisten Lab Virtual (Live AI)**: Modul bantuan interaktif terintegrasi untuk tanya jawab prosedur dan panduan perangkat.
- **Responsif & Dual Theme**: Tampilan antarmuka modern yang ramah perangkat seluler (*mobile-first*) dengan dukungan mode terang (*Light Mode*) dan gelap (*Dark Mode*).

---

## Persyaratan Sistem

- **PHP**: ^8.1
- **Composer**: ^2.0
- **Node.js**: ^18.0 & NPM
- **Database**: MySQL / MariaDB

---

## Panduan Instalasi & Menjalankan Sistem

1. **Clone repositori dan pasang dependensi backend:**
   ```bash
   composer install
   ```

2. **Pasang dependensi frontend:**
   ```bash
   npm install
   ```

3. **Konfigurasi Environment:**
   Salin `.env.example` ke `.env` dan sesuaikan koneksi database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeder:**
   ```bash
   php artisan migrate --seed
   ```

5. **Kompilasi Aset Frontend:**
   ```bash
   npm run build
   # atau untuk mode pengembangan:
   npm run dev
   ```

6. **Jalankan Web Server:**
   ```bash
   php artisan serve
   ```

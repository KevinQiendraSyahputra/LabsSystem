DOKUMENTASI DAN REGISTER INVENTARIS CODEBASE

ATURAN KERJA PENGEMBANGAN
1. Baca file ini terlebih dahulu sebelum mencari file, route, atau controller.
2. Setiap ada penambahan migrasi, service, model, atau perombakan route, langsung perbarui file ini.
3. Koding PHP dan Blade ditulis bersih, tanpa komentar yang tidak esensial.
4. Nama method, variabel, dan controller harus ringkas, jelas, mengikuti standar PSR-12 dan Laravel conventions.
5. Format antarmuka teks polos profesional tanpa emotikon atau emoji Unicode.
6. Seluruh eksekusi migrasi, seeding, dan optimasi cache dijalankan melalui Artisan CLI (`php artisan ...`).

INFORMASI ARSITEKTUR SISTEM & MODUL UTAMA

1. Stack Teknologi:
   - Bahasa: PHP 8.2+
   - Framework: Laravel 11.x
   - Frontend: Blade Templates + Tailwind CSS (Vite build)
   - Database: MySQL / PostgreSQL (didukung Eloquent ORM)
   - Queue / Cache: Redis / Database Driver

2. Navigasi Sidebar dan Modul UI:
   - Dashboard: Statistik total aset, barang masuk, barang keluar, dan peringatan stok menipis.
   - Master Data: Kategori Barang, Lokasi Gudang/Ruangan, Satuan Barang, Vendor/Supplier.
   - Manajemen Inventaris: Daftar Barang, Cetak Barcode/QR Code, Riwayat Mutasi.
   - Transaksi: Barang Masuk (Restock), Barang Keluar (Pengeluaran), Peminjaman & Pengembalian Aset.
   - Laporan & Audit: Stock Opname, Ekspor Laporan PDF/Excel, Log Aktivitas Pengguna.
   - Pengaturan: Hak Akses (Role & Permissions), Pengaturan Profil, Backup Database.

3. Standar Penanganan Transaksi Stok & Integritas:
   - Setiap mutasi kuantitas barang wajib dibungkus dalam `DB::transaction(function () { ... })`.
   - Update stok dilakukan secara atomic melalui method Eloquent (`increment` / `decrement`) atau via InventoryService khusus.
   - Riwayat pencatatan log transaksi menggunakan Model Observer (`ItemObserver`, `TransactionObserver`).

REGISTER FILE, ROUTE, MODEL, DAN SERVICE DETAIL

A. MODELS & DATABASE MIGRATIONS (app/Models/ & database/migrations/)

Path: app/Models/Item.php
Kegunaan: Model utama data inventaris (nama barang, SKU/kode barang, stok, kategori_id, lokasi_id).
Relasi: BelongsTo Category, BelongsTo Location, HasMany ItemMovement.

Path: app/Models/Category.php
Kegunaan: Pengelompokan jenis barang inventaris.
Relasi: HasMany Item.

Path: app/Models/ItemMovement.php
Kegunaan: Pencatatan mutasi riwayat pergerakan keluar/masuk/opname stok secara real-time.
Relasi: BelongsTo Item, BelongsTo User.

Path: database/migrations/xxxx_xx_xx_create_items_table.php
Kegunaan: Skema tabel aset barang, penomoran kode unik barang (indexed), kuantitas, minimum_stock threshold, dan status aset.

B. CONTROLLERS & FORM REQUESTS (app/Http/Controllers/ & app/Http/Requests/)

Path: app/Http/Controllers/DashboardController.php
Method: index()
Kegunaan: Menghitung KPI dashboard (total valuasi aset, stok kritis, aktivitas terbaru).

Path: app/Http/Controllers/ItemController.php
Method: index, create, store, edit, update, destroy, printBarcode
Kegunaan: Manajemen data aset inventaris lengkap dengan validasi stok dan filter pencarian.

Path: app/Http/Requests/StoreItemRequest.php & UpdateItemRequest.php
Kegunaan: Validasi data input form inventaris (SKU unik, tipe data kuantitas, format nominal harga, dimensi).

C. BUSINESS LOGIC & SERVICES (app/Services/)

Path: app/Services/InventoryTransactionService.php
Kegunaan: Menangani transaksi pergerakan barang (stok masuk, keluar, penyesuaian opname) di dalam DB Transaction, validasi ketersediaan stok sebelum checkout, dan dispatching event notifikasi stok kritis.

D. BLADE TEMPLATES & UI COMPONENTS (resources/views/)

Path: resources/views/layouts/app.blade.php
Kegunaan: Master layout Blade dengan sidebar navigasi responsif, header status, dan integrasi Tailwind Vite.

Path: resources/views/items/index.blade.php
Kegunaan: Tampilan data tabel barang inventaris, filter kategori dinamis, badge status ketersediaan, dan pagination. Mengikuti aturan vertical spacing kelipatan 4px dan container kelipatan 8px.

Path: resources/views/items/form.blade.php
Kegunaan: Form input penambahan dan pengeditan data barang tanpa auto-cropping horizontal/vertikal.
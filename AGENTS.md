# UI, Spacing & Layout Rules — Laravel & PHP Edition

Pedoman arsitektur kode, penulisan antarmuka Blade/Tailwind, dan standarisasi sistem untuk Inventaris Codebase:

## 1. Anti-Cropping & Container Auto-Resize (Wajib Mutlak)
- **Dilarang Keras Memotong Elemen Vertikal (Crop Height)**:
  - Dilarang memberi fixed height sempit (misal `h-32`, `h-48`) pada card data inventaris atau container form input dinamis.
  - Gunakan tinggi fleksibel alami: `h-auto` dengan padding vertikal (`p-4`, `p-6`). Konten di dalamnya harus bernapas lega mengikuti tinggi dinamis elemen anak.
- **Dilarang Nested Scrollbar**:
  - Hindari membungkus card kecil di dalam card lain dengan `overflow-y-scroll` ganda. Cukup sediakan satu scrollbar utama pada viewport halaman dashboard (`main` content area). Tabel inventaris data besar wajib menggunakan pagination Laravel standar (`->paginate()`), bukan card sempit dengan scrollbar mikro.
- **Anti-Cropping Horizontal & Responsivitas**:
  - Dilarang membiarkan tombol aksi tabel (Edit, Hapus, Detail) terpotong di layar tablet/mobile.
  - Gunakan `flex-wrap`, `break-words`, dan responsive wrapper `overflow-x-auto` khusus untuk tag `<table>`.

## 2. Jarak ke Bawah (Vertical Spacing) — Kelipatan 4px
- Semua margin vertikal (`mb-*`, `my-*`) dan padding vertikal (`py-*`) wajib kelipatan 4px.
- Gunakan skala: `4px` (`1`), `8px` (`2`), `12px` (`3`), `16px` (`4`), `20px` (`5`), `24px` (`6`), `32px` (`8`).

## 3. Spacing Elemen Utama — Kelipatan 8px
- Semua padding card, dialog modal, dan gap layout grid wajib kelipatan 8px:
  - `p-2` (8px), `p-4` (16px), `p-6` (24px), `p-8` (32px).
  - `gap-2` (8px), `gap-4` (16px), `gap-6` (24px), `gap-8` (32px).

## 4. Larangan Emotikon & Emoji Grafis (Wajib Mutlak)
- **Dilarang keras menggunakan emoji grafis Unicode** (seperti 🚀, 📦, ⚠️, 🔒, 🔥, dll.) di dalam antarmuka UI, teks notifikasi flash session, validasi form request, response JSON API, maupun pesan log sistem.
- Gunakan bahasa profesional, bersih, lugas, dan formal.
- Untuk kebutuhan ikon visual, gunakan library ikon SVG resmi (seperti Blade UI Kit, Heroicons, atau FontAwesome SVG components).

## 5. Larangan Menyebutkan Brand Luar (Standar Production)
- **Dilarang keras menampilkan nama modul eksternal, library scraping pihak ketiga, atau brand referensi** di teks antarmuka publik, dialog konfirmasi, maupun notifikasi toast.
- **Seluruh fitur adalah produk resmi dan fitur internal Inventaris** (contoh: *Inventaris Asset Tracker*, *Inventaris Stock Monitor*, *Inventaris Barcode Management*, *Inventaris Audit Engine*).

## 6. Modifikasi Kode Terarah (Targeted / Incremental Upgrades)
- **Dilarang menimpa (overwrite) file class atau controller secara membabi buta**.
- Setiap penambahan logika harus dilakukan secara modular pada Controller, Model Eloquent, Service Class, Form Request, atau Blade Component terkait.
- Seluruh method yang sudah berjalan stabil, validation rules, dan relasi database lama **wajib dipertahankan 100% tanpa breaking changes**.

## 7. Perlindungan Struktur Model & Migrasi Database
- **Dilarang keras mengubah migration lama yang sudah berjalan di production**:
  - Setiap perubahan skema tabel wajib dibuat melalui file migrasi baru (`php artisan make:migration add_columns_to_table`).
  - Raw SQL query dilarang digunakan jika masih bisa dihandle oleh Eloquent ORM atau Query Builder demi proteksi SQL Injection dan konsistensi tipe data.
- **Arsitektur Standar Laravel**:
  - Logika bisnis ditempatkan pada **Services / Actions**, bukan menumpuk ribuan baris di controller tunggal.
  - Validasi form wajib menggunakan Form Request (`app/Http/Requests`).

## 8. Eksekusi Terstruktur & Analisis Root-Cause
- Setiap modifikasi alur sistem inventory (stok masuk, stok keluar, opname) wajib dianalisis dampaknya terhadap integritas database transaction (`DB::transaction`).
- Hindari loop trial-and-error tanpa memahami relasi model dan flow lifecycle Laravel.
# UI, Spacing & Layout Rules — Inventaris Platform (Laravel + PHP)

Instruksi khusus AI untuk pengembangan sistem Inventaris berbasis PHP / Laravel:

## 1. Anti-Cropping & Container Auto-Resize (Wajib Mutlak)
- **Card Fleksibel (Dynamic Vertical Sizing)**:
  - Setiap card ringkasan inventaris, panel metrik, dan tabel wajib menggunakan `h-auto` dengan padding standar (`p-4` atau `p-6`). Dilarang membatasi tinggi card dengan fixed pixel atau overflow hidden yang memotong konten.
- **Single Page Scroll Hierarchy**:
  - Hindari scrollbar bertingkat. Viewport utama dikontrol oleh master layout (`resources/views/layouts/app.blade.php`).
- **Responsive Layout & No Horizontal Clipping**:
  - Seluruh layout dashboard inventaris wajib responsif (`w-full`, `max-w-7xl mx-auto`).
  - Bungkus tabel data barang dengan `<div class="overflow-x-auto">` agar tidak merusak layout saat dibuka pada resolusi kecil.

## 2. Jarak ke Bawah (Vertical Spacing) — Kelipatan 4px
- Wajib mematuhi skala Tailwind kelipatan 4px:
  - `4px` (`space-y-1`, `mb-1`, `py-1`) : Label & bantuan teks
  - `8px` (`space-y-2`, `mb-2`, `py-2`) : Antar form control / baris ringkas
  - `12px` (`space-y-3`, `mb-3`, `py-3`) : Separator spacing
  - `16px` (`space-y-4`, `mb-4`, `py-4`) : Antar form group input
  - `24px` (`space-y-6`, `mb-6`, `py-6`) : Pemisah antar sub-section form
  - `32px` (`space-y-8`, `mb-8`, `py-8`) : Jarak antar blok kartu utama

## 3. Spacing Elemen Utama — Kelipatan 8px
- Container utama dan grid gap wajib kelipatan 8px:
  - `p-2` (8px), `p-4` (16px), `p-6` (24px), `p-8` (32px).
  - `gap-4` (16px), `gap-6` (24px), `gap-8` (32px).

## 4. Larangan Emotikon & Emoji Grafis (Wajib Mutlak)
- Dilarang keras menyisipkan emoji grafis Unicode di dalam teks UI, alert pesan sukses/gagal, session flashdata, modal konfirmasi, atau API response JSON.
- Gunakan bahasa baku Indonesia/Inggris yang profesional.
- Gunakan Blade SVG icon (Heroicons atau Lucide) untuk indikator status barang.

## 5. Standar Penamaan Internal Inventaris
- Seluruh modul, sub-sistem, dan menu wajib menggunakan penamaan resmi internal **Inventaris** (contoh: *Inventaris Core*, *Inventaris Stock Management*, *Inventaris Transaction Report*). Dilarang menyematkan nama package atau referensi eksternal.

## 6. Prinsip Modifikasi PHP/Laravel Terarah
- Modifikasi kode wajib presisi. Jangan menghapus atau menulis ulang controller secara utuh jika hanya perlu memperbaiki satu method.
- Selalu pisahkan concerns: Route (`routes/web.php`), Controller (`app/Http/Controllers`), Form Request (`app/Http/Requests`), Model Eloquent (`app/Models`), dan Tampilan (`resources/views`).

## 7. Database Integrity & Atomic Actions
- Operasi manipulasi stok barang (pengurangan, penambahan, mutasi gudang) **wajib dibungkus dengan `DB::transaction()`** untuk mencegah inkonsistensi data / race condition.
- Wajib menerapkan validasi ketat pada request input sebelum memproses database.
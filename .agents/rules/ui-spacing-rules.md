# UI & Spacing Rules

Aturan layout, spacing, dan container sizing untuk seluruh UI proyek berbasis Laravel Blade dan Tailwind CSS:

## 1. Jarak ke Bawah (Vertical Spacing) — Kelipatan 4px
- Semua jarak vertikal, margin bawah (`mb-*`), dan padding vertikal (`py-*`) antar elemen **wajib** menggunakan kelipatan **4px**.
- Skala ukuran vertikal yang diizinkan (Tailwind CSS standard):
  - `4px` (`space-y-1`, `py-1`, `my-1`) : Micro spacing (jarak antara label & sub-label, padding internal badge)
  - `8px` (`space-y-2`, `py-2`, `my-2`) : Compact vertical spacing (jarak antara input field & button, antar baris kontrol form)
  - `12px` (`space-y-3`, `py-3`, `my-3`) : Medium vertical spacing (jarak sebelum/sesudah garis separator `<hr>`)
  - `16px` (`space-y-4`, `py-4`, `my-4`) : Standard vertical spacing (jarak antar form group, card/section kecil)
  - `20px` (`space-y-5`, `py-5`, `my-5`) : Large vertical spacing
  - `24px` (`space-y-6`, `py-6`, `my-6`) : Section divider spacing
  - `32px` (`space-y-8`, `py-8`, `my-8`) : Major component break
- Penerapan pada Blade Template:
  - Gunakan utilitas Tailwind: `space-y-2`, `space-y-4`, `mb-4`, `py-2`.
  - Dilarang menggunakan nilai sembarang arbitrary class yang melanggar grid 4px (seperti `mb-[3px]`, `py-[7px]`, `h-[11px]`).

## 2. Spacing Elemen Utama (Main Component & Container Spacing) — Kelipatan 8px
- Semua container utama, padding card, layout grid, dan jarak antar section **wajib** menggunakan kelipatan **8px**.
- Skala ukuran elemen utama yang diizinkan:
  - `8px` (`p-2`, `gap-2`) : Spacing dasar antar elemen dalam satu card / grup tombol
  - `16px` (`p-4`, `gap-4`) : Standard container padding, modal/dialog inner padding, gap antar card utama
  - `24px` (`p-6`, `gap-6`) : Large container padding, card header-to-body distance
  - `32px` (`p-8`, `gap-8`) : Outer page wrapper margin, padding layout dashboard utama
  - `40px` (`p-10`, `gap-10`) : Screen boundary safe zone
  - `48px` (`p-12`, `gap-12`) : Hero/Banner container separation
- Aturan Grid & Flexbox:
  - Gunakan `gap-4` (16px), `gap-6` (24px), atau `gap-8` (32px) pada container grid `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3`.
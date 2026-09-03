# UI & Spacing Rules

Aturan layout dan spacing desain untuk seluruh UI proyek (PHP Laravel / Vercel, dan Browser Extension):

## 1. Jarak ke Bawah (Vertical Spacing) — Kelipatan 4px
- Semua jarak vertikal / margin bawah / padding vertikal antar elemen **wajib** menggunakan kelipatan **4px**.
- Skala ukuran vertikal yang diizinkan:
  - `4px` (`4.0f`) : Micro spacing (jarak antara label & sub-label, jarak internal badge)
  - `8px` (`8.0f`) : Compact vertical spacing (jarak antara input field & button, antar baris kontrol)
  - `12px` (`12.0f`) : Medium vertical spacing (jarak sebelum/sesudah separator)
  - `16px` (`16.0f`) : Standard vertical spacing (jarak antar card/section kecil)
  - `20px` (`20.0f`) : Large vertical spacing
  - `24px` (`24.0f`) : Section divider spacing
  - `32px` (`32.0f`) : Major component break
- Implementasi Native C++ (ImGui):
  - Gunakan `ImGui::Dummy(ImVec2(0.0f, 4.0f))`, `ImGui::Dummy(ImVec2(0.0f, 8.0f))`, `ImGui::Dummy(ImVec2(0.0f, 12.0f))`, `ImGui::Dummy(ImVec2(0.0f, 16.0f))`, `ImGui::Dummy(ImVec2(0.0f, 24.0f))`.
  - Dilarang menggunakan nilai sembarang yang tidak sesuai grid 4px (seperti 3px, 5px, 6px, 7px, 9px, 10px, 11px, 13px, 15px).

## 2. Spacing Elemen Utama (Main Component & Container Spacing) — Kelipatan 8px
- Semua container utama, window padding, layout grid, dan jarak antar komponen utama **wajib** menggunakan kelipatan **8px**.
- Skala ukuran elemen utama yang diizinkan:
  - `8px` (`8.0f`) : Spacing dasar antar elemen dalam satu grup / card
  - `16px` (`16.0f`) : Standard container padding, modal/dialog inner padding, gap antar card utama
  - `24px` (`24.0f`) : Large container padding, header-to-content distance
  - `32px` (`32.0f`) : Page/Screen outer margin, padding layout dashboard utama
  - `40px` (`40.0f`) : Screen boundary safe zone
  - `48px` (`48.0f`) : Hero/Banner container separation
- Implementasi Native C++ (ImGui):
  - `style.WindowPadding = ImVec2(16.0f, 16.0f)` / `ImVec2(24.0f, 24.0f)`
  - `style.ItemSpacing = ImVec2(16.0f, 8.0f)` / `ImVec2(8.0f, 8.0f)`
  - Jarak antar card/kolom utama: `8.0f`, `16.0f`, `24.0f`, `32.0f`
- Implementasi Web & Browser Extension (CSS / Tailwind):
  - Gunakan kelas spacing Tailwind kelipatan 8px (`p-2` = 8px, `p-4` = 16px, `p-6` = 24px, `p-8` = 32px, `gap-2`, `gap-4`, `gap-6`, `gap-8`).
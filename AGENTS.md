# UI, Spacing & Layout Rules

Aturan layout, container sizing, dan spacing desain untuk seluruh UI proyek (Native C++ ImGui, Web / Vercel, dan Browser Extension):

## 1. Anti-Cropping & Container Auto-Resize (Wajib Mutlak)
- **Dilarang keras memotong elemen secara vertikal (Crop Size Y)**:
  - Dilarang memberi fixed height sempit pada card konfigurasi yang berisi kontrol bertingkat.
  - Untuk setiap card konfigurasi/pengaturan, **wajib** menggunakan mode Auto-Resize vertikal:
    `ImGui::BeginChild("card_name", ImVec2(-1, 0.0f), ImGuiChildFlags_Borders | ImGuiChildFlags_AutoResizeY, ImGuiWindowFlags_NoScrollbar | ImGuiWindowFlags_NoScrollWithMouse);`
  - Seluruh elemen kontrol di dalamnya harus dapat bernapas lega dengan padding vertikal kelipatan 4px/8px.
- **Dilarang Nested Scrollframe (Scrollframe di dalam Scrollframe)**:
  - Container card harus mengembang secara alami (AutoResizeY) mengikuti kontennya, sehingga hanya ada 1 scrollbar utama di halaman/tab (kecuali untuk listbox multi-select khusus dengan item ratusan baris).
- **Anti-Cropping Horizontal (Lebar Kanan)**:
  - Dilarang keras membiarkan tombol atau input terpotong di sebelah kanan window.
  - Jika satu baris kontrol/tombol melebihi lebar kontainer yang tersedia (`GetContentRegionAvail().x`), **wajib** dipindahkan ke baris baru di bawahnya (`ImGui::Dummy(ImVec2(0.0f, 4.0f));`), bukan dipaksa `SameLine` sampai terpotong.

## 2. Jarak ke Bawah (Vertical Spacing) — Kelipatan 4px
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

## 3. Spacing Elemen Utama (Main Component & Container Spacing) — Kelipatan 8px
- Semua container utama, window padding, layout grid, dan jarak antar komponen utama **wajib** menggunakan kelipatan **8px**.
- Skala ukuran elemen utama yang diizinkan:
  - `8px` (`8.0f`) : Spacing dasar antar elemen dalam satu grup / card
  - `16px` (`16.0f`) : Standard container padding, modal/dialog inner padding, gap antar card utama
  - `24px` (`24.0f`) : Large container padding, header-to-content distance
  - `32px` (`32.0f`) : Page/Screen outer margin, padding layout dashboard utama
  - `40px` (`40.0f`) : Screen boundary safe zone
  - `48px` (`48.0f`) : Hero/Banner container separation
## 4. Larangan Emotikon & Emoji Grafis (Wajib Mutlak)
- **Dilarang keras menggunakan emotikon atau karakter emoji grafis Unicode** (seperti emoji roket, perisai, mahkota, sirine/alarm, petir, kotak/paket, permata, api, stop, komputer, grafik, dll.) di dalam seluruh teks antarmuka (UI), dialog pesan, toast notification, status loading, log sistem, webhook payload, maupun respon percakapan kepada pengguna.
- Gunakan bahasa profesional, bersih, lugas, dan formal.
- Untuk ikon visual native pada tombol atau header ImGui C++, gunakan ikon resmi dari font glyph FontAwesome (`ICON_FA_*`), jangan pernah mencampur dengan emoji grafis Unicode.

## 5. Larangan Menyebutkan Nama Brand / Repositori Luar di Seluruh UI & Teks (Wajib Mutlak - Standar Production)
- **Dilarang keras menampilkan nama proyek referensi, repositori GitHub eksternal, atau brand pihak ketiga** (seperti `OrbitBot`, `sms-parser-android`, `adorsys`, `OP AutoClicker`, `PyMacroRecord`, `Klick'r`, `ZClicker`, `Smart-AutoClicker`, `WinputManager`, dll.) di dalam:
  - Seluruh teks antarmuka (UI), judul kartu (card header), sub-judul/deskripsi, tombol, modal dialog, status badge, maupun log sistem.
  - Dokumentasi user-facing dan respon percakapan kepada pengguna.
- **Seluruh fitur adalah produk resmi dan fitur internal HeLLM** dan wajib menggunakan penamaan native profesional HeLLM (contoh: *HeLLM WhatsApp Automation Studio*, *HeLLM Smart Pattern Extractor*, *HeLLM Ultra-Fast Clicker Engine*, *HeLLM Macro Action Sequencer*).

## 6. Prinsip Modifikasi Kode Terarah (Targeted / Incremental Upgrades - Dilarang Menimpa File Utuh)
- **Dilarang keras menimpa (overwrite/re-dump) seluruh isi file kode sumber atau menghapus fitur yang sudah ada** saat melakukan upgrade atau perbaikan bug.
- Setiap modifikasi kode **wajib dilakukan secara inkremental, terfokus, dan presisi** hanya pada fungsi (*function*), *struct*, atau blok komponen yang bersangkutan.
- Seluruh struktur yang sudah stabil, tata letak UI yang sudah disetujui, dan fitur-fitur sebelumnya **wajib dipertahankan 100% tanpa regresi**.

## 7. Perlindungan Mutlak Struktur File Besar & Larangan Pemotongan Baris Kode (Zero-Truncation Guarantee)
- **Dilarang keras memotong atau mengurangi baris kode file monolitik (`main.cpp`, dll.)**:
  - File sumber utama memiliki puluhan ribu baris kode fitur lengkap (25.000+ baris).
  - Setiap modifikasi **wajib mempertahankan seluruh fungsi yang sudah ada**. Dilarang keras merekonstruksi file hanya dari potongan sebagian (partial dump).
  - Skrip pengubah file wajib memverifikasi bahwa total baris kode tidak mengalami penyusutan abnormal sebelum menyimpan perubahan.

## 8. Eksekusi Terstruktur, Analisis Mendalam & Larangan Eksekusi Buta (No Blind Trial-and-Error)
- Setiap tindakan harus diawali analisis akar masalah (root-cause) yang jelas dan tuntas.
- Modifikasi harus dirancang dalam 1 perencanaan yang matang dan presisi sebelum dieksekusi, tanpa looping/trial-error tanpa arah yang membuang kuota/token.



# knowledge_base.py
# Library Pengetahuan Lengkap Asisten Lab TKJ (Winshark Community)

KNOWLEDGE_BASE = [
    # =========================================================================
    # 1. GREETINGS, IDENTITAS & CHIT-CHAT UMUM
    # =========================================================================
    {
        "intent": "greeting",
        "keywords": ["halo", "hai", "hello", "hei", "pagi", "siang", "sore", "malam", "assalamualaikum", "ass", "bot", "tes", "ping", "p"],
        "questions": [
            "halo asisten", "selamat pagi", "hai bot lab", "apakah ada orang?",
            "assalamualaikum min", "halo apa kabar?", "ping", "tes bot"
        ],
        "response": "Halo! 👋 Saya <strong>Asisten Lab Virtual</strong> Laboratorium TKJ Winshark. Ada yang bisa saya bantu terkait peminjaman alat, informasi praktikum, atau bantuan teknis hari ini?"
    },
    {
        "intent": "identity",
        "keywords": ["siapa kamu", "kamu siapa", "bot apa", "nama kamu", "asisten apa", "tentang bot", "siapa buat", "developer"],
        "questions": [
            "siapa namamu?", "kamu bot apa?", "apa fungsi asisten ini?",
            "siapa yang membuat bot ini?", "tentang sistem peminjaman lab"
        ],
        "response": "Saya adalah <strong>Asisten Virtual Lab TKJ</strong> berbasis AI. Tugas saya adalah membantu siswa, koordinator, dan guru dalam memperoleh informasi cepat mengenai katalog alat, status peminjaman, panduan teknis jaringan, dan operasional laboratorium."
    },
    {
        "intent": "thank_you",
        "keywords": ["makasih", "terima kasih", "thanks", "tengkyu", "ok makasih", "siap min", "mantap", "nuhun", "syukron"],
        "questions": [
            "terima kasih banyak infonya", "makasih ya bot", "ok paham makasih",
            "sangat membantu terima kasih", "mantap infonya min"
        ],
        "response": "Sama-sama! 😊 Senang bisa membantu kelancaran praktikum Anda. Jika butuh bantuan seputar alat lab lainnya, jangan ragu untuk bertanya lagi ya!"
    },
    {
        "intent": "kemampuan_bot",
        "keywords": ["bisa apa", "fitur bot", "menu apa", "fungsi kamu", "bantuan apa", "list perintah"],
        "questions": [
            "kamu bisa bantu apa saja?", "apa saja fitur yang bisa ditanyakan?",
            "bagaimana cara menggunakan chatbot ini?", "apa saja topik yang tersedia?"
        ],
        "response": "🤖 <strong>Saya dapat membantu menjawab:</strong><br>• 📦 <strong>Peminjaman & Pengembalian:</strong> Alur pinjam, batas durasi, syarat, cek status, dan denda/sanksi.<br>• 🛠️ <strong>Katalog Alat:</strong> MikroTik, Cisco, Fiber Optic, Crimping, Kabel UTP, Switch, Access Point, Server.<br>• 📷 <strong>Fitur Sistem:</strong> Scan QR Code, update profil/foto, jadwal pengumuman per kelas, maintenance.<br>• 🕒 <strong>Operasional:</strong> Jam buka/tutup lab, tata tertib, kontak WhatsApp admin & media sosial."
    },
    {
        "intent": "goodbye",
        "keywords": ["bye", "dadah", "sampai jumpa", "selamat tinggal", "pamit", "keluar"],
        "questions": [
            "dadah bot", "sampai jumpa lagi", "saya pamit dulu", "terima kasih bye"
        ],
        "response": "Sampai jumpa! 👋 Selamat melanjutkan praktikum dan jaga selalu ketertiban di laboratorium TKJ."
    },

    # =========================================================================
    # 2. MEDIA SOSIAL & KONTAK PENGELOLA
    # =========================================================================
    {
        "intent": "instagram",
        "keywords": ["instagram", "instagramm", "ig", "sosmed", "medsos", "feed", "foto lab", "akun ig", "follow"],
        "questions": [
            "apa akun instagram lab?", "minta link instagramm nya", "ada ig lab tkj?",
            "sosmed resmi lab apa?", "akun media sosial laboratorium"
        ],
        "response": "📸 <strong>Instagram Resmi Lab TKJ:</strong><br>Kunjungi dan ikuti dokumentasi praktikum, tutorial jaringan, serta update kegiatan kami di Instagram: <a href='https://instagram.com/winshark_lab' target='_blank' class='text-indigo-600 font-bold underline'>@winshark_lab</a> 🚀"
    },
    {
        "intent": "kontak_admin",
        "keywords": ["admin", "whatsapp", "wa", "telepon", "kontak", "hubungi", "helpdesk", "nomor wa", "call center", "teknisi"],
        "questions": [
            "nomor whatsapp admin berapa?", "mau hubungi teknisi lab", "kontak pengelola lab",
            "chat wa admin lab", "minta nomor telepon bantuan lab"
        ],
        "response": "📞 <strong>Kontak Resmi Pengelola Lab TKJ:</strong><br>Untuk bantuan cepat, konfirmasi pengajuan mendesak, atau kendala alat:<br>• <strong>WhatsApp Admin:</strong> <a href='https://wa.me/6287874589054?text=Halo+Admin+Lab+TKJ%2C+saya+butuh+bantuan.' target='_blank' class='text-emerald-600 font-bold underline'>0878-7458-9054 (Klik untuk Chat)</a><br>• <strong>Ruang Kantor:</strong> Ruangan Laboratorium TKJ (Lantai Dasar)"
    },
    {
        "intent": "lokasi_lab",
        "keywords": ["lokasi", "alamat", "gedung", "lantai", "dimana lab", "posisi lab", "ruang lab"],
        "questions": [
            "dimana lokasi lab tkj?", "lab tkj di lantai berapa?", "letak laboratorium komputer dimana?",
            "posisi ruang teknisi lab"
        ],
        "response": "📍 <strong>Lokasi Laboratorium TKJ:</strong><br>• <strong>Lab TKJ 1 (Jaringan & Routing):</strong> Gedung Praktikum Barat - Lantai 2 (Ruang 204).<br>• <strong>Lab TKJ 2 (Hardware & Fiber Optic):</strong> Gedung Praktikum Barat - Lantai 2 (Ruang 205).<br>• <strong>Ruang Server & Teknisi:</strong> Sebelah Lab TKJ 1."
    },

    # =========================================================================
    # 3. PROSEDUR PEMINJAMAN ALAT
    # =========================================================================
    {
        "intent": "cara_pinjam",
        "keywords": ["cara pinjam", "pinjam", "meminjam", "minjem", "sewa", "ambil barang", "prosedur pinjam", "alur pinjam"],
        "questions": [
            "bagaimana cara pinjam alat?", "gimana minjem router?", "alur peminjaman barang lab",
            "langkah-langkah meminjam peralatan praktikum", "tata cara pinjam"
        ],
        "response": "📦 <strong>Alur Lengkap Peminjaman Alat:</strong><br>1. Masuk ke menu <strong>Katalog Alat & Pinjam</strong> di sidebar.<br>2. Cari dan pilih alat yang berstatus <em>Tersedia</em>.<br>3. Klik tombol <strong>Pinjam</strong>.<br>4. Isi form: tanggal pinjam, estimasi kembali, dan keperluan praktikum.<br>5. Klik <strong>Ajukan Peminjaman</strong>.<br>6. Tunggu status berubah menjadi <strong>Disetujui</strong>, lalu ambil alat di ruang teknisi lab."
    },
    {
        "intent": "durasi_peminjaman",
        "keywords": ["berapa lama", "durasi", "batas waktu", "tenggat", "maksimal pinjam", "tempo", "berapa hari", "jangka waktu"],
        "questions": [
            "berapa hari maksimal boleh pinjam alat?", "durasi peminjaman berapa lama?",
            "apakah boleh pinjam alat selama 1 minggu?", "batas waktu peminjaman tang crimping"
        ],
        "response": "⏱️ <strong>Ketentuan Durasi Peminjaman:</strong><br>• <strong>Praktikum Reguler Jam Sekolah:</strong> Maksimal 1 hari kerja (wajib dikembalikan sebelum pukul 15.00 WIB di hari yang sama).<br>• <strong>Tugas Akhir / Proyek / Persiapan UKK:</strong> Maksimal 3 hari kerja (wajib melampirkan izin dari Guru Produktif terkait)."
    },
    {
        "intent": "syarat_peminjaman",
        "keywords": ["syarat", "ketentuan", "persyaratan", "aturan pinjam", "siapa yang boleh", "kriteria"],
        "questions": [
            "apa syarat meminjam alat lab?", "siapa saja yang boleh meminjam?",
            "apakah siswa kelas X boleh pinjam router?", "persyaratan peminjaman barang"
        ],
        "response": "📋 <strong>Syarat & Ketentuan Peminjaman:</strong><br>1. Terdaftar aktif sebagai Siswa, Koordinator, atau Guru TKJ.<br>2. Profil akun telah lengkap (Nama, NIS/NIP, Kelas, dan No. WhatsApp aktif).<br>3. Tidak sedang dalam masa sanksi penangguhan peminjaman.<br>4. Tidak memiliki tanggungan alat yang belum dikembalikan."
    },
    {
        "intent": "limit_jumlah_alat",
        "keywords": ["limit", "maksimal barang", "berapa unit", "boleh banyak", "jumlah maksimal", "kuota pinjam", "banyak alat"],
        "questions": [
            "berapa banyak alat yang bisa dipinjam sekaligus?", "apakah ada limit barang per siswa?",
            "bolehkah meminjam 3 router sekaligus?", "kuota maksimal peminjaman"
        ],
        "response": "🔢 <strong>Batas Kuota Peminjaman:</strong><br>Setiap akun siswa dibatasi meminjam maksimal <strong>2 unit alat berbeda</strong> dalam satu waktu bersamaan agar seluruh siswa mendapatkan giliran praktikum secara adil."
    },
    {
        "intent": "pinjam_kelompok",
        "keywords": ["kelompok", "rombongan", "tim", "pinjam bareng", "tugas kelompok", "kelompok praktikum"],
        "questions": [
            "bagaimana cara pinjam alat untuk satu kelompok?", "apakah bisa meminjam atas nama kelompok?",
            "pinjam router untuk kelompok praktikum"
        ],
        "response": "👥 <strong>Peminjaman Atas Nama Kelompok:</strong><br>Cukup <strong>1 orang (Ketua Kelompok)</strong> yang mengajukan di sistem. Pada kolom <em>Keperluan</em>, cantumkan format: <code>Praktikum [Materi] - Kelompok [No] - Anggota: [Nama 1, Nama 2]</code>."
    },
    {
        "intent": "pinjam_luar_jam",
        "keywords": ["bawa pulang", "luar jam", "ke rumah", "pinjam weekend", "dibawa pulang", "menginap"],
        "questions": [
            "apakah alat lab boleh dibawa pulang ke rumah?", "bolehkah meminjam alat saat hari libur?",
            "alat dibawa pulang untuk tugas"
        ],
        "response": "🏠 <strong>Peminjaman Alat ke Luar Sekolah:</strong><br>Peralatan lab pada dasarnya hanya untuk praktikum di lingkungan sekolah. Peminjaman ke rumah hanya diizinkan untuk <strong>persiapan lomba LKS atau UKK</strong> dengan persetujuan tertulis dari Kepala Laboratorium TKJ."
    },
    {
        "intent": "batalkan_pengajuan",
        "keywords": ["batal", "batalkan", "cancel", "tidak jadi", "hapus pengajuan", "salah pinjam"],
        "questions": [
            "bagaimana cara membatalkan peminjaman yang salah?", "tidak jadi meminjam alat",
            "cara cancel pengajuan peminjaman"
        ],
        "response": "🚫 <strong>Membatalkan Pengajuan Peminjaman:</strong><br>Jika status pengajuan masih <strong>Menunggu</strong>, Anda dapat membatalkannya melalui menu <strong>Peminjaman Saya</strong> lalu klik tombol <em>Batal</em>, atau hubungi Admin via WhatsApp jika tombol tidak tersedia."
    },

    # =========================================================================
    # 4. PROSEDUR PENGEMBALIAN & STATUS PENGAJUAN
    # =========================================================================
    {
        "intent": "cara_kembali",
        "keywords": ["kembali", "kembalikan", "mengembalikan", "balikin", "selesai pinjam", "taruh alat", "alur pengembalian"],
        "questions": [
            "bagaimana cara mengembalikan alat?", "saya sudah selesai pakai alat",
            "prosedur pengembalian router", "langkah pengembalian barang lab"
        ],
        "response": "🔄 <strong>Prosedur Resmi Pengembalian Alat:</strong><br>1. Buka menu <strong>Peminjaman Saya</strong>.<br>2. Cari data alat yang dipinjam, lalu klik tombol <strong>Ajukan Kembali</strong>.<br>3. Bawa fisik alat, adaptor, dan kabel pendukung ke meja Teknisi Lab.<br>4. Teknisi memeriksa kelengkapan & fungsi alat.<br>5. Status di sistem akan otomatis diperbarui menjadi <strong>Selesai</strong>."
    },
    {
        "intent": "kembali_diwakilkan",
        "keywords": ["titip", "titipkan", "diwakilkan", "teman yang balikin", "wakil", "titip teman"],
        "questions": [
            "apakah pengembalian alat bisa dititipkan ke teman?", "teman saya yang balikin barang boleh?",
            "titip pengembalian router"
        ],
        "response": "⚠️ <strong>Pengembalian yang Diwakilkan:</strong><br>Boleh dititipkan kepada teman sekelas, namun <strong>tanggung jawab kondisi barang tetap berada pada nama peminjam</strong> yang tertera di sistem sampai teknisi lab menyelesaikan verifikasi."
    },
    {
        "intent": "cek_status",
        "keywords": ["status", "cek status", "pantau", "lacak", "sudah acc", "disetujui", "proses", "arti status"],
        "questions": [
            "bagaimana cara melihat status peminjaman saya?", "apakah pengajuan saya sudah di-acc?",
            "arti status kuning dan hijau", "lacak peminjaman alat"
        ],
        "response": "📋 <strong>Arti Status di menu 'Peminjaman Saya':</strong><br>• 🟡 <strong>Menunggu:</strong> Pengajuan sedang dalam antrean verifikasi Admin.<br>• 🟢 <strong>Disetujui:</strong> Pengajuan diterima! Silakan ambil alat di Lab TKJ.<br>• 🔴 <strong>Ditolak:</strong> Pengajuan tidak disetujui (alat sedang habis/keperluan tidak valid).<br>• ✅ <strong>Selesai:</strong> Alat telah diperiksa dan sukses dikembalikan."
    },
    {
        "intent": "status_lama_menunggu",
        "keywords": ["lama", "belum di acc", "belum disetujui", "kapan di acc", "menunggu terus", "pending"],
        "questions": [
            "kenapa pengajuan saya statusnya menunggu terus?", "kapan pinjaman saya disetujui?",
            "sudah lama mengajukan tapi belum di acc"
        ],
        "response": "⏳ <strong>Pengajuan Masih Menunggu?</strong><br>Admin biasanya memverifikasi pengajuan pada jam kerja (08.00 - 15.00 WIB). Jika praktikum Anda mendesak, silakan hubungi <a href='https://wa.me/628123456789' target='_blank' class='text-emerald-600 font-bold underline'>WhatsApp Admin</a> dengan menyertakan Nama & Nama Alat."
    },
    {
        "intent": "pengajuan_ditolak",
        "keywords": ["ditolak", "kenapa ditolak", "alasan ditolak", "tidak di acc", "gagal pinjam", "rejected"],
        "questions": [
            "kenapa pengajuan pinjam saya ditolak?", "alasan alat tidak boleh dipinjam",
            "mengapa status saya merah ditolak?"
        ],
        "response": "❌ <strong>Alasan Umum Pengajuan Ditolak:</strong><br>1. Stok alat habis atau sedang dijadwalkan untuk kelas lain.<br>2. Anda masih memiliki pinjaman aktif yang belum dikembalikan.<br>3. Deskripsi keperluan praktikum belum jelas.<br>4. Pengajuan dilakukan di luar jam operasional laboratorium."
    },

    # =========================================================================
    # 5. KERUSAKAN, KEHILANGAN, SANKSI & DENDA
    # =========================================================================
    {
        "intent": "alat_rusak",
        "keywords": ["rusak", "patah", "mati", "tidak fungsi", "komponen rusak", "kerusakan", "konslet", "kebakar", "pecah"],
        "questions": [
            "bagaimana kalau alat rusak saat dipakai?", "crimping tool patah saat praktikum",
            "router tidak mau menyala", "alat lab tidak sengaja rusak"
        ],
        "response": "⚠️ <strong>Prosedur Jika Alat Mengalami Kerusakan:</strong><br>1. Segera hentikan pemakaian dan <strong>jangan membongkar alat sendiri</strong>.<br>2. Laporkan ke Teknisi/Guru Pendamping Lab.<br>3. Jika kerusakan murni teknis/usia alat, peminjam tidak dikenakan sanksi.<br>4. Jika kerusakan akibat kelalaian fatal (terbanting/tersiram air), peminjam wajib memperbaiki atau mengganti komponen yang rusak."
    },
    {
        "intent": "alat_hilang",
        "keywords": ["hilang", "ketinggalan", "lenyap", "hilang barang", "hilang di lab", "kemalingan"],
        "questions": [
            "alat yang saya pinjam hilang bagaimana?", "sanksi jika alat lab hilang",
            "menghilangkan mikrotik lab", "adaptor router hilang"
        ],
        "response": "🚨 <strong>Ketentuan Barang Hilang:</strong><br>Peminjam wajib melaporkan ke Kepala Lab dan <strong>mengganti unit alat</strong> dengan tipe, merk, dan spesifikasi yang sama persis dalam waktu maksimal 14 hari kalender."
    },
    {
        "intent": "sanksi_terlambat",
        "keywords": ["telat", "terlambat", "denda", "sanksi", "hukuman", "lewat batas waktu", "overdue"],
        "questions": [
            "apakah ada denda uang jika telat?", "sanksi telat mengembalikan alat",
            "saya lupa mengembalikan alat kemarin", "denda keterlambatan pinjam"
        ],
        "response": "⚠️ <strong>Sanksi Keterlambatan Pengembalian:</strong><br>Laboratorium TKJ <strong>tidak memungut denda uang</strong>. Sebagai gantinya, akun yang terlambat akan dikenakan:<br>• <strong>Suspend Akun (1-2 Minggu):</strong> Tidak dapat meminjam alat apapun di sistem.<br>• <strong>Sanksi Disiplin:</strong> Menjalankan piket pembersihan laboratorium."
    },
    {
        "intent": "kelengkapan_tertinggal",
        "keywords": ["tertinggal", "kabel power hilang", "adaptor ketinggalan", "kelengkapan", "dus hilang"],
        "questions": [
            "adaptor router tertinggal di rumah", "kabel bawaan alat belum dibawa",
            "alat lengkap tapi adaptornya ketinggalan"
        ],
        "response": "🔌 <strong>Kelengkapan Alat Tertinggal:</strong><br>Pengembalian dianggap <strong>belum selesai</strong> sampai seluruh kelengkapan (adaptor daya, kabel console, modul patch cord) dikembalikan secara utuh ke ruang lab."
    },

    # =========================================================================
    # 6. KATALOG PERANGKAT: MIKROTIK & ROUTING
    # =========================================================================
    {
        "intent": "mikrotik_hex",
        "keywords": ["rb750gr3", "hex", "mikrotik hex", "rb750", "router gigabit"],
        "questions": [
            "spesifikasi mikrotik rb750gr3?", "apakah mikrotik hex tersedia?",
            "pinjam mikrotik port gigabit", "routerboard 750 ready?"
        ],
        "response": "🌐 <strong>MikroTik RouterBOARD hEX (RB750Gr3):</strong><br>• <strong>Spesifikasi:</strong> 5 Port Gigabit Ethernet, Dual Core CPU 880MHz, RAM 256MB, Slot MicroSD & USB.<br>• <strong>Fungsi Praktikum:</strong> Routing statis/dinamis (OSPF/BGP), Hotspot Server, Bandwidth Limiter (Queue), Load Balancing, dan VPN.<br>• <strong>Status:</strong> Cek ketersediaan stok di menu <strong>Katalog Alat & Pinjam</strong>."
    },
    {
        "intent": "mikrotik_haplite",
        "keywords": ["rb941", "hap lite", "haplite", "mikrotik wifi", "router wifi mikrotik"],
        "questions": [
            "apakah ada mikrotik hAP Lite?", "router mikrotik yang ada wifinya",
            "pinjam rb941 2nd", "stok haplite ada?"
        ],
        "response": "📶 <strong>MikroTik hAP Lite (RB941-2nD):</strong><br>• <strong>Spesifikasi:</strong> 4 Port Fast Ethernet 10/100, Wireless 2.4GHz 802.11b/g/n internal antena, CPU 650MHz, RAM 32MB.<br>• <strong>Fungsi Praktikum:</strong> Konfigurasi Wireless AP, Hotspot Voucher, DHCP Server, Firewall Filter Rules dasar UKK."
    },
    {
        "intent": "reset_mikrotik",
        "keywords": ["reset mikrotik", "lupa password mikrotik", "hard reset", "reset tombol", "netinstall"],
        "questions": [
            "bagaimana cara reset mikrotik?", "lupa user admin mikrotik lab",
            "cara mengembalikan default routerboard"
        ],
        "response": "🔄 <strong>Cara Hard Reset Router MikroTik:</strong><br>1. Cabut colokan power adaptor.<br>2. Tekan dan tahan tombol <strong>RES / RESET</strong> menggunakan ujung pena/klip.<br>3. Colokkan kembali power adaptor sambil tetap menahan tombol reset.<br>4. Tunggu lampu indikator <strong>ACT / USR</strong> mulai berkedip (sekitar 5-10 detik), lalu segera lepas tombol reset.<br>5. Buka Winbox untuk login default (User: <code>admin</code>, Password: <em>kosong</em>)."
    },

    # =========================================================================
    # 7. KATALOG PERANGKAT: FIBER OPTIC (FO)
    # =========================================================================
    {
        "intent": "fusion_splicer",
        "keywords": ["splicer", "fusion splicer", "alat sambung fo", "sambung fiber", "las fiber"],
        "questions": [
            "apakah boleh pinjam fusion splicer?", "alat sambung fiber optic ready?",
            "cara pinjam mesin splicer"
        ],
        "response": "💡 <strong>Fusion Splicer Fiber Optic:</strong><br>• <strong>Fungsi:</strong> Menyambung dua ujung core kaca fiber optic menggunakan busur listrik presisi tinggi.<br>• <strong>Perhatian:</strong> Splicer adalah alat presisi tinggi. Peminjaman <strong>wajib didampingi Guru atau Teknisi Lab</strong> dan tidak diperkenankan dipinjam tanpa izin pembimbing praktikum."
    },
    {
        "intent": "fiber_cleaver",
        "keywords": ["cleaver", "fiber cleaver", "pemotong core", "potong kaca fiber"],
        "questions": [
            "alat pemotong kaca fiber optic", "pinjam fiber cleaver", "cara potong fiber optic"
        ],
        "response": "🔪 <strong>Fiber Cleaver (Pemotong Core):</strong><br>Digunakan untuk memotong ujung core fiber optic dengan sudut kemiringan tepat 90 derajat sebelum proses splicing. Pastikan pisau cleaver bersih dari serpihan kaca."
    },
    {
        "intent": "opm_vfl",
        "keywords": ["opm", "vfl", "laser fo", "optical power meter", "laser fiber", "ukur redaman"],
        "questions": [
            "alat ukur redaman kabel fo", "pinjam laser senter fiber optic",
            "alat tes kabel putus fiber optic", "fungsi optical power meter"
        ],
        "response": "🔦 <strong>OPM & Visual Fault Locator (VFL):</strong><br>• <strong>VFL (Laser Senter Merah):</strong> Mendeteksi kabel FO putus/bengkok pada jarak s.d 10 KM.<br>• <strong>OPM (Optical Power Meter):</strong> Mengukur besar redaman (dBm/loss) pada sambungan saluran kabel fiber optik."
    },
    {
        "intent": "stripper_fo",
        "keywords": ["stripper", "stripper fo", "dropcore", "kupas kabel fiber", "tang fo"],
        "questions": [
            "tang kupas kabel fiber optic", "pinjam stripper dropcore", "alat kupas cladding fiber"
        ],
        "response": "✂️ <strong>Stripper Fiber Optic:</strong><br>Tersedia <strong>Drop Core Stripper</strong> (untuk mengupas jaket luar kabel hitam) dan <strong>3-Hole Miller Stripper</strong> (untuk mengupas lapisan buffer & coating kaca 125µm/250µm)."
    },

    # =========================================================================
    # 8. KATALOG PERANGKAT: KABEL, CRIMPING & PENGKABELAN
    # =========================================================================
    {
        "intent": "tang_crimping",
        "keywords": ["crimping", "tang crimping", "crimping tool", "proskit", "alat jepit rj45"],
        "questions": [
            "mau pinjam tang crimping", "crimping tool ready?", "alat pasang rj45",
            "tang crimping rj45 rj11"
        ],
        "response": "✂️ <strong>Tang Crimping RJ45 & RJ11:</strong><br>Tersedia unit Tang Crimping Heavy-Duty merk Pro'sKit dan Schneider. Dilengkapi pisau pemotong dan pengupas kabel UTP untuk pembuatan kabel straight/cross."
    },
    {
        "intent": "lan_tester",
        "keywords": ["lan tester", "tester kabel", "cek kabel lan", "cek kabel utp", "tester rj45"],
        "questions": [
            "alat cek kabel lan", "pinjam lan tester", "bagaimana cara cek kabel lan sudah benar?"
        ],
        "response": "📟 <strong>LAN Cable Tester RJ45/RJ11:</strong><br>Alat penguji urutan pin 1 sampai 8 kabel jaringan. Lampu indikator 1 s.d 8 yang menyala berurutan menandakan kabel straight terpasang sempurna tanpa korsleting."
    },
    {
        "intent": "urutan_kabel_lan",
        "keywords": ["urutan kabel", "susunan kabel", "straight", "cross", "t568b", "t568a", "warna kabel"],
        "questions": [
            "apa urutan kabel straight?", "urutan warna kabel cross",
            "susunan standar kabel lan t568b", "warna urutan rj45"
        ],
        "response": "🌈 <strong>Standar Urutan Warna Kabel LAN (T568B):</strong><br>1. Putih-Oranye<br>2. Oranye<br>3. Putih-Hijau<br>4. Biru<br>5. Putih-Biru<br>6. Hijau<br>7. Putih-Cokelat<br>8. Cokelat<br><em>(Untuk kabel Straight: kedua ujung T568B. Untuk Cross: satu ujung T568B, ujung lainnya T568A).</em>"
    },

    # =========================================================================
    # 9. KATALOG PERANGKAT: SWITCH & CISCO
    # =========================================================================
    {
        "intent": "cisco_switch",
        "keywords": ["cisco", "switch cisco", "catalyst", "c2960", "manageable switch", "switch catalyst"],
        "questions": [
            "apakah ada switch cisco?", "pinjam switch manageable",
            "cisco catalyst 2960 ready?", "perangkat konfigurasi vlan cisco"
        ],
        "response": "🔀 <strong>Cisco Catalyst Switch 2960:</strong><br>• <strong>Fasilitas:</strong> 24 Port FastEthernet + 2 Gigabit Uplink.<br>• <strong>Materi Praktikum:</strong> Konfigurasi VLAN, VTP (VLAN Trunking Protocol), STP (Spanning Tree), Port Security, dan Inter-VLAN Routing."
    },
    {
        "intent": "kabel_console",
        "keywords": ["kabel console", "rollover", "console usb", "kabel cisco", "kabel rj45 to usb"],
        "questions": [
            "kabel buat konfigurasi cisco", "pinjam kabel console usb",
            "kabel setting switch cisco lewat laptop"
        ],
        "response": "🔌 <strong>Kabel Console RJ45 to USB:</strong><br>Kabel khusus untuk menghubungkan port Console pada Switch/Router Cisco ke port USB laptop menggunakan software PuTTY atau TeraTerm (Baud rate: 9600)."
    },

    # =========================================================================
    # 10. KATALOG PERANGKAT: ACCESS POINT & WIRELESS
    # =========================================================================
    {
        "intent": "access_point",
        "keywords": ["access point", "ap", "wifi", "tplink", "omada", "unifi", "ubiquiti", "wireless ap"],
        "questions": [
            "mau pinjam access point wifi", "ap tp-link ready?", "pinjam unifi access point",
            "perangkat pemancar wifi lab"
        ],
        "response": "📡 <strong>Access Point & Wireless Lab:</strong><br>• <strong>TP-Link Omada / EAP Series:</strong> Cocok untuk setting multi-SSID dan VLAN over Wireless.<br>• <strong>Ubiquiti UniFi AP:</strong> Praktikum captive portal voucher dan manajemen AP terpusat."
    },
    {
        "intent": "radio_outdoor",
        "keywords": ["radio", "outdoor", "litebeam", "nanostation", "wireless outdoor", "point to point", "p2p"],
        "questions": [
            "ada radio outdoor point to point?", "pinjam ubiquiti litebeam",
            "alat praktikum jaringan jarak jauh"
        ],
        "response": "🛰️ <strong>Wireless Radio Outdoor (PTP / PTMP):</strong><br>Tersedia Ubiquiti LiteBeam M5 dan NanoStation Loco M5 (5GHz) untuk simulasi transmisi data jaringan nirkabel jarak jauh antar gedung."
    },

    # =========================================================================
    # 11. FITUR SCAN QR CODE & CETAK LABEL
    # =========================================================================
    {
        "intent": "scan_qr",
        "keywords": ["scan", "qr", "barcode", "kamera", "tidak bisa scan", "gagal scan", "pindai", "cara scan"],
        "questions": [
            "bagaimana cara scan qr code barang?", "kamera scan tidak terbuka",
            "cara cepat lihat info alat lewat barcode", "scanner qr tidak berfungsi"
        ],
        "response": "📷 <strong>Panduan Fitur Scan QR Code:</strong><br>1. Buka menu <strong>Scan QR Code</strong> di topbar atau menu samping.<br>2. Beri izin (*Allow*) akses kamera pada browser Anda.<br>3. Arahkan kamera ke stiker QR Code yang menempel pada bodi alat.<br>4. Sistem akan otomatis membuka detail spesifikasi dan tombol cepat peminjaman.<br><br><em>Tips: Jika kamera gelap, gunakan tombol switch camera atau pastikan pencahayaan cukup.</em>"
    },
    {
        "intent": "qr_rusak",
        "keywords": ["qr hilang", "qr sobek", "barcode hilang", "tidak terbaca", "stiker lepas"],
        "questions": [
            "bagaimana jika stiker QR Code pada alat rusak?", "barcode tidak bisa di-scan",
            "stiker QR lepas dari router"
        ],
        "response": "🏷️ <strong>Stiker QR Code Rusak / Lepas:</strong><br>Anda tetap dapat mencari alat tersebut secara manual melalui kolom pencarian di menu <strong>Katalog Alat & Pinjam</strong>. Harap laporkan juga ke teknisi lab agar stiker QR baru dapat dicetak ulang."
    },

    # =========================================================================
    # 12. PENGUMUMAN LAB, JADWAL & FILTER KELAS
    # =========================================================================
    {
        "intent": "pengumuman_berita",
        "keywords": ["pengumuman", "berita", "info lab", "jadwal ukk", "jadwal praktikum", "informasi terbaru"],
        "questions": [
            "dimana melihat pengumuman lab?", "jadwal praktikum lab tkj",
            "info ujian kompetensi keahlian ukk", "kapan jadwal kalibrasi alat?"
        ],
        "response": "📢 <strong>Menu Pengumuman Lab:</strong><br>Informasi kalibrasi perangkat, jadwal praktikum kelas, dan berita resmi guru dapat diakses melalui menu <strong>Pengumuman Lab</strong>. Pengumuman otomatis disesuaikan dengan kelas Anda."
    },
    {
        "intent": "filter_pengumuman_kelas",
        "keywords": ["pengumuman kelas", "target kelas", "berita tidak muncul", "kelas beda", "info kelas"],
        "questions": [
            "kenapa pengumuman kelas saya berbeda?", "pengumuman ditargetkan untuk siapa?",
            "kenapa berita kelas 12 tidak muncul di akun kelas 11?"
        ],
        "response": "🎯 <strong>Sistem Filter Target Kelas:</strong><br>Sistem kami secara cerdas memfilter berita. Pengumuman khusus kelas (misal: <em>XII TKJ 1</em>) hanya akan tampil pada siswa dari kelas tersebut, sedangkan pengumuman umum akan diterima oleh seluruh siswa."
    },

    # =========================================================================
    # 13. AKUN, PROFIL, KELAS & KATA SANDI
    # =========================================================================
    {
        "intent": "edit_profil",
        "keywords": ["edit profil", "ganti foto", "foto profil", "cropper", "crop foto", "ganti wa", "update nomor"],
        "questions": [
            "bagaimana cara ganti foto profil?", "cara crop foto profil",
            "ubah nomor whatsapp akun", "update data profil akun"
        ],
        "response": "👤 <strong>Pengaturan Profil Saya:</strong><br>Buka menu <strong>Profil Saya</strong> di sidebar untuk:<br>• <strong>Ganti & Crop Foto:</strong> Unggah foto profil dengan fitur zoom/rotate ala Instagram.<br>• <strong>Nomor WhatsApp:</strong> Perbarui kontak agar notifikasi peminjaman sampai ke HP Anda.<br>• <strong>Data Diri:</strong> Cek Nomor Induk Siswa (NIS) Anda."
    },
    {
        "intent": "ganti_password",
        "keywords": ["ganti password", "ubah sandi", "kata sandi", "lupa password", "password baru", "reset sandi"],
        "questions": [
            "bagaimana cara ganti kata sandi?", "cara ubah password akun",
            "lupa password akun lab", "syarat kata sandi baru"
        ],
        "response": "🔐 <strong>Cara Mengubah Kata Sandi:</strong><br>1. Masuk ke menu <strong>Profil Saya</strong>.<br>2. Gulir ke bagian formulir <strong>Keamanan & Kata Sandi</strong>.<br>3. Masukkan kata sandi saat ini.<br>4. Masukkan kata sandi baru (minimal 6 karakter) dan konfirmasi.<br>5. Klik <strong>Perbarui Kata Sandi</strong>."
    },
    {
        "intent": "ubah_kelas_jabatan",
        "keywords": ["ubah kelas", "ganti kelas", "salah kelas", "pindah kelas", "kelas terkunci", "jabatan salah"],
        "questions": [
            "kenapa kelas saya terkunci dan tidak bisa diedit?", "bagaimana cara ganti data kelas?",
            "data kelas saya salah input"
        ],
        "response": "🔒 <strong>Perubahan Data Kelas / Jabatan:</strong><br>Kolom kelas dikunci (read-only) demi mencegah manipulasi hak akses peminjaman. Jika kelas Anda salah, silakan hubungi <strong>Admin / Teknisi Lab</strong> agar data kelas Anda diperbarui langsung di database."
    },

    # =========================================================================
    # 14. FITUR MAINTENANCE & PERBAIKAN ALAT
    # =========================================================================
    {
        "intent": "fitur_maintenance",
        "keywords": ["maintenance", "perbaikan", "catat rusak", "laporan maintenance", "riwayat maintenance", "koordinator"],
        "questions": [
            "apa itu menu maintenance?", "siapa yang bisa akses maintenance?",
            "cara mencatat alat yang rusak", "fitur koordinator lab maintenance"
        ],
        "response": "🔧 <strong>Fitur Maintenance Laboratorium:</strong><br>Menu Maintenance dikhususkan untuk <strong>Koordinator Lab</strong> dan <strong>Guru/Admin</strong> untuk mendokumentasikan log kerusakan, proses perbaikan teknisi, pengadaan sparepart, dan kalibrasi alat."
    },
    {
        "intent": "lapor_kerusakan_siswa",
        "keywords": ["lapor rusak", "lapor alat", "ada barang rusak", "meja rusak", "pc lab mati"],
        "questions": [
            "bagaimana siswa melaporkan alat yang rusak?", "saya menemukan tang crimping rusak di lemari",
            "komputer lab nomor 5 mati"
        ],
        "response": "📝 <strong>Cara Siswa Melaporkan Kerusakan:</strong><br>Sampaikan langsung kepada <strong>Koordinator Lab</strong> kelas Anda atau kirim pesan ke Admin via WhatsApp dengan menyebutkan: <em>Nama Alat / No Meja PC dan gejala kerusakannya</em>."
    },

    # =========================================================================
    # 15. OPERASIONAL LAB & TATA TERTIB
    # =========================================================================
    {
        "intent": "jam_operasional",
        "keywords": ["jam buka", "jam operasional", "jadwal lab", "kapan buka", "tutup jam", "hari sabtu", "hari minggu", "libur"],
        "questions": [
            "jam berapa lab tkj buka?", "kapan laboratorium tutup?",
            "apakah hari sabtu dan minggu lab buka?", "jadwal operasional lab"
        ],
        "response": "🕐 <strong>Jam Operasional Resmi Lab TKJ:</strong><br>📅 <strong>Senin – Jumat:</strong> 08.00 – 15.00 WIB<br>📅 <strong>Sabtu & Minggu:</strong> Tutup / Libur Sekolah<br><br><em>Pengajuan peminjaman di luar jam operasional akan diproses pada hari kerja berikutnya.</em>"
    },
    {
        "intent": "tata_tertib",
        "keywords": ["tata tertib", "aturan", "makan", "minum", "sepatu", "sandal", "peraturan lab", "dilarang"],
        "questions": [
            "apa saja tata tertib di lab tkj?", "apakah boleh makan dan minum di lab?",
            "peraturan saat berada di dalam laboratorium", "apakah wajib pakai sandal lab?"
        ],
        "response": "📜 <strong>Tata Tertib Laboratorium Komputer TKJ:</strong><br>1. 🚫 <strong>Dilarang keras</strong> membawa makanan & minuman ke dalam ruang lab.<br>2. 👟 Wajib melepas sepatu luar dan mengenakan <strong>sandal lab khusus</strong>.<br>3. 🔌 Dilarang mengubah susunan kabel LAN pada rak server tanpa instruksi guru.<br>4. 🪑 Rapikan kembali kursi, matikan PC, dan buang sampah sebelum meninggalkan lab.<br>5. 🤫 Menjaga ketenangan dan tidak bermain game saat jam pelajaran berlangsung."
    },
    {
        "intent": "kebersihan_lab",
        "keywords": ["piket", "kebersihan", "sampah", "sandal lab", "sapu", "bersihkan meja"],
        "questions": [
            "siapa yang wajib piket lab?", "aturan sandal lab tkj",
            "kebersihan laboratorium komputer"
        ],
        "response": "🧹 <strong>Kebersihan & Kerapihan Lab:</strong><br>Setiap kelas yang menggunakan ruangan lab wajib menunjuk regu piket untuk merapikan alat, mematikan saklar listrik utama, dan membersihkan ruangan seusai jam praktikum."
    },

    # =========================================================================
    # 16. MANAJEMEN INVENTARIS & SPLIT UNIT (FITUR SISTEM)
    # =========================================================================
    {
        "intent": "split_units",
        "keywords": ["split unit", "pisah unit", "kondisi per unit", "split barang", "unit management"],
        "questions": [
            "apa itu fitur split units pada barang?", "bagaimana cara memisahkan kondisi unit?",
            "cara mengubah kondisi sebagian barang di inventaris"
        ],
        "response": "🔀 <strong>Fitur Split Unit Inventaris:</strong><br>Fitur <em>Split Units</em> memungkinkan Admin atau Kepala Lab memecah jumlah total suatu barang menjadi rincian kondisi spesifik per-unit (misal: 10 unit router dipecah menjadi 8 Baik dan 2 Perbaikan) sehingga data stok yang tampil di katalog selalu akurat."
    },
    {
        "intent": "kondisi_barang",
        "keywords": ["kondisi barang", "status kondisi", "baik", "perawatan", "perbaikan", "rusak berat", "hilang"],
        "questions": [
            "apa saja tingkatan kondisi barang di inventaris?", "arti status kondisi alat lab",
            "perbedaan kondisi perawatan dan perbaikan"
        ],
        "response": "🏷️ <strong>Tingkatan Kondisi Barang di Sistem:</strong><br>• 🟢 <strong>Baik:</strong> Alat prima dan siap dipinjam.<br>• 🟡 <strong>Perawatan:</strong> Sedang dalam pemeliharaan rutin/kalibrasi.<br>• 🟠 <strong>Perbaikan:</strong> Mengalami kendala teknis dan menunggu servis.<br>• 🔴 <strong>Rusak Berat:</strong> Tidak dapat digunakan / diusulkan afkir.<br>• ⚫ <strong>Hilang:</strong> Unit tidak ditemukan saat audit inventaris."
    },

    # =========================================================================
    # 17. LAPORAN & EKSPOR DOKUMEN PDF
    # =========================================================================
    {
        "intent": "laporan_pdf",
        "keywords": ["laporan", "cetak pdf", "ekspor pdf", "unduh laporan", "rekap peminjaman", "laporan inventaris", "print"],
        "questions": [
            "bagaimana cara mencetak laporan inventaris lab?", "cara download laporan format pdf",
            "dimana letak menu laporan peminjaman?"
        ],
        "response": "📄 <strong>Cetak & Ekspor Laporan PDF:</strong><br>Pengelola (Admin / Kepala Lab) dapat membuka menu <strong>Laporan Inventaris</strong> atau <strong>Laporan Maintenance</strong> di sidebar, lalu klik tombol <strong>Export PDF</strong> untuk mengunduh rekap resmi siap cetak lengkap dengan kop surat dan kolom tanda tangan."
    },

    # =========================================================================
    # 18. PANDUAN PRAKTIKUM & TROUBLESHOOTING JARINGAN TKJ
    # =========================================================================
    {
        "intent": "panduan_mikrotik",
        "keywords": ["mikrotik", "routerboard", "winbox", "default ip", "reset mikrotik", "rb941", "rb750", "haplite"],
        "questions": [
            "berapa ip default mikrotik?", "bagaimana cara reset router mikrotik?",
            "kenapa mikrotik tidak terdeteksi di winbox?", "cara login routerboard pertama kali"
        ],
        "response": "📡 <strong>Panduan Cepat MikroTik RouterOS:</strong><br>• <strong>Default IP:</strong> <code>192.168.88.1</code> (Port Ether 2-4).<br>• <strong>Default Login:</strong> User <code>admin</code> tanpa password.<br>• <strong>Cara Akses:</strong> Buka aplikasi <em>Winbox</em>, klik tab <em>Neighbors</em>, pilih MAC Address router lalu klik <em>Connect</em>.<br>• <strong>Hard Reset:</strong> Cabut power, tekan & tahan tombol <em>Reset</em>, colok power kembali, tahan 5 detik hingga lampu ACT berkedip, lalu lepas."
    },
    {
        "intent": "panduan_kabel_lan",
        "keywords": ["crimping", "kabel lan", "utp", "rj45", "straight", "cross", "urutan warna", "t568b", "t568a"],
        "questions": [
            "apa urutan warna kabel straight?", "urutan pin kabel cross",
            "bagaimana cara crimping kabel rj45?", "standar kabel t568b"
        ],
        "response": "🔌 <strong>Standar Urutan Kabel UTP (T568B - Straight):</strong><br>1. Putih-Oranye<br>2. Oranye<br>3. Putih-Hijau<br>4. Biru<br>5. Putih-Biru<br>6. Hijau<br>7. Putih-Cokelat<br>8. Cokelat<br><br><em>Kabel Straight digunakan untuk menghubungkan Router ke Switch atau Switch ke PC.</em>"
    },
    {
        "intent": "panduan_fiber_optic",
        "keywords": ["fiber optic", "fo", "splicer", "cleaver", "fusion", "sambung fiber", "vfl", "laser fo", "redaman"],
        "questions": [
            "bagaimana cara menggunakan fusion splicer?", "apa fungsi fiber cleaver?",
            "alat untuk cek kabel fiber optic putus", "batas toleransi redaman fiber optic"
        ],
        "response": "💡 <strong>Panduan Peralatan Fiber Optic:</strong><br>• <strong>Fiber Cleaver:</strong> Memotong core kaca dengan sudut presisi 90°.<br>• <strong>Fusion Splicer:</strong> Melebur dan menyambung dua ujung serat kaca (estimasi loss ideal < 0.03 dB).<br>• <strong>Visual Fault Locator (VFL):</strong> Senter laser merah (650nm) untuk mencari titik bending/patah.<br>• <strong>OPM (Optical Power Meter):</strong> Mengukur daya sinyal yang diterima pada panjang gelombang 1310/1550nm."
    },
    {
        "intent": "panduan_ip_address",
        "keywords": ["ip address", "subnetting", "gateway", "dns", "dhcp", "ip statis", "setting ip"],
        "questions": [
            "bagaimana cara setting ip statis di komputer lab?", "apa itu default gateway?",
            "dns google berapa?"
        ],
        "response": "🌐 <strong>Panduan Konfigurasi IP Address:</strong><br>• <strong>Buka:</strong> Control Panel > Network & Internet > Network Connections > Properties IPv4.<br>• <strong>Pilih:</strong> <em>Use the following IP address</em>.<br>• <strong>DNS Terpercaya:</strong> <code>8.8.8.8</code> (Google) atau <code>1.1.1.1</code> (Cloudflare)."
    },

    # =========================================================================
    # 19. SANKSI & TANGGUNG JAWAB ALAT
    # =========================================================================
    {
        "intent": "sanksi_kerusakan",
        "keywords": ["sanksi", "denda", "ganti rugi", "alat pecah", "menghilangkan", "terlambat", "hukuman"],
        "questions": [
            "apa sanksi jika terlambat mengembalikan alat?", "bagaimana jika alat lab hilang atau rusak?",
            "apakah ada denda uang peminjaman?"
        ],
        "response": "⚖️ <strong>Kebijakan Tanggung Jawab & Sanksi:</strong><br>• <strong>Keterlambatan:</strong> Akun ditangguhkan dari peminjaman alat selama 3 hari kerja.<br>• <strong>Kerusakan/Kelalaian:</strong> Peminjam wajib mengoordinasikan penggantian unit atau servis dengan teknisi lab.<br>• <strong>Kehilangan:</strong> Wajib mengganti dengan unit/tipe yang setara setelah diverifikasi oleh Kepala Lab."
    },

    # =========================================================================
    # 20. HAK AKSES & PERAN PENGGUNA (ROLE PERMISSIONS)
    # =========================================================================
    {
        "intent": "peran_role",
        "keywords": ["role", "hak akses", "admin lab", "kepala lab", "koordinator", "guru", "siswa", "tingkatan akun"],
        "questions": [
            "apa perbedaan role admin dan siswa?", "apa fungsi role koordinator lab?",
            "siapa yang berhak menyetujui peminjaman?"
        ],
        "response": "👥 <strong>Hak Akses Role di Sistem:</strong><br>• <strong>Siswa & Guru:</strong> Mengakses katalog, mengajukan peminjaman, tracking status, dan scan QR.<br>• <strong>Koordinator Lab:</strong> Mencatat log pemeliharaan/maintenance alat lab.<br>• <strong>Kepala Lab & Admin:</strong> Hak penuh persetujuan pinjaman, manajemen inventaris, CRUD pengguna, dan cetak laporan resmi."
    },

    # =========================================================================
    # 21. MATERI JARINGAN DASAR (OSI, TCP/IP, TOPOLOGI)
    # =========================================================================
    {
        "intent": "osi_layer",
        "keywords": ["osi", "layer", "model osi", "7 lapisan", "application layer", "presentation", "session", "transport", "network", "data link", "physical"],
        "questions": [
            "apa itu model osi?", "sebutkan 7 lapisan osi",
            "jelaskan fungsi masing-masing layer osi", "pengertian osi layer"
        ],
        "response": "📚 <strong>Model OSI (Open Systems Interconnection):</strong><br>Model referensi 7 lapisan untuk komunikasi jaringan:<br>1. <strong>Physical:</strong> Transmisi bit melalui media (kabel, radio).<br>2. <strong>Data Link:</strong> Frame, MAC address, error detection.<br>3. <strong>Network:</strong> Routing, IP address, paket.<br>4. <strong>Transport:</strong> TCP/UDP, segment, flow control.<br>5. <strong>Session:</strong> Membangun, mengelola, dan mengakhiri sesi.<br>6. <strong>Presentation:</strong> Format data, enkripsi, kompresi.<br>7. <strong>Application:</strong> Protokol aplikasi (HTTP, FTP, DNS)."
    },
    {
        "intent": "tcp_ip_model",
        "keywords": ["tcp/ip", "model tcp", "protokol tcp", "udp", "internet protocol", "4 lapisan"],
        "questions": [
            "apa perbedaan tcp/ip dengan osi?", "jelaskan model tcp/ip",
            "apa itu protokol tcp dan udp?", "lapisan pada tcp/ip"
        ],
        "response": "🌐 <strong>Model TCP/IP (4 Lapisan):</strong><br>1. <strong>Network Access / Link:</strong> Menggabungkan Physical & Data Link OSI.<br>2. <strong>Internet:</strong> Protokol IP, ICMP, ARP.<br>3. <strong>Transport:</strong> TCP (connection-oriented) dan UDP (connectionless).<br>4. <strong>Application:</strong> HTTP, FTP, SMTP, DNS, dll.<br><em>Model TCP/IP lebih ringkas dan menjadi standar internet modern.</em>"
    },
    {
        "intent": "topologi_jaringan",
        "keywords": ["topologi", "star", "bus", "ring", "mesh", "tree", "hybrid", "topologi star"],
        "questions": [
            "apa saja jenis topologi jaringan?", "kelebihan topologi star",
            "gambar topologi ring", "perbedaan topologi bus dan star"
        ],
        "response": "🕸️ <strong>Jenis-Jenis Topologi Jaringan:</strong><br>• <strong>Star:</strong> Semua node terhubung ke pusat (switch/hub). Kelebihan: mudah troubleshooting.<br>• <strong>Bus:</strong> Satu kabel backbone, murah tapi sulit deteksi kerusakan.<br>• <strong>Ring:</strong> Setiap perangkat terhubung membentuk cincin.<br>• <strong>Mesh:</strong> Semua terhubung ke semua, sangat redundan.<br>• <strong>Tree/Hierarchical:</strong> Gabungan star dan bus, digunakan di kampus."
    },
    {
        "intent": "perangkat_jaringan",
        "keywords": ["perangkat jaringan", "router", "switch", "hub", "bridge", "repeater", "modem", "access point", "nic"],
        "questions": [
            "apa fungsi router?", "perbedaan hub dan switch",
            "sebutkan perangkat jaringan komputer", "fungsi access point"
        ],
        "response": "🖥️ <strong>Perangkat Jaringan Komputer:</strong><br>• <strong>Router:</strong> Menghubungkan dua jaringan berbeda dan memilih jalur terbaik (routing).<br>• <strong>Switch:</strong> Meneruskan data berdasarkan MAC address pada LAN.<br>• <strong>Hub:</strong> Mirip switch tapi broadcast ke semua port (lebih lambat).<br>• <strong>Access Point (AP):</strong> Memancarkan sinyal WiFi dari jaringan kabel.<br>• <strong>Modem:</strong> Mengubah sinyal analog ke digital (atau sebaliknya).<br>• <strong>NIC (Network Interface Card):</strong> Kartu jaringan pada perangkat."
    },

    # =========================================================================
    # 22. IP ADDRESS & SUBNETTING
    # =========================================================================
    {
        "intent": "ip_address_kelas",
        "keywords": ["ip address", "kelas ip", "ipv4", "ip publik", "ip privat", "range ip"],
        "questions": [
            "apa itu ip address?", "kelas ip address ada berapa?",
            "contoh ip privat dan publik", "range ip kelas c"
        ],
        "response": "🔢 <strong>IP Address (IPv4):</strong><br>Alamat logis 32-bit dibagi 4 oktet (0-255).<br>• <strong>Kelas A:</strong> 1.0.0.0 – 126.255.255.255 (Subnet mask /8)<br>• <strong>Kelas B:</strong> 128.0.0.0 – 191.255.255.255 (/16)<br>• <strong>Kelas C:</strong> 192.0.0.0 – 223.255.255.255 (/24)<br>• <strong>IP Privat:</strong> 10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16"
    },
    {
        "intent": "subnetting",
        "keywords": ["subnet", "subnetting", "prefix", "cidr", "/24", "netmask", "menghitung subnet"],
        "questions": [
            "bagaimana cara menghitung subnet mask?", "apa itu cidr?",
            "contoh subnetting kelas c", "cara membagi jaringan menjadi beberapa subnet"
        ],
        "response": "🧮 <strong>Subnetting Dasar:</strong><br>• <strong>Notasi CIDR:</strong> /24 berarti 24 bit network, sisa 8 bit host (254 host usable).<br>• <strong>Contoh:</strong> 192.168.1.0/24 -> Subnet mask 255.255.255.0.<br>• <strong>Menghitung Host:</strong> 2^(jumlah bit host) - 2 (network & broadcast).<br>• <strong>Membagi /24 menjadi 4 subnet:</strong> gunakan /26 -> mask 255.255.255.192, tiap subnet 62 host."
    },
    {
        "intent": "gateway_dns",
        "keywords": ["gateway", "default gateway", "dns", "domain name system", "fungsi gateway", "dns server"],
        "questions": [
            "apa itu default gateway?", "fungsi dns server",
            "bagaimana cara mengetahui ip gateway?", "dns google berapa"
        ],
        "response": "🚪 <strong>Gateway & DNS:</strong><br>• <strong>Default Gateway:</strong> Alamat IP perangkat (biasanya router) yang menjadi pintu keluar menuju jaringan lain (internet).<br>• <strong>DNS (Domain Name System):</strong> Menerjemahkan nama domain (google.com) menjadi IP address (142.250.x.x).<br>• <strong>DNS Publik Terkenal:</strong> Google 8.8.8.8 / 8.8.4.4, Cloudflare 1.1.1.1, OpenDNS 208.67.222.222."
    },
    {
        "intent": "ipv6",
        "keywords": ["ipv6", "alamat ipv6", "ip versi 6", "128 bit", "hexadesimal ipv6"],
        "questions": [
            "apa itu ipv6?", "contoh alamat ipv6",
            "perbedaan ipv4 dan ipv6", "berapa bit ipv6"
        ],
        "response": "🔢 <strong>IPv6 (Internet Protocol version 6):</strong><br>• <strong>Panjang:</strong> 128 bit (dibanding IPv4 32 bit).<br>• <strong>Format:</strong> 8 kelompok heksadesimal dipisah titik dua, contoh: <code>2001:0db8:85a3:0000:0000:8a2e:0370:7334</code>.<br>• <strong>Keunggulan:</strong> Kapasitas sangat besar, autoconfiguration, keamanan lebih baik (IPsec bawaan)."
    },

    # =========================================================================
    # 23. VLAN & SWITCHING
    # =========================================================================
    {
        "intent": "vlan_dasar",
        "keywords": ["vlan", "virtual lan", "membuat vlan", "vlan id", "trunk", "access port"],
        "questions": [
            "apa itu vlan?", "cara konfigurasi vlan di cisco",
            "fungsi vlan", "perbedaan access port dan trunk port"
        ],
        "response": "🔀 <strong>VLAN (Virtual LAN):</strong><br>Metode membagi satu switch fisik menjadi beberapa jaringan logis terpisah.<br>• <strong>Access Port:</strong> Port yang hanya membawa satu VLAN (untuk perangkat akhir).<br>• <strong>Trunk Port:</strong> Port yang membawa banyak VLAN antar switch (menggunakan tagging 802.1Q).<br>• <strong>Contoh VLAN ID:</strong> 10 (Guru), 20 (Siswa), 30 (Tamu).<br>• <strong>Perintah Cisco:</strong> <code>vlan 10</code>, <code>name GURU</code>."
    },
    {
        "intent": "stp_protocol",
        "keywords": ["stp", "spanning tree", "loop", "redundansi", "rstp", "pvst"],
        "questions": [
            "apa itu spanning tree protocol?", "fungsi stp pada switch",
            "cara mencegah loop jaringan", "perbedaan stp dan rstp"
        ],
        "response": "🌲 <strong>Spanning Tree Protocol (STP):</strong><br>Protokol untuk mencegah <strong>looping</strong> pada jaringan redundant switch.<br>• <strong>Cara Kerja:</strong> Memilih root bridge, menonaktifkan port yang menyebabkan loop.<br>• <strong>RSTP (Rapid STP):</strong> Konvergensi lebih cepat (1-2 detik vs 30-50 detik).<br>• <strong>PVST+:</strong> STP per VLAN (Cisco proprietary)."
    },
    {
        "intent": "port_security",
        "keywords": ["port security", "mac address", "keamanan port", "sticky mac", "violation"],
        "questions": [
            "bagaimana cara mengamankan port switch?", "apa itu port security?",
            "cara membatasi jumlah perangkat di satu port", "sticky mac address"
        ],
        "response": "🔒 <strong>Port Security pada Switch Cisco:</strong><br>Fitur untuk membatasi perangkat yang boleh terhubung ke satu port berdasarkan MAC address.<br>• <strong>Maximum MAC:</strong> Jumlah maksimal perangkat (misal 1).<br>• <strong>Violation Mode:</strong> Shutdown, restrict, protect.<br>• <strong>Sticky MAC:</strong> Switch otomatis menyimpan MAC pertama yang terdeteksi sebagai MAC yang aman."
    },

    # =========================================================================
    # 24. ROUTING & MIKROTIK LANJUT
    # =========================================================================
    {
        "intent": "routing_statis",
        "keywords": ["routing statis", "static route", "ip route", "menambah route", "default route"],
        "questions": [
            "apa itu routing statis?", "cara menambah static route di mikrotik",
            "contoh konfigurasi routing statis", "perbedaan routing statis dan dinamis"
        ],
        "response": "🗺️ <strong>Routing Statis:</strong><br>Metode routing manual dengan menambahkan entri jalur secara eksplisit.<br>• <strong>Perintah MikroTik:</strong> <code>/ip route add dst-address=192.168.2.0/24 gateway=192.168.1.1</code><br>• <strong>Kelebihan:</strong> Mudah dikonfigurasi, tidak memakan resource.<br>• <strong>Kekurangan:</strong> Harus diupdate manual jika ada perubahan topologi."
    },
    {
        "intent": "routing_dinamis",
        "keywords": ["ospf", "bgp", "rip", "routing dinamis", "protokol routing", "eigrp"],
        "questions": [
            "apa itu ospf?", "perbedaan ospf dan bgp",
            "cara konfigurasi ospf di mikrotik", "protokol routing dinamis"
        ],
        "response": "🔄 <strong>Routing Dinamis (Dynamic Routing):</strong><br>Router saling bertukar informasi rute secara otomatis.<br>• <strong>RIP:</strong> Distance vector, hop count maks 15, cocok jaringan kecil.<br>• <strong>OSPF:</strong> Link-state, area-based, cepat konvergen, digunakan di enterprise.<br>• <strong>BGP:</strong> Exterior gateway protocol, routing antar ISP/internet.<br>• <strong>EIGRP:</strong> Cisco proprietary, hybrid (distance vector + link state)."
    },
    {
        "intent": "hotspot_mikrotik",
        "keywords": ["hotspot", "voucher", "login hotspot", "mikrotik hotspot", "wallgarden", "userman"],
        "questions": [
            "bagaimana setup hotspot di mikrotik?", "cara membuat voucher hotspot",
            "konfigurasi halaman login mikrotik", "apa itu walled garden"
        ],
        "response": "📶 <strong>Hotspot Server MikroTik:</strong><br>• <strong>Setup:</strong> <code>/ip hotspot setup</code> pilih interface, tentukan IP, DNS, dan user default.<br>• <strong>Login Page:</strong> Otomatis dibuat di <code>hotspot/login.html</code>.<br>• <strong>Voucher:</strong> Gunakan <em>User Manager</em> atau sistem voucher eksternal.<br>• <strong>Walled Garden:</strong> Daftar situs yang bisa diakses tanpa login (misal halaman pembayaran)."
    },
    {
        "intent": "bandwidth_management",
        "keywords": ["bandwidth", "limit", "queue", "simple queue", "queue tree", "pcq", "traffic shaping"],
        "questions": [
            "cara limit bandwidth di mikrotik", "apa itu simple queue?",
            "perbedaan simple queue dan queue tree", "membagi bandwidth per user"
        ],
        "response": "📊 <strong>Manajemen Bandwidth MikroTik:</strong><br>• <strong>Simple Queue:</strong> Cara termudah, limit berdasarkan IP target/interface.<br>• <strong>Queue Tree:</strong> Lebih kompleks, menggunakan mangle untuk marking traffic.<br>• <strong>PCQ (Per Connection Queue):</strong> Membagi bandwidth rata per user/connection.<br>• <strong>Contoh Simple Queue:</strong> <code>/queue simple add target=192.168.1.0/24 max-limit=2M/2M</code>"
    },

    # =========================================================================
    # 25. FIREWALL & KEAMANAN JARINGAN
    # =========================================================================
    {
        "intent": "firewall_dasar",
        "keywords": ["firewall", "filter rules", "blokir situs", "blokir ip", "keamanan jaringan", "iptables"],
        "questions": [
            "bagaimana cara blokir akses internet di mikrotik?", "cara filter website di router",
            "apa itu firewall?", "cara blokir ip tertentu"
        ],
        "response": "🛡️ <strong>Firewall MikroTik (Filter Rules):</strong><br>• <strong>Blokir IP:</strong> <code>/ip firewall filter add chain=forward src-address=192.168.1.100 action=drop</code><br>• <strong>Blokir Website:</strong> Bisa menggunakan Layer 7 protocol atau DNS static.<br>• <strong>Contoh Blokir Port:</strong> <code>chain=forward protocol=tcp dst-port=23 action=drop</code>"
    },
    {
        "intent": "vpn",
        "keywords": ["vpn", "pptp", "l2tp", "sstp", "openvpn", "ipsec", "tunnel"],
        "questions": [
            "cara setting vpn di mikrotik", "apa itu vpn?",
            "jenis-jenis vpn", "perbedaan pptp dan l2tp"
        ],
        "response": "🔐 <strong>VPN (Virtual Private Network):</strong><br>Membuat koneksi aman terenkripsi melalui jaringan publik.<br>• <strong>PPTP:</strong> Mudah, keamanan rendah (usang).<br>• <strong>L2TP/IPsec:</strong> Lebih aman, didukung banyak perangkat.<br>• <strong>SSTP:</strong> Berbasis SSL, port 443 (sulit diblokir).<br>• <strong>OpenVPN:</strong> Open source, fleksibel, keamanan tinggi.<br>• <strong>WireGuard:</strong> Modern, cepat, minimalis."
    },
    {
        "intent": "keamanan_wifi",
        "keywords": ["wifi security", "wpa", "wpa2", "wpa3", "wep", "password wifi", "keamanan wireless"],
        "questions": [
            "apa itu wpa2?", "perbedaan wpa dan wpa2",
            "standar keamanan wifi terbaru", "bagaimana cara mengamankan wifi"
        ],
        "response": "📶 <strong>Keamanan Jaringan Wireless:</strong><br>• <strong>WEP:</strong> Sangat lemah, mudah diretas.<br>• <strong>WPA:</strong> Perbaikan dari WEP, masih bisa diserang.<br>• <strong>WPA2:</strong> Standar saat ini, gunakan AES (bukan TKIP).<br>• <strong>WPA3:</strong> Terbaru, perlindungan lebih kuat terhadap brute force.<br>• <strong>Tips:</strong> Gunakan password panjang, nonaktifkan WPS, ganti SSID default."
    },

    # =========================================================================
    # 26. SERVER & LAYANAN (DHCP, DNS, FTP, WEB)
    # =========================================================================
    {
        "intent": "dhcp_server",
        "keywords": ["dhcp", "dhcp server", "ip otomatis", "lease time", "dhcp pool"],
        "questions": [
            "cara setting dhcp server di mikrotik", "apa itu dhcp?",
            "bagaimana cara memberi ip otomatis", "lease time dhcp"
        ],
        "response": "📡 <strong>DHCP Server:</strong><br>• <strong>Fungsi:</strong> Memberikan IP address secara otomatis kepada client.<br>• <strong>Setup MikroTik:</strong> <code>/ip dhcp-server setup</code> pilih interface, tentukan pool.<br>• <strong>Lease Time:</strong> Durasi peminjaman IP (default 10 menit).<br>• <strong>Pool:</strong> Range IP yang akan dibagikan, misal 192.168.1.10-192.168.1.100."
    },
    {
        "intent": "dns_server",
        "keywords": ["dns server", "membuat dns", "dns lokal", "bind", "forwarder", "domain lokal"],
        "questions": [
            "cara setting dns server di server sendiri", "apa itu dns forwarder?",
            "membuat domain lokal", "cara install bind9"
        ],
        "response": "🌐 <strong>DNS Server Lokal:</strong><br>• <strong>Software:</strong> BIND9 (Linux), atau fitur DNS pada MikroTik.<br>• <strong>Forwarder:</strong> Meneruskan query ke DNS publik (8.8.8.8).<br>• <strong>Domain Lokal:</strong> Contoh <code>lab.tkj</code> diarahkan ke IP server.<br>• <strong>Perintah dasar BIND:</strong> edit <code>/etc/bind/named.conf.local</code>, definisikan zone, buat file db."
    },
    {
        "intent": "ftp_server",
        "keywords": ["ftp", "file transfer", "server ftp", "vsftpd", "proftpd", "upload file"],
        "questions": [
            "cara setting ftp server di linux", "apa itu ftp?",
            "software ftp server", "cara mengakses ftp dari windows"
        ],
        "response": "📁 <strong>FTP Server:</strong><br>• <strong>Fungsi:</strong> Transfer file antar perangkat dalam jaringan.<br>• <strong>Software Linux:</strong> vsftpd (sangat aman), ProFTPD.<br>• <strong>Port default:</strong> 21 untuk kontrol, 20 untuk data (mode aktif) atau random port (pasif).<br>• <strong>Klien FTP:</strong> FileZilla, WinSCP, atau browser (ftp://alamat)."
    },
    {
        "intent": "web_server",
        "keywords": ["web server", "apache", "nginx", "http", "hosting lokal", "php", "xampp"],
        "questions": [
            "cara membuat web server lokal", "apa itu apache?",
            "perbedaan apache dan nginx", "cara install xampp"
        ],
        "response": "🌍 <strong>Web Server:</strong><br>• <strong>Apache:</strong> Paling populer, banyak modul, cocok untuk hosting bersama.<br>• <strong>Nginx:</strong> Ringan, cepat, cocok untuk situs dengan traffic tinggi atau reverse proxy.<br>• <strong>XAMPP:</strong> Paket Apache + MySQL + PHP + Perl untuk Windows/Linux (praktis untuk belajar).<br>• <strong>Port default:</strong> 80 (HTTP) dan 443 (HTTPS)."
    },

    # =========================================================================
    # 27. TROUBLESHOOTING UMUM JARINGAN
    # =========================================================================
    {
        "intent": "koneksi_lambat",
        "keywords": ["lambat", "lemot", "koneksi lambat", "internet lambat", "latency", "ping tinggi"],
        "questions": [
            "kenapa internet di lab lambat?", "cara mengatasi koneksi lambat",
            "ping tinggi ke router", "penyebab jaringan lemot"
        ],
        "response": "🐢 <strong>Troubleshooting Koneksi Lambat:</strong><br>1. Cek bandwidth yang terpakai (gunakan <code>torch</code> di MikroTik).<br>2. Periksa ada tidaknya loop jaringan.<br>3. Tes ping ke gateway dan ke 8.8.8.8.<br>4. Cek CPU usage router.<br>5. Lakukan speedtest untuk membandingkan dengan bandwidth dari ISP."
    },
    {
        "intent": "ip_conflict",
        "keywords": ["ip conflict", "bentrokan ip", "duplikat ip", "ip sama", "arp conflict"],
        "questions": [
            "bagaimana cara mengatasi ip conflict?", "apa penyebab ip conflict?",
            "duplikat ip di jaringan", "pesan ip conflict di windows"
        ],
        "response": "⚠️ <strong>IP Conflict:</strong><br>Terjadi jika dua perangkat menggunakan IP yang sama.<br>• <strong>Penyebab:</strong> IP statis ganda, DHCP server ganda, atau client yang tidak release IP.<br>• <strong>Solusi:</strong> Cek dengan <code>arp -a</code> untuk melihat MAC, gunakan DHCP dengan reservation, atau set IP statis di luar range DHCP."
    },
    {
        "intent": "wifi_tidak_connect",
        "keywords": ["wifi tidak connect", "tidak bisa connect wifi", "gagal konek wifi", "limited access", "authentication problem"],
        "questions": [
            "kenapa laptop tidak bisa terhubung ke wifi?", "wifi connected but no internet",
            "cara mengatasi limited access wifi", "authentication problem wifi"
        ],
        "response": "📶 <strong>Troubleshooting WiFi Tidak Connect:</strong><br>1. Pastikan password benar (case sensitive).<br>2. Lupakan (forget) jaringan lalu reconnect.<br>3. Periksa apakah MAC filtering aktif di router.<br>4. Cek sinyal: terlalu jauh atau terhalang tembok tebal.<br>5. Restart adapter WiFi (disable/enable).<br>6. Update driver wireless."
    },
    {
        "intent": "tidak_bisa_internet",
        "keywords": ["tidak bisa internet", "no internet", "terputus", "internet mati", "wifi connected no internet"],
        "questions": [
            "sudah connect wifi tapi tidak bisa internet", "laptop connected tapi tidak bisa browsing",
            "cara cek koneksi internet", "tidak ada akses internet"
        ],
        "response": "🔌 <strong>Langkah Cek Tidak Bisa Internet:</strong><br>1. <code>ping 8.8.8.8</code> -> jika berhasil, masalah DNS.<br>2. <code>nslookup google.com</code> -> cek apakah DNS resolving bekerja.<br>3. Cek gateway: <code>ipconfig</code> (Windows) atau <code>ifconfig</code> (Linux) pastikan default gateway benar.<br>4. Cek kabel WAN di router, status di ISP."
    },

    # =========================================================================
    # 28. PANDUAN PRAKTIKUM KHUSUS
    # =========================================================================
    {
        "intent": "praktikum_vlan",
        "keywords": ["praktikum vlan", "tugas vlan", "langkah konfigurasi vlan", "switch vlan", "konfigurasi vlan antar switch"],
        "questions": [
            "langkah praktikum vlan di cisco", "tugas konfigurasi vlan",
            "bagaimana menghubungkan 2 vlan berbeda?", "cara setting vlan di switch"
        ],
        "response": "🔧 <strong>Langkah Praktikum VLAN Cisco:</strong><br>1. Buat VLAN: <code>vlan 10</code>, <code>name SISWA</code>.<br>2. Assign port ke VLAN: <code>interface fastEthernet 0/1</code>, <code>switchport mode access</code>, <code>switchport access vlan 10</code>.<br>3. Konfigurasi trunk antar switch: <code>switchport mode trunk</code>.<br>4. Routing antar VLAN memerlukan device L3 (router atau switch L3)."
    },
    {
        "intent": "praktikum_hotspot",
        "keywords": ["praktikum hotspot", "tugas hotspot", "konfigurasi hotspot", "voucher hotspot", "login page mikrotik"],
        "questions": [
            "langkah praktikum hotspot mikrotik", "tugas membuat voucher wifi",
            "cara mengubah tampilan login hotspot", "konfigurasi hotspot untuk ukk"
        ],
        "response": "📶 <strong>Praktikum Hotspot MikroTik:</strong><br>1. Setup: <code>/ip hotspot setup</code> pilih interface, set IP.<br>2. Buat user: <code>/ip hotspot user add name=test password=123</code>.<br>3. Akses login page: buka <code>http://[IP hotspot]</code>.<br>4. Kustomisasi: edit file <code>login.html</code> di <code>Files</code>.<br>5. Buat voucher: gunakan <em>User Manager</em> untuk membuat banyak user dengan batas waktu."
    },
    {
        "intent": "praktikum_fiber_splicing",
        "keywords": ["praktikum splicing", "tugas fiber optic", "langkah splicing", "sambung fiber", "fusi fiber"],
        "questions": [
            "langkah praktikum fusion splicer", "tugas penyambungan fiber optic",
            "cara memotong fiber optic", "prosedur splicing yang benar"
        ],
        "response": "💡 <strong>Langkah Praktikum Splicing Fiber Optic:</strong><br>1. Kupas jaket kabel menggunakan stripper dropcore.<br>2. Bersihkan serat kaca dengan alkohol dan tissue khusus.<br>3. Potong core dengan fiber cleaver (sudut 90°).<br>4. Masukkan ke fusion splicer, lakukan alignment dan fusi.<br>5. Lindungi sambungan dengan protection sleeve.<br>6. Ukur loss menggunakan OPM atau splicer (harus < 0.05 dB)."
    },
    {
        "intent": "praktikum_crimping",
        "keywords": ["praktikum crimping", "tugas crimping", "cara crimping rj45", "langkah crimping", "ujian crimping"],
        "questions": [
            "langkah praktikum crimping kabel lan", "tugas membuat kabel straight",
            "cara memasang konektor rj45", "prosedur crimping yang benar"
        ],
        "response": "✂️ <strong>Langkah Praktikum Crimping RJ45:</strong><br>1. Kupas jaket luar kabel UTP sekitar 3-4 cm.<br>2. Susun 8 kabel sesuai urutan T568B.<br>3. Ratakan dan potong ujung kabel agar sama panjang.<br>4. Masukkan ke konektor RJ45 dengan posisi pin menghadap ke atas.<br>5. Tekan tang crimping dengan kuat hingga berbunyi klik.<br>6. Tes dengan LAN tester."
    },

    # =========================================================================
    # 29. KEBIJAKAN TAMBAHAN LAB
    # =========================================================================
    {
        "intent": "kebijakan_software",
        "keywords": ["software legal", "lisensi", "software bajakan", "instal software", "aplikasi lab"],
        "questions": [
            "bolehkah menginstall software sendiri di pc lab?", "kebijakan penggunaan software di lab",
            "apakah boleh membawa software dari luar?", "lisensi software lab"
        ],
        "response": "💿 <strong>Kebijakan Software Laboratorium:</strong><br>• Dilarang menginstall software tanpa izin teknisi lab.<br>• Hanya software yang telah dilisensikan atau open source yang boleh digunakan.<br>• Penggunaan software bajakan dapat dikenakan sanksi disiplin.<br>• Jika membutuhkan software khusus untuk praktikum, ajukan permohonan ke Kepala Lab."
    },
    {
        "intent": "penggunaan_listrik",
        "keywords": ["listrik", "daya", "colokan", "power", "steker", "kabel listrik"],
        "questions": [
            "aturan penggunaan listrik di lab", "berapa daya listrik lab tkj?",
            "bolehkah mencolok charger sendiri?", "penggunaan stop kontak lab"
        ],
        "response": "⚡ <strong>Aturan Penggunaan Listrik:</strong><br>• Dilarang mencolok perangkat pribadi tanpa izin.<br>• Jangan membebani satu stop kontak dengan banyak perangkat.<br>• Matikan perangkat setelah selesai digunakan.<br>• Segera laporkan kabel atau stop kontak yang rusak."
    },
    {
        "intent": "keamanan_data",
        "keywords": ["data pribadi", "keamanan data", "backup", "flashdisk", "virus", "ransomware"],
        "questions": [
            "bagaimana mengamankan data praktikum?", "aturan penggunaan flashdisk di lab",
            "cara mencegah virus di komputer lab", "backup data siswa"
        ],
        "response": "🔒 <strong>Keamanan Data & Perangkat:</strong><br>• Jangan mencolokkan flashdisk yang tidak dikenal ke PC lab.<br>• Selalu backup file tugas di cloud atau media pribadi.<br>• Gunakan antivirus yang selalu diupdate.<br>• Jangan berbagi password akun kepada orang lain."
    },

    # =========================================================================
    # 30. FAQ LAINNYA
    # =========================================================================
    {
        "intent": "cara_login",
        "keywords": ["cara login", "login akun", "gagal login", "tidak bisa masuk", "password salah"],
        "questions": [
            "bagaimana cara login ke sistem?", "saya tidak bisa login akun lab",
            "password salah terus", "cara masuk dashboard peminjaman"
        ],
        "response": "🔑 <strong>Bantuan Login:</strong><br>• Gunakan NIS/NIP sebagai username dan password yang telah didaftarkan.<br>• Pastikan CAPS LOCK tidak aktif.<br>• Jika lupa password, hubungi Admin untuk reset.<br>• Bersihkan cache browser atau coba mode incognito."
    },
    {
        "intent": "notifikasi_whatsapp",
        "keywords": ["notifikasi", "whatsapp", "pesan wa", "info wa", "notif peminjaman"],
        "questions": [
            "apakah ada notifikasi whatsapp untuk peminjaman?", "cara mengaktifkan notifikasi wa",
            "kenapa saya tidak menerima notifikasi wa?", "update nomor wa untuk notifikasi"
        ],
        "response": "📲 <strong>Notifikasi WhatsApp:</strong><br>• Pastikan nomor WhatsApp di profil sudah benar dan aktif.<br>• Notifikasi dikirim saat pengajuan diproses atau jatuh tempo.<br>• Jika tidak menerima, periksa apakah nomor terdaftar dengan format 62xxx.<br>• Hubungi Admin jika masalah berlanjut."
    },
    {
        "intent": "bahasa_indonesia",
        "keywords": ["bahasa", "indonesia", "inggris", "translate", "arti istilah"],
        "questions": [
            "apakah chatbot bisa bahasa inggris?", "saya tidak mengerti istilah jaringan",
            "terjemahkan istilah tkj", "kamus istilah jaringan"
        ],
        "response": "🇮🇩 <strong>Bahasa:</strong><br>Sistem beroperasi dalam Bahasa Indonesia. Beberapa istilah teknis tetap menggunakan istilah asing yang umum (contoh: router, switch, VLAN). Anda dapat bertanya arti istilah tersebut dan saya akan menjelaskannya dalam bahasa Indonesia."
    },
    {
        "intent": "permintaan_fitur",
        "keywords": ["fitur baru", "saran", "request fitur", "usulan", "pengembangan"],
        "questions": [
            "saya punya saran fitur untuk sistem", "bagaimana cara mengusulkan fitur baru?",
            "mau request penambahan menu", "feedback untuk lab"
        ],
        "response": "💡 <strong>Usulan Fitur:</strong><br>Kami terbuka untuk masukan. Silakan sampaikan saran Anda melalui WhatsApp Admin atau langsung ke Koordinator Lab. Tim pengembang akan mengevaluasi dan mungkin mengimplementasikannya pada versi berikutnya."
    },

    # =========================================================================
    # 31. TAMBAHAN PANDUAN MIKROTIK LANJUT
    # =========================================================================
    {
        "intent": "mikrotik_wireless",
        "keywords": ["mikrotik wireless", "wlan", "wireless mikrotik", "ssid", "bridge mode", "ap mode"],
        "questions": [
            "cara setting wireless di mikrotik", "mode wireless mikrotik",
            "membuat ssid di routerboard", "konfigurasi ap mikrotik"
        ],
        "response": "📡 <strong>Konfigurasi Wireless MikroTik:</strong><br>• <strong>AP Mode:</strong> <code>/interface wireless set wlan1 mode=ap-bridge ssid=LabTKJ</code><br>• <strong>Security:</strong> <code>/interface wireless security-profiles set default authentication-types=wpa2-psk mode=dynamic-keys wpa2-pre-shared-key=password</code><br>• <strong>Bridge:</strong> Gabungkan wlan1 dengan ethernet agar client WiFi mendapat IP dari jaringan yang sama."
    },
    {
        "intent": "mikrotik_backup",
        "keywords": ["backup mikrotik", "export config", "simpan konfigurasi", "restore mikrotik", ".backup"],
        "questions": [
            "cara backup konfigurasi mikrotik", "export setting routerboard",
            "menyimpan konfigurasi mikrotik", "restore mikrotik dari file backup"
        ],
        "response": "💾 <strong>Backup & Restore MikroTik:</strong><br>• <strong>Backup Biner:</strong> <code>/system backup save name=backup-2024</code> (file .backup).<br>• <strong>Export Script:</strong> <code>/export file=config</code> (menghasilkan file .rsc berisi perintah teks).<br>• <strong>Restore:</strong> Upload file .backup lalu <code>/system backup load name=backup-2024</code>.<br>• Selalu simpan backup di tempat aman sebelum melakukan perubahan besar."
    },
    {
        "intent": "mikrotik_firewall_l7",
        "keywords": ["layer 7", "l7", "blokir aplikasi", "blokir youtube", "blokir game", "regex l7"],
        "questions": [
            "cara blokir aplikasi tertentu di mikrotik", "layer 7 protocol mikrotik",
            "memblokir game online", "memblokir youtube dengan mikrotik"
        ],
        "response": "🚫 <strong>Blokir Aplikasi dengan Layer 7 Protocol:</strong><br>1. Buat L7 regex: <code>/ip firewall layer7-protocol add name=blokir-yt regexp=\"^.+(youtube.com).*$\"</code><br>2. Buat filter rule: <code>/ip firewall filter add chain=forward layer7-protocol=blokir-yt action=drop</code><br>Catatan: Metode L7 tidak 100% akurat karena banyak aplikasi menggunakan enkripsi (HTTPS)."
    },
    {
        "intent": "mikrotik_user_manager",
        "keywords": ["user manager", "userman", "voucher generator", "user hotspot", "batas waktu", "limit kuota"],
        "questions": [
            "cara generate voucher di mikrotik", "apa itu user manager?",
            "membuat user dengan kuota terbatas", "konfigurasi user manager hotspot"
        ],
        "response": "👥 <strong>User Manager MikroTik:</strong><br>Paket untuk manajemen user hotspot, PPP, dan DHCP.<br>• <strong>Aktifkan:</strong> <code>/tool user-manager set enabled=yes</code><br>• <strong>Tambah User:</strong> Melalui web interface <code>http://[IP router]/userman</code><br>• <strong>Voucher:</strong> Buat batch user dengan profil tertentu (kecepatan, waktu, kuota)."
    },

    # =========================================================================
    # 32. PANDUAN CISCO IOS
    # =========================================================================
    {
        "intent": "cisco_mode",
        "keywords": ["cisco ios", "user mode", "privileged mode", "global config", "interface mode", "line mode"],
        "questions": [
            "mode-mode pada cisco ios", "perbedaan user mode dan privileged mode",
            "cara masuk global configuration mode", "mode konfigurasi cisco"
        ],
        "response": "🖥️ <strong>Mode pada Cisco IOS:</strong><br>• <strong>User EXEC Mode:</strong> Prompt <code>Switch></code>, hanya melihat status.<br>• <strong>Privileged EXEC Mode:</strong> Prompt <code>Switch#</code>, dapat melihat semua dan backup.<br>• <strong>Global Configuration Mode:</strong> Prompt <code>Switch(config)#</code>, konfigurasi global.<br>• <strong>Interface Mode:</strong> <code>Switch(config-if)#</code>, konfigurasi port tertentu.<br>• <strong>Line Mode:</strong> <code>Switch(config-line)#</code>, konfigurasi akses (console/vty)."
    },
    {
        "intent": "cisco_basic_commands",
        "keywords": ["perintah cisco", "show running-config", "show version", "hostname", "enable password", "line vty"],
        "questions": [
            "perintah dasar cisco", "cara set hostname switch",
            "menyimpan konfigurasi cisco", "menampilkan konfigurasi berjalan"
        ],
        "response": "⌨️ <strong>Perintah Dasar Cisco IOS:</strong><br>• <code>enable</code> - masuk privileged mode.<br>• <code>configure terminal</code> - masuk global config.<br>• <code>hostname NAMA</code> - set nama perangkat.<br>• <code>show running-config</code> - lihat konfigurasi aktif.<br>• <code>copy running-config startup-config</code> - simpan konfigurasi.<br>• <code>show version</code> - informasi perangkat."
    },
    {
        "intent": "cisco_telnet_ssh",
        "keywords": ["telnet", "ssh", "remote access", "vty", "konfigurasi ssh", "enable secret"],
        "questions": [
            "cara remote switch cisco", "setting ssh di cisco",
            "perbedaan telnet dan ssh", "cara mengaktifkan telnet"
        ],
        "response": "🔐 <strong>Remote Access Cisco (Telnet/SSH):</strong><br>• <strong>Telnet:</strong> Tidak terenkripsi, tidak disarankan.<br>• <strong>SSH:</strong> Aman, menggunakan enkripsi.<br>• <strong>Konfigurasi SSH:</strong> set domain, generate RSA key, buat user, aktifkan pada line vty.<br>• <strong>Line VTY:</strong> <code>line vty 0 4</code>, <code>login local</code>, <code>transport input ssh</code>."
    },

    # =========================================================================
    # 33. PANDUAN LINUX & SERVER
    # =========================================================================
    {
        "intent": "linux_dasar",
        "keywords": ["linux", "perintah linux", "terminal", "ubuntu", "debian", "command line"],
        "questions": [
            "perintah dasar linux", "cara menggunakan terminal ubuntu",
            "distro linux untuk server", "perintah navigasi file linux"
        ],
        "response": "🐧 <strong>Perintah Dasar Linux:</strong><br>• <code>ls</code> - list file.<br>• <code>cd</code> - pindah direktori.<br>• <code>cp</code> - copy file.<br>• <code>mv</code> - pindah/rename.<br>• <code>rm</code> - hapus file.<br>• <code>sudo</code> - menjalankan perintah sebagai superuser.<br>• <code>apt update && apt upgrade</code> - update sistem (Debian/Ubuntu)."
    },
    {
        "intent": "linux_network",
        "keywords": ["ifconfig", "ip addr", "network manager", "static ip linux", "ethernet linux"],
        "questions": [
            "cara setting ip statis di linux", "perintah cek ip linux",
            "konfigurasi jaringan ubuntu", "file konfigurasi network linux"
        ],
        "response": "🖧 <strong>Konfigurasi Jaringan Linux:</strong><br>• <strong>Cek IP:</strong> <code>ip addr show</code> atau <code>ifconfig</code>.<br>• <strong>Set IP Statis (Ubuntu 18.04+):</strong> edit file <code>/etc/netplan/01-netcfg.yaml</code>, lalu <code>netplan apply</code>.<br>• <strong>Restart Network:</strong> <code>systemctl restart networking</code> (Debian) atau <code>systemctl restart NetworkManager</code>."
    },
    {
        "intent": "linux_services",
        "keywords": ["systemctl", "service linux", "start service", "enable service", "status service"],
        "questions": [
            "cara menjalankan service di linux", "perintah systemctl",
            "mengaktifkan service saat boot", "mengecek status service"
        ],
        "response": "⚙️ <strong>Manajemen Service Linux (systemd):</strong><br>• <code>systemctl start [service]</code> - jalankan service.<br>• <code>systemctl stop [service]</code> - hentikan.<br>• <code>systemctl restart [service]</code> - restart.<br>• <code>systemctl status [service]</code> - lihat status.<br>• <code>systemctl enable [service]</code> - aktifkan saat boot.<br>• Contoh: <code>systemctl restart apache2</code>."
    },

    # =========================================================================
    # 34. KEAMANAN JARINGAN LANJUT
    # =========================================================================
    {
        "intent": "serangan_jaringan",
        "keywords": ["serangan", "ddos", "phishing", "malware", "virus", "ransomware", "serangan siber"],
        "questions": [
            "jenis serangan jaringan", "apa itu ddos?",
            "cara mencegah phishing", "contoh serangan siber"
        ],
        "response": "🛡️ <strong>Jenis Serangan Jaringan:</strong><br>• <strong>DDoS:</strong> Membanjiri server dengan traffic hingga tidak bisa diakses.<br>• <strong>Phishing:</strong> Menipu korban untuk memberikan data sensitif melalui situs/email palsu.<br>• <strong>Malware/Virus:</strong> Software berbahaya yang merusak atau mencuri data.<br>• <strong>Man-in-the-Middle (MitM):</strong> Penyadapan komunikasi.<br>• <strong>Brute Force:</strong> Mencoba banyak password untuk masuk."
    },
    {
        "intent": "keamanan_password",
        "keywords": ["password kuat", "keamanan password", "hash", "password hash", "brute force protection"],
        "questions": [
            "cara membuat password yang kuat", "apa itu hash password?",
            "menghindari brute force", "enkripsi password"
        ],
        "response": "🔑 <strong>Keamanan Password:</strong><br>• Gunakan minimal 12 karakter kombinasi huruf besar/kecil, angka, simbol.<br>• Jangan gunakan kata umum atau data pribadi.<br>• Gunakan password manager.<br>• Password disimpan dalam bentuk hash (misal bcrypt, SHA-256 dengan salt), bukan plaintext.<br>• Aktifkan two-factor authentication (2FA) jika memungkinkan."
    },

    # =========================================================================
    # 35. PANDUAN WINDOWS SERVER
    # =========================================================================
    {
        "intent": "active_directory",
        "keywords": ["active directory", "ad", "domain controller", "gpo", "user management", "ldap"],
        "questions": [
            "apa itu active directory?", "cara install active directory",
            "fungsi domain controller", "mengelola user di AD"
        ],
        "response": "🏢 <strong>Active Directory (AD):</strong><br>Layanan direktori Microsoft untuk manajemen terpusat user, komputer, dan kebijakan.<br>• <strong>Domain Controller (DC):</strong> Server yang menjalankan AD.<br>• <strong>GPO (Group Policy):</strong> Menerapkan kebijakan ke banyak komputer sekaligus.<br>• <strong>Instalasi:</strong> Melalui Server Manager > Add Roles > Active Directory Domain Services."
    },
    {
        "intent": "dns_windows",
        "keywords": ["dns server windows", "forward lookup zone", "reverse lookup", "dns zone", "windows dns"],
        "questions": [
            "cara setting dns server di windows server", "membuat forward lookup zone",
            "perbedaan forward dan reverse zone", "dns di active directory"
        ],
        "response": "🖥️ <strong>DNS Server di Windows Server:</strong><br>• Buka Server Manager > DNS.<br>• Buat zone baru: Forward Lookup Zone untuk domain.<br>• Tambahkan host (A record) atau alias (CNAME).<br>• Forwarder ke DNS publik jika diperlukan.<br>• Integrasi dengan Active Directory untuk replikasi."
    },

    # =========================================================================
    # 36. PANDUAN VIRTUALISASI
    # =========================================================================
    {
        "intent": "virtualbox",
        "keywords": ["virtualbox", "virtual machine", "vm", "instal virtualbox", "mesin virtual"],
        "questions": [
            "cara install virtualbox di windows", "apa itu virtual machine?",
            "membuat vm linux", "setting jaringan virtualbox"
        ],
        "response": "💻 <strong>VirtualBox:</strong><br>Software virtualisasi gratis untuk menjalankan sistem operasi lain di dalam komputer.<br>• <strong>Install:</strong> Download dari virtualbox.org, jalankan installer.<br>• <strong>Buat VM:</strong> Klik New, pilih tipe OS, alokasikan RAM dan disk.<br>• <strong>Jaringan:</strong> Mode NAT untuk akses internet, Bridge untuk IP satu jaringan, Host-only untuk jaringan lokal antar VM."
    },
    {
        "intent": "vmware",
        "keywords": ["vmware", "vmware workstation", "esxi", "virtualisasi server", "vsphere"],
        "questions": [
            "perbedaan virtualbox dan vmware", "apa itu vmware esxi?",
            "virtualisasi server", "cara install vmware workstation"
        ],
        "response": "🖥️ <strong>VMware:</strong><br>Perusahaan penyedia solusi virtualisasi.<br>• <strong>VMware Workstation:</strong> Virtualisasi di komputer pribadi (mirip VirtualBox).<br>• <strong>VMware ESXi:</strong> Hypervisor langsung di atas hardware (bare-metal) untuk server.<br>• <strong>vSphere:</strong> Platform manajemen untuk banyak host ESXi."
    },

    # =========================================================================
    # 37. INTERNET OF THINGS (IOT) DASAR
    # =========================================================================
    {
        "intent": "iot_pengertian",
        "keywords": ["iot", "internet of things", "arduino", "raspberry", "sensor", "mikrokontroler"],
        "questions": [
            "apa itu iot?", "contoh perangkat iot",
            "belajar iot mulai dari mana?", "perbedaan arduino dan raspberry"
        ],
        "response": "🌐 <strong>Internet of Things (IoT):</strong><br>Konsep perangkat fisik yang saling terhubung dan bertukar data melalui internet.<br>• <strong>Contoh:</strong> Smart home, sensor suhu, kamera CCTV, smart agriculture.<br>• <strong>Mikrokontroler:</strong> Arduino (sederhana, C/C++), ESP8266/ESP32 (WiFi built-in).<br>• <strong>Komputer mini:</strong> Raspberry Pi (Linux, bisa menjalankan server)."
    },
    {
        "intent": "sensor_dasar",
        "keywords": ["sensor suhu", "dht11", "dht22", "ultrasonik", "pir", "sensor jarak"],
        "questions": [
            "jenis sensor untuk iot", "cara menggunakan sensor dht11",
            "sensor suhu dan kelembaban", "sensor gerak untuk proyek"
        ],
        "response": "🌡️ <strong>Sensor Umum untuk Proyek IoT:</strong><br>• <strong>DHT11/DHT22:</strong> Suhu dan kelembaban.<br>• <strong>HC-SR04:</strong> Ultrasonik, mengukur jarak.<br>• <strong>PIR:</strong> Mendeteksi gerakan.<br>• <strong>LDR:</strong> Sensor cahaya.<br>• <strong>MQ-2:</strong> Sensor gas/asap."
    },

    # =========================================================================
    # 38. PROYEK TKJ & TUGAS AKHIR
    # =========================================================================
    {
        "intent": "ide_proyek_tkj",
        "keywords": ["proyek tkj", "tugas akhir", "skripsi", "judul proyek", "proyek jaringan", "tugas besar"],
        "questions": [
            "ide proyek jaringan untuk siswa tkj", "contoh tugas akhir tkj",
            "proyek sederhana mikrotik", "judul proyek fiber optic"
        ],
        "response": "💡 <strong>Ide Proyek TKJ:</strong><br>• <strong>MikroTik:</strong> Hotspot dengan voucher, load balancing dua ISP, VPN server.<br>• <strong>Cisco:</strong> Desain VLAN untuk sekolah, inter-VLAN routing.<br>• <strong>Fiber Optic:</strong> Instalasi kabel FO antar gedung, pengukuran redaman.<br>• <strong>IoT:</strong> Monitoring suhu ruang server menggunakan ESP8266 dan Thingspeak."
    },
    {
        "intent": "dokumentasi_proyek",
        "keywords": ["dokumentasi", "laporan proyek", "format laporan", "bab 1", "cover laporan"],
        "questions": [
            "format laporan proyek tkj", "cara menyusun laporan praktikum",
            "apa saja isi laporan proyek?", "template laporan tugas akhir"
        ],
        "response": "📝 <strong>Struktur Laporan Proyek TKJ:</strong><br>1. Cover / Halaman Judul.<br>2. Kata Pengantar.<br>3. Daftar Isi.<br>4. Bab 1 Pendahuluan (Latar Belakang, Rumusan Masalah, Tujuan).<br>5. Bab 2 Landasan Teori.<br>6. Bab 3 Perancangan dan Implementasi.<br>7. Bab 4 Pengujian dan Analisis.<br>8. Bab 5 Penutup (Kesimpulan & Saran).<br>9. Daftar Pustaka."
    },

    # =========================================================================
    # 39. STANDAR & SERTIFIKASI TKJ
    # =========================================================================
    {
        "intent": "sertifikasi_mikrotik",
        "keywords": ["mtcna", "mtcre", "sertifikasi mikrotik", "ujian mikrotik", "mikrotik academy"],
        "questions": [
            "apa itu mtcna?", "cara mendapatkan sertifikasi mikrotik",
            "ujian mikrotik untuk siswa", "materi mtcna"
        ],
        "response": "🎓 <strong>Sertifikasi MikroTik:</strong><br>• <strong>MTCNA (MikroTik Certified Network Associate):</strong> Sertifikasi dasar, mencakup routing, firewall, wireless, QoS.<br>• <strong>MTCRE (MikroTik Certified Routing Engineer):</strong> Routing lanjut (OSPF, BGP).<br>• <strong>Ujian:</strong> Online atau di tempat training, biasanya 60 soal pilihan ganda, passing 60%."
    },
    {
        "intent": "sertifikasi_cisco",
        "keywords": ["ccna", "ccnp", "cisco certified", "sertifikasi cisco", "ujian ccna"],
        "questions": [
            "apa itu ccna?", "syarat ujian ccna",
            "materi ccna", "perbedaan ccna dan ccnp"
        ],
        "response": "🎓 <strong>Sertifikasi Cisco:</strong><br>• <strong>CCNA (Cisco Certified Network Associate):</strong> Dasar jaringan, routing & switching, keamanan, otomasi.<br>• <strong>CCNP (Cisco Certified Network Professional):</strong> Level lanjut.<br>• <strong>Ujian:</strong> 200-301 CCNA, 120 menit, passing sekitar 825/1000."
    },

    # =========================================================================
    # 40. TIPS & TRIK PRAKTIKUM
    # =========================================================================
    {
        "intent": "tips_crimping",
        "keywords": ["tips crimping", "kabel sering gagal", "rj45 tidak connect", "crimping rapi"],
        "questions": [
            "tips crimping agar berhasil", "kenapa kabel crimping tidak connect?",
            "cara membuat kabel lan rapi", "kesalahan umum crimping"
        ],
        "response": "💡 <strong>Tips Crimping Kabel LAN:</strong><br>• Pastikan urutan warna benar sebelum memotong.<br>• Gunakan stripper khusus agar tidak melukai kabel dalam.<br>• Ratakan ujung kabel dengan pemotong, pastikan semua masuk sampai ujung konektor.<br>• Tekan tang crimping dengan sekali gerakan kuat.<br>• Selalu tes dengan LAN tester."
    },
    {
        "intent": "tips_splicing",
        "keywords": ["tips splicing", "fiber gagal", "loss tinggi", "splicer error", "sambungan fiber jelek"],
        "questions": [
            "tips splicing fiber optic", "kenapa hasil splicing loss tinggi?",
            "cara membersihkan fiber sebelum splicing", "kesalahan splicing"
        ],
        "response": "💡 <strong>Tips Splicing Fiber Optic:</strong><br>• Bersihkan meja kerja dari debu.<br>• Gunakan alkohol 99% untuk membersihkan serat.<br>• Potong dengan cleaver tepat sebelum dimasukkan ke splicer.<br>• Pastikan elektroda splicer dalam kondisi baik.<br>• Lakukan kalibrasi splicer secara berkala."
    },

    # =========================================================================
    # 41. KEBIJAKAN PRAKTIKUM & K3
    # =========================================================================
    {
        "intent": "k3_lab",
        "keywords": ["k3", "keselamatan kerja", "safety", "alat pelindung", "kacamata", "sarung tangan"],
        "questions": [
            "apa saja aturan keselamatan di lab?", "alat pelindung diri untuk praktikum fiber",
            "k3 laboratorium komputer", "bahaya bekerja dengan laser"
        ],
        "response": "⚠️ <strong>Keselamatan dan Kesehatan Kerja (K3) Lab:</strong><br>• Gunakan kacamata pelindung saat splicing fiber optic (serpihan kaca sangat berbahaya).<br>• Jangan melihat langsung ke ujung kabel fiber yang ditembak laser VFL.<br>• Gunakan alas kaki khusus (sandal lab).<br>• Matikan listrik sebelum membuka casing perangkat.<br>• Laporkan segera jika ada kabel terkelupas atau bau terbakar."
    },
    {
        "intent": "p3k",
        "keywords": ["p3k", "pertolongan pertama", "luka", "kecelakaan lab", "kotak p3k"],
        "questions": [
            "dimana letak kotak p3k di lab?", "pertolongan pertama luka sayat",
            "kecelakaan kerja di laboratorium", "isi kotak p3k lab"
        ],
        "response": "🚑 <strong>P3K di Laboratorium:</strong><br>• Kotak P3K tersedia di dekat pintu masuk Lab TKJ 1.<br>• Luka kecil: bersihkan dengan antiseptik, tutup dengan plester.<br>• Luka serius: tekan luka dengan kain bersih, segera hubungi guru/teknisi.<br>• Jika terkena serpihan kaca fiber, jangan digosok, bilas dengan air mengalir dan segera periksa ke UKS."
    },

    # =========================================================================
    # 42. PANDUAN PRAKTIKUM SERVER
    # =========================================================================
    {
        "intent": "install_web_server",
        "keywords": ["install apache", "install nginx", "setup web server", "lamp", "lemp"],
        "questions": [
            "cara install apache di ubuntu", "setup web server lengkap",
            "perbedaan lamp dan lemp", "install php dan mysql"
        ],
        "response": "🖥️ <strong>Instalasi Web Server (LAMP/LEMP):</strong><br>• <strong>LAMP:</strong> Linux, Apache, MySQL, PHP.<br>• <strong>LEMP:</strong> Linux, Nginx (Engine-X), MySQL, PHP.<br>• <strong>Instalasi Ubuntu:</strong> <code>sudo apt install apache2 mysql-server php libapache2-mod-php</code><br>• <strong>Test:</strong> buka <code>http://localhost</code> di browser."
    },
    {
        "intent": "database_mysql",
        "keywords": ["mysql", "database", "membuat database", "phpmyadmin", "sql"],
        "questions": [
            "cara membuat database mysql", "akses phpmyadmin",
            "perintah dasar sql", "cara backup database"
        ],
        "response": "🗄️ <strong>Database MySQL:</strong><br>• <strong>Login:</strong> <code>mysql -u root -p</code><br>• <strong>Buat database:</strong> <code>CREATE DATABASE lab_tkj;</code><br>• <strong>Buat user:</strong> <code>CREATE USER 'user'@'localhost' IDENTIFIED BY 'password';</code><br>• <strong>Grant:</strong> <code>GRANT ALL PRIVILEGES ON lab_tkj.* TO 'user'@'localhost';</code><br>• <strong>PhpMyAdmin:</strong> Web interface untuk mengelola MySQL."
    },

    # =========================================================================
    # 43. MONITORING & MANAJEMEN JARINGAN
    # =========================================================================
    {
        "intent": "monitoring_jaringan",
        "keywords": ["monitoring", "the dude", "cacti", "zabbix", "prtg", "snmp"],
        "questions": [
            "software monitoring jaringan", "apa itu snmp?",
            "cara monitoring traffic mikrotik", "monitoring server dengan zabbix"
        ],
        "response": "📈 <strong>Monitoring Jaringan:</strong><br>• <strong>SNMP (Simple Network Management Protocol):</strong> Mengumpulkan data dari perangkat.<br>• <strong>The Dude (MikroTik):</strong> Gratis, visual map, monitoring up/down.<br>• <strong>Cacti:</strong> Grafik traffic berbasis RRDtool.<br>• <strong>Zabbix:</strong> Monitoring server, network, aplikasi dengan alerting.<br>• <strong>PRTG:</strong> Komersial, mudah digunakan."
    },
    {
        "intent": "netflow",
        "keywords": ["netflow", "traffic analysis", "ip flow", "ntopng", "analisa traffic"],
        "questions": [
            "apa itu netflow?", "cara analisa traffic jaringan",
            "tools untuk melihat traffic per ip", "export netflow dari mikrotik"
        ],
        "response": "📊 <strong>NetFlow / Traffic Analysis:</strong><br>• <strong>NetFlow:</strong> Protokol untuk mengumpulkan informasi traffic IP.<br>• <strong>MikroTik:</strong> <code>/ip traffic-flow set enabled=yes</code>, kirim ke kolektor.<br>• <strong>Tools:</strong> ntopng, SolarWinds, PRTG, ELK Stack.<br>• <strong>Manfaat:</strong> Mengetahui IP mana yang paling banyak memakai bandwidth."
    },

    # =========================================================================
    # 44. CLOUD COMPUTING DASAR
    # =========================================================================
    {
        "intent": "cloud_computing",
        "keywords": ["cloud", "aws", "google cloud", "azure", "iaas", "paas", "saas"],
        "questions": [
            "apa itu cloud computing?", "perbedaan iaas paas saas",
            "contoh layanan cloud", "belajar cloud untuk pemula"
        ],
        "response": "☁️ <strong>Cloud Computing:</strong><br>Penyediaan sumber daya komputasi (server, storage, aplikasi) melalui internet.<br>• <strong>IaaS:</strong> Infrastruktur (VM, storage) contoh AWS EC2, Google Compute Engine.<br>• <strong>PaaS:</strong> Platform untuk deploy aplikasi (Heroku, Google App Engine).<br>• <strong>SaaS:</strong> Software siap pakai (Gmail, Office 365, Canva)."
    },

    # =========================================================================
    # 45. KEBIJAKAN PENGGUNAAN INTERNET DI LAB
    # =========================================================================
    {
        "intent": "internet_sehat",
        "keywords": ["internet sehat", "blokir situs", "konten negatif", "penggunaan internet", "filter konten"],
        "questions": [
            "aturan penggunaan internet di lab", "cara memblokir konten negatif",
            "situs yang dilarang diakses", "internet sehat di sekolah"
        ],
        "response": "🛡️ <strong>Penggunaan Internet Sehat di Lab:</strong><br>• Akses hanya untuk keperluan pembelajaran.<br>• Dilarang mengakses situs pornografi, judi, atau konten ilegal.<br>• Beberapa kategori situs diblokir oleh firewall sekolah.<br>• Guru dapat meminta teknisi untuk membuka blokir situs edukasi tertentu."
    },

    # =========================================================================
    # 46. PANDUAN PENGGUNAAN PERALATAN KHUSUS
    # =========================================================================
    {
        "intent": "penggunaan_opm",
        "keywords": ["cara pakai opm", "optical power meter", "mengukur dbm", "kalibrasi opm"],
        "questions": [
            "cara menggunakan optical power meter", "bagaimana mengukur daya fiber optic?",
            "satuan dbm", "cara kalibrasi opm"
        ],
        "response": "📏 <strong>Penggunaan Optical Power Meter (OPM):</strong><br>1. Nyalakan OPM, pilih panjang gelombang (1310nm atau 1550nm).<br>2. Hubungkan patchcord dari sumber cahaya ke port OPM.<br>3. Baca nilai daya dalam dBm.<br>4. Bandingkan dengan standar: Rx sensitivity biasanya -20 s.d -30 dBm untuk link FO."
    },
    {
        "intent": "penggunaan_vfl",
        "keywords": ["cara pakai vfl", "laser fiber", "mencari kabel putus", "visual fault locator"],
        "questions": [
            "cara menggunakan vfl", "mencari titik putus fiber optic",
            "laser merah fiber optic", "apakah vfl berbahaya untuk mata?"
        ],
        "response": "🔦 <strong>Penggunaan VFL (Visual Fault Locator):</strong><br>1. Pasang baterai, nyalakan.<br>2. Hubungkan ke salah satu ujung kabel fiber.<br>3. Perhatikan di sepanjang kabel atau di ujung lain: jika ada cahaya merah bocor, itu titik bending/putus.<br>4. Jangan melihat langsung ke ujung kabel yang ditembak laser."
    },

    # =========================================================================
    # 47. INFORMASI TEKNISI & PERBAIKAN
    # =========================================================================
    {
        "intent": "jadwal_teknisi",
        "keywords": ["teknisi lab", "jadwal teknisi", "panggil teknisi", "perbaikan alat", "service alat"],
        "questions": [
            "kapan teknisi lab tersedia?", "cara memanggil teknisi",
            "jadwal perbaikan alat lab", "teknisi datang jam berapa?"
        ],
        "response": "🛠️ <strong>Ketersediaan Teknisi Lab:</strong><br>• Teknisi hadir setiap hari kerja pukul 08.00 - 15.00 WIB.<br>• Untuk alat yang mendesak, hubungi via WhatsApp.<br>• Perbaikan alat biasanya dilakukan di ruang teknisi (sebelah Lab TKJ 1)."
    },

    # =========================================================================
    # 48. PENGADAAN & INVENTARIS BARU
    # =========================================================================
    {
        "intent": "pengadaan_alat",
        "keywords": ["pengadaan", "beli alat baru", "usulan alat", "inventaris baru", "proposal alat"],
        "questions": [
            "bagaimana mengusulkan alat baru untuk lab?", "prosedur pengadaan alat praktikum",
            "siapa yang menentukan pembelian alat?", "proposal pengadaan router"
        ],
        "response": "📦 <strong>Prosedur Pengadaan Alat Baru:</strong><br>1. Guru/Koordinator mengajukan daftar kebutuhan ke Kepala Lab.<br>2. Kepala Lab membuat proposal pengadaan.<br>3. Proposal disetujui oleh pihak sekolah (Wakasek Sarana).<br>4. Pembelian dilakukan, kemudian barang dicatat di sistem inventaris."
    },

    # =========================================================================
    # 49. KEBIJAKAN PINJAM RUANGAN LAB
    # =========================================================================
    {
        "intent": "pinjam_ruangan",
        "keywords": ["pinjam ruangan", "sewa lab", "penggunaan lab", "booking lab", "jadwal ruangan"],
        "questions": [
            "bolehkah meminjam ruangan lab untuk acara?", "cara booking laboratorium",
            "jadwal penggunaan lab tkj", "peminjaman ruangan untuk pelatihan"
        ],
        "response": "🏫 <strong>Peminjaman Ruangan Lab:</strong><br>• Ruangan lab dapat dipinjam untuk kegiatan sekolah dengan izin Kepala Lab.<br>• Isi formulir peminjaman ruangan di Admin.<br>• Prioritas penggunaan untuk kelas praktikum.<br>• Pastikan ruangan dikembalikan dalam keadaan bersih dan rapi."
    },

    # =========================================================================
    # 50. TAMBAHAN PERTANYAAN UMUM
    # =========================================================================
    {
        "intent": "wifi_password",
        "keywords": ["password wifi", "wifi lab", "ssid lab", "kata sandi wifi", "wifi sekolah"],
        "questions": [
            "apa password wifi lab tkj?", "nama ssid wifi lab",
            "cara connect ke wifi lab", "wifi untuk siswa"
        ],
        "response": "📶 <strong>WiFi Laboratorium TKJ:</strong><br>• <strong>SSID:</strong> <code>LAB-TKJ</code><br>• <strong>Password:</strong> Diberitahukan oleh guru/teknisi saat praktikum.<br>• Jaringan WiFi khusus untuk keperluan pembelajaran, bukan untuk umum."
    },
    {
        "intent": "printer_lab",
        "keywords": ["printer", "cetak dokumen", "print tugas", "printer lab", "ngeprint"],
        "questions": [
            "bolehkah menggunakan printer lab?", "cara print di lab tkj",
            "biaya print di lab", "printer untuk siswa"
        ],
        "response": "🖨️ <strong>Printer Laboratorium:</strong><br>• Printer hanya untuk keperluan administrasi lab dan tugas yang diizinkan guru.<br>• Tidak melayani print massal untuk siswa.<br>• Jika butuh print, gunakan jasa fotokopi di luar sekolah atau minta izin khusus."
    },
    {
        "intent": "ruang_server",
        "keywords": ["ruang server", "server lab", "akses server", "rak server", "data center"],
        "questions": [
            "siapa yang boleh masuk ruang server?", "apa isi ruang server lab?",
            "cara akses server lab", "keamanan ruang server"
        ],
        "response": "🗄️ <strong>Ruang Server Lab TKJ:</strong><br>• Hanya teknisi dan Kepala Lab yang boleh masuk tanpa izin.<br>• Siswa dapat masuk jika didampingi guru untuk keperluan praktikum.<br>• Ruang server berisi router utama, switch core, server, dan penyimpanan data.<br>• Suhu ruangan dijaga dengan AC."
    },

    # =========================================================================
    # 51. PANDUAN TROUBLESHOOTING PERANGKAT
    # =========================================================================
    {
        "intent": "router_mati",
        "keywords": ["router tidak menyala", "mikrotik mati", "adaptor rusak", "power router", "lampu indikator mati"],
        "questions": [
            "kenapa router mikrotik tidak menyala?", "router tidak ada lampu sama sekali",
            "adaptor router panas", "mikrotik mati total"
        ],
        "response": "🔌 <strong>Troubleshooting Router Mati Total:</strong><br>1. Cek adaptor: apakah ada tegangan keluar? Gunakan multimeter.<br>2. Coba ganti adaptor dengan spesifikasi sama (biasanya 12V/1A atau 24V).<br>3. Cek kabel power dan konektor.<br>4. Jika masih mati, kemungkinan kerusakan hardware; laporkan ke teknisi."
    },
    {
        "intent": "switch_port_mati",
        "keywords": ["port switch mati", "port tidak berfungsi", "led port mati", "switch port rusak"],
        "questions": [
            "kenapa salah satu port switch tidak berfungsi?", "port switch tidak ada lampu",
            "cara cek port switch", "port lan rusak"
        ],
        "response": "🔌 <strong>Troubleshooting Port Switch Mati:</strong><br>1. Ganti kabel UTP dengan yang baru/diketahui baik.<br>2. Coba colok ke port lain.<br>3. Periksa apakah port di-disable secara administrasi (<code>shutdown</code>).<br>4. Cek LED indikator: jika tidak menyala sama sekali, kemungkinan port hardware rusak."
    },

    # =========================================================================
    # 52. MATERI PRAKTIKUM UKK
    # =========================================================================
    {
        "intent": "materi_ukk",
        "keywords": ["ukk", "ujian kompetensi", "paket soal", "skema ukk", "ujian praktik tkj"],
        "questions": [
            "apa saja materi ukk tkj?", "contoh soal ukk jaringan",
            "paket ukk tkj terbaru", "persiapan ujian kompetensi"
        ],
        "response": "📚 <strong>Materi Umum UKK TKJ:</strong><br>• <strong>Jaringan Komputer:</strong> Crimping, VLAN, routing, firewall, wireless.<br>• <strong>Administrasi Server:</strong> Linux, Windows Server, DNS, Web, FTP.<br>• <strong>Keamanan Jaringan:</strong> VPN, Firewall, Monitoring.<br>• <strong>Fiber Optic:</strong> Splicing, pengukuran loss.<br>• <strong>IoT dasar:</strong> Sensor, mikrokontroler."
    },
    {
        "intent": "tips_ukk",
        "keywords": ["tips ukk", "lulus ukk", "strategi ujian", "belajar ukk", "persiapan ujian"],
        "questions": [
            "tips lulus ukk tkj", "cara belajar efektif untuk ukk",
            "hal penting saat ujian praktik", "strategi mengerjakan ukk"
        ],
        "response": "🎯 <strong>Tips Sukses UKK TKJ:</strong><br>• Pahami topologi dan instruksi soal dengan teliti.<br>• Latih kecepatan crimping dan konfigurasi dasar.<br>• Biasakan menyimpan konfigurasi (<code>write</code> atau <code>save</code>).<br>• Jaga ketenangan, kerjakan yang mudah dulu.<br>• Selalu backup konfigurasi sebelum melakukan perubahan besar."
    },

    # =========================================================================
    # 53. PANDUAN PRAKTIKUM JARINGAN NIRKABEL
    # =========================================================================
    {
        "intent": "wireless_ptp",
        "keywords": ["point to point", "ptp", "wireless bridge", "ubiquiti ptp", "litebeam config"],
        "questions": [
            "cara setting point to point ubiquiti", "wireless bridge jarak jauh",
            "konfigurasi litebeam ptp", "mode station dan access point"
        ],
        "response": "🛰️ <strong>Wireless Point-to-Point (PTP):</strong><br>• Satu sisi sebagai <strong>Access Point</strong>, sisi lain sebagai <strong>Station</strong>.<br>• Atur channel dan frekuensi sama.<br>• Gunakan IP satu subnet untuk kedua perangkat.<br>• Pastikan line of sight (LOS) tidak terhalang.<br>• Gunakan fitur alignment tool pada Ubiquiti untuk mengarahkan antena."
    },
    {
        "intent": "wireless_ptmp",
        "keywords": ["point to multipoint", "ptmp", "wireless distribution", "sektor antena", "base station"],
        "questions": [
            "apa itu ptmp?", "cara setting point to multipoint",
            "antenna sektor untuk ptmp", "wireless distribution system"
        ],
        "response": "📡 <strong>Wireless Point-to-Multipoint (PTMP):</strong><br>• Satu Access Point (base station) melayani banyak Station.<br>• Biasanya menggunakan antena sektor (60°, 90°, 120°).<br>• Konfigurasi mirip PTP, hanya AP melayani banyak klien.<br>• Perhatikan kapasitas bandwidth total dibagi jumlah klien."
    },

    # =========================================================================
    # 54. KEBIJAKAN PEMINJAMAN KHUSUS
    # =========================================================================
    {
        "intent": "pinjam_splicer",
        "keywords": ["pinjam fusion splicer", "splicer untuk tugas", "alat splicing fiber", "pinjam alat presisi"],
        "questions": [
            "apakah siswa boleh meminjam fusion splicer?", "syarat pinjam splicer",
            "peminjaman fusion splicer untuk lomba", "alat fiber optic untuk tugas akhir"
        ],
        "response": "⚠️ <strong>Peminjaman Fusion Splicer:</strong><br>• Fusion splicer hanya boleh dipinjam dengan pendampingan guru/teknisi.<br>• Tidak boleh dibawa keluar sekolah tanpa izin tertulis Kepala Lab.<br>• Ajukan melalui sistem, tetapi harus ada nama guru pendamping di form keperluan."
    },

    # =========================================================================
    # 55. PANDUAN PRAKTIKUM KEAMANAN JARINGAN
    # =========================================================================
    {
        "intent": "praktikum_firewall",
        "keywords": ["praktikum firewall", "blokir port", "filter rules mikrotik", "keamanan jaringan dasar"],
        "questions": [
            "langkah praktikum firewall mikrotik", "tugas blokir akses internet",
            "cara memblokir port di router", "membuat rule firewall"
        ],
        "response": "🛡️ <strong>Praktikum Firewall MikroTik:</strong><br>1. Blokir akses ke IP tertentu: <code>/ip firewall filter add chain=forward dst-address=192.168.1.100 action=drop</code><br>2. Blokir port Telnet: <code>chain=forward protocol=tcp dst-port=23 action=drop</code><br>3. Izinkan hanya port tertentu: buat rule allow di atas, lalu drop all di bawah."
    },
    {
        "intent": "praktikum_vpn",
        "keywords": ["praktikum vpn", "setting pptp", "vpn server mikrotik", "uji koneksi vpn"],
        "questions": [
            "cara membuat vpn server di mikrotik", "tugas vpn untuk ukk",
            "setting pptp server", "test koneksi vpn"
        ],
        "response": "🔐 <strong>Praktikum VPN PPTP di MikroTik:</strong><br>1. Aktifkan PPTP server: <code>/interface pptp-server server set enabled=yes</code><br>2. Buat user: <code>/ppp secret add name=user password=123 service=pptp</code><br>3. Buat profile: <code>/ppp profile add name=pptp-profile local-address=10.0.0.1 remote-address=10.0.0.2</code><br>4. Test dari client Windows/Linux dengan VPN connection."
    },

    # =========================================================================
    # 56. PANDUAN PRAKTIKUM MONITORING
    # =========================================================================
    {
        "intent": "praktikum_snmp",
        "keywords": ["snmp", "monitoring snmp", "cacti snmp", "community string"],
        "questions": [
            "cara setting snmp di mikrotik", "apa itu community string?",
            "monitoring traffic dengan cacti", "praktikum monitoring jaringan"
        ],
        "response": "📈 <strong>Praktikum SNMP Monitoring:</strong><br>1. Aktifkan SNMP di MikroTik: <code>/snmp set enabled=yes community=public</code><br>2. Install Cacti di server Linux.<br>3. Tambahkan perangkat dengan IP dan community string.<br>4. Buat grafik traffic interface.<br>5. Amati traffic saat ada aktivitas download/upload."
    },

    # =========================================================================
    # 57. PANDUAN PRAKTIKUM VIRTUALISASI
    # =========================================================================
    {
        "intent": "praktikum_virtualbox",
        "keywords": ["praktikum virtualbox", "install vm", "setting jaringan vm", "virtualisasi praktikum"],
        "questions": [
            "tugas membuat virtual machine", "cara setting jaringan di virtualbox",
            "install linux di vm", "praktikum server virtual"
        ],
        "response": "💻 <strong>Praktikum VirtualBox:</strong><br>1. Buat VM baru: pilih Linux, alokasikan RAM 1-2 GB, buat virtual disk 10 GB.<br>2. Mount ISO Ubuntu, install seperti biasa.<br>3. Setelah install, ubah network adapter: NAT untuk internet, Host-only untuk jaringan lokal antar VM.<br>4. Lakukan konfigurasi IP, test ping antar VM."
    },

    # =========================================================================
    # 58. PANDUAN PRAKTIKUM IOT
    # =========================================================================
    {
        "intent": "praktikum_iot",
        "keywords": ["praktikum iot", "arduino", "esp8266", "sensor suhu", "thingspeak", "mqtt"],
        "questions": [
            "proyek iot sederhana", "cara program esp8266",
            "mengirim data sensor ke internet", "tugas iot arduino"
        ],
        "response": "🌐 <strong>Praktikum IoT Sederhana:</strong><br>1. Siapkan ESP8266 (NodeMCU) dan sensor DHT11.<br>2. Program dengan Arduino IDE: baca suhu, kirim ke Thingspeak via HTTP GET.<br>3. Buat akun Thingspeak, buat channel, dapatkan API key.<br>4. Upload kode, cek grafik di Thingspeak.<br>5. Bisa juga menggunakan MQTT (broker: mosquitto)."
    },

    # =========================================================================
    # 59. PANDUAN PRAKTIKUM FIBER OPTIC (ADVANCED)
    # =========================================================================
    {
        "intent": "praktikum_otdr",
        "keywords": ["otdr", "optical time domain reflectometer", "ukur jarak fiber", "loss event", "reflektansi"],
        "questions": [
            "cara menggunakan otdr", "apa itu otdr?",
            "mengukur panjang kabel fiber optic", "analisa hasil otdr"
        ],
        "response": "📡 <strong>OTDR (Optical Time Domain Reflectometer):</strong><br>Alat untuk mengukur panjang fiber, lokasi sambungan, dan loss.<br>• <strong>Cara pakai:</strong> Hubungkan ke salah satu ujung, pilih panjang gelombang & range, jalankan auto test.<br>• <strong>Hasil:</strong> Grafik daya terhadap jarak. Event (penurunan tajam) menunjukkan konektor/sambungan.<br>• <strong>Kelebihan:</strong> Tidak perlu akses kedua ujung."
    },

    # =========================================================================
    # 60. PANDUAN PRAKTIKUM ROUTING (ADVANCED)
    # =========================================================================
    {
        "intent": "praktikum_ospf",
        "keywords": ["ospf", "routing ospf", "area ospf", "cost ospf", "neighbor ospf"],
        "questions": [
            "cara konfigurasi ospf di mikrotik", "apa itu area di ospf?",
            "menentukan cost ospf", "mengecek neighbor ospf"
        ],
        "response": "🔄 <strong>Praktikum OSPF MikroTik:</strong><br>1. Tambahkan IP ke interface.<br>2. Buat instance: <code>/routing ospf instance add name=default router-id=1.1.1.1</code><br>3. Buat area: <code>/routing ospf area add name=backbone area-id=0.0.0.0</code><br>4. Tambahkan network: <code>/routing ospf network add network=192.168.1.0/24 area=backbone</code><br>5. Cek neighbor: <code>/routing ospf neighbor print</code>"
    },
    {
        "intent": "praktikum_bgp",
        "keywords": ["bgp", "routing bgp", "autonomous system", "as number", "peering bgp"],
        "questions": [
            "apa itu bgp?", "cara konfigurasi bgp di mikrotik",
            "autonomous system number", "bgp peering"
        ],
        "response": "🌍 <strong>Praktikum BGP Dasar:</strong><br>• <strong>Autonomous System (AS):</strong> Nomor identitas jaringan (1-65535).<br>• <strong>Konfigurasi:</strong> <code>/routing bgp instance set default as=65001</code><br>• <strong>Peer:</strong> <code>/routing bgp peer add remote-address=10.0.0.2 remote-as=65002</code><br>• <strong>Umumkan Network:</strong> <code>/routing bgp network add network=192.168.1.0/24</code>"
    },

    # =========================================================================
    # 61. TAMBAHAN PERTANYAAN UMUM LAB
    # =========================================================================
    {
        "intent": "jadwal_praktikum",
        "keywords": ["jadwal praktikum", "jadwal kelas lab", "kapan praktikum", "roster lab"],
        "questions": [
            "dimana melihat jadwal praktikum?", "jadwal kelas 10 tkj",
            "kapan lab digunakan oleh kelas lain?", "roster penggunaan lab"
        ],
        "response": "📅 <strong>Jadwal Praktikum:</strong><br>Jadwal penggunaan lab dapat dilihat di papan pengumuman lab atau menu <strong>Pengumuman Lab</strong> pada sistem. Jadwal juga ditempel di pintu masuk Lab TKJ 1."
    },
    {
        "intent": "kalibrasi_alat",
        "keywords": ["kalibrasi", "jadwal kalibrasi", "kalibrasi splicer", "kalibrasi opm"],
        "questions": [
            "kapan alat lab dikalibrasi?", "frekuensi kalibrasi alat ukur",
            "kenapa alat perlu dikalibrasi?", "jadwal kalibrasi opm"
        ],
        "response": "📏 <strong>Kalibrasi Alat:</strong><br>• Alat ukur seperti OPM, LAN tester, dan splicer perlu dikalibrasi minimal setahun sekali.<br>• Kalibrasi dilakukan oleh pihak ketiga atau teknisi khusus.<br>• Hasil kalibrasi dicatat di sistem maintenance."
    },
    {
        "intent": "peminjaman_cepat",
        "keywords": ["pinjam cepat", "pinjam mendadak", "urgent", "peminjaman hari ini"],
        "questions": [
            "apakah bisa pinjam alat mendadak hari ini?", "peminjaman cepat tanpa antri",
            "urgent pinjam router", "cara pinjam alat untuk hari ini"
        ],
        "response": "⚡ <strong>Peminjaman Mendadak:</strong><br>Jika kebutuhan mendesak, ajukan melalui sistem lalu segera hubungi Admin via WhatsApp dengan menyebutkan nama dan alat. Admin akan memprioritaskan jika memungkinkan, namun tetap tergantung ketersediaan stok."
    },

    # =========================================================================
    # 62. PANDUAN PRAKTIKUM WIRELESS (ADVANCED)
    # =========================================================================
    {
        "intent": "praktikum_captive_portal",
        "keywords": ["captive portal", "halaman login wifi", "login page custom", "wifi voucher", "freeradius"],
        "questions": [
            "cara membuat captive portal", "login wifi dengan akun",
            "integrasi mikrotik dengan radius", "tampilan login hotspot kustom"
        ],
        "response": "🌐 <strong>Captive Portal:</strong><br>• <strong>Definisi:</strong> Halaman login yang muncul saat user mencoba akses internet via WiFi.<br>• <strong>MikroTik:</strong> Fitur hotspot sudah menyediakan captive portal.<br>• <strong>Kustomisasi:</strong> Edit file <code>login.html</code> di menu Files.<br>• <strong>Freeradius:</strong> Untuk autentikasi terpusat dengan banyak user."
    },

    # =========================================================================
    # 63. PANDUAN PRAKTIKUM KEAMANAN WIRELESS
    # =========================================================================
    {
        "intent": "praktikum_wpa2",
        "keywords": ["wpa2", "konfigurasi wpa2", "wireless security", "password wifi", "crack wifi"],
        "questions": [
            "cara setting wpa2 di mikrotik", "mengamankan jaringan wifi",
            "perbedaan wpa dan wpa2", "uji keamanan wifi"
        ],
        "response": "🔐 <strong>Praktikum Keamanan Wireless:</strong><br>1. Set security profile: <code>/interface wireless security-profiles add name=wpa2 mode=dynamic-keys authentication-types=wpa2-psk wpa2-pre-shared-key=rahasia123</code><br>2. Terapkan ke interface wireless.<br>3. Test dari laptop: connect, masukkan password.<br>4. Gunakan tools seperti aircrack-ng untuk menguji kekuatan password (hanya untuk pembelajaran)."
    },

    # =========================================================================
    # 64. PANDUAN PRAKTIKUM TROUBLESHOOTING
    # =========================================================================
    {
        "intent": "praktikum_troubleshooting",
        "keywords": ["troubleshooting", "simulasi kerusakan", "mencari masalah", "problem solving jaringan"],
        "questions": [
            "langkah troubleshooting jaringan", "simulasi kerusakan untuk ujian",
            "metode pemecahan masalah", "checklist troubleshooting"
        ],
        "response": "🔍 <strong>Metodologi Troubleshooting Jaringan:</strong><br>1. Identifikasi gejala.<br>2. Tentukan lingkup masalah (satu user atau semua?).<br>3. Periksa lapisan OSI dari bawah: kabel, IP, aplikasi.<br>4. Gunakan alat: ping, traceroute, nslookup, cable tester.<br>5. Catat solusi untuk referensi."
    },

    # =========================================================================
    # 65. PANDUAN PRAKTIKUM MANAJEMEN USER
    # =========================================================================
    {
        "intent": "praktikum_user_management",
        "keywords": ["manajemen user", "user windows server", "user linux", "group policy", "hak akses user"],
        "questions": [
            "cara membuat user di windows server", "manajemen user linux",
            "group policy untuk siswa", "user dan group di linux"
        ],
        "response": "👥 <strong>Manajemen User Praktikum:</strong><br>• <strong>Windows Server:</strong> Active Directory Users and Computers.<br>• <strong>Linux:</strong> <code>useradd</code>, <code>passwd</code>, <code>groupadd</code>, <code>usermod</code>.<br>• <strong>GPO:</strong> Batasi akses control panel, set wallpaper, dll."
    },

    # =========================================================================
    # 66. INFORMASI TAMBAHAN ALAT LAB
    # =========================================================================
    {
        "intent": "spesifikasi_pc_lab",
        "keywords": ["spesifikasi pc", "komputer lab", "prosesor lab", "ram pc lab"],
        "questions": [
            "spesifikasi komputer di lab tkj?", "berapa ram pc lab?",
            "prosesor komputer lab", "pc untuk praktikum server"
        ],
        "response": "🖥️ <strong>Spesifikasi PC Laboratorium TKJ:</strong><br>• <strong>Prosesor:</strong> Intel Core i5 / AMD Ryzen 5.<br>• <strong>RAM:</strong> 8 GB DDR4.<br>• <strong>Storage:</strong> SSD 256 GB + HDD 500 GB.<br>• <strong>OS:</strong> Windows 10/11, beberapa dual boot Ubuntu 22.04.<br>• <strong>Jumlah:</strong> 20 unit (Lab TKJ 1) dan 20 unit (Lab TKJ 2)."
    },
    {
        "intent": "spesifikasi_server",
        "keywords": ["spesifikasi server", "server lab", "server praktikum", "hardware server"],
        "questions": [
            "spesifikasi server laboratorium?", "apa server yang dipakai di lab?",
            "server untuk virtualisasi", "kapasitas server lab"
        ],
        "response": "🗄️ <strong>Spesifikasi Server Lab:</strong><br>• <strong>Prosesor:</strong> Intel Xeon E-2234 (4 core).<br>• <strong>RAM:</strong> 32 GB ECC.<br>• <strong>Storage:</strong> 2x 1TB HDD RAID 1 + 500GB SSD.<br>• <strong>Fungsi:</strong> Virtualisasi (Proxmox), file server, DNS, dan monitoring."
    },

    # =========================================================================
    # 67. PANDUAN PRAKTIKUM BACKUP & RESTORE
    # =========================================================================
    {
        "intent": "praktikum_backup",
        "keywords": ["backup", "restore", "backup server", "backup database", "jadwal backup"],
        "questions": [
            "cara backup konfigurasi perangkat", "backup database mysql",
            "strategi backup server", "restore data dari backup"
        ],
        "response": "💾 <strong>Praktikum Backup & Restore:</strong><br>• <strong>MikroTik:</strong> <code>/export file=backup</code> atau <code>/system backup save</code>.<br>• <strong>Cisco:</strong> <code>copy running-config tftp:</code>.<br>• <strong>MySQL:</strong> <code>mysqldump -u root -p database > backup.sql</code>.<br>• <strong>Linux:</strong> gunakan <code>rsync</code> atau <code>tar</code> untuk folder penting."
    },

    # =========================================================================
    # 68. PANDUAN PRAKTIKUM LOG MANAGEMENT
    # =========================================================================
    {
        "intent": "praktikum_log",
        "keywords": ["log", "syslog", "log server", "melihat log", "event log"],
        "questions": [
            "cara melihat log di mikrotik", "apa itu syslog?",
            "konfigurasi log server", "analisa log firewall"
        ],
        "response": "📋 <strong>Manajemen Log:</strong><br>• <strong>MikroTik:</strong> <code>/log print</code> untuk melihat log lokal.<br>• <strong>Kirim ke Syslog Server:</strong> <code>/system logging action set remote bsd-syslog=yes remote=192.168.1.10</code><br>• <strong>Linux:</strong> <code>/var/log/syslog</code>, gunakan <code>tail -f</code> untuk monitoring realtime.<br>• <strong>Windows:</strong> Event Viewer."
    },

    # =========================================================================
    # 69. PANDUAN PRAKTIKUM QUALITY OF SERVICE (QOS)
    # =========================================================================
    {
        "intent": "praktikum_qos",
        "keywords": ["qos", "quality of service", "prioritas traffic", "voip", "video streaming"],
        "questions": [
            "cara setting qos di mikrotik", "prioritas traffic voip",
            "queue tree untuk qos", "manajemen bandwidth per aplikasi"
        ],
        "response": "🎛️ <strong>Quality of Service (QoS):</strong><br>• <strong>Tujuan:</strong> Memberikan prioritas pada traffic penting (VoIP, video conference).<br>• <strong>Mangle:</strong> Tandai traffic berdasarkan port/protokol.<br>• <strong>Queue Tree:</strong> Buat antrian dengan prioritas berbeda.<br>• <strong>Contoh:</strong> Traffic port 5060 (SIP) diberi prioritas 1, download besar prioritas 8."
    },

    # =========================================================================
    # 70. PANDUAN PRAKTIKUM LOAD BALANCING
    # =========================================================================
    {
        "intent": "praktikum_load_balance",
        "keywords": ["load balancing", "dua isp", "multi wan", "failover", "ecmp"],
        "questions": [
            "cara load balancing dua isp di mikrotik", "konfigurasi failover",
            "menggabungkan dua koneksi internet", "ecmp di mikrotik"
        ],
        "response": "⚖️ <strong>Load Balancing & Failover:</strong><br>• <strong>Failover:</strong> Satu ISP utama, ISP cadangan aktif jika utama mati (gunakan check gateway).<br>• <strong>Load Balancing:</strong> Membagi traffic ke dua ISP (misal 50:50).<br>• <strong>ECMP:</strong> Equal Cost Multi-Path dengan dua route default.<br>• <strong>Mangle:</strong> Tandai koneksi agar tidak pindah-pindah ISP."
    },

    # =========================================================================
    # 71. INFORMASI TAMBAHAN KEBIJAKAN
    # =========================================================================
    {
        "intent": "kebijakan_akun",
        "keywords": ["kebijakan akun", "akun dinonaktifkan", "suspend", "blokir akun", "sanksi akun"],
        "questions": [
            "kenapa akun saya dinonaktifkan?", "kebijakan suspend akun",
            "berapa lama akun diblokir?", "cara mengaktifkan kembali akun"
        ],
        "response": "🚫 <strong>Kebijakan Akun:</strong><br>• Akun dapat dinonaktifkan sementara jika melanggar aturan (terlambat mengembalikan, kerusakan alat).<br>• Durasi suspend bervariasi (1-2 minggu) tergantung pelanggaran.<br>• Untuk pengaktifan kembali, hubungi Admin setelah masa sanksi selesai."
    },

    # =========================================================================
    # 72. PANDUAN PRAKTIKUM FILE SHARING
    # =========================================================================
    {
        "intent": "praktikum_file_sharing",
        "keywords": ["file sharing", "samba", "nfs", "share folder", "network drive"],
        "questions": [
            "cara sharing folder di windows", "setting samba server linux",
            "akses folder bersama", "nfs vs samba"
        ],
        "response": "📁 <strong>File Sharing:</strong><br>• <strong>Windows:</strong> Klik kanan folder > Properties > Sharing > Share.<br>• <strong>Linux Samba:</strong> <code>sudo apt install samba</code>, edit <code>/etc/samba/smb.conf</code>, tambahkan share.<br>• <strong>NFS:</strong> Cocok untuk sistem Linux/Unix.<br>• <strong>Akses:</strong> Gunakan <code>\\IP\share</code> di Windows atau mount di Linux."
    },

    # =========================================================================
    # 73. PANDUAN PRAKTIKUM REMOTE DESKTOP
    # =========================================================================
    {
        "intent": "praktikum_remote_desktop",
        "keywords": ["remote desktop", "rdp", "vnc", "teamviewer", "anydesk", "remote access"],
        "questions": [
            "cara remote desktop windows", "setting vnc server linux",
            "perbedaan rdp dan vnc", "remote komputer lab"
        ],
        "response": "🖥️ <strong>Remote Desktop:</strong><br>• <strong>Windows RDP:</strong> Buka <code>mstsc</code>, masukkan IP komputer target, login.<br>• <strong>Linux VNC:</strong> Install <code>tightvncserver</code>, jalankan, akses dengan VNC viewer.<br>• <strong>Third-party:</strong> TeamViewer, AnyDesk (mudah untuk non-teknis).<br>• <strong>Keamanan:</strong> Gunakan VPN jika remote dari luar jaringan."
    },

    # =========================================================================
    # 74. PANDUAN PRAKTIKUM PACKET TRACER
    # =========================================================================
    {
        "intent": "packet_tracer",
        "keywords": ["packet tracer", "simulasi jaringan", "cisco packet tracer", "simulasi vlan", "simulasi routing"],
        "questions": [
            "cara menggunakan cisco packet tracer", "membuat simulasi jaringan",
            "tutorial packet tracer vlan", "download packet tracer gratis"
        ],
        "response": "🧪 <strong>Cisco Packet Tracer:</strong><br>• <strong>Fungsi:</strong> Simulasi jaringan Cisco tanpa perangkat fisik.<br>• <strong>Fitur:</strong> Buat topologi, konfigurasi perangkat, mode simulasi untuk melihat paket.<br>• <strong>Kegunaan:</strong> Latihan VLAN, routing, ACL, dll.<br>• <strong>Ketersediaan:</strong> Gratis dengan mendaftar di Cisco Networking Academy."
    },

    # =========================================================================
    # 75. PANDUAN PRAKTIKUM WIRESHARK
    # =========================================================================
    {
        "intent": "wireshark",
        "keywords": ["wireshark", "packet capture", "analisa paket", "sniffing", "network analyzer"],
        "questions": [
            "cara menggunakan wireshark", "capture traffic jaringan",
            "analisa protokol dengan wireshark", "filter paket wireshark"
        ],
        "response": "🦈 <strong>Wireshark:</strong><br>• <strong>Fungsi:</strong> Menganalisis paket data di jaringan.<br>• <strong>Cara Pakai:</strong> Pilih interface, klik Start, gunakan filter (contoh: <code>ip.addr == 192.168.1.1</code>).<br>• <strong>Manfaat:</strong> Debugging protokol, keamanan, belajar cara kerja jaringan."
    },

    # =========================================================================
    # 76. PANDUAN PRAKTIKUM NETWORK SCANNING
    # =========================================================================
    {
        "intent": "network_scanning",
        "keywords": ["network scanning", "nmap", "scan ip", "port scanning", "host discovery"],
        "questions": [
            "cara scan jaringan dengan nmap", "menemukan perangkat di jaringan",
            "port scanning untuk keamanan", "perintah nmap dasar"
        ],
        "response": "🔍 <strong>Network Scanning dengan Nmap:</strong><br>• <strong>Scan host aktif:</strong> <code>nmap -sn 192.168.1.0/24</code><br>• <strong>Scan port:</strong> <code>nmap -p 1-1000 192.168.1.1</code><br>• <strong>Deteksi OS:</strong> <code>nmap -O 192.168.1.1</code><br>• <strong>Catatan:</strong> Gunakan hanya pada jaringan yang Anda miliki atau diizinkan."
    },

    # =========================================================================
    # 77. PANDUAN PRAKTIKUM BANDWIDTH TEST
    # =========================================================================
    {
        "intent": "bandwidth_test",
        "keywords": ["bandwidth test", "speedtest", "tes kecepatan", "iperf", "ukur bandwidth"],
        "questions": [
            "cara tes kecepatan internet di lab", "menggunakan iperf",
            "speedtest lokal", "mengukur bandwidth internal"
        ],
        "response": "🚀 <strong>Bandwidth Test:</strong><br>• <strong>Internet:</strong> Speedtest.net atau fast.com.<br>• <strong>Lokal:</strong> Gunakan <code>iperf3</code>: server <code>iperf3 -s</code>, client <code>iperf3 -c [IP server]</code>.<br>• <strong>MikroTik:</strong> <code>/tool bandwidth-test address=[IP] direction=both</code>"
    },

    # =========================================================================
    # 78. INFORMASI TAMBAHAN TENTANG LAB
    # =========================================================================
    {
        "intent": "sejarah_lab",
        "keywords": ["sejarah lab", "tentang lab", "didirikan", "lab tkj berdiri", "profil lab"],
        "questions": [
            "kapan lab tkj didirikan?", "sejarah laboratorium tkj",
            "profil lab tkj winshark", "tentang laboratorium"
        ],
        "response": "🏫 <strong>Sejarah Singkat Lab TKJ:</strong><br>Laboratorium TKJ Winshark didirikan pada tahun 2015 seiring dengan dibukanya jurusan Teknik Komputer dan Jaringan. Lab terus berkembang dengan penambahan perangkat modern seperti MikroTik, Cisco, dan peralatan fiber optic."
    },

    # =========================================================================
    # 79. PANDUAN PRAKTIKUM PEMROGRAMAN JARINGAN
    # =========================================================================
    {
        "intent": "python_network",
        "keywords": ["python jaringan", "socket programming", "python network", "api", "network automation"],
        "questions": [
            "belajar python untuk jaringan", "socket programming dengan python",
            "automation jaringan dengan python", "contoh script python network"
        ],
        "response": "🐍 <strong>Python untuk Jaringan:</strong><br>• <strong>Socket:</strong> Modul bawaan untuk komunikasi TCP/UDP.<br>• <strong>Netmiko:</strong> Otomasi perangkat jaringan (SSH).<br>• <strong>Requests:</strong> Akses API (contoh: REST API MikroTik).<br>• <strong>Contoh socket server:</strong> bind, listen, accept, recv, send."
    },
    {
        "intent": "bash_scripting",
        "keywords": ["bash", "script linux", "automation linux", "shell script", "cron job"],
        "questions": [
            "belajar bash scripting", "membuat script otomatis linux",
            "cron job untuk backup", "contoh shell script"
        ],
        "response": "🖥️ <strong>Bash Scripting:</strong><br>• <strong>Buat file:</strong> <code>#!/bin/bash</code> di baris pertama.<br>• <strong>Jalankan:</strong> <code>chmod +x script.sh</code> lalu <code>./script.sh</code>.<br>• <strong>Cron:</strong> <code>crontab -e</code> untuk jadwal otomatis.<br>• <strong>Contoh:</strong> backup folder dengan tar setiap malam."
    },

    # =========================================================================
    # 80. TAMBAHAN FAQ (PENUTUP)
    # =========================================================================
    {
        "intent": "terima_kasih_khusus",
        "keywords": ["terima kasih banyak", "sangat membantu", "thanks a lot", "appreciate", "good bot"],
        "questions": [
            "terima kasih sudah membantu", "bot ini sangat membantu",
            "makasih banget informasinya", "ok terima kasih banyak"
        ],
        "response": "😊 Sama-sama! Saya senang bisa membantu. Jika ada pertanyaan lain seputar Lab TKJ, jangan sungkan untuk bertanya lagi."
    },
    {
        "intent": "request_fitur_baru",
        "keywords": ["minta fitur", "tambah fitur", "fitur apa lagi", "request improvement"],
        "questions": [
            "bisa tambahkan fitur x?", "saya ingin fitur baru di sistem",
            "saran untuk pengembangan chatbot", "request penambahan topik"
        ],
        "response": "💡 Terima kasih atas masukannya. Silakan sampaikan detail fitur yang diinginkan melalui Admin Lab. Tim pengembang akan mengevaluasi untuk update berikutnya."
    },
    {
        "intent": "error_sistem",
        "keywords": ["error", "bug", "tidak berfungsi", "system error", "crash"],
        "questions": [
            "sistem error", "menu tidak berfungsi", "tombol tidak bisa diklik",
            "aplikasi crash", "bug pada sistem"
        ],
        "response": "🐞 <strong>Laporan Error/Bug:</strong><br>Jika menemukan kendala teknis pada sistem, mohon laporkan ke Admin dengan menyertakan screenshot dan deskripsi langkah yang dilakukan. Tim akan segera memperbaikinya."
    },
    {
        "intent": "kontak_darurat",
        "keywords": ["darurat", "emergency", "penting", "segera", "urgent help"],
        "questions": [
            "kontak darurat lab", "nomor penting lab",
            "bantuan mendesak", "hubungi cepat"
        ],
        "response": "🚨 <strong>Kontak Darurat Lab:</strong><br>• <strong>WhatsApp Admin:</strong> 0878-7458-9054<br>• <strong>Ruang Teknisi:</strong> Sebelah Lab TKJ 1<br>• <strong>Jam operasional:</strong> Senin-Jumat 08.00-15.00 WIB<br>• Untuk keadaan darurat (kebakaran, kecelakaan), segera hubungi guru/teknisi terdekat."
    },
        # =========================================================================
    # 81. TAMBAHAN PANDUAN MIKROTIK (TYPO & VARIASI)
    # =========================================================================
    {
        "intent": "mikrotik_typo",
        "keywords": ["mikrotikk", "mikrotik router", "mikrotik rb", "router mikrotik", "mikrotik setup", "mikrotikk router", "mikrotik config", "konfigurasi mikrotik", "setting mikrotik", "mikrotik dasar"],
        "questions": [
            "gimana setting mikrotik?", "mikrotikk cara pakai", "konfigurasi dasar mikrotik router",
            "mikrotik rb750 cara setting", "panduan mikrotik untuk pemula"
        ],
        "response": "📡 <strong>Panduan Dasar MikroTik:</strong><br>• <strong>Default IP:</strong> 192.168.88.1<br>• <strong>Login:</strong> admin (tanpa password)<br>• <strong>Akses:</strong> Gunakan Winbox atau browser.<br>• <strong>Reset:</strong> Cabut power, tekan tombol reset, colok power, tahan 5 detik.<br>• <strong>Simpan konfigurasi:</strong> <code>/system backup save</code> atau <code>/export</code>."
    },
    {
        "intent": "mikrotik_hotspot_typo",
        "keywords": ["hotspot mikrotik", "mikrotik hotspot", "hotspot setup", "hotspot router", "mikrotik voucher", "voucher hotspot", "login hotspot", "hotspot mikrotikk"],
        "questions": [
            "cara bikin hotspot di mikrotik", "mikrotik voucher login", "hotspot setup mikrotik",
            "gimana setting hotspot mikrotik", "mikrotik hotspot tidak muncul login"
        ],
        "response": "📶 <strong>Hotspot MikroTik:</strong><br>1. <code>/ip hotspot setup</code> pilih interface, ikuti wizard.<br>2. Buat user: <code>/ip hotspot user add name=user1 password=123</code>.<br>3. Login page otomatis di <code>http://IP-hotspot</code>.<br>4. Kustomisasi file <code>login.html</code> di menu Files.<br>5. Untuk voucher, gunakan User Manager."
    },
    {
        "intent": "mikrotik_firewall_typo",
        "keywords": ["firewall mikrotik", "mikrotik firewall", "blokir mikrotik", "mikrotik blokir situs", "filter rules mikrotik", "mikrotik drop", "mikrotik block"],
        "questions": [
            "cara blokir ip di mikrotik", "mikrotik firewall rule", "mikrotik blokir youtube",
            "mikrotik filter traffic", "mikrotik drop semua"
        ],
        "response": "🛡️ <strong>Firewall MikroTik:</strong><br>• <strong>Blokir IP:</strong> <code>/ip firewall filter add chain=forward src-address=192.168.1.100 action=drop</code><br>• <strong>Blokir Port:</strong> <code>chain=forward protocol=tcp dst-port=80 action=drop</code><br>• <strong>Izinkan dulu, drop sisanya:</strong> Aturan allow di atas, drop all di bawah."
    },
    {
        "intent": "mikrotik_queue_typo",
        "keywords": ["queue mikrotik", "limit bandwidth mikrotik", "mikrotik limit", "simple queue mikrotik", "mikrotik bandwidth", "mikrotik speed limit"],
        "questions": [
            "cara limit bandwidth di mikrotik", "mikrotik simple queue", "mikrotik atur kecepatan",
            "mikrotik bandwidth per user", "mikrotik queue tree"
        ],
        "response": "📊 <strong>Limit Bandwidth MikroTik:</strong><br>• <strong>Simple Queue:</strong> <code>/queue simple add target=192.168.1.0/24 max-limit=1M/1M</code><br>• <strong>Queue Tree:</strong> Gunakan mangle untuk marking, lalu buat queue tree.<br>• <strong>PCQ:</strong> Untuk membagi rata per user."
    },

    # =========================================================================
    # 82. TAMBAHAN PANDUAN CISCO (TYPO & VARIASI)
    # =========================================================================
    {
        "intent": "cisco_typo",
        "keywords": ["cisco router", "cisco switch", "cisco ios", "cisco config", "cisco setting", "cisco command", "perintah cisco", "cisco dasar"],
        "questions": [
            "gimana setting cisco?", "cisco router basic config", "cisco switch setup",
            "cisco ios perintah", "cisco config dasar"
        ],
        "response": "🖥️ <strong>Dasar Cisco IOS:</strong><br>• <strong>Masuk privileged:</strong> <code>enable</code><br>• <strong>Global config:</strong> <code>configure terminal</code><br>• <strong>Set hostname:</strong> <code>hostname SW1</code><br>• <strong>Simpan konfigurasi:</strong> <code>copy running-config startup-config</code><br>• <strong>Lihat konfigurasi:</strong> <code>show running-config</code>"
    },
    {
        "intent": "cisco_vlan_typo",
        "keywords": ["vlan cisco", "cisco vlan", "vlan configuration", "vlan setup", "cisco vlan config", "switchport vlan", "vlan cisco command"],
        "questions": [
            "cara buat vlan di cisco", "cisco vlan setup", "vlan cisco perintah",
            "cisco switch vlan configuration", "vlan id cisco"
        ],
        "response": "🔀 <strong>VLAN di Cisco:</strong><br>1. <code>vlan 10</code><br>2. <code>name SISWA</code><br>3. Masuk interface: <code>interface fa0/1</code><br>4. <code>switchport mode access</code><br>5. <code>switchport access vlan 10</code><br>6. Trunk: <code>switchport mode trunk</code>"
    },
    {
        "intent": "cisco_password_typo",
        "keywords": ["cisco password", "enable secret cisco", "cisco enable password", "cisco login password", "cisco console password", "cisco vty password"],
        "questions": [
            "cara set password cisco", "cisco enable secret", "cisco console password",
            "cisco password line vty", "cisco ganti password"
        ],
        "response": "🔐 <strong>Password Cisco:</strong><br>• <strong>Enable secret:</strong> <code>enable secret rahasia123</code><br>• <strong>Console password:</strong> <code>line console 0</code> -> <code>password pass</code> -> <code>login</code><br>• <strong>VTY password:</strong> <code>line vty 0 4</code> -> <code>password pass</code> -> <code>login</code>"
    },

    # =========================================================================
    # 83. TAMBAHAN LINUX & SERVER
    # =========================================================================
    {
        "intent": "linux_permission",
        "keywords": ["chmod", "chown", "permission linux", "hak akses linux", "chmod 777", "linux file permission", "izin file linux"],
        "questions": [
            "cara ganti permission file linux", "chmod 755 artinya apa", "chown linux",
            "linux permission denied", "mengubah hak akses folder"
        ],
        "response": "🔐 <strong>Permission Linux:</strong><br>• <strong>chmod:</strong> Mengubah permission file/folder.<br>• <strong>Contoh:</strong> <code>chmod 755 file</code> (rwxr-xr-x)<br>• <strong>chmod 777:</strong> Semua bisa baca, tulis, eksekusi (tidak disarankan).<br>• <strong>chown:</strong> Mengubah pemilik file: <code>chown user:group file</code>"
    },
    {
        "intent": "linux_cron",
        "keywords": ["cron job", "crontab", "jadwal tugas linux", "linux scheduler", "cron linux", "crontab -e"],
        "questions": [
            "cara membuat cron job", "contoh crontab", "jadwal otomatis linux",
            "cron job backup", "crontab syntax"
        ],
        "response": "⏰ <strong>Cron Job Linux:</strong><br>• <strong>Edit crontab:</strong> <code>crontab -e</code><br>• <strong>Format:</strong> menit jam hari bulan hari_minggu perintah<br>• <strong>Contoh:</strong> <code>0 2 * * * /home/user/backup.sh</code> (setiap jam 2 pagi)<br>• <strong>Lihat daftar:</strong> <code>crontab -l</code>"
    },
    {
        "intent": "linux_package_manager",
        "keywords": ["apt", "yum", "dnf", "paket linux", "install software linux", "apt-get", "linux package"],
        "questions": [
            "cara install aplikasi di linux", "perintah apt", "linux package manager",
            "cara update linux", "install paket ubuntu"
        ],
        "response": "📦 <strong>Package Manager Linux:</strong><br>• <strong>Debian/Ubuntu:</strong> <code>apt update && apt upgrade</code>, <code>apt install nama-paket</code><br>• <strong>RedHat/CentOS/Fedora:</strong> <code>yum install nama-paket</code> atau <code>dnf install</code><br>• <strong>Cari paket:</strong> <code>apt search kata-kunci</code>"
    },
    {
        "intent": "linux_process",
        "keywords": ["ps", "top", "htop", "kill", "proses linux", "linux process", "melihat proses"],
        "questions": [
            "cara melihat proses di linux", "perintah kill", "linux top command",
            "menghentikan proses", "ps aux"
        ],
        "response": "⚙️ <strong>Manajemen Proses Linux:</strong><br>• <strong>Lihat proses:</strong> <code>ps aux</code> atau <code>top</code> / <code>htop</code><br>• <strong>Hentikan proses:</strong> <code>kill PID</code> atau <code>kill -9 PID</code><br>• <strong>Cari proses:</strong> <code>pgrep nama</code>"
    },

    # =========================================================================
    # 84. KEAMANAN JARINGAN LANJUTAN
    # =========================================================================
    {
        "intent": "security_pentest",
        "keywords": ["pentest", "penetration testing", "keamanan jaringan", "ethical hacking", "tools hacking", "kalilinux", "metasploit"],
        "questions": [
            "apa itu pentest?", "belajar ethical hacking", "tools untuk security testing",
            "kalilinux untuk apa?", "metasploit dasar"
        ],
        "response": "🛡️ <strong>Penetration Testing Dasar:</strong><br>• <strong>Pentest:</strong> Pengujian keamanan sistem secara legal.<br>• <strong>Tools populer:</strong> Nmap, Wireshark, Metasploit, Burp Suite, Aircrack-ng.<br>• <strong>Kali Linux:</strong> Distro Linux khusus security testing.<br>• <strong>Penting:</strong> Hanya lakukan pada sistem yang diizinkan."
    },
    {
        "intent": "security_ssl_tls",
        "keywords": ["ssl", "tls", "https", "sertifikat ssl", "enkripsi", "ssl certificate", "tls handshake"],
        "questions": [
            "apa itu ssl?", "perbedaan ssl dan tls", "cara pasang ssl",
            "https itu apa", "sertifikat ssl gratis"
        ],
        "response": "🔒 <strong>SSL/TLS:</strong><br>• <strong>SSL (Secure Sockets Layer):</strong> Protokol keamanan lama.<br>• <strong>TLS (Transport Layer Security):</strong> Versi modern pengganti SSL.<br>• <strong>Fungsi:</strong> Enkripsi data antara browser dan server.<br>• <strong>Sertifikat gratis:</strong> Let's Encrypt."
    },
    {
        "intent": "security_sql_injection",
        "keywords": ["sql injection", "serangan sql", "keamanan database", "sqli", "injection attack", "sql injection contoh"],
        "questions": [
            "apa itu sql injection?", "cara mencegah sql injection", "contoh serangan sql injection",
            "sql injection adalah", "keamanan aplikasi web"
        ],
        "response": "💉 <strong>SQL Injection:</strong><br>Serangan dengan menyisipkan perintah SQL berbahaya melalui input aplikasi.<br>• <strong>Pencegahan:</strong> Gunakan parameterized query / prepared statement.<br>• <strong>Validasi input:</strong> Batasi karakter khusus.<br>• <strong>Contoh serangan:</strong> <code>' OR '1'='1</code>"
    },

    # =========================================================================
    # 85. TAMBAHAN MONITORING & TROUBLESHOOTING
    # =========================================================================
    {
        "intent": "traceroute",
        "keywords": ["traceroute", "tracert", "lacak rute", "route jaringan", "hops", "traceroute command"],
        "questions": [
            "cara lacak rute jaringan", "perintah traceroute", "tracert windows",
            "melihat hop jaringan", "traceroute mikrotik"
        ],
        "response": "🗺️ <strong>Traceroute:</strong><br>• <strong>Windows:</strong> <code>tracert 8.8.8.8</code><br>• <strong>Linux/MikroTik:</strong> <code>traceroute 8.8.8.8</code><br>• <strong>Fungsi:</strong> Menampilkan setiap hop (router) yang dilalui paket."
    },
    {
        "intent": "ping_extended",
        "keywords": ["ping", "test koneksi", "ping command", "ping timeout", "ping flood", "ping besar"],
        "questions": [
            "cara test koneksi dengan ping", "ping terus menerus", "ping dengan ukuran paket",
            "ping timeout berapa", "ping google"
        ],
        "response": "📶 <strong>Perintah Ping:</strong><br>• <strong>Windows:</strong> <code>ping 8.8.8.8 -t</code> (terus menerus)<br>• <strong>Linux:</strong> <code>ping 8.8.8.8</code><br>• <strong>Ukuran paket:</strong> <code>ping -l 1000 8.8.8.8</code> (Windows) atau <code>ping -s 1000 8.8.8.8</code> (Linux)<br>• <strong>Hasil:</strong> Reply = koneksi OK, Request timed out = putus."
    },
    {
        "intent": "dns_troubleshoot",
        "keywords": ["dns error", "dns tidak berfungsi", "nslookup", "dns server not responding", "dns gagal", "perbaiki dns"],
        "questions": [
            "dns server not responding", "cara cek dns", "nslookup command",
            "ganti dns windows", "dns tidak bisa resolve"
        ],
        "response": "🔧 <strong>Troubleshooting DNS:</strong><br>1. Cek dengan <code>nslookup google.com</code><br>2. Ganti DNS ke 8.8.8.8 atau 1.1.1.1<br>3. Flush DNS: <code>ipconfig /flushdns</code> (Windows) atau <code>systemd-resolve --flush-caches</code> (Linux)<br>4. Periksa file hosts."
    },

    # =========================================================================
    # 86. CLOUD & DEVOPS DASAR
    # =========================================================================
    {
        "intent": "docker",
        "keywords": ["docker", "container", "docker container", "docker image", "docker compose", "virtualisasi container"],
        "questions": [
            "apa itu docker?", "cara install docker", "docker vs virtualbox",
            "docker container tutorial", "docker compose example"
        ],
        "response": "🐳 <strong>Docker:</strong><br>• <strong>Container:</strong> Virtualisasi ringan di level OS.<br>• <strong>Install Ubuntu:</strong> <code>sudo apt install docker.io</code><br>• <strong>Jalankan container:</strong> <code>docker run hello-world</code><br>• <strong>Docker Compose:</strong> Untuk menjalankan banyak container sekaligus."
    },
    {
        "intent": "git",
        "keywords": ["git", "github", "version control", "git commit", "git push", "git clone", "repositori"],
        "questions": [
            "apa itu git?", "cara menggunakan git", "git clone repository",
            "git commit dan push", "github untuk pemula"
        ],
        "response": "🔧 <strong>Git & GitHub:</strong><br>• <strong>Git:</strong> Version control system.<br>• <strong>Clone repo:</strong> <code>git clone URL</code><br>• <strong>Tambah file:</strong> <code>git add .</code><br>• <strong>Commit:</strong> <code>git commit -m \"pesan\"</code><br>• <strong>Push:</strong> <code>git push origin main</code>"
    },
    {
        "intent": "ci_cd",
        "keywords": ["ci/cd", "continuous integration", "jenkins", "github actions", "deployment otomatis", "devops pipeline"],
        "questions": [
            "apa itu ci/cd?", "cara setup jenkins", "github actions tutorial",
            "pipeline deployment", "devops untuk pemula"
        ],
        "response": "🔄 <strong>CI/CD:</strong><br>• <strong>CI (Continuous Integration):</strong> Otomatisasi build dan test setiap perubahan kode.<br>• <strong>CD (Continuous Delivery/Deployment):</strong> Otomatisasi rilis ke server.<br>• <strong>Tools:</strong> Jenkins, GitLab CI, GitHub Actions."
    },

    # =========================================================================
    # 87. PRAKTIKUM JARINGAN LANJUTAN
    # =========================================================================
    {
        "intent": "vlan_routing",
        "keywords": ["inter vlan routing", "vlan routing", "router on a stick", "vlan antar jaringan", "routing antar vlan"],
        "questions": [
            "cara menghubungkan dua vlan", "inter vlan routing cisco", "router on a stick configuration",
            "vlan routing mikrotik", "routing antar vlan beda switch"
        ],
        "response": "🔀 <strong>Inter-VLAN Routing:</strong><br>• <strong>Metode 1:</strong> Router on a stick (sub-interface pada router).<br>• <strong>Metode 2:</strong> Switch Layer 3 (SVIs).<br>• <strong>Cisco contoh:</strong> <code>interface fa0/0.10</code> -> <code>encapsulation dot1Q 10</code> -> <code>ip address 192.168.10.1 255.255.255.0</code>"
    },
    {
        "intent": "access_list",
        "keywords": ["acl", "access list", "access-list", "filter paket", "cisco acl", "mikrotik acl"],
        "questions": [
            "apa itu access list?", "cara konfigurasi acl cisco", "mikrotik access list",
            "filter trafik dengan acl", "access-list contoh"
        ],
        "response": "📋 <strong>Access Control List (ACL):</strong><br>• <strong>Fungsi:</strong> Menyaring paket berdasarkan IP, port, protokol.<br>• <strong>Cisco:</strong> <code>access-list 100 permit tcp any host 10.0.0.1 eq 80</code><br>• <strong>MikroTik:</strong> Menggunakan firewall filter rules."
    },
    {
        "intent": "nat_config",
        "keywords": ["nat", "network address translation", "ip nat", "masquerade", "nat mikrotik", "nat cisco", "port forwarding"],
        "questions": [
            "apa itu nat?", "cara setting nat di mikrotik", "nat cisco configuration",
            "port forwarding mikrotik", "masquerade mikrotik"
        ],
        "response": "🌐 <strong>NAT (Network Address Translation):</strong><br>• <strong>Fungsi:</strong> Menerjemahkan IP privat ke publik.<br>• <strong>MikroTik:</strong> <code>/ip firewall nat add chain=srcnat out-interface=ether1 action=masquerade</code><br>• <strong>Port Forwarding:</strong> <code>chain=dstnat protocol=tcp dst-port=80 action=dst-nat to-addresses=192.168.1.10 to-ports=80</code>"
    },

    # =========================================================================
    # 88. KEBIJAKAN & ADMINISTRASI
    # =========================================================================
    {
        "intent": "prosedur_audit",
        "keywords": ["audit lab", "audit inventaris", "pemeriksaan alat", "stock opname", "audit tahunan", "cek fisik alat"],
        "questions": [
            "bagaimana prosedur audit lab?", "kapan audit inventaris dilakukan?",
            "stock opname alat lab", "cek fisik barang lab"
        ],
        "response": "🔍 <strong>Audit Inventaris Lab:</strong><br>1. Dilakukan minimal setahun sekali.<br>2. Tim audit mencocokkan data sistem dengan fisik barang.<br>3. Setiap selisih dicatat dan dilaporkan ke Kepala Lab.<br>4. Hasil audit digunakan untuk perbaikan manajemen."
    },
    {
        "intent": "peminjaman_ruang_kelas",
        "keywords": ["pinjam lab", "ruangan praktikum", "lab untuk kelas", "jadwal lab", "booking ruangan", "penggunaan lab"],
        "questions": [
            "cara pinjam ruangan lab untuk kelas", "jadwal penggunaan lab per kelas",
            "booking lab untuk praktikum", "ruangan lab dipakai siapa saja"
        ],
        "response": "📅 <strong>Peminjaman Ruangan Lab:</strong><br>• Guru mengajukan jadwal penggunaan lab ke Admin.<br>• Lab diprioritaskan untuk kelas praktikum terjadwal.<br>• Penggunaan di luar jam pelajaran memerlukan izin khusus."
    },

    # =========================================================================
    # 89. PRAKTIKUM IOT LANJUTAN
    # =========================================================================
    {
        "intent": "mqtt_protocol",
        "keywords": ["mqtt", "protokol iot", "broker mqtt", "mosquitto", "publish subscribe", "mqtt broker"],
        "questions": [
            "apa itu mqtt?", "cara install mosquitto", "mqtt publish subscribe",
            "broker mqtt lokal", "mqtt arduino"
        ],
        "response": "📡 <strong>MQTT:</strong><br>Protokol ringan untuk IoT berbasis publish/subscribe.<br>• <strong>Broker:</strong> Mosquitto, HiveMQ.<br>• <strong>Install Mosquitto:</strong> <code>sudo apt install mosquitto</code><br>• <strong>Subscribe:</strong> <code>mosquitto_sub -t topik</code><br>• <strong>Publish:</strong> <code>mosquitto_pub -t topik -m pesan</code>"
    },
    {
        "intent": "thingspeak",
        "keywords": ["thingspeak", "iot cloud", "kirim data sensor", "thingspeak arduino", "api thingspeak"],
        "questions": [
            "cara kirim data ke thingspeak", "thingspeak arduino code", "thingspeak api",
            "monitoring sensor online", "thingspeak tutorial"
        ],
        "response": "📈 <strong>ThingSpeak:</strong><br>Platform cloud untuk menyimpan dan visualisasi data sensor.<br>• Buat akun dan channel, dapatkan API key.<br>• Kirim data via HTTP GET: <code>GET https://api.thingspeak.com/update?api_key=XXX&field1=25</code>"
    },

    # =========================================================================
    # 90. KEBIJAKAN PENGGUNAAN PERANGKAT LUNAK
    # =========================================================================
    {
        "intent": "software_opensource",
        "keywords": ["open source", "software gratis", "lisensi open source", "aplikasi open source", "free software"],
        "questions": [
            "apa itu open source?", "contoh software open source untuk tkj",
            "lisensi open source", "software gratis untuk lab"
        ],
        "response": "💻 <strong>Open Source Software:</strong><br>• <strong>Definisi:</strong> Software dengan kode sumber terbuka dan bebas digunakan.<br>• <strong>Contoh:</strong> Linux, Apache, MySQL, GNS3, Wireshark, VirtualBox.<br>• <strong>Lisensi umum:</strong> GPL, MIT, Apache."
    },
    {
        "intent": "software_berlisensi",
        "keywords": ["lisensi software", "software berbayar", "lisensi microsoft", "adobe license", "software legal"],
        "questions": [
            "apakah lab punya lisensi software berbayar?", "software berlisensi di lab",
            "lisensi windows di lab", "software apa yang berbayar"
        ],
        "response": "📄 <strong>Software Berlisensi di Lab:</strong><br>• Lab menggunakan Windows dengan lisensi volume licensing dari sekolah.<br>• Software lain yang berbayar harus memiliki lisensi resmi.<br>• Dilarang menggunakan crack atau keygen."
    },

    # =========================================================================
    # 91. TAMBAHAN TROUBLESHOOTING HARDWARE
    # =========================================================================
    {
        "intent": "komputer_tidak_menyala",
        "keywords": ["pc tidak menyala", "komputer mati", "cpu tidak hidup", "power supply rusak", "pc no power", "komputer tidak ada respon"],
        "questions": [
            "komputer lab tidak mau nyala", "pc tidak ada daya", "power supply rusak ciri-ciri",
            "cpu tidak menyala tapi lampu indikator nyala", "pc mati total"
        ],
        "response": "🔌 <strong>PC Tidak Menyala:</strong><br>1. Cek kabel power dan stop kontak.<br>2. Cek tombol power (mungkin longgar).<br>3. Cek power supply dengan multimeter.<br>4. Coba ganti power supply.<br>5. Jika masih mati, kemungkinan motherboard rusak."
    },
    {
        "intent": "monitor_tidak_tampil",
        "keywords": ["monitor tidak nyala", "layar blank", "tidak ada tampilan", "monitor hitam", "vga tidak terdeteksi", "display tidak muncul"],
        "questions": [
            "monitor komputer tidak menampilkan gambar", "layar blank saat pc dinyalakan",
            "vga tidak terdeteksi", "monitor hitam tapi cpu menyala"
        ],
        "response": "🖥️ <strong>Monitor Tidak Tampil:</strong><br>1. Cek kabel VGA/HDMI terpasang benar.<br>2. Pastikan monitor menyala dan input benar.<br>3. Coba ganti kabel atau port.<br>4. Periksa RAM, bersihkan dengan penghapus.<br>5. Cek VGA card / onboard."
    },
    {
        "intent": "keyboard_mouse_error",
        "keywords": ["keyboard tidak berfungsi", "mouse tidak jalan", "usb tidak terdeteksi", "keyboard error", "mouse mati"],
        "questions": [
            "keyboard tidak merespon", "mouse tidak bergerak", "usb device not recognized",
            "keyboard error saat boot", "mouse dan keyboard mati"
        ],
        "response": "⌨️ <strong>Keyboard/Mouse Tidak Berfungsi:</strong><br>1. Coba colok ke port USB lain.<br>2. Restart komputer.<br>3. Cek di Device Manager (Windows).<br>4. Bersihkan port USB.<br>5. Jika nirkabel, cek baterai."
    },

    # =========================================================================
    # 92. PANDUAN PRAKTIKUM FIBER OPTIC LANJUTAN
    # =========================================================================
    {
        "intent": "fiber_loss_budget",
        "keywords": ["loss budget", "anggaran redaman", "perhitungan loss fo", "link budget fiber", "redaman total fiber"],
        "questions": [
            "cara menghitung loss budget fiber optic", "apa itu loss budget", "redaman total kabel fo",
            "perhitungan link budget", "loss budget contoh"
        ],
        "response": "📐 <strong>Loss Budget Fiber Optic:</strong><br>• <strong>Rumus:</strong> Total Loss = (Panjang kabel x Redaman per km) + (Jumlah konektor x Loss per konektor) + (Jumlah sambungan x Loss per sambungan)<br>• <strong>Nilai umum:</strong> Redaman kabel 0.35 dB/km (1310nm), konektor 0.5 dB, sambungan 0.1 dB."
    },
    {
        "intent": "fiber_connector_types",
        "keywords": ["konektor fiber", "sc lc fc st", "jenis konektor fo", "fiber connector", "sc connector", "lc connector"],
        "questions": [
            "jenis konektor fiber optic", "perbedaan sc dan lc", "konektor fc st",
            "fiber optic connector types", "konektor fo"
        ],
        "response": "🔌 <strong>Jenis Konektor Fiber Optic:</strong><br>• <strong>SC:</strong> Konektor persegi, snap-in, umum untuk patch panel.<br>• <strong>LC:</strong> Kecil, sering untuk SFP module.<br>• <strong>FC:</strong> Berulir, untuk perangkat presisi.<br>• <strong>ST:</strong> Bayonet, mirip BNC."
    },

    # =========================================================================
    # 93. PANDUAN PRAKTIKUM WIRELESS LANJUTAN
    # =========================================================================
    {
        "intent": "wireless_survey",
        "keywords": ["site survey wifi", "wireless survey", "analisa sinyal wifi", "heatmap wifi", "ekahau", "wifi analyzer"],
        "questions": [
            "cara survey sinyal wifi", "aplikasi untuk cek sinyal wifi", "site survey wireless",
            "heatmap wifi", "menganalisa interferensi wifi"
        ],
        "response": "📶 <strong>Wireless Site Survey:</strong><br>• <strong>Tujuan:</strong> Memetakan kekuatan sinyal dan interferensi.<br>• <strong>Tools:</strong> Ekahau HeatMapper, NetSpot, WiFi Analyzer (Android).<br>• <strong>Parameter:</strong> RSSI (dBm), SNR, channel overlap."
    },
    {
        "intent": "wireless_channel",
        "keywords": ["channel wifi", "interferensi channel", "pilih channel wifi", "channel 1 6 11", "wireless channel width"],
        "questions": [
            "channel wifi yang bagus", "cara pilih channel wifi", "interferensi wifi channel",
            "channel width 20 40 mhz", "wifi channel 1 6 11"
        ],
        "response": "📻 <strong>Channel WiFi:</strong><br>• <strong>2.4 GHz:</strong> Gunakan channel 1, 6, 11 untuk menghindari overlap.<br>• <strong>5 GHz:</strong> Banyak channel non-overlap.<br>• <strong>Channel Width:</strong> 20 MHz lebih stabil, 40/80 MHz lebih cepat tapi rentan interferensi."
    },

    # =========================================================================
    # 94. PANDUAN PRAKTIKUM SERVER (LANJUTAN)
    # =========================================================================
    {
        "intent": "nginx_reverse_proxy",
        "keywords": ["nginx reverse proxy", "proxy server", "reverse proxy nginx", "nginx proxy pass", "load balancer nginx"],
        "questions": [
            "cara setting reverse proxy nginx", "nginx proxy pass", "nginx load balancer",
            "reverse proxy untuk web server", "nginx config proxy"
        ],
        "response": "🔄 <strong>Nginx Reverse Proxy:</strong><br>• <strong>Fungsi:</strong> Meneruskan request ke server backend.<br>• <strong>Contoh config:</strong><br><code>location / { proxy_pass http://127.0.0.1:8080; }</code>"
    },
    {
        "intent": "mysql_replication",
        "keywords": ["mysql replication", "replikasi database", "master slave mysql", "mysql backup realtime", "replikasi mysql"],
        "questions": [
            "cara replikasi mysql", "master slave mysql configuration", "mysql replication setup",
            "database replikasi", "mysql backup otomatis"
        ],
        "response": "🗄️ <strong>Replikasi MySQL:</strong><br>• <strong>Master-Slave:</strong> Data master disalin ke slave secara realtime.<br>• <strong>Kegunaan:</strong> Backup, load balancing baca.<br>• <strong>Konfigurasi:</strong> Aktifkan binary log di master, set server-id unik, buat user replikasi."
    },

    # =========================================================================
    # 95. TAMBAHAN KEBIJAKAN & ETIKA
    # =========================================================================
    {
        "intent": "etika_internet",
        "keywords": ["etika internet", "netiket", "aturan online", "cyber bullying", "etika digital", "sopan di internet"],
        "questions": [
            "apa itu netiket?", "etika menggunakan internet", "cyber bullying adalah",
            "aturan berinternet yang baik", "etika digital untuk siswa"
        ],
        "response": "🌐 <strong>Etika Internet (Netiket):</strong><br>• Hormati pengguna lain, jangan menyebar hoax.<br>• Jangan melakukan cyber bullying.<br>• Jaga privasi diri dan orang lain.<br>• Gunakan bahasa yang sopan di media sosial."
    },
    {
        "intent": "hak_cipta",
        "keywords": ["hak cipta", "copyright", "plagiarisme", "penggunaan konten", "lisensi konten", "fair use"],
        "questions": [
            "apa itu hak cipta?", "aturan penggunaan konten digital", "plagiarisme adalah",
            "fair use artinya", "lisensi creative commons"
        ],
        "response": "©️ <strong>Hak Cipta:</strong><br>• Karya orang lain dilindungi undang-undang.<br>• Dilarang menyalin tanpa izin.<br>• Gunakan konten dengan lisensi bebas (Creative Commons) atau buat sendiri.<br>• Selalu cantumkan sumber jika mengutip."
    },

    # =========================================================================
    # 96. TAMBAHAN PERTANYAAN SEPUTAR LAB
    # =========================================================================
    {
        "intent": "kebersihan_ruang_server",
        "keywords": ["kebersihan server", "suhu server", "ac server", "debu server", "maintenance server room"],
        "questions": [
            "bagaimana menjaga suhu ruang server?", "kebersihan ruang server",
            "suhu ideal server", "debu di server"
        ],
        "response": "🌡️ <strong>Perawatan Ruang Server:</strong><br>• Suhu ideal 18-24°C.<br>• Kelembaban 40-60%.<br>• Bersihkan debu secara berkala.<br>• Pastikan AC berfungsi dan ada ventilasi."
    },
    {
        "intent": "pengadaan_sparepart",
        "keywords": ["sparepart lab", "beli sparepart", "pengadaan sparepart", "stok sparepart", "komponen cadangan"],
        "questions": [
            "bagaimana mengajukan sparepart baru?", "stok sparepart lab",
            "prosedur pembelian sparepart", "sparepart apa yang harus distok"
        ],
        "response": "🔩 <strong>Pengadaan Sparepart:</strong><br>• Usulan dari teknisi atau guru ke Kepala Lab.<br>• Sparepart umum: konektor RJ45, kabel UTP, adaptor, modul SFP, baterai CMOS."
    },

    # =========================================================================
    # 97. PANDUAN PRAKTIKUM KEAMANAN FISIK
    # =========================================================================
    {
        "intent": "keamanan_fisik",
        "keywords": ["keamanan fisik lab", "kunci lab", "akses ruang lab", "cctv lab", "pengamanan perangkat"],
        "questions": [
            "bagaimana keamanan fisik lab?", "apakah lab punya cctv?",
            "siapa yang pegang kunci lab", "pengamanan perangkat lab"
        ],
        "response": "🔐 <strong>Keamanan Fisik Lab:</strong><br>• Lab dikunci saat tidak digunakan.<br>• Hanya petugas yang berwenang memiliki kunci.<br>• Terdapat CCTV di beberapa titik.<br>• Perangkat berharga disimpan di lemari terkunci."
    },

    # =========================================================================
    # 98. TAMBAHAN PANDUAN PRAKTIKUM VIRTUALISASI
    # =========================================================================
    {
        "intent": "proxmox",
        "keywords": ["proxmox", "virtualisasi server", "proxmox ve", "container lxc", "proxmox install"],
        "questions": [
            "apa itu proxmox?", "cara install proxmox", "proxmox vs vmware",
            "container lxc proxmox", "proxmox tutorial"
        ],
        "response": "🖥️ <strong>Proxmox VE:</strong><br>Platform virtualisasi open source berbasis Debian.<br>• Mendukung KVM (VM) dan LXC (container).<br>• Manajemen via web interface.<br>• Cocok untuk server lab."
    },
    {
        "intent": "vm_network_mode",
        "keywords": ["vm network", "bridged nat host-only", "virtualbox network mode", "vmware network", "mode jaringan vm"],
        "questions": [
            "perbedaan nat bridged host-only", "virtualbox network setting", "mode jaringan vmware",
            "bridge vs nat virtualbox", "host-only network"
        ],
        "response": "🌐 <strong>Mode Jaringan VM:</strong><br>• <strong>NAT:</strong> VM dapat akses internet via host, tidak terlihat dari luar.<br>• <strong>Bridge:</strong> VM seperti perangkat fisik di jaringan yang sama.<br>• <strong>Host-only:</strong> Jaringan internal hanya antara host dan VM."
    },

    # =========================================================================
    # 99. TAMBAHAN PERTANYAAN SISWA BARU
    # =========================================================================
    {
        "intent": "siswa_baru",
        "keywords": ["siswa baru", "orientasi lab", "pengenalan lab", "aturan untuk siswa baru", "kelas 10 tkj"],
        "questions": [
            "saya siswa baru, apa yang harus saya ketahui tentang lab?",
            "orientasi lab tkj", "aturan lab untuk siswa baru",
            "pengenalan laboratorium tkj"
        ],
        "response": "🎒 <strong>Selamat datang siswa baru!</strong><br>• Kenali tata tertib lab.<br>• Pahami alur peminjaman alat.<br>• Jangan ragu bertanya ke kakak kelas atau guru.<br>• Ikuti pengarahan dari koordinator lab."
    },
    {
        "intent": "kegiatan_ekstra",
        "keywords": ["ekstrakurikuler", "kegiatan lab", "club tkj", "organisasi lab", "komunitas tkj"],
        "questions": [
            "apakah ada ekstrakurikuler tkj?", "kegiatan lab di luar jam pelajaran",
            "club jaringan", "organisasi siswa tkj"
        ],
        "response": "👥 <strong>Kegiatan Ekstrakurikuler TKJ:</strong><br>• Terdapat klub jaringan dan robotika.<br>• Info lebih lanjut melalui guru pembina atau mading lab."
    },

    # =========================================================================
    # 100. TAMBAHAN PERTANYAAN UMUM (PENUTUP)
    # =========================================================================
    {
        "intent": "kontak_guru",
        "keywords": ["kontak guru", "guru tkj", "wali kelas", "pembimbing praktikum", "guru produktif"],
        "questions": [
            "bagaimana menghubungi guru tkj?", "kontak wali kelas tkj",
            "guru produktif jaringan", "pembimbing praktikum"
        ],
        "response": "👨‍🏫 <strong>Kontak Guru:</strong><br>• Hubungi melalui wali kelas atau staff TU.<br>• Guru produktif biasanya ada di ruang guru jurusan TKJ."
    },
    {
        "intent": "masa_peminjaman",
        "keywords": ["masa pinjam", "perpanjang pinjam", "perpanjangan peminjaman", "extend pinjam", "pinjam lebih lama"],
        "questions": [
            "bisa perpanjang masa pinjam alat?", "cara perpanjang peminjaman",
            "pinjam alat lebih dari 3 hari", "perpanjangan masa pinjam alat lab"
        ],
        "response": "⏳ <strong>Perpanjangan Peminjaman:</strong><br>• Ajukan perpanjangan sebelum jatuh tempo melalui sistem.<br>• Perpanjangan maksimal 1 kali dengan alasan jelas.<br>• Harus disetujui Admin."
    },
    {
        "intent": "error_umum",
        "keywords": ["error umum", "pesan error", "kode error", "troubleshoot umum", "masalah umum"],
        "questions": [
            "saya dapat pesan error", "kode error 404", "error 500",
            "tidak bisa akses halaman", "masalah umum sistem"
        ],
        "response": "🐞 <strong>Error Umum:</strong><br>• <strong>404:</strong> Halaman tidak ditemukan.<br>• <strong>500:</strong> Kesalahan server.<br>• <strong>403:</strong> Akses dilarang.<br>• Jika menemui error, laporkan ke Admin dengan screenshot."
    },
    {
        "intent": "update_sistem",
        "keywords": ["update sistem", "pembaruan sistem", "versi terbaru", "changelog", "fitur baru sistem"],
        "questions": [
            "kapan sistem diupdate?", "apa saja fitur terbaru?",
            "versi sistem saat ini", "changelog sistem lab"
        ],
        "response": "🔄 <strong>Update Sistem:</strong><br>• Sistem diperbarui secara berkala oleh tim pengembang.<br>• Informasi perubahan dapat dilihat di menu Pengumuman Lab atau ditanyakan ke Admin."
    },
    {
        "intent": "saran_kritik",
        "keywords": ["saran", "kritik", "masukan", "keluhan", "feedback lab", "komplain"],
        "questions": [
            "bagaimana menyampaikan keluhan?", "saran untuk lab",
            "kotak saran lab", "komplain pelayanan lab"
        ],
        "response": "📝 <strong>Saran & Kritik:</strong><br>• Sampaikan langsung ke Admin atau melalui kotak saran yang tersedia.<br>• Masukan Anda sangat berarti untuk perbaikan layanan."
    },

    # =========================================================================
    # 101. TAMBAHAN PERTANYAAN TEKNIS MENDALAM
    # =========================================================================
    {
        "intent": "subnet_calculator",
        "keywords": ["kalkulator subnet", "hitung ip", "subnet mask calculator", "ip calculator", "menghitung range ip"],
        "questions": [
            "cara menghitung range ip", "subnet calculator online", "hitung ip address",
            "mencari network dan broadcast", "kalkulator ip"
        ],
        "response": "🧮 <strong>Menghitung Subnet:</strong><br>• Gunakan tools online seperti subnet-calculator.com.<br>• Atau gunakan perintah di Linux: <code>ipcalc 192.168.1.0/24</code><br>• Rumus host: 2^(32-prefix) - 2."
    },
    {
        "intent": "binary_conversion",
        "keywords": ["biner", "desimal ke biner", "konversi ip", "binary ip", "ip address binary"],
        "questions": [
            "cara mengubah ip ke biner", "konversi desimal ke biner",
            "ip address dalam biner", "belajar biner untuk subnetting"
        ],
        "response": "🔢 <strong>Konversi Biner:</strong><br>• Setiap oktet IP adalah 8 bit.<br>• Contoh: 192 = 11000000, 168 = 10101000.<br>• Latihan penting untuk memahami subnetting."
    },
    {
        "intent": "osi_troubleshoot",
        "keywords": ["troubleshoot osi", "layer osi troubleshooting", "pendekatan osi", "problem jaringan per layer"],
        "questions": [
            "cara troubleshoot berdasarkan osi layer", "masalah di layer fisik",
            "troubleshoot dari bawah ke atas", "layer 2 vs layer 3 problem"
        ],
        "response": "🔍 <strong>Troubleshooting per OSI Layer:</strong><br>• <strong>Physical:</strong> Cek kabel, lampu indikator.<br>• <strong>Data Link:</strong> Cek MAC, VLAN.<br>• <strong>Network:</strong> Cek IP, routing.<br>• <strong>Transport:</strong> Cek port, firewall.<br>• <strong>Application:</strong> Cek DNS, service."
    },

    # =========================================================================
    # 102. PANDUAN PRAKTIKUM NETWORK AUTOMATION
    # =========================================================================
    {
        "intent": "ansible",
        "keywords": ["ansible", "otomasi jaringan", "network automation", "ansible playbook", "konfigurasi otomatis"],
        "questions": [
            "apa itu ansible?", "ansible untuk jaringan", "ansible playbook contoh",
            "otomasi konfigurasi router", "belajar ansible"
        ],
        "response": "🤖 <strong>Ansible:</strong><br>• Alat otomasi untuk konfigurasi banyak perangkat sekaligus.<br>• Menggunakan YAML playbook.<br>• Dapat mengelola server Linux dan perangkat jaringan (via SSH)."
    },
    {
        "intent": "netmiko",
        "keywords": ["netmiko", "python netmiko", "otomasi cisco", "python ssh", "network automation python"],
        "questions": [
            "cara menggunakan netmiko", "python netmiko cisco", "netmiko example",
            "otomasi perangkat jaringan dengan python", "netmiko install"
        ],
        "response": "🐍 <strong>Netmiko:</strong><br>Library Python untuk koneksi SSH ke perangkat jaringan.<br>• Install: <code>pip install netmiko</code><br>• Mendukung Cisco, MikroTik, Juniper, dll.<br>• Contoh: kirim perintah dan ambil output."
    },

    # =========================================================================
    # 103. TAMBAHAN PERTANYAAN LAB (PENUTUP 2)
    # =========================================================================
    {
        "intent": "terima_kasih_lain",
        "keywords": ["terimakasih", "makasih banyak", "thx", "tq", "thank you", "thanks bro"],
        "questions": [
            "terimakasih ya", "makasih banyak", "thx", "thanks"
        ],
        "response": "Sama-sama! Jika ada pertanyaan lain, silakan tanyakan."
    },
    {
        "intent": "bye_lain",
        "keywords": ["dadah", "bye bye", "see you", "sampai nanti", "goodbye"],
        "questions": [
            "dadah", "bye", "see you", "sampai nanti"
        ],
        "response": "Sampai jumpa kembali!"
    },
    {
        "intent": "bantuan_lain",
        "keywords": ["help", "tolong", "bantuan", "aku bingung", "tidak paham"],
        "questions": [
            "help", "tolong", "saya butuh bantuan", "bingung"
        ],
        "response": "Silakan jelaskan kendala Anda, saya akan coba bantu."
    },
    {
        "intent": "reset_password",
        "keywords": ["reset password", "lupa sandi", "reset sandi", "ganti password lupa", "password hilang"],
        "questions": [
            "saya lupa password, bagaimana reset?", "cara reset password akun lab",
            "lupa sandi login", "password tidak bisa masuk"
        ],
        "response": "🔑 <strong>Reset Password:</strong><br>Hubungi Admin untuk reset password. Anda akan diminta verifikasi identitas."
    },
    {
        "intent": "info_kelas",
        "keywords": ["info kelas", "kelas berapa", "jadwal kelas", "ruang kelas", "wali kelas"],
        "questions": [
            "saya kelas berapa?", "info jadwal kelas", "wali kelas saya siapa?"
        ],
        "response": "Informasi kelas dapat dilihat di profil akun Anda atau tanyakan ke wali kelas."
    },
    {
        "intent": "peminjaman_bermasalah",
        "keywords": ["pinjam bermasalah", "alat tidak bisa dipinjam", "stok kosong", "alat tidak tersedia", "pinjam gagal"],
        "questions": [
            "kenapa alat tidak bisa dipinjam?", "stok kosong padahal ada",
            "alat tidak tersedia di katalog", "pinjam gagal"
        ],
        "response": "⚠️ <strong>Masalah Peminjaman:</strong><br>• Periksa status alat (mungkin sedang dipinjam atau maintenance).<br>• Hubungi Admin jika Anda yakin stok tersedia."
    },
    {
        "intent": "pengembalian_terlambat_info",
        "keywords": ["telat mengembalikan", "terlambat kembalikan", "denda telat", "sanksi telat", "terlambat pinjam"],
        "questions": [
            "saya telat mengembalikan, apa sanksinya?", "terlambat kembalikan alat",
            "denda keterlambatan", "sanksi telat pinjam"
        ],
        "response": "⏰ <strong>Keterlambatan:</strong><br>• Akun Anda akan ditangguhkan sementara.<br>• Segera kembalikan alat dan hubungi Admin."
    },
    {
        "intent": "cek_tanggungan",
        "keywords": ["tanggungan", "pinjaman aktif", "alat yang belum kembali", "cek pinjaman saya", "riwayat pinjam"],
        "questions": [
            "bagaimana cek alat yang masih saya pinjam?", "pinjaman aktif saya",
            "riwayat peminjaman", "tanggungan alat"
        ],
        "response": "📋 Lihat di menu <strong>Peminjaman Saya</strong> untuk melihat pinjaman aktif dan riwayat."
    },
    {
        "intent": "info_lab_libur",
        "keywords": ["lab libur", "lab tutup", "hari libur lab", "lab buka kapan", "jadwal libur"],
        "questions": [
            "apakah lab buka hari ini?", "lab libur nasional",
            "jadwal libur lab", "lab tutup sementara"
        ],
        "response": "📅 Lab mengikuti kalender akademik sekolah. Hari libur nasional lab tutup."
    },
    {
        "intent": "notifikasi_email",
        "keywords": ["notifikasi email", "email lab", "info email", "email notif", "email sistem"],
        "questions": [
            "apakah ada notifikasi email?", "email lab untuk info",
            "saya tidak menerima email", "email sistem lab"
        ],
        "response": "📧 Sistem dapat mengirim notifikasi email jika alamat email terdaftar di profil. Pastikan email Anda aktif."
    },
    {
        "intent": "pengumuman_khusus",
        "keywords": ["pengumuman khusus", "info penting", "pengumuman mendadak", "info terbaru lab"],
        "questions": [
            "ada pengumuman penting hari ini?", "info terbaru dari lab",
            "pengumuman mendadak", "pengumuman lab terbaru"
        ],
        "response": "📢 Cek menu <strong>Pengumuman Lab</strong> untuk informasi terbaru."
    },
    {
        "intent": "update_data_diri",
        "keywords": ["update data", "ubah data diri", "ganti nomor hp", "ubah alamat", "data profil salah"],
        "questions": [
            "cara update data diri", "ganti nomor hp di sistem", "ubah alamat email",
            "data profil saya salah"
        ],
        "response": "👤 Buka menu <strong>Profil Saya</strong> untuk mengubah data diri."
    },
    {
        "intent": "kehilangan_kartu",
        "keywords": ["kartu hilang", "kartu pelajar hilang", "kartu akses lab", "kartu identitas"],
        "questions": [
            "kartu akses lab hilang", "kartu pelajar hilang",
            "bagaimana mendapat kartu baru?", "kartu identitas lab"
        ],
        "response": "🪪 Segera lapor ke Admin untuk menonaktifkan kartu lama dan membuat kartu baru."
    },
    {
        "intent": "penggunaan_wifi_tamu",
        "keywords": ["wifi tamu", "guest wifi", "wifi untuk tamu", "akses wifi tamu"],
        "questions": [
            "apakah ada wifi khusus tamu?", "guest wifi lab",
            "tamu bisa pakai wifi lab?", "wifi untuk pengunjung"
        ],
        "response": "📶 WiFi tamu tersedia dengan bandwidth terbatas. Tanyakan password ke Admin."
    },
    {
        "intent": "tentang_ukm",
        "keywords": ["ukm", "unit kegiatan", "ekstrakurikuler", "kegiatan siswa", "club"],
        "questions": [
            "apa saja ukm di sekolah?", "kegiatan ekstrakurikuler",
            "club tkj", "organisasi siswa"
        ],
        "response": "👥 Terdapat berbagai UKM termasuk robotika, programming, dan multimedia. Info di mading sekolah."
    },
    {
        "intent": "bantuan_teknis",
        "keywords": ["bantuan teknis", "teknisi lab", "minta bantuan teknisi", "panggil teknisi"],
        "questions": [
            "saya butuh bantuan teknisi", "teknisi lab bisa dipanggil?",
            "masalah teknis di lab", "panggil teknisi sekarang"
        ],
        "response": "🛠️ Hubungi Admin via WhatsApp untuk memanggil teknisi lab."
    },
]
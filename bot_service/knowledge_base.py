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
    }
]
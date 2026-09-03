<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Maintenance;
use App\Models\Berita;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed Kelas
        $daftarKelas = [
            'X TKJ 1', 'X TKJ 2',
            'XI TKJ 1', 'XI TKJ 2',
            'XII TKJ 1', 'XII TKJ 2'
        ];

        foreach ($daftarKelas as $namaKelas) {
            \App\Models\Kelas::firstOrCreate(['nama_kelas' => $namaKelas]);
        }

        // 1. Create Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@labtkj.sch.id'],
            [
                'name' => 'Admin Laboratorium TKJ',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nomor_induk' => '198507152010011002',
                'kelas_atau_jabatan' => 'Kepala Lab',
                'telepon' => '081234567890',
            ]
        );

        $guru = User::firstOrCreate(
            ['email' => 'guru@labtkj.sch.id'],
            [
                'name' => 'Bpk. Rian Hidayat, S.Kom',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'nomor_induk' => '199003202015021004',
                'kelas_atau_jabatan' => 'Guru Produktif TKJ',
                'telepon' => '082198765432',
            ]
        );

        $siswa = User::firstOrCreate(
            ['email' => 'siswa@labtkj.sch.id'],
            [
                'name' => 'Ahmad Fajar',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'nomor_induk' => '202410101',
                'kelas_atau_jabatan' => 'XII TKJ 1',
                'telepon' => '085712345678',
            ]
        );

        // 2. Create Sample Barangs
        $barangData = [
            [
                'kode_barang' => 'TKJ-JRG-001',
                'nama_barang' => 'Router Mikrotik RB750Gr3 hEX',
                'kategori' => 'Jaringan',
                'merk' => 'MikroTik',
                'nomor_seri' => 'SN-RB750-984210',
                'jumlah' => 15,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'lokasi' => 'Lab TKJ 1 - Rak Jaringan A1',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2024,
                'tanggal_pembelian' => '2024-02-10',
                'harga' => 850000,
                'sumber_dana' => 'BOS',
                'deskripsi' => 'Router 5-port Gigabit Ethernet untuk praktikum konfigurasi routing dan hotspot.',
            ],
            [
                'kode_barang' => 'TKJ-JRG-002',
                'nama_barang' => 'Switch Cisco Catalyst 2960 24 Port',
                'kategori' => 'Jaringan',
                'merk' => 'Cisco',
                'nomor_seri' => 'CSCO-2960-771829',
                'jumlah' => 6,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'lokasi' => 'Lab TKJ 1 - Rack Server',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2023,
                'tanggal_pembelian' => '2023-08-15',
                'harga' => 3500000,
                'sumber_dana' => 'Sekolah',
                'deskripsi' => 'Manageable switch untuk praktikum VLAN, Trunking, dan Spanning Tree.',
            ],
            [
                'kode_barang' => 'TKJ-ALT-001',
                'nama_barang' => 'Crimping Tool RJ45 & RJ11 Pro\'sKit',
                'kategori' => 'Alat Praktik',
                'merk' => 'Pro\'sKit',
                'nomor_seri' => 'PK-CRIMP-091',
                'jumlah' => 20,
                'satuan' => 'Buah',
                'kondisi' => 'Baik',
                'lokasi' => 'Toolbox Alat Praktik Lemari 2',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2024,
                'tanggal_pembelian' => '2024-01-20',
                'harga' => 175000,
                'sumber_dana' => 'BOS',
                'deskripsi' => 'Tang crimping presisi untuk pembuatan kabel UTP straight dan cross.',
            ],
            [
                'kode_barang' => 'TKJ-ALT-002',
                'nama_barang' => 'Digital Network Cable Tester & Tracker',
                'kategori' => 'Alat Praktik',
                'merk' => 'Noyafa',
                'nomor_seri' => 'NF-806B-5541',
                'jumlah' => 8,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'lokasi' => 'Toolbox Alat Praktik Lemari 2',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2024,
                'tanggal_pembelian' => '2024-03-05',
                'harga' => 320000,
                'sumber_dana' => 'BOS',
                'deskripsi' => 'Tester kabel LAN digital dengan fungsi tone generator pelacak jalur kabel.',
            ],
            [
                'kode_barang' => 'TKJ-KMP-001',
                'nama_barang' => 'PC Client Lab Core i5 RAM 16GB SSD 512GB',
                'kategori' => 'Komputer',
                'merk' => 'Lenovo ThinkCentre',
                'nomor_seri' => 'LEN-TC-4401928',
                'jumlah' => 36,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'lokasi' => 'Lab TKJ 1 - Meja Siswa 1-36',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2023,
                'tanggal_pembelian' => '2023-11-12',
                'harga' => 7500000,
                'sumber_dana' => 'Sekolah',
                'deskripsi' => 'PC workstation siswa untuk simulasi Cisco Packet Tracer, Winbox, dan VirtualBox.',
            ],
            [
                'kode_barang' => 'TKJ-JRG-003',
                'nama_barang' => 'Access Point UniFi AC Long Range',
                'kategori' => 'Jaringan',
                'merk' => 'Ubiquiti',
                'nomor_seri' => 'UBNT-UAP-LR-9921',
                'jumlah' => 5,
                'satuan' => 'Unit',
                'kondisi' => 'Perawatan',
                'lokasi' => 'Plafon Lab TKJ 1 & 2',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2023,
                'tanggal_pembelian' => '2023-05-18',
                'harga' => 1950000,
                'sumber_dana' => 'Sekolah',
                'deskripsi' => 'Access point enterprise untuk jaringan nirkabel laboratorium dan praktikum hotspot.',
            ],
            [
                'kode_barang' => 'TKJ-HRD-001',
                'nama_barang' => 'Kabel UTP Cat6 305M Roll',
                'kategori' => 'Perangkat Keras',
                'merk' => 'Belden',
                'nomor_seri' => 'BLD-CAT6-USA-102',
                'jumlah' => 3,
                'satuan' => 'Box',
                'kondisi' => 'Baik',
                'lokasi' => 'Gudang Lab TKJ Rak C',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2024,
                'tanggal_pembelian' => '2024-04-01',
                'harga' => 1850000,
                'sumber_dana' => 'BOS',
                'deskripsi' => 'Roll kabel UTP Cat6 original untuk bahan praktik pengkabelan.',
            ],
            [
                'kode_barang' => 'TKJ-HRD-002',
                'nama_barang' => 'Fusion Splicer Fiber Optik Core Alignment',
                'kategori' => 'Alat Praktik',
                'merk' => 'Sumitomo',
                'nomor_seri' => 'SUM-Z2C-991823',
                'jumlah' => 2,
                'satuan' => 'Unit',
                'kondisi' => 'Perbaikan',
                'lokasi' => 'Lemari Khusus Fiber Optik',
                'penanggung_jawab' => 'Admin Laboratorium TKJ',
                'tahun_pembelian' => 2022,
                'tanggal_pembelian' => '2022-09-10',
                'harga' => 28000000,
                'sumber_dana' => 'Hibah',
                'deskripsi' => 'Mesin sambung serat optik presisi tinggi untuk materi praktikum Fiber Optic.',
            ],
        ];

        foreach ($barangData as $item) {
            Barang::updateOrCreate(['kode_barang' => $item['kode_barang']], $item);
        }

        // 3. Create Sample Peminjaman
        $mikrotik = Barang::where('kode_barang', 'TKJ-JRG-001')->first();
        $crimpTool = Barang::where('kode_barang', 'TKJ-ALT-001')->first();

        if ($mikrotik) {
            Peminjaman::create([
                'barang_id' => $mikrotik->id,
                'user_id' => $siswa->id,
                'kode_peminjaman' => 'PINJAM-20260819-001',
                'nama_peminjam' => $siswa->name,
                'kelas_atau_jabatan' => $siswa->kelas_atau_jabatan,
                'kontak' => $siswa->telepon,
                'keperluan' => 'Praktikum konfigurasi VPN dan Firewall untuk Uji Kompetensi Keahlian (UKK).',
                'jumlah_pinjam' => 1,
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(2)->toDateString(),
                'status' => 'Dipinjam',
                'approved_by' => $admin->id,
            ]);
        }

        if ($crimpTool) {
            Peminjaman::create([
                'barang_id' => $crimpTool->id,
                'user_id' => $guru->id,
                'kode_peminjaman' => 'PINJAM-20260818-002',
                'nama_peminjam' => $guru->name,
                'kelas_atau_jabatan' => $guru->kelas_atau_jabatan,
                'kontak' => $guru->telepon,
                'keperluan' => 'Demonstrasi pemasangan konektor RJ45 Cat6 di kelas X TKJ.',
                'jumlah_pinjam' => 4,
                'tanggal_pinjam' => now()->subDays(3)->toDateString(),
                'tanggal_kembali_rencana' => now()->subDays(1)->toDateString(),
                'tanggal_kembali_aktual' => now()->subDays(1)->toDateString(),
                'status' => 'Dikembalikan',
                'kondisi_kembali' => 'Baik',
                'catatan' => 'Alat dikembalikan lengkap dan dalam kondisi prima.',
                'approved_by' => $admin->id,
            ]);
        }

        // 4. Create Sample Maintenance
        $splicer = Barang::where('kode_barang', 'TKJ-HRD-002')->first();
        if ($splicer) {
            Maintenance::create([
                'barang_id' => $splicer->id,
                'user_id' => $admin->id,
                'teknisi' => 'PT. Optik Solusindo Servis',
                'tanggal_maintenance' => now()->subDays(5)->toDateString(),
                'jenis' => 'Korektif',
                'deskripsi_kerusakan' => 'Elektroda arc discharge tumpul sehingga loss sambungan melebihi 0.05 dB.',
                'tindakan' => 'Penggantian pasang elektroda baru dan kalibrasi sistem optic kamera pembesar.',
                'biaya' => 850000,
                'status' => 'Proses',
                'catatan' => 'Sedang menunggu pengujian sambungan final oleh teknisi rekanan.',
            ]);
        }

        // 5. Create Sample Berita / Pengumuman
        Berita::create([
            'user_id' => $admin->id,
            'judul' => 'Jadwal Kalibrasi Perangkat Lab & Uji Kompetensi Keahlian (UKK) 2026',
            'isi' => "Diberitahukan kepada seluruh siswa kelas XII TKJ bahwa peminjaman perangkat router dan switch untuk persiapan UKK wajib menyertakan surat rekomendasi guru pembimbing.\n\nHarap menjaga kebersihan dan mengembalikan seluruh peralatan praktikum tepat waktu ke rak masing-masing setelah jam praktik selesai.",
        ]);

        Berita::create([
            'user_id' => $admin->id,
            'judul' => 'Penambahan 20 Unit Crimping Tool & 15 Router Mikrotik Baru',
            'isi' => "Alhamdulillah laboratorium telah menerima tambahan 20 unit Tang Crimping Pro'sKit dan 15 Router Mikrotik RB750Gr3 dari anggaran BOS 2026.\n\nSiswa dan Guru dapat mulai mengajukan peminjaman alat secara online melalui menu 'Katalog Alat & Pinjam' di sistem ini.",
        ]);
    }
}

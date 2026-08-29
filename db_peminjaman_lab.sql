-- Database Export: db_peminjaman_lab
-- Exported on: 2026-08-19 08:27:12
-- Siap di-import ke phpMyAdmin InfinityFree

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `barangs`;
CREATE TABLE `barangs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_seri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unit',
  `kondisi` enum('Baik','Perawatan','Perbaikan','Rusak Berat','Hilang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baik',
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penanggung_jawab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `laboratorium` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_pembelian` year DEFAULT NULL,
  `tanggal_pembelian` date DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `sumber_dana` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barangs_kode_barang_unique` (`kode_barang`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('1', 'TKJ-JRG-001', 'Router Mikrotik RB750Gr3 hEX', 'Jaringan', 'MikroTik', 'SN-RB750-984210', '15', 'Unit', 'Baik', 'Lab TKJ 1 - Rak Jaringan A1', 'Admin Laboratorium TKJ', '2024', '2024-02-10', '850000.00', 'BOS', 'Router 5-port Gigabit Ethernet untuk praktikum konfigurasi routing dan hotspot.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('2', 'TKJ-JRG-002', 'Switch Cisco Catalyst 2960 24 Port', 'Jaringan', 'Cisco', 'CSCO-2960-771829', '6', 'Unit', 'Baik', 'Lab TKJ 1 - Rack Server', 'Admin Laboratorium TKJ', '2023', '2023-08-15', '3500000.00', 'Sekolah', 'Manageable switch untuk praktikum VLAN, Trunking, dan Spanning Tree.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('3', 'TKJ-ALT-001', 'Crimping Tool RJ45 & RJ11 Pro\'sKit', 'Alat Praktik', 'Pro\'sKit', 'PK-CRIMP-091', '20', 'Buah', 'Baik', 'Toolbox Alat Praktik Lemari 2', 'Admin Laboratorium TKJ', '2024', '2024-01-20', '175000.00', 'BOS', 'Tang crimping presisi untuk pembuatan kabel UTP straight dan cross.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('4', 'TKJ-ALT-002', 'Digital Network Cable Tester & Tracker', 'Alat Praktik', 'Noyafa', 'NF-806B-5541', '8', 'Unit', 'Baik', 'Toolbox Alat Praktik Lemari 2', 'Admin Laboratorium TKJ', '2024', '2024-03-05', '320000.00', 'BOS', 'Tester kabel LAN digital dengan fungsi tone generator pelacak jalur kabel.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('5', 'TKJ-KMP-001', 'PC Client Lab Core i5 RAM 16GB SSD 512GB', 'Komputer', 'Lenovo ThinkCentre', 'LEN-TC-4401928', '36', 'Unit', 'Baik', 'Lab TKJ 1 - Meja Siswa 1-36', 'Admin Laboratorium TKJ', '2023', '2023-11-12', '7500000.00', 'Sekolah', 'PC workstation siswa untuk simulasi Cisco Packet Tracer, Winbox, dan VirtualBox.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('6', 'TKJ-JRG-003', 'Access Point UniFi AC Long Range', 'Jaringan', 'Ubiquiti', 'UBNT-UAP-LR-9921', '5', 'Unit', 'Perawatan', 'Plafon Lab TKJ 1 & 2', 'Admin Laboratorium TKJ', '2023', '2023-05-18', '1950000.00', 'Sekolah', 'Access point enterprise untuk jaringan nirkabel laboratorium dan praktikum hotspot.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('7', 'TKJ-HRD-001', 'Kabel UTP Cat6 305M Roll', 'Perangkat Keras', 'Belden', 'BLD-CAT6-USA-102', '3', 'Box', 'Baik', 'Gudang Lab TKJ Rak C', 'Admin Laboratorium TKJ', '2024', '2024-04-01', '1850000.00', 'BOS', 'Roll kabel UTP Cat6 original untuk bahan praktik pengkabelan.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `barangs` (`id`, `kode_barang`, `nama_barang`, `kategori`, `merk`, `nomor_seri`, `jumlah`, `satuan`, `kondisi`, `lokasi`, `penanggung_jawab`, `tahun_pembelian`, `tanggal_pembelian`, `harga`, `sumber_dana`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES ('8', 'TKJ-HRD-002', 'Fusion Splicer Fiber Optik Core Alignment', 'Alat Praktik', 'Sumitomo', 'SUM-Z2C-991823', '2', 'Unit', 'Perbaikan', 'Lemari Khusus Fiber Optik', 'Admin Laboratorium TKJ', '2022', '2022-09-10', '28000000.00', 'Hibah', 'Mesin sambung serat optik presisi tinggi untuk materi praktikum Fiber Optic.', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');

DROP TABLE IF EXISTS `beritas`;
CREATE TABLE `beritas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `beritas_user_id_foreign` (`user_id`),
  CONSTRAINT `beritas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `beritas` (`id`, `judul`, `isi`, `user_id`, `created_at`, `updated_at`) VALUES ('1', 'Jadwal Kalibrasi Perangkat Lab & Uji Kompetensi Keahlian (UKK) 2026', 'Diberitahukan kepada seluruh siswa kelas XII TKJ bahwa peminjaman perangkat router dan switch untuk persiapan UKK wajib menyertakan surat rekomendasi guru pembimbing.\n\nHarap menjaga kebersihan dan mengembalikan seluruh peralatan praktikum tepat waktu ke rak masing-masing setelah jam praktik selesai.', '1', '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `beritas` (`id`, `judul`, `isi`, `user_id`, `created_at`, `updated_at`) VALUES ('2', 'Penambahan 20 Unit Crimping Tool & 15 Router Mikrotik Baru', 'Alhamdulillah laboratorium telah menerima tambahan 20 unit Tang Crimping Pro\'sKit dan 15 Router Mikrotik RB750Gr3 dari anggaran BOS 2026.\n\nSiswa dan Guru dapat mulai mengajukan peminjaman alat secara online melalui menu \'Katalog Alat & Pinjam\' di sistem ini.', '1', '2026-08-19 08:20:46', '2026-08-19 08:20:46');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kelas_nama_kelas_unique` (`nama_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kelas` (`id`, `nama_kelas`, `created_at`, `updated_at`) VALUES ('1', 'X TKJ 1', '2026-08-19 08:20:45', '2026-08-19 08:20:45');
INSERT INTO `kelas` (`id`, `nama_kelas`, `created_at`, `updated_at`) VALUES ('2', 'X TKJ 2', '2026-08-19 08:20:45', '2026-08-19 08:20:45');
INSERT INTO `kelas` (`id`, `nama_kelas`, `created_at`, `updated_at`) VALUES ('3', 'XI TKJ 1', '2026-08-19 08:20:45', '2026-08-19 08:20:45');
INSERT INTO `kelas` (`id`, `nama_kelas`, `created_at`, `updated_at`) VALUES ('4', 'XI TKJ 2', '2026-08-19 08:20:45', '2026-08-19 08:20:45');
INSERT INTO `kelas` (`id`, `nama_kelas`, `created_at`, `updated_at`) VALUES ('5', 'XII TKJ 1', '2026-08-19 08:20:45', '2026-08-19 08:20:45');
INSERT INTO `kelas` (`id`, `nama_kelas`, `created_at`, `updated_at`) VALUES ('6', 'XII TKJ 2', '2026-08-19 08:20:45', '2026-08-19 08:20:45');

DROP TABLE IF EXISTS `maintenances`;
CREATE TABLE `maintenances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `barang_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `teknisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_maintenance` date NOT NULL,
  `jenis` enum('Preventif','Korektif','Penggantian') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Korektif',
  `deskripsi_kerusakan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tindakan` text COLLATE utf8mb4_unicode_ci,
  `biaya` decimal(15,2) DEFAULT NULL,
  `status` enum('Selesai','Proses','Pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintenances_barang_id_foreign` (`barang_id`),
  KEY `maintenances_user_id_foreign` (`user_id`),
  CONSTRAINT `maintenances_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maintenances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `maintenances` (`id`, `barang_id`, `user_id`, `teknisi`, `tanggal_maintenance`, `jenis`, `deskripsi_kerusakan`, `tindakan`, `biaya`, `status`, `catatan`, `created_at`, `updated_at`) VALUES ('1', '8', '1', 'PT. Optik Solusindo Servis', '2026-08-14', 'Korektif', 'Elektroda arc discharge tumpul sehingga loss sambungan melebihi 0.05 dB.', 'Penggantian pasang elektroda baru dan kalibrasi sistem optic kamera pembesar.', '850000.00', 'Proses', 'Sedang menunggu pengujian sambungan final oleh teknisi rekanan.', '2026-08-19 08:20:46', '2026-08-19 08:20:46');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '2014_10_12_100000_create_password_reset_tokens_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '2019_08_19_000000_create_failed_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2019_12_14_000001_create_personal_access_tokens_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2026_08_19_031653_create_barangs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2026_08_19_040739_add_role_to_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2026_08_19_045125_create_beritas_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2026_08_19_062944_create_peminjamans_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2026_08_19_062952_create_maintenances_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2026_08_19_082000_create_kelas_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('11', '2026_08_20_031200_add_kondisi_per_unit_to_barangs_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('12', '2026_08_20_033013_add_read_beritas_to_users_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('13', '2026_08_20_042858_add_unit_index_to_maintenances_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('14', '2026_08_20_063300_add_unit_index_to_peminjamans_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('15', '2026_08_20_064759_change_unit_index_to_string_in_peminjamans_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('16', '2026_08_21_083500_add_laboratorium_and_make_barang_id_nullable_in_maintenances_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('17', '2026_08_22_045833_add_foto_to_users_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('18', '2026_08_23_062121_add_laboratorium_to_barangs_table', '3');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `peminjamans`;
CREATE TABLE `peminjamans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `barang_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `kode_peminjaman` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_peminjam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_atau_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keperluan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_pinjam` int NOT NULL DEFAULT '1',
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `status` enum('Menunggu Persetujuan','Dipinjam','Dikembalikan','Terlambat','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dipinjam',
  `kondisi_kembali` enum('Baik','Perawatan','Perbaikan','Rusak Berat','Hilang') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `alasan_penolakan` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `peminjamans_kode_peminjaman_unique` (`kode_peminjaman`),
  KEY `peminjamans_barang_id_foreign` (`barang_id`),
  KEY `peminjamans_user_id_foreign` (`user_id`),
  KEY `peminjamans_approved_by_foreign` (`approved_by`),
  CONSTRAINT `peminjamans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peminjamans_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peminjamans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `peminjamans` (`id`, `barang_id`, `user_id`, `kode_peminjaman`, `nama_peminjam`, `kelas_atau_jabatan`, `kontak`, `keperluan`, `jumlah_pinjam`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `tanggal_kembali_aktual`, `status`, `kondisi_kembali`, `catatan`, `alasan_penolakan`, `approved_by`, `created_at`, `updated_at`) VALUES ('1', '1', '3', 'PINJAM-20260819-001', 'Ahmad Fajar', 'XII TKJ 1', '085712345678', 'Praktikum konfigurasi VPN dan Firewall untuk Uji Kompetensi Keahlian (UKK).', '1', '2026-08-19', '2026-08-21', NULL, 'Dipinjam', NULL, NULL, NULL, '1', '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `peminjamans` (`id`, `barang_id`, `user_id`, `kode_peminjaman`, `nama_peminjam`, `kelas_atau_jabatan`, `kontak`, `keperluan`, `jumlah_pinjam`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `tanggal_kembali_aktual`, `status`, `kondisi_kembali`, `catatan`, `alasan_penolakan`, `approved_by`, `created_at`, `updated_at`) VALUES ('2', '3', '2', 'PINJAM-20260818-002', 'Bpk. Rian Hidayat, S.Kom', 'Guru Produktif TKJ', '082198765432', 'Demonstrasi pemasangan konektor RJ45 Cat6 di kelas X TKJ.', '4', '2026-08-16', '2026-08-18', '2026-08-18', 'Dikembalikan', 'Baik', 'Alat dikembalikan lengkap dan dalam kondisi prima.', NULL, '1', '2026-08-19 08:20:46', '2026-08-19 08:20:46');

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'siswa',
  `nomor_induk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas_atau_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `nomor_induk`, `kelas_atau_jabatan`, `telepon`, `remember_token`, `created_at`, `updated_at`) VALUES ('1', 'Admin Laboratorium TKJ', 'admin@labtkj.sch.id', NULL, '$2y$12$I5Kgl/SAw4uG4bAkq0Hxvuy25yoYr7ZAOY0dhfOPJbSreo/xSwZki', 'admin', '198507152010011002', 'Kepala Lab TKJ', '081234567890', NULL, '2026-08-19 08:20:45', '2026-08-19 08:20:45');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `nomor_induk`, `kelas_atau_jabatan`, `telepon`, `remember_token`, `created_at`, `updated_at`) VALUES ('2', 'Bpk. Rian Hidayat, S.Kom', 'guru@labtkj.sch.id', NULL, '$2y$12$bUbK5uAEBKkhlGCIUZyM9eS5xLD5pMgqW4QigTAPZwV/qO.rpRPsq', 'guru', '199003202015021004', 'Guru Produktif TKJ', '082198765432', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `nomor_induk`, `kelas_atau_jabatan`, `telepon`, `remember_token`, `created_at`, `updated_at`) VALUES ('3', 'Ahmad Fajar', 'siswa@labtkj.sch.id', NULL, '$2y$12$1t1VThtbynmT5qqBlFZfL.Fy5HDOWCOaaSkV2SxTm4CD1PGcWCWEq', 'siswa', '202410101', 'XII TKJ 1', '085712345678', NULL, '2026-08-19 08:20:46', '2026-08-19 08:20:46');

SET FOREIGN_KEY_CHECKS=1;

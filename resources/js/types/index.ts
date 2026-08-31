export type UserRole = 'admin' | 'guru' | 'siswa' | 'kepala_lab' | 'koordinator_lab';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  avatar_url?: string;
  created_at?: string;
}

export type KondisiBarang = 'baik' | 'rusak_ringan' | 'rusak_berat';
export type StatusBarang = 'tersedia' | 'dipinjam' | 'maintenance';

export interface Barang {
  id: number;
  kode_barang: string;
  nama_barang: string;
  kategori: string;
  kondisi: KondisiBarang;
  status: StatusBarang;
  stok: number;
  lokasi?: string;
  foto?: string;
  deskripsi?: string;
}

export type StatusPeminjaman = 'menunggu' | 'disetujui' | 'ditolak' | 'dipinjam' | 'pengajuan_kembali' | 'selesai';

export interface Peminjaman {
  id: number;
  user_id: number;
  barang_id: number;
  tanggal_pinjam: string;
  tanggal_kembali?: string;
  status: StatusPeminjaman;
  keperluan?: string;
  user?: User;
  barang?: Barang;
}

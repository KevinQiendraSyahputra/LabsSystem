<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'merk',
        'nomor_seri',
        'jumlah',
        'satuan',
        'kondisi',
        'kondisi_per_unit',
        'lokasi',
        'laboratorium',
        'penanggung_jawab',
        'tahun_pembelian',
        'tanggal_pembelian',
        'harga',
        'sumber_dana',
        'deskripsi',
        'foto',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'harga'             => 'decimal:2',
    ];

    public static $laboratoriumList = [
        'Laboratorium TKJ',
        'Laboratorium AKL',
        'Laboratorium Pemasaran',
    ];

    public static $kategoriList = [
        'Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik', 'Furniture', 'Lainnya'
    ];

    public static $kondisiList = [
        'Baik'        => ['label' => 'Baik',        'color' => 'emerald', 'icon' => '🟢', 'desc' => 'Siap digunakan'],
        'Perawatan'   => ['label' => 'Perawatan',   'color' => 'yellow',  'icon' => '🟡', 'desc' => 'Perlu pengecekan'],
        'Perbaikan'   => ['label' => 'Perbaikan',   'color' => 'orange',  'icon' => '🟠', 'desc' => 'Sedang diperbaiki'],
        'Rusak Berat' => ['label' => 'Rusak Berat', 'color' => 'red',     'icon' => '🔴', 'desc' => 'Tidak layak pakai'],
        'Hilang'      => ['label' => 'Hilang',      'color' => 'slate',   'icon' => '⚫', 'desc' => 'Tidak ditemukan'],
    ];

    public static $satuanList = ['Unit', 'Meter', 'Box', 'Set', 'Buah', 'Lembar', 'Paket'];

    public static $sumberDanaList = ['BOS', 'Sekolah', 'Hibah', 'Donasi', 'APBN', 'Lainnya'];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function getPeminjamanAktifAttribute()
    {
        return (int) $this->peminjamans()
            ->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan', 'Terlambat'])
            ->sum('jumlah_pinjam');
    }

    /**
     * Menghitung stok unit yang benar-benar siap dipinjam
     * (Total Unit Baik - Unit yang sedang dipinjam)
     */
    public function getStokTersediaAttribute()
    {
        $totalUnit = (int) $this->jumlah;

        // Jika kondisi global barang sudah rusak/hilang/perbaikan total
        if ($this->kondisi !== 'Baik' && empty($this->kondisi_per_unit)) {
            return 0;
        }

        // Hitung berapa unit yang rusak/maintenance dari JSON kondisi_per_unit
        $unitBermasalah = 0;
        if (!empty($this->kondisi_per_unit)) {
            $kondisiUnits = is_array($this->kondisi_per_unit)
                ? $this->kondisi_per_unit
                : (json_decode($this->kondisi_per_unit, true) ?: []);

            foreach ($kondisiUnits as $kondisi) {
                if ($kondisi !== 'Baik') {
                    $unitBermasalah++;
                }
            }
        }

        $unitBaik = max(0, $totalUnit - $unitBermasalah);
        $tersedia = $unitBaik - $this->peminjaman_aktif;

        return max(0, $tersedia);
    }

    public function getTotalNilaiAttribute()
    {
        return ($this->harga ?? 0) * $this->jumlah;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'barang_id',
        'user_id',
        'kode_peminjaman',
        'nama_peminjam',
        'kelas_atau_jabatan',
        'kontak',
        'keperluan',
        'jumlah_pinjam',
        'unit_index',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'status',
        'kondisi_kembali',
        'catatan',
        'alasan_penolakan',
        'approved_by',
    ];

    protected $casts = [
        'tanggal_pinjam'           => 'date',
        'tanggal_kembali_rencana'  => 'date',
        'tanggal_kembali_aktual'   => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isTerlambat(): bool
    {
        return $this->status === 'Dipinjam'
            && $this->tanggal_kembali_rencana < now()->toDateString();
    }
}

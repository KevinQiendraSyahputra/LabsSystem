<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'maintenances';

    public static $laboratoriumList = [
        'Laboratorium TKJ',
        'Laboratorium AKL',
        'Laboratorium Pemasaran',
    ];

    protected $fillable = [
        'laboratorium',
        'barang_id',
        'unit_index',
        'user_id',
        'teknisi',
        'tanggal_maintenance',
        'jenis',
        'deskripsi_kerusakan',
        'tindakan',
        'biaya',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_maintenance' => 'date',
        'biaya' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
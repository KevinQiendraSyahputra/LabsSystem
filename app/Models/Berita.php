<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'isi',
        'target_kelas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope query untuk menyaring berita/pengumuman berdasarkan user yang sedang login.
     * Staf/Admin/Guru dapat melihat seluruh pengumuman.
     * Siswa/Koordinator Lab hanya melihat pengumuman publik atau pengumuman yang persis ditujukan untuk kelasnya.
     */
    public function scopeForUser($query, ?User $user)
    {
        if (!$user) {
            return $query;
        }

        $roleName = strtolower(trim($user->role ?? ''));
        $isStaffOrAdmin = in_array($roleName, ['admin', 'guru', 'staf', 'kepala_lab', 'teknisi']);

        // Staf / Admin / Guru dapat melihat semua pengumuman
        if ($isStaffOrAdmin && !$user->isSiswa() && !$user->isKoordinatorLab()) {
            return $query;
        }

        // Siswa & Koordinator Lab disaring berdasarkan kelas
        $userKelas = trim($user->kelas_atau_jabatan ?? '');

        return $query->where(function ($q) use ($userKelas) {
            // 1. Pengumuman Publik / Umum untuk Semua
            $q->whereNull('target_kelas')
              ->orWhere('target_kelas', '')
              ->orWhere('target_kelas', 'LIKE', '%Semua Pengguna%')
              ->orWhere('target_kelas', 'LIKE', '%Semua Kelas%')
              ->orWhereRaw('LOWER(TRIM(target_kelas)) = ?', ['semua']);

            // 2. Pengumuman khusus kelas user (Cocok persis dengan nama kelas)
            if (!empty($userKelas)) {
                $q->orWhereRaw('LOWER(TRIM(target_kelas)) = ?', [strtolower($userKelas)]);
            }
        });
    }
}
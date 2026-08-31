<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'foto',
        'password',
        'role',
        'nomor_induk',
        'kelas_atau_jabatan',
        'laboratorium_penugasan',
        'telepon',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // =============================================
    // DAFTAR ROLE SISTEM
    // =============================================
    public static $roleList = [
        'admin'           => 'Admin',
        'kepala_lab'      => 'Kepala Laboratorium',
        'koordinator_lab' => 'Koordinator Laboratorium',
        'guru'            => 'Guru',
        'siswa'           => 'Siswa',
    ];

    // =============================================
    // RELASI
    // =============================================
    public function beritas()
    {
        return $this->hasMany(Berita::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    // =============================================
    // ROLE HELPER METHODS
    // =============================================

    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    public function isKepalaLab(): bool
    {
        return strtolower($this->role) === 'kepala_lab';
    }

    public function isKoordinatorLab(): bool
    {
        return strtolower($this->role) === 'koordinator_lab';
    }

    public function isGuru(): bool
    {
        return strtolower($this->role) === 'guru';
    }

    public function isSiswa(): bool
    {
        return in_array(strtolower($this->role), ['siswa', 'user']);
    }

    /** Apakah user bisa mengakses fitur maintenance? */
    public function canAccessMaintenance(): bool
    {
        return in_array(strtolower($this->role), ['admin', 'kepala_lab', 'koordinator_lab']);
    }

    /** Apakah user bisa melihat laporan semua lab? */
    public function canSeeAllLabs(): bool
    {
        return in_array(strtolower($this->role), ['admin', 'kepala_lab']);
    }

    /**
     * Dapatkan lab yang dimiliki user berdasarkan role & kelas/penugasan.
     * Mengembalikan null jika user bisa akses semua lab.
     * Mengembalikan string lab jika user terbatas ke 1 lab.
     */
    public function getLaboratoriumScopedAttribute(): ?string
    {
        // Admin & kepala_lab lihat semua lab
        if ($this->isAdmin() || $this->isKepalaLab()) {
            return null;
        }

        // Koordinator lab: berdasarkan laboratorium_penugasan
        if ($this->isKoordinatorLab() && $this->laboratorium_penugasan) {
            return $this->laboratorium_penugasan;
        }

        // Guru & Siswa: deteksi dari kelas_atau_jabatan
        $kelas = strtoupper($this->kelas_atau_jabatan ?? '');

        if (str_contains($kelas, 'TKJ') || str_contains($kelas, 'TEKNIK KOMPUTER')) {
            return 'Laboratorium TKJ';
        }
        if (str_contains($kelas, 'AKL') || str_contains($kelas, 'AKUNTANSI')) {
            return 'Laboratorium AKL';
        }
        if (str_contains($kelas, 'PEMASARAN') || str_contains($kelas, 'BISNIS') || str_contains($kelas, 'PM ')) {
            return 'Laboratorium Pemasaran';
        }

        // Tidak teridentifikasi: tampil semua
        return null;
    }

    /** Label role yang user-friendly */
    public function getRoleLabelAttribute(): string
    {
        return self::$roleList[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Dapatkan URL foto profil yang kompatibel di semua hosting (uploads & storage)
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }
        $clean = ltrim($this->foto, '/');
        if (file_exists(public_path('uploads/' . $clean)) || file_exists(base_path('uploads/' . $clean))) {
            return asset('uploads/' . $clean) . '?v=' . ($this->updated_at ? $this->updated_at->timestamp : time());
        }
        return asset('storage/' . $clean) . '?v=' . ($this->updated_at ? $this->updated_at->timestamp : time());
    }
}


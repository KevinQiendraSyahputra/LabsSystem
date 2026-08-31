<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
{
    /**
     * Daftar pengumuman yang dapat dilihat user.
     */
    public function index()
    {
        $user = Auth::user();

        $beritas = Berita::with('user')
            ->forUser($user)
            ->latest()
            ->paginate(10);

        return view('berita.index', compact('beritas'));
    }

    /**
     * Form membuat pengumuman.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isGuru()) {
            abort(403, 'Hanya guru dan admin yang diizinkan menulis pengumuman baru.');
        }

        return view('berita.create');
    }

    /**
     * Simpan pengumuman baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isGuru()) {
            abort(403, 'Hanya guru dan admin yang diizinkan menerbitkan pengumuman.');
        }

        $validated = $request->validate([
            'judul'        => ['required', 'string', 'max:255'],
            'isi'          => ['required', 'string'],
            'target_kelas' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['user_id'] = $user->id;

        Berita::create($validated);

        return redirect()
            ->route('berita.index')
            ->with('success', 'Pengumuman laboratorium berhasil diterbitkan!');
    }

    /**
     * Detail pengumuman.
     *
     * Saat detail berhasil dibuka, pengumuman langsung ditandai
     * sebagai sudah dibaca untuk user yang sedang login.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $berita = Berita::with('user')->findOrFail($id);

        $roleName = strtolower(trim($user->role ?? ''));

        $isStaffOrAdmin = in_array(
            $roleName,
            ['admin', 'guru', 'staf', 'kepala_lab', 'teknisi'],
            true
        );

        $isSiswa = !$isStaffOrAdmin || in_array(
            $roleName,
            ['siswa', 'user', 'murid', 'koordinator_lab', 'koordinator lab', 'koordinator'],
            true
        );

        /*
         * Proteksi target kelas.
         * Pengumuman yang bukan untuk user tidak boleh dibuka hanya
         * dengan menebak URL detail.
         */
        if ($isSiswa && $berita->target_kelas) {
            $targetKelas = trim($berita->target_kelas);

            $isPublic =
                str_contains($targetKelas, 'Semua Pengguna')
                || str_contains($targetKelas, 'Semua Kelas')
                || strtolower($targetKelas) === 'semua';

            $userKelas = trim($user->kelas_atau_jabatan ?? '');

            if (
                !$isPublic
                && strtolower($targetKelas) !== strtolower($userKelas)
            ) {
                abort(
                    403,
                    'Pengumuman ini hanya ditujukan untuk ' . $berita->target_kelas
                );
            }
        }

        /*
         * Setelah lolos proteksi akses, tandai pengumuman sebagai dibaca.
         * Akibatnya:
         * - badge angka pada lonceng berkurang;
         * - item ini hilang dari dropdown lonceng;
         * - pengumuman tetap ada di halaman /berita.
         */
        $this->markAsRead($user, (int) $berita->id);

        /*
         * Rekomendasi berita lain tetap mengikuti filter user.
         */
        $beritaLain = Berita::where('id', '!=', $berita->id)
            ->forUser($user)
            ->latest()
            ->take(3)
            ->get();

        return view('berita.show', compact('berita', 'beritaLain'));
    }

    /**
     * Form edit.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $berita = Berita::findOrFail($id);

        if (
            !$user->isAdmin()
            && (!$user->isGuru() || (int) $berita->user_id !== (int) $user->id)
        ) {
            abort(403, 'Akses ditolak.');
        }

        return view('berita.edit', compact('berita'));
    }

    /**
     * Update pengumuman.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $berita = Berita::findOrFail($id);

        if (
            !$user->isAdmin()
            && (!$user->isGuru() || (int) $berita->user_id !== (int) $user->id)
        ) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'judul'        => ['required', 'string', 'max:255'],
            'isi'          => ['required', 'string'],
            'target_kelas' => ['nullable', 'string', 'max:100'],
        ]);

        $berita->update($validated);

        return redirect()
            ->route('berita.index')
            ->with('success', 'Pengumuman laboratorium berhasil diperbarui!');
    }

    /**
     * Hapus pengumuman.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $berita = Berita::findOrFail($id);

        if (
            !$user->isAdmin()
            && (!$user->isGuru() || (int) $berita->user_id !== (int) $user->id)
        ) {
            abort(403, 'Akses ditolak.');
        }

        $berita->delete();

        return redirect()
            ->route('berita.index')
            ->with('success', 'Pengumuman laboratorium berhasil dihapus!');
    }

    /**
     * Tandai seluruh pengumuman yang relevan untuk user sebagai dibaca.
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();

        $allIds = Berita::forUser($user)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $readIds = $this->getReadIds($user);

        $user->read_beritas = json_encode(
            array_values(
                array_unique(
                    array_merge($readIds, $allIds)
                )
            )
        );

        $user->save();

        return redirect()
            ->back()
            ->with('success', 'Semua pengumuman telah ditandai sebagai dibaca.');
    }

    /**
     * Ambil ID berita yang sudah dibaca user.
     */
    private function getReadIds($user): array
    {
        if (!$user || empty($user->read_beritas)) {
            return [];
        }

        $decoded = json_decode($user->read_beritas, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(
            array_unique(
                array_map('intval', $decoded)
            )
        );
    }

    /**
     * Tandai satu pengumuman sebagai dibaca.
     */
    private function markAsRead($user, int $beritaId): void
    {
        $readIds = $this->getReadIds($user);

        if (in_array($beritaId, $readIds, true)) {
            return;
        }

        $readIds[] = $beritaId;

        $user->read_beritas = json_encode(
            array_values(
                array_unique($readIds)
            )
        );

        $user->save();
    }
}

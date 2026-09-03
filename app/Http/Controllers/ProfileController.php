<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        try {
            $daftarKelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        } catch (\Throwable $e) {
            $daftarKelas = collect();
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'daftarKelas' => $daftarKelas,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // 1. Handle foto_base64 dari Cropper.js (Prioritas Utama: Sangat Cepat & Kompatibel Semua Browser/HP)
        if ($request->filled('foto_base64')) {
            $this->safeDeleteFoto($user->foto);
            $fotoPath = $this->safeSaveBase64Foto($request->input('foto_base64'), 'avatars');
            $user->foto = $fotoPath;
            $validated['foto'] = $fotoPath;
        }
        // 2. Handle upload foto reguler via file input
        elseif ($request->hasFile('foto')) {
            $this->safeDeleteFoto($user->foto);
            $fotoPath = $this->safeSaveFoto($request->file('foto'), 'avatars');
            $user->foto = $fotoPath;
            $validated['foto'] = $fotoPath;
        }

        // Siswa dan pengguna non-admin tidak dapat mengubah kelas/jabatan sendiri
        if (!$user->isAdmin()) {
            unset($validated['kelas_atau_jabatan']);
        }

        unset($validated['foto_base64']);
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil dan foto akun berhasil diperbarui!');
    }

    /**
     * Delete user profile photo.
     */
    public function deleteFoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->safeDeleteFoto($user->foto);

        $user->foto = null;
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Foto profil berhasil dihapus!');
    }

    /**
     * Helper aman untuk menyimpan foto base64 (dari Cropper.js)
     */
    private function safeSaveBase64Foto(string $base64Data, string $folder = 'avatars'): string
    {
        if (file_exists(app_path('Helpers/ImageHelper.php'))) {
            require_once app_path('Helpers/ImageHelper.php');
        }

        if (class_exists(\App\Helpers\ImageHelper::class) && method_exists(\App\Helpers\ImageHelper::class, 'saveBase64AsWebp')) {
            return \App\Helpers\ImageHelper::saveBase64AsWebp($base64Data, $folder, 85);
        }

        // Fallback simpan base64 mandiri jika helper belum ada
        $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.webp';
        $relativeFolder = trim($folder, '/');
        $relativeFilePath = $relativeFolder . '/' . $filename;

        $directories = [
            public_path('uploads/' . $relativeFolder),
            base_path('uploads/' . $relativeFolder),
            public_path('storage/' . $relativeFolder),
            storage_path('app/public/' . $relativeFolder),
            base_path('storage/' . $relativeFolder),
            base_path('public/storage/' . $relativeFolder),
        ];

        foreach ($directories as $dir) {
            if (!\Illuminate\Support\Facades\File::isDirectory($dir)) {
                @\Illuminate\Support\Facades\File::makeDirectory($dir, 0755, true, true);
            }
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }
        $decoded = base64_decode($base64Data);
        if ($decoded !== false) {
            foreach ($directories as $dir) {
                @file_put_contents($dir . '/' . $filename, $decoded);
            }
        }

        return $relativeFilePath;
    }

    /**
     * Helper aman untuk menyimpan foto file (WebP jika ImageHelper ada, atau fallback storage)
     */
    private function safeSaveFoto($file, string $folder = 'avatars'): string
    {
        if (file_exists(app_path('Helpers/ImageHelper.php'))) {
            require_once app_path('Helpers/ImageHelper.php');
        }

        if (class_exists(\App\Helpers\ImageHelper::class)) {
            return \App\Helpers\ImageHelper::saveAsWebp($file, $folder, 85);
        }

        $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
        $relativeFolder = trim($folder, '/');
        $relativeFilePath = $relativeFolder . '/' . $filename;

        $directories = [
            public_path('uploads/' . $relativeFolder),
            base_path('uploads/' . $relativeFolder),
            public_path('storage/' . $relativeFolder),
            storage_path('app/public/' . $relativeFolder),
            base_path('storage/' . $relativeFolder),
            base_path('public/storage/' . $relativeFolder),
        ];

        foreach ($directories as $dir) {
            if (!\Illuminate\Support\Facades\File::isDirectory($dir)) {
                @\Illuminate\Support\Facades\File::makeDirectory($dir, 0755, true, true);
            }
            @copy($file->getRealPath(), $dir . '/' . $filename);
        }

        return $relativeFilePath;
    }

    /**
     * Helper aman untuk menghapus foto dari storage & public
     */
    private function safeDeleteFoto(?string $path): void
    {
        if (empty($path)) return;

        if (file_exists(app_path('Helpers/ImageHelper.php'))) {
            require_once app_path('Helpers/ImageHelper.php');
        }

        if (class_exists(\App\Helpers\ImageHelper::class)) {
            \App\Helpers\ImageHelper::deleteIfExists($path);
            return;
        }

        $clean = ltrim($path, '/');
        $candidates = [
            public_path('uploads/' . $clean),
            base_path('uploads/' . $clean),
            public_path('storage/' . $clean),
            storage_path('app/public/' . $clean),
            base_path('storage/' . $clean),
            base_path('public/storage/' . $clean),
        ];

        foreach ($candidates as $candidate) {
            if (\Illuminate\Support\Facades\File::exists($candidate)) {
                @\Illuminate\Support\Facades\File::delete($candidate);
            }
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

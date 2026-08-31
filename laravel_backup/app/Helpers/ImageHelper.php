<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Konversi dan simpan uploaded file menjadi format WebP berkualitas tinggi & terkompresi.
     *
     * @param UploadedFile $file
     * @param string $folder (contoh: 'avatars', 'barang')
     * @param int $quality (1-100)
     * @return string Relatif path (contoh: 'avatars/1724500000_abc123.webp')
     */
    public static function saveAsWebp(UploadedFile $file, string $folder = 'avatars', int $quality = 85): string
    {
        $filename = time() . '_' . Str::random(10) . '.webp';
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
            if (!File::isDirectory($dir)) {
                @File::makeDirectory($dir, 0755, true, true);
            }
        }

        $sourcePath = $file->getRealPath();
        $saved = false;

        // Coba konversi via GD jika didukung
        if (function_exists('imagewebp') && function_exists('imagecreatefromstring')) {
            $rawContent = file_get_contents($sourcePath);
            if ($rawContent !== false) {
                $image = @imagecreatefromstring($rawContent);
                if ($image !== false) {
                    // Pertahankan transparansi PNG / WebP
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);

                    // Simpan ke semua direktori kandidat
                    foreach ($directories as $dir) {
                        @imagewebp($image, $dir . '/' . $filename, $quality);
                    }
                    imagedestroy($image);
                    $saved = true;
                }
            }
        }

        // Fallback jika GD gagal / file sudah webp
        if (!$saved) {
            foreach ($directories as $dir) {
                @copy($sourcePath, $dir . '/' . $filename);
            }
        }

        return $relativeFilePath;
    }

    /**
     * Konversi dan simpan data base64 (dari Cropper.js) menjadi format WebP berkualitas tinggi.
     */
    public static function saveBase64AsWebp(string $base64Data, string $folder = 'avatars', int $quality = 85): string
    {
        $filename = time() . '_' . Str::random(10) . '.webp';
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
            if (!File::isDirectory($dir)) {
                @File::makeDirectory($dir, 0755, true, true);
            }
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }
        $decoded = base64_decode($base64Data);

        if ($decoded !== false) {
            $saved = false;
            if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
                $image = @imagecreatefromstring($decoded);
                if ($image !== false) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    foreach ($directories as $dir) {
                        @imagewebp($image, $dir . '/' . $filename, $quality);
                    }
                    imagedestroy($image);
                    $saved = true;
                }
            }
            if (!$saved) {
                foreach ($directories as $dir) {
                    @file_put_contents($dir . '/' . $filename, $decoded);
                }
            }
        }

        return $relativeFilePath;
    }

    /**
     * Hapus file gambar dari semua lokasi kandidat
     */
    public static function deleteIfExists(?string $path): void
    {
        if (empty($path)) return;

        $cleanPath = ltrim($path, '/');
        $candidates = [
            public_path('uploads/' . $cleanPath),
            base_path('uploads/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            storage_path('app/public/' . $cleanPath),
            base_path('storage/' . $cleanPath),
            base_path('public/storage/' . $cleanPath),
        ];

        foreach ($candidates as $file) {
            if (File::exists($file)) {
                @File::delete($file);
            }
        }
    }
}
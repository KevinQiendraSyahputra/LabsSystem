<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BarangController extends Controller
{
    private function generateKode(string $kategori): string
    {
        $prefix = match($kategori) {
            'Jaringan'        => 'TKJ-JRG',
            'Komputer'        => 'TKJ-KMP',
            'Perangkat Keras' => 'TKJ-HRD',
            'Alat Praktik'    => 'TKJ-ALT',
            'Furniture'       => 'TKJ-FRN',
            default           => 'TKJ-LNY',
        };

        $last = Barang::where('kode_barang', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('kode_barang');

        $num = $last ? ((int) substr($last, -3)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('merk', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_seri', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kondisi')) {
            $konFilter = $request->kondisi;
            $query->where(function ($q) use ($konFilter) {
                $q->where('kondisi', $konFilter)
                  ->orWhere('kondisi_per_unit', 'like', '%"' . $konFilter . '"%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('sumber_dana')) {
            $query->where('sumber_dana', $request->sumber_dana);
        }

        if ($request->filled('laboratorium')) {
            $lab = $request->laboratorium;
            if ($lab === 'Laboratorium TKJ') {
                $query->where(function ($q) use ($lab) {
                    $q->where('laboratorium', $lab)
                      ->orWhereNull('laboratorium')
                      ->orWhere('laboratorium', '');
                });
            } else {
                $query->where('laboratorium', $lab);
            }
        }

        $barangs = $query->latest()->paginate(12)->withQueryString();

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        if ($request->has('harga') && is_string($request->harga)) {
            $cleaned = preg_replace('/[^0-9]/', '', $request->harga);
            $request->merge(['harga' => $cleaned !== '' ? $cleaned : null]);
        }

        $validated = $request->validate([
            'nama_barang'       => 'required|string|max:255',
            'kategori'          => 'required|string',
            'laboratorium'      => 'nullable|string|in:Laboratorium TKJ,Laboratorium AKL,Laboratorium Pemasaran',
            'merk'              => 'nullable|string|max:100',
            'nomor_seri'        => 'nullable|string|max:100',
            'jumlah'            => 'required|integer|min:1',
            'satuan'            => 'required|string',
            'kondisi'           => 'required|in:Baik,Perawatan,Perbaikan,Rusak Berat,Hilang',
            'lokasi'            => 'nullable|string|max:100',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'tahun_pembelian'   => 'nullable|digits:4|integer',
            'tanggal_pembelian' => 'nullable|date',
            'harga'             => 'nullable|numeric|min:0',
            'sumber_dana'       => 'nullable|string',
            'deskripsi'         => 'nullable|string',
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->safeSaveFoto($request->file('foto'), 'barang');
        }

        $validated['laboratorium'] = $validated['laboratorium'] ?? 'Laboratorium TKJ';
        $validated['kode_barang'] = $this->generateKode($validated['kategori']);

        // Default JSON kondisi seluruh unit
        $kondisiPerUnit = [];
        for ($i = 1; $i <= (int)$validated['jumlah']; $i++) {
            $kondisiPerUnit[$i] = $validated['kondisi'];
        }
        $validated['kondisi_per_unit'] = json_encode($kondisiPerUnit);

        Barang::create($validated);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan! Kode: ' . $validated['kode_barang']);
    }

    public function show(Barang $barang)
    {
        $barang->load([
            'peminjamans' => fn($q) => $q->latest()->take(5), 
            'maintenances' => fn($q) => $q->latest()->take(5)
        ]);
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        if ($request->has('harga') && is_string($request->harga)) {
            $cleaned = preg_replace('/[^0-9]/', '', $request->harga);
            $request->merge(['harga' => $cleaned !== '' ? $cleaned : null]);
        }

        $validated = $request->validate([
            'nama_barang'       => 'required|string|max:255',
            'kategori'          => 'required|string',
            'laboratorium'      => 'nullable|string|in:Laboratorium TKJ,Laboratorium AKL,Laboratorium Pemasaran',
            'merk'              => 'nullable|string|max:100',
            'nomor_seri'        => 'nullable|string|max:100',
            'jumlah'            => 'required|integer|min:1',
            'satuan'            => 'required|string',
            'kondisi'           => 'required|in:Baik,Perawatan,Perbaikan,Rusak Berat,Hilang',
            'lokasi'            => 'nullable|string|max:100',
            'penanggung_jawab'  => 'nullable|string|max:100',
            'tahun_pembelian'   => 'nullable|digits:4|integer',
            'tanggal_pembelian' => 'nullable|date',
            'harga'             => 'nullable|numeric|min:0',
            'sumber_dana'       => 'nullable|string',
            'deskripsi'         => 'nullable|string',
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $this->safeDeleteFoto($barang->foto);
            $validated['foto'] = $this->safeSaveFoto($request->file('foto'), 'barang');
        }

        $validated['laboratorium'] = $validated['laboratorium'] ?? 'Laboratorium TKJ';

        $oldJumlah = (int)$barang->jumlah;
        $newJumlah = (int)$validated['jumlah'];

        $kondisiPerUnit = [];
        if ($barang->kondisi_per_unit) {
            $kondisiPerUnit = is_array($barang->kondisi_per_unit)
                ? $barang->kondisi_per_unit
                : (json_decode($barang->kondisi_per_unit, true) ?: []);
        }

        if ($newJumlah > $oldJumlah) {
            for ($i = $oldJumlah + 1; $i <= $newJumlah; $i++) {
                $kondisiPerUnit[$i] = $validated['kondisi'];
            }
        } elseif ($newJumlah < $oldJumlah) {
            $kondisiPerUnit = array_slice($kondisiPerUnit, 0, $newJumlah, true);
        }

        for ($i = 1; $i <= $newJumlah; $i++) {
            if (!isset($kondisiPerUnit[$i])) {
                $kondisiPerUnit[$i] = $validated['kondisi'];
            }
        }
        $validated['kondisi_per_unit'] = json_encode($kondisiPerUnit);

        $barang->update($validated);

        return redirect()->route('barang.index')->with('success', 'Data barang ' . $barang->nama_barang . ' berhasil diperbarui!');
    }

    /**
     * Update kondisi via AJAX (Tanpa Refresh)
     */
    public function updateKondisi(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'kondisi'    => 'required|in:Baik,Perawatan,Perbaikan,Rusak Berat,Hilang',
            'unit_index' => 'nullable|integer|min:1',
        ]);

        if ($request->filled('unit_index')) {
            $unitIndex = (int)$request->unit_index;
            $kondisiPerUnit = [];
            if ($barang->kondisi_per_unit) {
                $kondisiPerUnit = is_array($barang->kondisi_per_unit)
                    ? $barang->kondisi_per_unit
                    : (json_decode($barang->kondisi_per_unit, true) ?: []);
            }

            $kondisiPerUnit[$unitIndex] = $validated['kondisi'];

            $counts = array_count_values(array_filter($kondisiPerUnit));
            $globalKondisi = 'Baik';
            if (!empty($counts['Rusak Berat']) && $counts['Rusak Berat'] === count($kondisiPerUnit)) {
                $globalKondisi = 'Rusak Berat';
            } elseif (!empty($counts['Hilang']) && $counts['Hilang'] === count($kondisiPerUnit)) {
                $globalKondisi = 'Hilang';
            } elseif (!empty($counts['Perbaikan'])) {
                $globalKondisi = 'Perbaikan';
            } elseif (!empty($counts['Perawatan'])) {
                $globalKondisi = 'Perawatan';
            }

            $barang->update([
                'kondisi_per_unit' => json_encode($kondisiPerUnit),
                'kondisi'          => $globalKondisi,
            ]);

            return response()->json([
                'status'         => 'success',
                'message'        => 'Kondisi ' . $barang->nama_barang . ' Unit ' . $unitIndex . ' berhasil diubah menjadi ' . $validated['kondisi'] . '!',
                'unit_index'     => $unitIndex,
                'kondisi'        => $validated['kondisi'],
                'global_kondisi' => $globalKondisi,
            ]);
        }

        $barang->update(['kondisi' => $validated['kondisi']]);

        return response()->json([
            'status'         => 'success',
            'message'        => 'Kondisi barang berhasil diubah menjadi ' . $validated['kondisi'] . '!',
            'global_kondisi' => $validated['kondisi'],
        ]);
    }

    public function destroy(Barang $barang)
    {
        $this->safeDeleteFoto($barang->foto);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    /**
     * Hapus banyak data barang sekaligus (Bulk Delete)
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:barangs,id',
        ]);

        $count = 0;
        DB::transaction(function () use ($validated, &$count) {
            $barangs = Barang::whereIn('id', $validated['ids'])->get();
            foreach ($barangs as $barang) {
                $this->safeDeleteFoto($barang->foto);
                $barang->delete();
                $count++;
            }
        });

        return redirect()->route('barang.index')->with('success', "{$count} data barang berhasil dihapus!");
    }

    /**
     * Helper aman simpan foto barang
     */
    private function safeSaveFoto($file, string $folder = 'barang'): string
    {
        if (file_exists(app_path('Helpers/ImageHelper.php'))) {
            require_once app_path('Helpers/ImageHelper.php');
        }

        if (class_exists(\App\Helpers\ImageHelper::class)) {
            return \App\Helpers\ImageHelper::saveAsWebp($file, $folder, 85);
        }

        $path = $file->store($folder, 'public');
        $pubTarget = public_path('storage/' . $folder);
        if (!\Illuminate\Support\Facades\File::isDirectory($pubTarget)) {
            \Illuminate\Support\Facades\File::makeDirectory($pubTarget, 0755, true, true);
        }
        @copy(storage_path('app/public/' . $path), public_path('storage/' . $path));
        return $path;
    }

    /**
     * Helper aman hapus foto barang
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
        @\Illuminate\Support\Facades\File::delete(storage_path('app/public/' . $clean));
        @\Illuminate\Support\Facades\File::delete(public_path('storage/' . $clean));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Maintenance;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $lockedLab = $user->isKoordinatorLab() ? ($user->laboratorium_penugasan ?: 'Laboratorium AKL') : null;

        $query = Maintenance::with('barang', 'user');

        if ($lockedLab) {
            $query->where('laboratorium', $lockedLab);
        } elseif ($request->filled('laboratorium')) {
            $query->where('laboratorium', $request->laboratorium);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->whereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$term}%")->orWhere('kode_barang', 'like', "%{$term}%"))
                  ->orWhere('teknisi', 'like', "%{$term}%")
                  ->orWhere('deskripsi_kerusakan', 'like', "%{$term}%")
                  ->orWhere('laboratorium', 'like', "%{$term}%");
            });
        }

        $maintenances = $query->latest('tanggal_maintenance')->latest('id')->paginate(10)->withQueryString();
        return view('maintenance.index', compact('maintenances', 'lockedLab'));
    }

    public function create()
    {
        $user      = auth()->user();
        $lockedLab = $user->isKoordinatorLab() ? ($user->laboratorium_penugasan ?: 'Laboratorium AKL') : null;

        // Jika koordinator lab, hanya tampilkan barang di lab penugasannya
        $barangsQuery = Barang::orderBy('nama_barang');
        if ($lockedLab) {
            $barangsQuery->where('laboratorium', $lockedLab);
        }
        $barangs = $barangsQuery->get();

        return view('maintenance.create', compact('barangs', 'lockedLab'));
    }

    public function store(Request $request)
    {
        $user      = auth()->user();
        $lockedLab = $user->isKoordinatorLab() ? ($user->laboratorium_penugasan ?: 'Laboratorium AKL') : null;

        // Koordinator lab: paksa override lab dengan lab penugasan mereka
        if ($lockedLab) {
            $request->merge(['laboratorium' => $lockedLab]);
        }

        // Normalisasi biaya jika diinput '-' atau teks kosong
        if ($request->filled('biaya')) {
            $cleanedBiaya = trim((string)$request->biaya);
            if ($cleanedBiaya === '-' || !is_numeric($cleanedBiaya)) {
                $request->merge(['biaya' => null]);
            }
        }

        $validated = $request->validate([
            'laboratorium'        => 'required|string|in:Laboratorium TKJ,Laboratorium AKL,Laboratorium Pemasaran',
            'barang_id'           => 'nullable|exists:barangs,id',
            'unit_index'          => 'nullable',
            'teknisi'             => 'required|string|max:100',
            'tanggal_maintenance' => 'required|date',
            'jenis'               => 'required|in:Preventif,Korektif,Penggantian',
            'deskripsi_kerusakan' => 'required|string',
            'tindakan'            => 'nullable|string',
            'biaya'               => 'nullable|numeric|min:0',
            'status'              => 'required|in:Selesai,Proses,Pending',
            'catatan'             => 'nullable|string',
        ]);

        if (empty($validated['barang_id'])) {
            $validated['barang_id'] = null;
            $validated['unit_index'] = null;
        } elseif (is_array($request->unit_index)) {
            $validated['unit_index'] = implode(',', array_filter($request->unit_index));
        } else {
            $validated['unit_index'] = $request->unit_index ?: null;
        }

        $validated['user_id'] = Auth::id();

        $maintenance = Maintenance::create($validated);

        if (!empty($validated['barang_id'])) {
            $this->syncBarangCondition($validated['barang_id'], $validated['unit_index'] ?? null, $validated['status']);
        }

        try {
            TelegramService::sendMaintenanceBaru($maintenance);
        } catch (\Throwable $e) {
            Log::error("Gagal kirim notifikasi maintenance ke Telegram: " . $e->getMessage());
        }

        return redirect()->route('maintenance.index')->with('success', 'Data maintenance berhasil dicatat!');
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load('barang', 'user');
        return view('maintenance.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $user = auth()->user();

        // Otorisasi: Koordinator hanya bisa mengedit maintenance di laboratorium miliknya
        if ($user->isKoordinatorLab() && $user->laboratorium_penugasan && $maintenance->laboratorium !== $user->laboratorium_penugasan) {
            abort(403, 'Akses Ditolak: Anda hanya memiliki izin mengedit data maintenance untuk ' . $user->laboratorium_penugasan . '.');
        }

        $lockedLab = $user->isKoordinatorLab() ? $user->laboratorium_penugasan : null;

        $barangsQuery = Barang::orderBy('nama_barang');
        if ($lockedLab) {
            $barangsQuery->where('laboratorium', $lockedLab);
        }
        $barangs = $barangsQuery->get();

        return view('maintenance.edit', compact('maintenance', 'barangs', 'lockedLab'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $user = auth()->user();

        // Otorisasi: Koordinator hanya bisa mengupdate maintenance di laboratorium miliknya
        if ($user->isKoordinatorLab() && $user->laboratorium_penugasan && $maintenance->laboratorium !== $user->laboratorium_penugasan) {
            abort(403, 'Akses Ditolak: Anda hanya memiliki izin mengedit data maintenance untuk ' . $user->laboratorium_penugasan . '.');
        }

        // Koordinator lab: paksa laboratorium tetap pada lab penugasannya
        if ($user->isKoordinatorLab() && $user->laboratorium_penugasan) {
            $request->merge(['laboratorium' => $user->laboratorium_penugasan]);
        }

        // Normalisasi biaya jika diinput '-' atau teks kosong
        if ($request->filled('biaya')) {
            $cleanedBiaya = trim((string)$request->biaya);
            if ($cleanedBiaya === '-' || !is_numeric($cleanedBiaya)) {
                $request->merge(['biaya' => null]);
            }
        }

        $validated = $request->validate([
            'laboratorium'        => 'required|string|in:Laboratorium TKJ,Laboratorium AKL,Laboratorium Pemasaran',
            'barang_id'           => 'nullable|exists:barangs,id',
            'unit_index'          => 'nullable',
            'teknisi'             => 'required|string|max:100',
            'tanggal_maintenance' => 'required|date',
            'jenis'               => 'required|in:Preventif,Korektif,Penggantian',
            'deskripsi_kerusakan' => 'required|string',
            'tindakan'            => 'nullable|string',
            'biaya'               => 'nullable|numeric|min:0',
            'status'              => 'required|in:Selesai,Proses,Pending',
            'catatan'             => 'nullable|string',
        ]);

        if (empty($validated['barang_id'])) {
            $validated['barang_id'] = null;
            $validated['unit_index'] = null;
        } elseif (is_array($request->unit_index)) {
            $validated['unit_index'] = implode(',', array_filter($request->unit_index));
        } else {
            $validated['unit_index'] = $request->unit_index ?: null;
        }

        $maintenance->update($validated);

        if (!empty($validated['barang_id'])) {
            $this->syncBarangCondition($validated['barang_id'], $validated['unit_index'] ?? null, $validated['status']);
        }

        try {
            TelegramService::sendMaintenanceUpdate($maintenance);
        } catch (\Throwable $e) {
            Log::error("Gagal kirim notifikasi update maintenance ke Telegram: " . $e->getMessage());
        }

        return redirect()->route('maintenance.show', $maintenance)->with('success', 'Data maintenance berhasil diperbarui!');
    }

    public function destroy(Maintenance $maintenance)
    {
        $user = auth()->user();

        // Otorisasi: Koordinator hanya bisa menghapus maintenance di laboratorium miliknya
        if ($user->isKoordinatorLab() && $user->laboratorium_penugasan && $maintenance->laboratorium !== $user->laboratorium_penugasan) {
            abort(403, 'Akses Ditolak: Anda hanya memiliki izin menghapus data maintenance untuk ' . $user->laboratorium_penugasan . '.');
        }

        $maintenance->delete();
        return redirect()->route('maintenance.index')->with('success', 'Data maintenance berhasil dihapus!');
    }

    private function syncBarangCondition($barangId, $unitIndexStr, $status)
    {
        $barang = Barang::find($barangId);
        if (!$barang) return;

        $targetKondisi = ($status === 'Selesai') ? 'Baik' : (($status === 'Proses') ? 'Perbaikan' : 'Perawatan');

        if (!empty($unitIndexStr)) {
            $unitIndexes = array_filter(array_map('trim', explode(',', (string)$unitIndexStr)));

            $kondisiPerUnit = [];
            if ($barang->kondisi_per_unit) {
                $kondisiPerUnit = is_array($barang->kondisi_per_unit) 
                    ? $barang->kondisi_per_unit 
                    : (json_decode($barang->kondisi_per_unit, true) ?: []);
            }

            foreach ($unitIndexes as $idx) {
                $kondisiPerUnit[$idx] = $targetKondisi;
            }

            $allUnitsGood = true;
            for ($i = 1; $i <= $barang->jumlah; $i++) {
                $k = $kondisiPerUnit[$i] ?? $barang->kondisi;
                if ($k !== 'Baik') {
                    $allUnitsGood = false;
                }
            }

            $barang->update([
                'kondisi_per_unit' => json_encode($kondisiPerUnit),
                'kondisi'          => $allUnitsGood ? 'Baik' : $targetKondisi,
            ]);
        } else {
            $barang->update(['kondisi' => $targetKondisi]);
        }
    }
}
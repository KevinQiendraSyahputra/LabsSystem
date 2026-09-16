<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select([
            'id', 
            'name', 
            'email', 
            'foto', 
            'role', 
            'nomor_induk', 
            'kelas_atau_jabatan', 
            'laboratorium_penugasan', 
            'telepon', 
            'created_at'
        ])->withCount('peminjamans');

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('nomor_induk', 'like', "%{$term}%")
                  ->orWhere('kelas_atau_jabatan', 'like', "%{$term}%");
            });
        }

        if ($request->filled('role')) {
            if ($request->role === 'kepala_lab') {
                $query->where(function ($q) {
                    $q->where('role', 'kepala_lab')
                      ->orWhere(function ($q2) {
                          $q2->where('role', 'guru')
                             ->where(function ($q3) {
                                 $q3->where('kelas_atau_jabatan', 'like', '%Kepala Lab%')
                                    ->orWhere('kelas_atau_jabatan', 'like', '%Kepala Laboratorium%');
                             });
                      });
                });
            } elseif ($request->role === 'guru') {
                $query->where(function ($q) {
                    $q->where('role', 'guru')
                      ->orWhere('role', 'kepala_lab');
                });
            } else {
                $query->where('role', $request->role);
            }
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $users = $query->latest('id')->paginate($perPage)->withQueryString();
        
        $roleList = property_exists(User::class, 'roleList') 
            ? User::$roleList 
            : ['admin' => 'Admin', 'kepala_lab' => 'Kepala Laboratorium', 'koordinator_lab' => 'Koordinator Lab', 'guru' => 'Guru', 'siswa' => 'Siswa'];

        $laboratoriumList = property_exists(Barang::class, 'laboratoriumList') 
            ? Barang::$laboratoriumList 
            : ['Laboratorium TKJ', 'Laboratorium AKL', 'Laboratorium Pemasaran'];

        return view('pengguna.index', compact('users', 'roleList', 'laboratoriumList'));
    }

    public function create()
    {
        return redirect()->route('pengguna.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|unique:users,email',
            'password'               => 'required|string|min:6',
            'role'                   => 'required|in:admin,kepala_lab,koordinator_lab,guru,siswa',
            'nomor_induk'            => 'nullable|string|max:50',
            'kelas_atau_jabatan'     => 'nullable|string|max:100',
            'laboratorium_penugasan' => 'nullable|string|in:Laboratorium TKJ,Laboratorium AKL,Laboratorium Pemasaran',
            'telepon'                => 'nullable|string|max:30',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if (!in_array($validated['role'], ['kepala_lab', 'koordinator_lab'])) {
            $validated['laboratorium_penugasan'] = null;
        }
        if ($validated['role'] === 'admin') {
            $validated['kelas_atau_jabatan'] = null;
            $validated['laboratorium_penugasan'] = null;
        }

        User::create($validated);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function edit(User $pengguna)
    {
        return redirect()->route('pengguna.index');
    }

    public function update(Request $request, User $pengguna)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|unique:users,email,' . $pengguna->id,
            'role'                   => 'required|in:admin,kepala_lab,koordinator_lab,guru,siswa',
            'nomor_induk'            => 'nullable|string|max:50',
            'kelas_atau_jabatan'     => 'nullable|string|max:100',
            'laboratorium_penugasan' => 'nullable|string|in:Laboratorium TKJ,Laboratorium AKL,Laboratorium Pemasaran',
            'telepon'                => 'nullable|string|max:30',
            'password'               => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (!in_array($validated['role'], ['kepala_lab', 'koordinator_lab'])) {
            $validated['laboratorium_penugasan'] = null;
        }
        if ($validated['role'] === 'admin') {
            $validated['kelas_atau_jabatan'] = null;
            $validated['laboratorium_penugasan'] = null;
        }

        $pengguna->update($validated);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function show(User $pengguna)
    {
        return redirect()->route('pengguna.index');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $pengguna->delete();
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus!');
    }

    /**
     * Hapus banyak akun pengguna sekaligus (Bulk Delete)
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $currentUserId = auth()->id();
        $targetIds = array_filter($validated['ids'], fn($id) => (int)$id !== $currentUserId);

        if (empty($targetIds)) {
            return back()->with('error', 'Tidak ada akun yang dapat dihapus (akun Anda sendiri tidak dapat dihapus).');
        }

        $count = 0;
        DB::transaction(function () use ($targetIds, &$count) {
            $users = User::whereIn('id', $targetIds)->get();
            foreach ($users as $user) {
                $user->delete();
                $count++;
            }
        });

        return redirect()->route('pengguna.index')->with('success', "{$count} akun pengguna berhasil dihapus!");
    }

    /**
     * Endpoint live polling status real-time pengguna
     */
    public function onlineStatuses(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids), fn($v) => is_numeric($v));
        }

        $statuses = [];
        $currentAuthId = (int) auth()->id();

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $numId = (int) $id;
                if ($numId <= 0) continue;
                $statuses[$numId] = \Illuminate\Support\Facades\Cache::has('user-is-online-' . $numId);
            }
        }

        return response()->json([
            'status' => 'success',
            'online_statuses' => $statuses,
        ]);
    }
}
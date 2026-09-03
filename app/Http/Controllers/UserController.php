<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            $query->where('role', $request->role);
        }

        $users = $query->latest('id')->paginate(10)->withQueryString();
        
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
}
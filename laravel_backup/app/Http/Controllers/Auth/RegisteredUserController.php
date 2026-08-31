<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $daftarKelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        return view('auth.register', compact('daftarKelas'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nomor_induk'        => ['required', 'string', 'max:50', 'unique:'.User::class],
            'kelas_atau_jabatan' => ['required', 'string', 'max:100'],
            'telepon'            => ['required', 'string', 'max:30'],
            'password'           => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'nomor_induk'        => $request->nomor_induk,
            'kelas_atau_jabatan' => $request->kelas_atau_jabatan,
            'telepon'            => $request->telepon,
            'role'               => 'siswa', // Default register as siswa/student
            'password'           => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

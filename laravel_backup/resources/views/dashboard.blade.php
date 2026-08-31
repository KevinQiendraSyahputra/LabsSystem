@extends('layouts.app')

@section('content')
<div class="min-h-full bg-slate-50/70 pb-12 sm:pb-16 pb-[calc(3rem+env(safe-area-inset-bottom,0px))]">
    <main class="mx-auto w-full max-w-screen-2xl px-3 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        @php
            $dashboardData = [
                'totalBarang' => (int) ($totalBarang ?? 0),
                'barangBaik' => (int) ($barangBaik ?? 0),
                'barangRusak' => (int) ($barangRusak ?? 0),
                'barangPerbaikan' => (int) ($barangPerbaikan ?? 0),
                'barangPerawatan' => (int) ($barangPerawatan ?? 0),
                'barangHilang' => (int) ($barangHilang ?? 0),
                'totalNilai' => (float) ($totalNilai ?? 0),
                'barangDipinjam' => (int) ($barangDipinjam ?? 0),
                'maintenanceBulanIni' => (int) ($maintenanceBulanIni ?? 0),
                'barangBaruTahunIni' => (int) ($barangBaruTahunIni ?? 0),
                'peminjamanTerlambat' => (int) ($peminjamanTerlambat ?? 0),
                'peminjamanAktif' => collect($peminjamanAktif ?? [])->map(function($p) {
                    return [
                        'id' => $p->id,
                        'nama_peminjam' => $p->nama_peminjam,
                        'barang' => [
                            'nama_barang' => $p->barang->nama_barang ?? '-'
                        ],
                        'tanggal_kembali_rencana' => \Carbon\Carbon::parse($p->tanggal_kembali_rencana)->format('d/m/Y'),
                        'status' => $p->status
                    ];
                })->values()->all(),
                'kategoriStats' => collect($kategoriStats ?? [])->map(function($k) {
                    return [
                        'kategori' => $k->kategori,
                        'total' => (int) $k->total
                    ];
                })->values()->all(),
                'kondisiStats' => $kondisiStats ?? [],
                'barangTerbaru' => collect($barangTerbaru ?? [])->map(function($b) {
                    return [
                        'id' => $b->id,
                        'nama_barang' => $b->nama_barang,
                        'kode_barang' => $b->kode_barang,
                        'kategori' => $b->kategori,
                        'kondisi' => $b->kondisi,
                        'jumlah' => (int) $b->jumlah,
                        'satuan' => $b->satuan ?? 'Unit'
                    ];
                })->values()->all(),
                'routes' => [
                    'tambahBarang' => route('barang.create'),
                    'peminjaman' => route('peminjaman.index'),
                    'laporan' => route('laporan.index'),
                    'barangIndex' => route('barang.index'),
                ]
            ];
        @endphp

        <!-- React Admin Dashboard App with Skeleton & Modern Animations -->
        <div data-react-component="AdminDashboardApp" data-props='@json($dashboardData)'></div>
    </main>
</div>
@endsection
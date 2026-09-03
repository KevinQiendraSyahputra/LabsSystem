<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Maintenance — {{ now()->format('d F Y') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; color: #1e293b; background: #f1f5f9; padding: 16px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .wrapper { max-width: 1200px; margin: 0 auto; background: #fff; padding: 32px; border-radius: 20px; box-shadow: 0 4px 24px rgba(15,23,42,0.08); }
        .print-controls { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px 20px; margin-bottom: 24px; display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; padding: 10px 20px; border-radius: 12px; border: none; cursor: pointer; text-decoration: none; transition: all .15s; }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-primary:hover { background: #4338ca; }
        .btn-back { background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; }
        .btn-back:hover { background: #e2e8f0; }
        /* Kop Surat */
        .kop { display: flex; align-items: center; gap: 20px; padding-bottom: 16px; border-bottom: 3px solid #0f172a; margin-bottom: 24px; }
        .kop-logo { width: 68px; height: 68px; object-fit: contain; }
        .kop-text h1 { font-size: 17px; font-weight: 800; color: #0f172a; letter-spacing: .5px; }
        .kop-text h2 { font-size: 13px; font-weight: 700; color: #334155; margin-top: 2px; }
        .kop-text p { font-size: 11px; color: #64748b; margin-top: 2px; line-height: 1.4; }
        .doc-title { text-align: center; margin: 16px 0 20px 0; }
        .doc-title h3 { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; }
        .doc-title p { font-size: 12px; color: #475569; margin-top: 4px; font-weight: 600; }
        .filter-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; gap: 24px; flex-wrap: wrap; }
        .filter-info span { font-size: 11px; color: #64748b; }
        .filter-info strong { color: #0f172a; font-weight: 800; }
        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 24px; }
        table th { background: #0f172a; color: white; padding: 10px 12px; text-align: left; font-weight: 800; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
        table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; line-height: 1.5; }
        table tr:nth-child(even) td { background: #f8fafc; }
        table tr.total-row td { background: #f8fafc; font-weight: 800; border-top: 2px solid #0f172a; padding: 12px; }
        .kondisi-badge { font-weight: 800; font-size: 11px; color: #0f172a; }
        .badge-selesai, .badge-proses, .badge-pending, .badge-preventif, .badge-korektif, .badge-penggantian { color: #0f172a; }
        .unit-badge { display: inline-block; background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; border-radius: 6px; padding: 1px 6px; font-size: 10px; font-weight: 700; margin-left: 4px; }
        .kode { font-family: monospace; font-size: 10px; font-weight: 800; color: #4f46e5; }
        /* Tanda Tangan */
        .ttd-section { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 32px; page-break-inside: avoid; }
        .ttd-block { text-align: center; }
        .ttd-block .ttd-title { font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 4px; }
        .ttd-block .ttd-role { font-size: 11px; color: #475569; margin-bottom: 64px; font-weight: 600; }
        .ttd-block .ttd-line { border-top: 1.5px solid #334155; padding-top: 4px; display: inline-block; width: 80%; }
        .ttd-block .ttd-name { font-size: 11px; font-weight: 800; color: #0f172a; }
        .ttd-block .ttd-nip { font-size: 10px; color: #64748b; margin-top: 2px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        @media print {
            body { background: white; padding: 0; font-size: 10px; }
            .wrapper { max-width: 100%; padding: 20px; border-radius: 0; box-shadow: none; }
            .print-controls { display: none !important; }
            table th { font-size: 9px; padding: 8px 10px; }
            table td { font-size: 10px; padding: 8px 10px; }
            @page { size: A4 landscape; margin: 1cm; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Tombol Kontrol --}}
    <div class="print-controls">
        <div style="display:flex;gap:8px;align-items:center">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Print
            </button>
            <a href="{{ route('laporan.maintenance') }}" class="btn btn-back">← Kembali ke Laporan</a>
        </div>
        <span style="font-size:11px;color:#64748b;font-weight:600">Preview dokumen PDF — Laporan Maintenance Laboratorium</span>
    </div>

    {{-- Kop Surat --}}
    <div class="kop">
        <img src="{{ asset('uploads/Logo/Logo_winshark.webp') }}" class="kop-logo" alt="Logo"
             onerror="this.style.display='none'">
        <div class="kop-text">
            <h1>SMK MUHAMMADIYAH 2 PEKANBARU</h1>
            <h2>Laboratorium Komputer & Jaringan</h2>
            <p>Jl. KH. Ahmad Dahlan No.90, Kp. Melayu, Sukajadi, Kota Pekanbaru, Riau 28122, <br>Telp : (0761) 35778 <br>Email : smkmudapekanbaru@gmail.com</p>
        </div>
    </div>

    {{-- Judul Dokumen --}}
    <div class="doc-title">
        <h3>Laporan Maintenance Laboratorium</h3>
        <p>{{ $filterLab ?? 'Semua Laboratorium' }} &nbsp;—&nbsp; Per Tanggal: {{ now()->format('d F Y') }}</p>
    </div>

    {{-- Info Filter --}}
    <div class="filter-info">
        <span>Laboratorium: <strong>{{ $filterLab ?? 'Semua' }}</strong></span>
        <span>Total Record: <strong>{{ $maintenances->count() }} Data</strong></span>
        <span>Total Biaya: <strong>Rp {{ number_format($totalBiaya ?? 0, 0, ',', '.') }}</strong></span>
        <span>Dicetak: <strong>{{ now()->format('d/m/Y H:i') }}</strong></span>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:35px">No</th>
                <th style="width:85px">Tanggal</th>
                <th style="width:90px">Lab</th>
                <th>Barang / Fasilitas</th>
                <th style="width:100px">Teknisi</th>
                <th class="text-center" style="width:85px">Jenis</th>
                <th>Deskripsi Kerusakan</th>
                <th>Tindakan</th>
                <th class="text-right" style="width:110px">Biaya</th>
                <th class="text-center" style="width:85px">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $i => $m)
                @php
                    $statusBadge = match($m->status) {
                        'Selesai' => 'badge-selesai',
                        'Proses'  => 'badge-proses',
                        default   => 'badge-pending',
                    };
                    $jenisBadge = match($m->jenis) {
                        'Preventif'   => 'badge-preventif',
                        'Korektif'    => 'badge-korektif',
                        'Penggantian' => 'badge-penggantian',
                        default       => 'badge-pending',
                    };
                @endphp
                <tr>
                    <td class="text-center" style="font-weight:700;color:#64748b">{{ $i + 1 }}</td>
                    <td style="font-weight:600;color:#334155">{{ $m->tanggal_maintenance ? \Carbon\Carbon::parse($m->tanggal_maintenance)->format('d/m/Y') : '—' }}</td>
                    <td style="font-weight:600;color:#475569;font-size:10px">{{ str_replace('Laboratorium ', '', $m->laboratorium ?? '—') }}</td>
                    <td style="font-weight:700;color:#0f172a">
                        @if($m->barang)
                            {{ $m->barang->nama_barang }}
                            <span class="kode">{{ $m->barang->kode_barang }}</span>
                            @if($m->unit_index) <span class="unit-badge">Unit {{ $m->unit_index }}</span> @endif
                        @else
                            <span style="color:#64748b;font-style:italic">Umum / Fasilitas Lab</span>
                        @endif
                    </td>
                    <td style="color:#64748b;font-weight:500">{{ $m->teknisi ?? '—' }}</td>
                    <td class="text-center"><span class="kondisi-badge {{ $jenisBadge }}">{{ $m->jenis }}</span></td>
                    <td style="color:#334155">{{ $m->deskripsi_kerusakan }}</td>
                    <td style="color:#64748b">{{ $m->tindakan ?? '—' }}</td>
                    <td class="text-right" style="font-family:monospace;font-weight:800;color:#0f172a">
                        {{ $m->biaya ? 'Rp '.number_format($m->biaya, 0, ',', '.') : '—' }}
                    </td>
                    <td class="text-center"><span class="kondisi-badge {{ $statusBadge }}">{{ $m->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center;padding:24px;color:#94a3b8;font-weight:700">Tidak ada data riwayat maintenance</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="8" style="font-weight:800;text-align:right;font-size:10px">TOTAL BIAYA MAINTENANCE:</td>
                <td class="text-right" style="font-family:monospace;font-weight:800;font-size:11px;color:#0f172a">Rp {{ number_format($totalBiaya ?? 0, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- Tanda Mengetahui --}}
    @php
        $kepalaInfo = null;
        $labKey = $filterLab ?: null;
        if ($labKey && isset($kepalaLabUsers[$labKey])) {
            $kepalaInfo = $kepalaLabUsers[$labKey];
        } elseif ($labKey && isset($kepalaLabDefault[$labKey])) {
            $def = $kepalaLabDefault[$labKey];
            $kepalaInfo = (object)['name' => $def['nama'], 'nomor_induk' => $def['nip']];
        } else {
            $def = $kepalaLabDefault['Laboratorium TKJ'] ?? ['nama' => 'Bu Sellya, S.Pd', 'nip' => '-'];
            $kepalaInfo = (object)['name' => $def['nama'], 'nomor_induk' => $def['nip']];
        }
    @endphp
    <div class="ttd-section">
        <div class="ttd-block">
            <div class="ttd-title">Dibuat Oleh</div>
            <div class="ttd-role">{{ Auth::user()->canAccessMaintenance() && !Auth::user()->isAdmin() ? 'Koordinator Laboratorium' : 'Admin Laboratorium' }}</div>
            <div>
                <span class="ttd-line"></span>
                <div class="ttd-name">( {{ Auth::user()->name ?? 'Admin Laboratorium' }} )</div>
                <div class="ttd-nip">NIP. {{ Auth::user()->nomor_induk ?? '—' }}</div>
            </div>
        </div>
        <div class="ttd-block">
            <div class="ttd-title">Mengetahui</div>
            <div class="ttd-role">Koordinator Laboratorium</div>
            <div>
                <span class="ttd-line"></span>
                <div class="ttd-name">( _________________ )</div>
                <div class="ttd-nip">NIP. _______________</div>
            </div>
        </div>
        <div class="ttd-block">
            <div class="ttd-title">Mengetahui</div>
            <div class="ttd-role">Kepala Laboratorium</div>
            <div>
                <span class="ttd-line"></span>
                <div class="ttd-name">{{ $kepalaInfo->name }}</div>
                <div class="ttd-nip">NIP. {{ $kepalaInfo->nomor_induk ?? '-' }}</div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
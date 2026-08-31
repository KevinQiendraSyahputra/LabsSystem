<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Laporan Maintenance — {{ now()->format('d F Y') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            font-size: 11px; 
            color: #1e293b; 
            background: #f1f5f9; 
            padding: 12px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .screen-wrapper { 
            max-width: 1100px; 
            margin: 0 auto; 
            background: #ffffff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08);
        }

        /* Controls (Non-Print) */
        .print-controls { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 12px; 
            padding: 12px 16px; 
            margin-bottom: 16px; 
            display: flex; 
            gap: 10px; 
            align-items: center; 
            justify-content: space-between;
            flex-wrap: wrap; 
        }

        .btn-group { display: flex; gap: 8px; align-items: center; }

        .btn { 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            padding: 8px 16px; 
            border-radius: 10px; 
            font-size: 12px; 
            font-weight: 700; 
            cursor: pointer; 
            border: none; 
            transition: all 0.2s; 
            text-decoration: none; 
        }

        .btn-print { background: #4f46e5; color: #fff; box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25); }
        .btn-print:hover { background: #4338ca; }
        .btn-back { background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; }
        .btn-back:hover { background: #f1f5f9; color: #1e293b; }

        /* Header Document */
        .doc-header { 
            text-align: center; 
            border-bottom: 2.5px solid #1e3a8a; 
            padding-bottom: 12px; 
            margin-bottom: 16px; 
        }
        .doc-header .sekolah { font-size: 15px; font-weight: 800; text-transform: uppercase; color: #1e3a8a; letter-spacing: 0.5px; }
        .doc-header .alamat  { font-size: 9.5px; color: #64748b; margin-top: 3px; }
        .doc-header .judul   { font-size: 14px; font-weight: 800; text-transform: uppercase; margin-top: 10px; color: #0f172a; }
        .doc-header .periode { font-size: 11px; font-weight: 600; color: #334155; margin-top: 2px; }
        .doc-header .tanggal { font-size: 9.5px; color: #94a3b8; margin-top: 2px; }

        /* Info Chips */
        .info-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
        .info-chip { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px 10px; font-size: 9.5px; color: #475569; }
        .info-chip strong { color: #0f172a; }

        /* Stats Grid */
        .stats-row { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 8px; 
            margin-bottom: 16px; 
        }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px; text-align: center; }
        .stat-card .val { font-size: 16px; font-weight: 800; }
        .stat-card .lbl { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-top: 2px; }

        /* Table Responsive Container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 16px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 9.5px; min-width: 780px; }
        thead tr { background: #1e3a8a; color: #ffffff; }
        thead th { padding: 8px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 7px 8px; vertical-align: middle; line-height: 1.4; }
        tfoot tr { background: #0f172a; color: #ffffff; }
        tfoot td { padding: 8px 8px; font-weight: 800; font-size: 10px; }

        /* Badges */
        .badge { display: inline-block; padding: 2px 7px; border-radius: 9999px; font-size: 8.5px; font-weight: 700; }
        .badge-selesai { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-proses  { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-pending { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-preventif   { background: #e0e7ff; color: #4338ca; }
        .badge-korektif    { background: #fee2e2; color: #b91c1c; }
        .badge-penggantian { background: #f3e8ff; color: #7e22ce; }
        .badge-lab { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .kode { font-family: 'JetBrains Mono', monospace; font-size: 8.5px; background: #f1f5f9; padding: 1px 4px; border-radius: 4px; color: #475569; }

        /* Signature Section */
        .signature-section { 
            margin-top: 36px; 
            display: flex; 
            justify-content: space-between; 
            gap: 20px; 
            page-break-inside: avoid; 
        }
        .signature-box { text-align: center; min-width: 180px; }
        .sig-title { font-weight: 700; font-size: 10.5px; color: #0f172a; }
        .sig-role  { font-size: 9.5px; color: #64748b; margin-top: 2px; }
        .signature-space { height: 60px; }
        .sig-name  { font-weight: 800; font-size: 10.5px; text-decoration: underline; color: #1e3a8a; }
        .sig-nip   { font-size: 9px; color: #64748b; margin-top: 2px; }

        /* Multi-Device Media Queries */
        @media (max-width: 640px) {
            body { padding: 6px; }
            .screen-wrapper { padding: 14px; border-radius: 12px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .signature-section { flex-direction: column; gap: 24px; align-items: center; }
            .signature-box { width: 100%; max-width: 240px; }
        }

        /* Print Media Setup */
        @media print {
            body { background: #ffffff; padding: 0; font-size: 9px; }
            .screen-wrapper { padding: 0; max-width: 100%; border-radius: 0; box-shadow: none; }
            .print-controls { display: none !important; }
            .table-responsive { overflow: visible; }
            table { min-width: 100%; font-size: 8.5px; }
            thead th, tbody td, tfoot td { padding: 4px 6px; }
            .signature-section { flex-direction: row; margin-top: 24px; justify-content: space-between; }
            .signature-box { width: auto; min-width: 180px; }
            @page { margin: 1cm 0.8cm; size: A4 landscape; }
        }
    </style>
</head>
<body>

<div class="screen-wrapper">

    {{-- Navigasi & Print Actions --}}
    <div class="print-controls">
        <div class="btn-group">
            <button class="btn btn-print" onclick="window.print()">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Download PDF
            </button>
            <a href="{{ route('laporan.maintenance') }}" class="btn btn-back">← Kembali ke Laporan</a>
        </div>
        <span style="font-size:11px;color:#94a3b8;font-weight:600;">Dicetak pada {{ now()->format('d/m/Y H:i') }} WIB</span>
    </div>

    {{-- Kop Dokumen Laporan --}}
    <div class="doc-header">
        <div class="sekolah">SMK Negeri / Swasta Winshark Community</div>
        <div class="alamat">Jl. Pendidikan No. 1, Pekanbaru, Riau | Telp: (0761) xxxxx | Email: info@smk.sch.id</div>
        <div class="judul">Laporan Maintenance Laboratorium</div>
        <div class="periode">
            @if(!empty($filterLab)) {{ $filterLab }} @else Semua Laboratorium @endif
            @if(!empty($tanggalDari) || !empty($tanggalSampai))
             — Periode: {{ !empty($tanggalDari) ? \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') : '...' }}
             s/d {{ !empty($tanggalSampai) ? \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') : '...' }}
            @endif
        </div>
        <div class="tanggal">Tanggal cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    {{-- Ringkasan Filter --}}
    <div class="info-row">
        <div class="info-chip"><strong>Total Record:</strong> {{ $maintenances->count() }} Data</div>
        <div class="info-chip"><strong>Total Biaya:</strong> Rp {{ number_format($totalBiaya ?? 0, 0, ',', '.') }}</div>
        @if(!empty($filterLab))    <div class="info-chip"><strong>Lab:</strong> {{ $filterLab }}</div> @endif
        @if(!empty($filterStatus)) <div class="info-chip"><strong>Status:</strong> {{ $filterStatus }}</div> @endif
        @if(!empty($filterJenis))  <div class="info-chip"><strong>Jenis:</strong> {{ $filterJenis }}</div> @endif
        @if(!empty($tanggalDari))  <div class="info-chip"><strong>Dari:</strong> {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</div> @endif
        @if(!empty($tanggalSampai))<div class="info-chip"><strong>Sampai:</strong> {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</div> @endif
    </div>

    {{-- Kartu Statistik --}}
    @php
        $countTotal   = $maintenances->count();
        $countSelesai = $maintenances->where('status', 'Selesai')->count();
        $countProses  = $maintenances->where('status', 'Proses')->count();
        $countPending = $maintenances->where('status', 'Pending')->count();
    @endphp
    <div class="stats-row">
        <div class="stat-card"><div class="val" style="color:#4f46e5">{{ $countTotal }}</div><div class="lbl">Total Data</div></div>
        <div class="stat-card"><div class="val" style="color:#16a34a">{{ $countSelesai }}</div><div class="lbl">Selesai</div></div>
        <div class="stat-card"><div class="val" style="color:#d97706">{{ $countProses }}</div><div class="lbl">Proses</div></div>
        <div class="stat-card"><div class="val" style="color:#64748b">{{ $countPending }}</div><div class="lbl">Pending</div></div>
    </div>

    {{-- Tabel Riwayat Maintenance --}}
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width:25px; text-align:center;">No</th>
                    <th style="width:70px">Tanggal</th>
                    <th style="width:100px">Laboratorium</th>
                    <th>Barang / Fasilitas</th>
                    <th style="width:90px">Teknisi</th>
                    <th style="width:70px; text-align:center">Jenis</th>
                    <th>Deskripsi Kerusakan</th>
                    <th>Tindakan</th>
                    <th style="width:85px; text-align:right">Biaya</th>
                    <th style="width:60px; text-align:center">Status</th>
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
                        default       => '',
                    };
                @endphp
                <tr>
                    <td style="text-align:center;color:#94a3b8;font-weight:700;">{{ $i+1 }}</td>
                    <td>{{ $m->tanggal_maintenance ? \Carbon\Carbon::parse($m->tanggal_maintenance)->format('d/m/Y') : '—' }}</td>
                    <td>
                        @if($m->laboratorium)
                            <span class="badge badge-lab">{{ str_replace('Laboratorium ','Lab ',$m->laboratorium) }}</span>
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($m->barang)
                            <strong>{{ $m->barang->nama_barang }}</strong><br>
                            <span class="kode">{{ $m->barang->kode_barang }}</span>
                            @if($m->unit_index) <span style="color:#64748b;font-weight:600;"> · Unit {{ $m->unit_index }}</span> @endif
                        @else
                            <span style="color:#64748b;font-style:italic">Umum / Fasilitas Lab</span>
                        @endif
                    </td>
                    <td>{{ $m->teknisi ?? '—' }}</td>
                    <td style="text-align:center"><span class="badge {{ $jenisBadge }}">{{ $m->jenis }}</span></td>
                    <td style="max-width:160px;">{{ $m->deskripsi_kerusakan }}</td>
                    <td style="max-width:160px; color:#475569;">{{ $m->tindakan ?? '—' }}</td>
                    <td style="text-align:right; font-family:'JetBrains Mono',monospace; font-weight:700;">
                        {{ $m->biaya ? 'Rp '.number_format($m->biaya,0,',','.') : '—' }}
                    </td>
                    <td style="text-align:center"><span class="badge {{ $statusBadge }}">{{ $m->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center; padding:24px; color:#94a3b8; font-weight:600;">Tidak ada data riwayat maintenance</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">TOTAL BIAYA MAINTENANCE</td>
                    <td style="text-align:right; font-family:'JetBrains Mono',monospace;">Rp {{ number_format($totalBiaya ?? 0, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Tanda Tangan Pengesahan --}}
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
    <div class="signature-section" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
        <div class="signature-box">
            <div class="sig-title">Dibuat oleh,</div>
            <div class="sig-role">{{ Auth::user()->canAccessMaintenance() && !Auth::user()->isAdmin() ? 'Koordinator Laboratorium' : 'Admin Laboratorium' }}</div>
            <div class="signature-space"></div>
            <div class="sig-name">( {{ Auth::user()->name ?? 'Admin Laboratorium' }} )</div>
            <div class="sig-nip">NIP. {{ Auth::user()->nomor_induk ?? '—' }}</div>
        </div>
        
        <div class="signature-box">
            <div class="sig-title">Mengetahui,</div>
            <div class="sig-role">Koordinator Laboratorium</div>
            <div class="signature-space"></div>
            <div class="sig-name">( _________________ )</div>
            <div class="sig-nip">NIP. _______________</div>
        </div>

        <div class="signature-box">
            <div class="sig-title">Menyetujui,</div>
            <div class="sig-role">Kepala Laboratorium</div>
            <div class="signature-space"></div>
            <div class="sig-name">( {{ $kepalaInfo->name }} )</div>
            <div class="sig-nip">NIP. {{ $kepalaInfo->nomor_induk ?? '—' }}</div>
        </div>
    </div>

</div>

</body>
</html>
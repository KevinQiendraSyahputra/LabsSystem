<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inventaris — {{ now()->format('d F Y') }}</title>
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
        table tr.lab-header td { background: #e0e7ff; color: #3730a3; font-weight: 800; font-size: 11px; letter-spacing: .5px; border-top: 2px solid #6366f1; padding: 10px 12px; }
        table tr.total-row td { background: #f8fafc; font-weight: 800; border-top: 2px solid #0f172a; padding: 12px; }
        .kondisi-badge { font-weight: 800; font-size: 11px; color: #0f172a; }
        .kondisi-baik, .kondisi-perawatan, .kondisi-perbaikan, .kondisi-rusak, .kondisi-hilang { color: #0f172a; }
        .unit-badge { display: inline-block; background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; border-radius: 6px; padding: 1px 6px; font-size: 10px; font-weight: 700; margin-left: 4px; }
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
            <a href="{{ route('laporan.index') }}" class="btn btn-back">← Kembali ke Laporan</a>
        </div>
        <span style="font-size:11px;color:#64748b;font-weight:600">Preview dokumen PDF — Laporan Inventaris Laboratorium</span>
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
        <h3>Laporan Inventaris Barang Laboratorium</h3>
        <p>{{ $filterLab ?? 'Semua Laboratorium' }} &nbsp;—&nbsp; Per Tanggal: {{ now()->format('d F Y') }}</p>
    </div>

    {{-- Info Filter --}}
    <div class="filter-info">
        <span>Laboratorium: <strong>{{ $filterLab ?? 'Semua' }}</strong></span>
        <span>Filter Kondisi: <strong>{{ $filterKondisi ?? 'Semua' }}</strong></span>
        <span>Total Jenis: <strong>{{ $barangs->count() }} Jenis</strong></span>
        <span>Total Unit: <strong>{{ number_format($totalUnitSum, 0, ',', '.') }} Unit</strong></span>
        <span>Total Aset: <strong>Rp {{ number_format($totalNilaiSum, 0, ',', '.') }}</strong></span>
        <span>Dicetak: <strong>{{ now()->format('d/m/Y H:i') }}</strong></span>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:35px">No</th>
                <th style="width:90px">Kode</th>
                <th>Nama Barang</th>
                <th style="width:90px">Merk</th>
                <th style="width:120px">Laboratorium</th>
                <th style="width:90px">Kategori</th>
                <th class="text-center" style="width:50px">Stok</th>
                <th style="width:60px">Satuan</th>
                <th class="text-center" style="width:85px">Kondisi</th>
                <th style="width:80px">Lokasi</th>
                <th class="text-center" style="width:55px">Tahun</th>
                <th class="text-right" style="width:95px">Harga (Rp)</th>
                <th class="text-right" style="width:105px">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1; 
                $currentLab = null;
            @endphp
            @forelse($barangs as $b)
                @php
                    $kondisiPerUnit = [];
                    if ($b->kondisi_per_unit) {
                        $kondisiPerUnit = is_array($b->kondisi_per_unit) 
                            ? $b->kondisi_per_unit 
                            : (json_decode($b->kondisi_per_unit, true) ?: []);
                    }

                    $matchingUnitIndices = [];
                    for ($i = 1; $i <= (int)$b->jumlah; $i++) {
                        $unitKon = $kondisiPerUnit[$i] ?? $b->kondisi;
                        if (!$reqKondisi || $unitKon === $reqKondisi) {
                            $matchingUnitIndices[] = $i;
                        }
                    }

                    if (empty($matchingUnitIndices)) {
                        continue;
                    }

                    $displayCount = count($matchingUnitIndices);
                    $displayKondisi = $reqKondisi ?: $b->kondisi;
                    $rowNilai = ($b->harga ?? 0) * $displayCount;
                    $grandTotalUnits += $displayCount;
                    $grandTotalNilai += $rowNilai;

                    $unitLabelHtml = '';
                    if ($reqKondisi && $displayCount < (int)$b->jumlah) {
                        if ($displayCount <= 5) {
                            $unitLabelHtml = '<span class="unit-badge">Unit ' . implode(', ', $matchingUnitIndices) . '</span>';
                        } else {
                            $unitLabelHtml = '<span class="unit-badge">' . $displayCount . ' Unit Terfilter</span>';
                        }
                    }
                @endphp

                @if($b->laboratorium !== $currentLab)
                    @php $currentLab = $b->laboratorium; @endphp
                    <tr class="lab-header">
                        <td colspan="13">{{ $b->laboratorium ?? 'Laboratorium Tidak Ditentukan' }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="text-center" style="font-weight:700;color:#64748b">{{ $no++ }}</td>
                    <td style="font-family:monospace;font-weight:800;color:#4f46e5;font-size:10px">{{ $b->kode_barang }}</td>
                    <td style="font-weight:700;color:#0f172a">
                        {{ $b->nama_barang }}
                        {!! $unitLabelHtml !!}
                    </td>
                    <td style="color:#64748b;font-weight:500">{{ $b->merk ?? '—' }}</td>
                    <td style="color:#475569;font-weight:600;font-size:10px">{{ $b->laboratorium ?? '—' }}</td>
                    <td style="font-weight:600;color:#334155">{{ $b->kategori }}</td>
                    <td class="text-center" style="font-weight:800;color:#0f172a">{{ $displayCount }}</td>
                    <td style="color:#64748b;font-weight:500">{{ $b->satuan }}</td>
                    <td class="text-center">
                        @php
                            $kMap = ['Baik'=>'baik','Perawatan'=>'perawatan','Perbaikan'=>'perbaikan','Rusak Berat'=>'rusak','Hilang'=>'hilang'];
                            $kClass = $kMap[$displayKondisi] ?? 'hilang';
                        @endphp
                        <span class="kondisi-badge kondisi-{{ $kClass }}">{{ $displayKondisi }}</span>
                    </td>
                    <td style="color:#64748b;font-weight:500">{{ $b->lokasi ?? '—' }}</td>
                    <td class="text-center" style="color:#64748b;font-weight:600">{{ $b->tahun_pembelian ?? '—' }}</td>
                    <td class="text-right" style="font-family:monospace;font-weight:600">{{ $b->harga ? 'Rp '.number_format($b->harga, 0, ',', '.') : '—' }}</td>
                    <td class="text-right" style="font-family:monospace;font-weight:800;color:#0f172a">{{ $b->harga ? 'Rp '.number_format($rowNilai, 0, ',', '.') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="13" style="text-align:center;padding:20px;color:#94a3b8">Tidak ada data inventaris</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6" style="font-weight:700;text-align:right;font-size:10px">TOTAL:</td>
                <td class="text-center" style="font-weight:800">{{ $grandTotalUnits }}</td>
                <td colspan="5"></td>
                <td class="text-right" style="font-family:monospace;font-weight:800;font-size:11px;color:#1e293b">Rp {{ number_format($grandTotalNilai, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Tanda Mengetahui --}}
    <div class="ttd-section">
        <div class="ttd-block">
            <div class="ttd-title">Dibuat Oleh</div>
            <div class="ttd-role">Admin Laboratorium</div>
            <div>
                <span class="ttd-line"></span>
                <div class="ttd-name">( _________________ )</div>
                <div class="ttd-nip">NIP. _______________</div>
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
            @php
                $kepalaInfo = null;
                if ($filterLab && isset($kepalaLabUsers[$filterLab])) {
                    $kepalaInfo = $kepalaLabUsers[$filterLab];
                } elseif ($filterLab && isset($kepalaLabDefault[$filterLab])) {
                    $def = $kepalaLabDefault[$filterLab];
                    $kepalaInfo = (object)['name' => $def['nama'], 'nomor_induk' => $def['nip']];
                } else {
                    $def = $kepalaLabDefault['Laboratorium TKJ'] ?? ['nama' => 'Bu Sellya, S.Pd', 'nip' => '-'];
                    $kepalaInfo = (object)['name' => $def['nama'], 'nomor_induk' => $def['nip']];
                }
            @endphp
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

<script>
    // Auto print jika ada parameter ?print=1
    const params = new URLSearchParams(window.location.search);
    if (params.get('autoprint') === '1') {
        setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>

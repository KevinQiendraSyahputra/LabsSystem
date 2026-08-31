<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inventaris — {{ now()->format('d F Y') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; color: #1e293b; background: #f1f5f9; padding: 12px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .wrapper { max-width: 1150px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 20px rgba(15,23,42,0.08); }
        .print-controls { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; padding: 8px 18px; border-radius: 10px; border: none; cursor: pointer; text-decoration: none; transition: all .15s; }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-primary:hover { background: #4338ca; }
        .btn-back { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-back:hover { background: #e2e8f0; }
        /* Kop Surat */
        .kop { display: flex; align-items: center; gap: 16px; padding-bottom: 12px; border-bottom: 2px solid #1e3a5f; margin-bottom: 16px; }
        .kop-logo { width: 64px; height: 64px; object-fit: contain; }
        .kop-text h1 { font-size: 15px; font-weight: 800; color: #1e3a5f; letter-spacing: .3px; }
        .kop-text h2 { font-size: 12px; font-weight: 600; color: #334155; margin-top: 2px; }
        .kop-text p { font-size: 10px; color: #64748b; margin-top: 1px; }
        .doc-title { text-align: center; margin: 12px 0; }
        .doc-title h3 { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1e293b; }
        .doc-title p { font-size: 11px; color: #475569; margin-top: 3px; }
        .filter-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; margin-bottom: 14px; display: flex; gap: 20px; flex-wrap: wrap; }
        .filter-info span { font-size: 10px; color: #64748b; }
        .filter-info strong { color: #1e293b; font-weight: 700; }
        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table th { background: #1e3a5f; color: white; padding: 6px 8px; text-align: left; font-weight: 700; font-size: 9px; text-transform: uppercase; letter-spacing: .5px; }
        table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        table tr:nth-child(even) td { background: #fafafa; }
        table tr.lab-header td { background: #e0e7ff; color: #3730a3; font-weight: 800; font-size: 10px; letter-spacing: .5px; border-top: 2px solid #6366f1; }
        table tr.total-row td { background: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1; }
        .kondisi-badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 700; }
        .kondisi-baik { background: #dcfce7; color: #166534; }
        .kondisi-perawatan { background: #fef9c3; color: #854d0e; }
        .kondisi-perbaikan { background: #ffedd5; color: #9a3412; }
        .kondisi-rusak { background: #fee2e2; color: #991b1b; }
        .kondisi-hilang { background: #f1f5f9; color: #475569; }
        /* Tanda Tangan */
        .ttd-section { margin-top: 36px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .ttd-block { text-align: center; }
        .ttd-block .ttd-title { font-size: 10px; font-weight: 700; color: #334155; margin-bottom: 4px; }
        .ttd-block .ttd-role { font-size: 10px; color: #475569; margin-bottom: 56px; }
        .ttd-block .ttd-line { border-top: 1.5px solid #334155; padding-top: 4px; display: inline-block; width: 80%; }
        .ttd-block .ttd-name { font-size: 10px; font-weight: 700; color: #1e293b; }
        .ttd-block .ttd-nip { font-size: 9px; color: #64748b; margin-top: 2px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        @media print {
            body { background: white; padding: 0; font-size: 10px; }
            .wrapper { max-width: 100%; padding: 16px; border-radius: 0; box-shadow: none; }
            .print-controls { display: none !important; }
            table th { font-size: 8px; padding: 5px 6px; }
            table td { font-size: 9px; padding: 4px 6px; }
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
            <a href="{{ url()->previous() }}" class="btn btn-back">← Kembali</a>
        </div>
        <span style="font-size:11px;color:#64748b">Preview dokumen PDF — Laporan Inventaris Laboratorium</span>
    </div>

    {{-- Kop Surat --}}
    <div class="kop">
        <img src="{{ asset('uploads/Logo/Logo_winshark.webp') }}" class="kop-logo" alt="Logo"
             onerror="this.style.display='none'">
        <div class="kop-text">
            <h1>SMK NEGERI / SWASTA</h1>
            <h2>Laboratorium Komputer & Jaringan</h2>
            <p>Jl. Pendidikan No. 1, Kota, Provinsi &nbsp;|&nbsp; Telp. (021) 123-456 &nbsp;|&nbsp; Website: www.sekolah.sch.id</p>
        </div>
    </div>

    {{-- Judul Dokumen --}}
    <div class="doc-title">
        <h3>Daftar Inventaris Laboratorium</h3>
        <p>{{ $filterLab ?? 'Semua Laboratorium' }} &nbsp;—&nbsp; Per Tanggal: {{ now()->format('d F Y') }}</p>
    </div>

    {{-- Info Filter --}}
    <div class="filter-info">
        <span>Laboratorium: <strong>{{ $filterLab ?? 'Semua' }}</strong></span>
        <span>Total Jenis: <strong>{{ $barangs->count() }} item</strong></span>
        <span>Total Nilai: <strong>Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong></span>
        <span>Dicetak: <strong>{{ now()->format('d/m/Y H:i') }}</strong></span>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th>Lab</th>
                <th>Kategori</th>
                <th class="text-center">Jml</th>
                <th>Satuan</th>
                <th class="text-center">Kondisi</th>
                <th>Lokasi</th>
                <th>Tahun</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Nilai Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $currentLab = null; @endphp
            @forelse($barangs as $b)
                @if($b->laboratorium !== $currentLab)
                    @php $currentLab = $b->laboratorium; @endphp
                    <tr class="lab-header">
                        <td colspan="13">{{ $b->laboratorium ?? 'Laboratorium Tidak Ditentukan' }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td style="font-family:monospace;font-weight:700;color:#4f46e5">{{ $b->kode_barang }}</td>
                    <td style="font-weight:600">{{ $b->nama_barang }}</td>
                    <td style="color:#64748b">{{ $b->merk ?? '—' }}</td>
                    <td style="color:#475569;font-size:9px">{{ str_replace('Laboratorium ', '', $b->laboratorium ?? '—') }}</td>
                    <td>{{ $b->kategori }}</td>
                    <td class="text-center" style="font-weight:700">{{ $b->jumlah }}</td>
                    <td style="color:#64748b">{{ $b->satuan }}</td>
                    <td class="text-center">
                        @php
                            $kMap = ['Baik'=>'baik','Perawatan'=>'perawatan','Perbaikan'=>'perbaikan','Rusak Berat'=>'rusak','Hilang'=>'hilang'];
                            $kClass = $kMap[$b->kondisi] ?? 'hilang';
                        @endphp
                        <span class="kondisi-badge kondisi-{{ $kClass }}">{{ $b->kondisi }}</span>
                    </td>
                    <td style="color:#64748b">{{ $b->lokasi ?? '—' }}</td>
                    <td class="text-center" style="color:#64748b">{{ $b->tahun_pembelian ?? '—' }}</td>
                    <td class="text-right" style="font-family:monospace">{{ $b->harga ? 'Rp '.number_format($b->harga, 0, ',', '.') : '—' }}</td>
                    <td class="text-right" style="font-family:monospace;font-weight:700">{{ $b->harga ? 'Rp '.number_format($b->harga * $b->jumlah, 0, ',', '.') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="13" style="text-align:center;padding:20px;color:#94a3b8">Tidak ada data inventaris</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6" style="font-weight:700;text-align:right;font-size:10px">TOTAL:</td>
                <td class="text-center" style="font-weight:800">{{ $barangs->sum('jumlah') }}</td>
                <td colspan="5"></td>
                <td class="text-right" style="font-family:monospace;font-weight:800;font-size:11px;color:#1e293b">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
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

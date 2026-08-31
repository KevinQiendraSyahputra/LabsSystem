<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris {{ $activeLab }} — {{ now()->format('d F Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; background: #f8fafc; }
        .screen-wrapper { max-width: 900px; margin: 0 auto; padding: 20px; }

        /* Header */
        .doc-header { text-align: center; border-bottom: 3px double #1e40af; padding-bottom: 14px; margin-bottom: 16px; }
        .doc-header .sekolah { font-size: 14px; font-weight: 800; text-transform: uppercase; color: #1e3a8a; letter-spacing: 0.5px; }
        .doc-header .alamat  { font-size: 9.5px; color: #64748b; margin-top: 2px; }
        .doc-header .judul   { font-size: 13px; font-weight: 800; text-transform: uppercase; margin-top: 10px; color: #1e293b; letter-spacing: 0.5px; }
        .doc-header .subjudul{ font-size: 11px; color: #334155; margin-top: 3px; }
        .doc-header .tanggal { font-size: 9.5px; color: #64748b; margin-top: 2px; }

        /* Info box */
        .info-box { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
        .info-item { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px; font-size: 9.5px; }
        .info-item strong { color: #334155; }

        /* Stats */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 14px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; text-align: center; }
        .stat-card .val { font-size: 16px; font-weight: 800; color: #1e293b; }
        .stat-card .lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 1px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 16px; }
        thead tr { background: #1e3a8a; color: #fff; }
        thead th { padding: 6px 8px; text-align: left; font-weight: 700; font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr.cat-row { background: #eff6ff; }
        tbody tr.cat-row td { font-weight: 700; color: #1e40af; font-size: 9px; text-transform: uppercase; padding: 4px 8px; }
        tbody td { padding: 5px 8px; vertical-align: middle; }
        tfoot tr { background: #1e293b; color: #fff; }
        tfoot td { padding: 7px 8px; font-weight: 800; font-size: 10px; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
        .badge-baik { background: #d1fae5; color: #065f46; }
        .badge-perawatan { background: #fef3c7; color: #92400e; }
        .badge-perbaikan { background: #ffedd5; color: #9a3412; }
        .badge-rusak { background: #fee2e2; color: #991b1b; }
        .badge-hilang { background: #f1f5f9; color: #475569; }
        .kode { font-family: 'Courier New', monospace; font-size: 8.5px; background: #f1f5f9; padding: 1px 4px; border-radius: 4px; }

        /* Signature */
        .signature-section { margin-top: 30px; display: flex; justify-content: space-between; gap: 20px; }
        .signature-box { text-align: center; flex: 1; }
        .signature-box .sig-title { font-weight: 700; font-size: 10.5px; color: #1e293b; }
        .signature-box .sig-role  { font-size: 9.5px; color: #475569; margin-top: 2px; }
        .signature-space { height: 60px; }
        .signature-box .sig-name  { font-weight: 800; font-size: 10.5px; text-decoration: underline; color: #1e3a8a; margin-top: 4px; }
        .signature-box .sig-nip   { font-size: 8.5px; color: #64748b; margin-top: 2px; }

        /* Print controls */
        .print-controls { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; border: none; transition: all 0.15s; }
        .btn-print { background: #dc2626; color: #fff; }
        .btn-print:hover { background: #b91c1c; }
        .btn-back  { background: #f1f5f9; color: #374151; text-decoration: none; }
        .btn-back:hover { background: #e2e8f0; }

        /* Print media */
        @media print {
            body { background: #fff; font-size: 9px; }
            .screen-wrapper { padding: 0; max-width: 100%; }
            .print-controls { display: none !important; }
            .doc-header { padding-bottom: 10px; margin-bottom: 12px; }
            table { font-size: 8.5px; }
            thead th, tbody td, tfoot td { padding: 4px 6px; }
            .stats-row { margin-bottom: 10px; }
            .signature-section { margin-top: 24px; page-break-inside: avoid; }
            @page { margin: 1.5cm 1.2cm; size: A4 landscape; }
        }
    </style>
</head>
<body>
<div class="screen-wrapper">

    {{-- Print Controls --}}
    <div class="print-controls">
        <button class="btn btn-print" onclick="window.print()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak / Print PDF
        </button>
        <a href="{{ route('inventaris.index', ['lab' => $activeLab]) }}" class="btn btn-back">
            ← Kembali
        </a>
        <span style="font-size:11px; color:#64748b;">Dokumen: Daftar Inventaris {{ $activeLab }} — Dicetak {{ now()->format('d/m/Y H:i') }}</span>
    </div>

    {{-- Document Header --}}
    <div class="doc-header">
        <div class="sekolah">SMK Negeri / Swasta [Nama Sekolah]</div>
        <div class="alamat">Jl. Pendidikan No. 1, Kota, Provinsi | Telp: (0xx) xxxxx | Email: info@smk.sch.id</div>
        <div class="judul">Daftar Inventaris Laboratorium</div>
        <div class="subjudul">{{ $activeLab }}</div>
        <div class="tanggal">Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</div>
    </div>

    {{-- Info Box --}}
    <div class="info-box">
        <div class="info-item"><strong>Laboratorium:</strong> {{ $activeLab }}</div>
        <div class="info-item"><strong>Total Jenis:</strong> {{ $barangs->count() }} item</div>
        <div class="info-item"><strong>Total Unit:</strong> {{ $totalUnit }} unit</div>
        <div class="info-item"><strong>Nilai Aset:</strong> Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
        @if(request('kategori'))<div class="info-item"><strong>Filter Kategori:</strong> {{ request('kategori') }}</div>@endif
        @if(request('kondisi'))<div class="info-item"><strong>Filter Kondisi:</strong> {{ request('kondisi') }}</div>@endif
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        @foreach(['Baik'=>'#059669','Perawatan'=>'#d97706','Perbaikan'=>'#ea580c','Rusak Berat'=>'#dc2626','Hilang'=>'#64748b'] as $k=>$color)
        <div class="stat-card">
            <div class="val" style="color:{{ $color }}">{{ $rekapKondisi[$k] }}</div>
            <div class="lbl">{{ $k }}</div>
        </div>
        @endforeach
    </div>

    {{-- Inventory Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:28px">No</th>
                <th style="width:90px">Kode Barang</th>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th style="width:45px; text-align:center">Jml</th>
                <th style="width:40px">Satuan</th>
                <th style="width:65px; text-align:center">Kondisi</th>
                <th>Lokasi</th>
                <th style="width:50px">Th. Beli</th>
                <th style="width:90px; text-align:right">Harga Satuan</th>
                <th style="width:95px; text-align:right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($byKategori as $kategori => $items)
                <tr class="cat-row">
                    <td colspan="11">▶ {{ strtoupper($kategori) }} &nbsp;({{ $items->count() }} item)</td>
                </tr>
                @foreach($items as $b)
                @php
                    $badgeClass = match($b->kondisi) {
                        'Baik'        => 'badge-baik',
                        'Perawatan'   => 'badge-perawatan',
                        'Perbaikan'   => 'badge-perbaikan',
                        'Rusak Berat' => 'badge-rusak',
                        default       => 'badge-hilang',
                    };
                @endphp
                <tr>
                    <td style="text-align:center; color:#94a3b8">{{ $no++ }}</td>
                    <td><span class="kode">{{ $b->kode_barang }}</span></td>
                    <td><strong>{{ $b->nama_barang }}</strong></td>
                    <td style="color:#64748b">{{ $b->merk ?? '—' }}</td>
                    <td style="text-align:center; font-weight:700">{{ $b->jumlah }}</td>
                    <td style="color:#64748b">{{ $b->satuan }}</td>
                    <td style="text-align:center"><span class="badge {{ $badgeClass }}">{{ $b->kondisi }}</span></td>
                    <td style="color:#64748b; font-size:8.5px">{{ $b->lokasi ?? '—' }}</td>
                    <td style="text-align:center">{{ $b->tahun_pembelian ?? '—' }}</td>
                    <td style="text-align:right; font-family:'Courier New',monospace">
                        {{ $b->harga ? 'Rp '.number_format($b->harga, 0, ',', '.') : '—' }}
                    </td>
                    <td style="text-align:right; font-family:'Courier New',monospace; font-weight:700">
                        {{ $b->harga ? 'Rp '.number_format($b->harga * $b->jumlah, 0, ',', '.') : '—' }}
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOTAL KESELURUHAN</td>
                <td style="text-align:center">{{ $totalUnit }}</td>
                <td colspan="5"></td>
                <td style="text-align:right">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Signature / Tanda Mengetahui --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="sig-title">Dibuat oleh,</div>
            <div class="sig-role">Admin Laboratorium</div>
            <div class="signature-space"></div>
            <div class="sig-name">( __________________________ )</div>
            <div class="sig-nip">NIP. ____________________</div>
        </div>
        <div class="signature-box" style="visibility:hidden; flex:1;"></div>
        <div class="signature-box">
            <div class="sig-title">Mengetahui,</div>
            @if(isset($kepalaLab[$activeLab]))
                <div class="sig-role">{{ $kepalaLab[$activeLab]['jabatan'] }}</div>
                <div class="signature-space"></div>
                <div class="sig-name">( {{ $kepalaLab[$activeLab]['nama'] }} )</div>
                <div class="sig-nip">NIP. {{ $kepalaLab[$activeLab]['nip'] }}</div>
            @else
                <div class="sig-role">Kepala Laboratorium</div>
                <div class="signature-space"></div>
                <div class="sig-name">( __________________________ )</div>
                <div class="sig-nip">NIP. ____________________</div>
            @endif
        </div>
    </div>

</div>
</body>
</html>

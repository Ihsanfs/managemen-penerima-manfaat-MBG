<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penerima Manfaat - {{ $profile->nama }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 11mm 11mm 13mm 11mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8.4pt;
            line-height: 1.4;
            color: #1f2937;
            margin: 0;
        }

        .head {
            border-bottom: 2pt solid #059669;
            padding-bottom: 5pt;
            margin-bottom: 8pt;
        }

        .org {
            font-size: 13pt;
            font-weight: bold;
            color: #065f46;
        }

        .addr {
            font-size: 7.6pt;
            color: #6b7280;
            margin-top: 1pt;
        }

        .doc-title {
            font-size: 11.5pt;
            font-weight: bold;
            color: #111827;
            margin: 0 0 2pt;
        }

        .doc-meta {
            font-size: 8pt;
            color: #4b5563;
            margin-bottom: 9pt;
        }

        .doc-meta strong {
            color: #065f46;
        }

        .badge {
            display: inline-block;
            padding: 1pt 5pt;
            border-radius: 8pt;
            font-size: 6.8pt;
            font-weight: bold;
            background: #ecfdf5;
            color: #047857;
        }

        .badge-warn {
            background: #fffbeb;
            color: #b45309;
        }

        h2 {
            font-size: 9.3pt;
            color: #065f46;
            margin: 8pt 0 4pt;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8pt;
        }

        table.data thead {
            display: table-header-group;
        }

        table.data th {
            background: #ecfdf5;
            color: #065f46;
            border: 0.6pt solid #a7f3d0;
            padding: 3.2pt 3pt;
            text-align: center;
            font-size: 7.2pt;
            font-weight: bold;
        }

        table.data th.left {
            text-align: left;
        }

        table.data td {
            border: 0.6pt solid #e5e7eb;
            padding: 3pt;
            text-align: center;
            font-size: 7.6pt;
            vertical-align: middle;
        }

        table.data td.left {
            text-align: left;
        }

        table.data tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        table.data tr {
            page-break-inside: avoid;
        }

        table.data tfoot td {
            background: #ecfdf5;
            border-top: 1.2pt solid #059669;
            color: #065f46;
            font-weight: bold;
        }

        .name {
            font-weight: bold;
            color: #111827;
        }

        .tag {
            display: inline-block;
            margin-left: 3pt;
            padding: 0.5pt 4pt;
            border-radius: 7pt;
            font-size: 6.3pt;
            font-weight: bold;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .tag-pos {
            background: #ecfdf5;
            color: #047857;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2pt;
        }

        .summary td {
            border: 0.6pt solid #e5e7eb;
            padding: 3.2pt 5pt;
            font-size: 7.8pt;
            width: 33.33%;
        }

        .summary .label {
            color: #4b5563;
        }

        .summary .value {
            float: right;
            font-weight: bold;
            color: #111827;
        }

        .summary .total td {
            background: #065f46;
            color: #ffffff;
            border-color: #065f46;
            font-weight: bold;
        }

        .summary .total .label,
        .summary .total .value {
            color: #ffffff;
        }

        .alert {
            border-left: 3pt solid #f59e0b;
            background: #fffbeb;
            color: #92400e;
            padding: 5pt 7pt;
            margin-bottom: 7pt;
            font-size: 7.8pt;
        }

        .alert-success {
            border-left-color: #10b981;
            background: #ecfdf5;
            color: #065f46;
        }

        .alert-danger {
            border-left-color: #ef4444;
            background: #fef2f2;
            color: #991b1b;
        }

        .legend {
            margin-top: 3pt;
            font-size: 6.9pt;
            color: #6b7280;
        }

        .footer {
            margin-top: 10pt;
            border-top: 0.6pt solid #d1d5db;
            padding-top: 4pt;
            font-size: 6.9pt;
            color: #9ca3af;
        }

        .footer .right {
            float: right;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    @php
        $totalSekolah = collect($sekolah)->sum('total');
        $totalPosyandu = collect($posyandu)->sum('total');
        $total = $data['snapshot']['total'];
        $isArsip = ! $tanggal->isToday();
    @endphp

    <div class="head">
        <div class="org">{{ $profile->nama }}</div>
        <div class="addr">
            @if ($profile->alamat)
                {{ $profile->alamat }}
            @endif
            @if ($profile->kecamatan)
                - Kec. {{ $profile->kecamatan }}
            @endif
            @if ($profile->kabupaten)
                , {{ $profile->kabupaten }}
            @endif
            @if ($profile->provinsi)
                , {{ $profile->provinsi }}
            @endif
            @if ($profile->telepon)
                | Telp. {{ $profile->telepon }}
            @endif
        </div>
    </div>

    <div class="doc-title">Laporan Data Penerima Manfaat</div>
    <div class="doc-meta">
        <strong>{{ $tanggal->locale('id')->translatedFormat('l, d F Y') }}</strong>
        &nbsp;|&nbsp; Status data:
        @if ($isArsip)
            <span class="badge badge-warn">ARSIP</span>
        @else
            <span class="badge">LIVE</span>
        @endif
        &nbsp;|&nbsp; Total: <strong>{{ $total }} orang</strong>
        &nbsp;|&nbsp; L: <strong>{{ $jmlLaki }}</strong> / P: <strong>{{ $jmlPerempuan }}</strong>
        &nbsp;|&nbsp; {{ collect($sekolah)->count() }} sekolah &amp; {{ collect($posyandu)->count() }} posyandu
    </div>

    @if ($data['noData'])
        <div class="alert alert-danger">
            Belum ada data tersimpan untuk tanggal ini dan tidak ada data sebelumnya yang dapat ditampilkan.
        </div>
    @elseif ($data['fallback'])
        <div class="alert">
            Tidak ada data tersimpan untuk tanggal {{ $tanggal->locale('id')->translatedFormat('d F Y') }}.
            Data yang ditampilkan berasal dari arsip terakhir tanggal
            {{ $data['fallbackDate']->locale('id')->translatedFormat('d F Y') }}.
        </div>
    @elseif ($isArsip && $data['report'])
        <div class="alert alert-success">
            Data arsip tersimpan sebagai {{ $data['report']->sumber_label }}
            pada {{ $data['report']->saved_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }} WIB.
        </div>
    @endif

    <h2>1. Rekap Sekolah</h2>
    <table class="data">
        <thead>
            <tr>
                <th style="width:7mm">No</th>
                <th class="left" style="width:78mm">Nama Sekolah</th>
                <th style="width:14mm">TK/PAUD</th>
                <th style="width:12mm">K1</th>
                <th style="width:12mm">K2</th>
                <th style="width:12mm">K3</th>
                <th style="width:12mm">K4</th>
                <th style="width:12mm">K5</th>
                <th style="width:12mm">K6</th>
                <th style="width:12mm">SMK</th>
                <th style="width:12mm">Guru</th>
                <th style="width:15mm">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sekolah as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="left">
                        <span class="name">{{ $row['nama'] }}</span>
                        @if ($row['jenjang'])
                            <span class="tag">{{ \App\Models\Institution::JENJANG[$row['jenjang']] ?? $row['jenjang'] }}</span>
                        @endif
                    </td>
                    <td>{{ $row['jenjang'] === 'tk_paud' ? ($row['rincian']['tk_paud'] ?: '-') : '-' }}</td>
                    @foreach ([1, 2, 3, 4, 5, 6] as $kelas)
                        <td>{{ $row['jenjang'] === 'sd' ? ($row['rincian']['kelas_' . $kelas] ?: '-') : '-' }}</td>
                    @endforeach
                    <td>{{ $row['jenjang'] === 'smk' ? ($row['rincian']['smk'] ?: '-') : '-' }}</td>
                    <td>{{ $row['rincian']['guru'] ?: '-' }}</td>
                    <td><strong>{{ $row['total'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="left">Belum ada data sekolah.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="left">JUMLAH SEKOLAH</td>
                <td>{{ collect($sekolah)->sum(fn ($row) => $row['rincian']['tk_paud']) ?: '-' }}</td>
                @foreach ([1, 2, 3, 4, 5, 6] as $kelas)
                    <td>{{ collect($sekolah)->sum(fn ($row) => $row['rincian']['kelas_' . $kelas]) ?: '-' }}</td>
                @endforeach
                <td>{{ collect($sekolah)->sum(fn ($row) => $row['rincian']['smk']) ?: '-' }}</td>
                <td>{{ collect($sekolah)->sum(fn ($row) => $row['rincian']['guru']) ?: '-' }}</td>
                <td>{{ $totalSekolah }}</td>
            </tr>
        </tfoot>
    </table>

    <h2>2. Rekap Posyandu</h2>
    <table class="data">
        <thead>
            <tr>
                <th style="width:7mm">No</th>
                <th class="left" style="width:150mm">Nama Posyandu</th>
                <th style="width:18mm">Balita</th>
                <th style="width:18mm">Bumil</th>
                <th style="width:18mm">Busui</th>
                <th style="width:18mm">Kader</th>
                <th style="width:20mm">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posyandu as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="left">
                        <span class="name">{{ $row['nama'] }}</span>
                        <span class="tag tag-pos">Posyandu</span>
                    </td>
                    <td>{{ $row['rincian']['balita'] ?: '-' }}</td>
                    <td>{{ $row['rincian']['bumil'] ?: '-' }}</td>
                    <td>{{ $row['rincian']['busui'] ?: '-' }}</td>
                    <td>{{ $row['rincian']['kader'] ?: '-' }}</td>
                    <td><strong>{{ $row['total'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="left">Belum ada data posyandu.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="left">JUMLAH POSYANDU</td>
                <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['balita']) ?: '-' }}</td>
                <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['bumil']) ?: '-' }}</td>
                <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['busui']) ?: '-' }}</td>
                <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['kader']) ?: '-' }}</td>
                <td>{{ $totalPosyandu }}</td>
            </tr>
        </tfoot>
    </table>

    <h2>3. Rekap Kategori</h2>
    <table class="summary">
        @php
            $kategoriJson = $data['snapshot']['kategori_json'] ?? [];
            $grupUrutan = \App\Services\DailyReportService::GRUP_URUTAN;
            $grupLabels = \App\Models\Category::grupLabels();
        @endphp
        @foreach (array_chunk($grupUrutan, 3) as $baris)
            <tr>
                @foreach ($baris as $grupKey)
                    @php
                        $nilai = isset($kategoriJson[$grupKey]) ? (int) $kategoriJson[$grupKey] : 0;
                        $label = $grupLabels[$grupKey] ?? $grupKey;
                    @endphp
                    <td>
                        <span class="label">{{ $label }}</span><span class="value">{{ $nilai }}</span>
                    </td>
                @endforeach
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="2"><span class="label">TOTAL PENERIMA MANFAAT</span></td>
            <td><span class="label">Jumlah</span><span class="value">{{ $total }} orang</span></td>
        </tr>
    </table>

    <div class="legend">
        Keterangan: tanda "-" berarti tidak ada penerima pada kategori tersebut.
        Data LIVE dihitung dari penerima aktif, sedangkan data ARSIP berasal dari snapshot tanggal terkait.
    </div>

    <div class="footer">
        Dicetak {{ $dicetak }} oleh {{ auth()->user()?->name ?? 'Sistem' }}.
        <span class="right">{{ $profile->nama }}</span>
    </div>
</body>
</html>

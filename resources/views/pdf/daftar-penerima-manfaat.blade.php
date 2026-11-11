<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Penerima Manfaat - {{ $profile->nama }}</title>
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
        }

        .badge-l {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .badge-p {
            background: #fdf2f8;
            color: #be185d;
        }

        .badge-aktif {
            background: #ecfdf5;
            color: #047857;
        }

        .badge-non_aktif {
            background: #fef2f2;
            color: #991b1b;
        }

        .badge-pindah {
            background: #fffbeb;
            color: #b45309;
        }

        .badge-kategori {
            background: #f0f9ff;
            color: #0369a1;
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

        .summary-box {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8pt;
        }

        .summary-box td {
            border: 0.6pt solid #e5e7eb;
            padding: 4pt 6pt;
            font-size: 8pt;
        }

        .summary-box .label {
            color: #4b5563;
        }

        .summary-box .value {
            font-weight: bold;
            color: #111827;
            text-align: right;
        }

        .summary-box .total td {
            background: #065f46;
            color: #ffffff;
            border-color: #065f46;
            font-weight: bold;
        }

        .summary-box .total .label,
        .summary-box .total .value {
            color: #ffffff;
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

    <div class="doc-title">Daftar Penerima Manfaat</div>
    <div class="doc-meta">
        Dicetak pada <strong>{{ $dicetak }}</strong>
        &nbsp;|&nbsp; Total: <strong>{{ $total }} orang</strong>
        &nbsp;|&nbsp; L: <strong>{{ $jmlLaki }}</strong> / P: <strong>{{ $jmlPerempuan }}</strong>
        @if ($filterLabel)
            &nbsp;|&nbsp; Filter: <strong>{{ $filterLabel }}</strong>
        @endif
    </div>

    @foreach ($groups as $groupLabel => $items)
        @if (!$loop->first)
            {{-- Tambahkan sedikit jarak antar grup --}}
        @endif
        <h2>{{ $groupLabel }} ({{ $items->count() }} orang)</h2>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:7mm">No</th>
                    <th class="left" style="width:65mm">Nama</th>
                    <th style="width:30mm">NIK</th>
                    <th style="width:20mm">NISN</th>
                    <th style="width:12mm">L/P</th>
                    <th style="width:25mm">Kategori</th>
                    <th style="width:15mm">Kelas</th>
                    <th style="width:20mm">Status</th>
                    <th class="left" style="width:45mm">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $b)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="left"><span class="name">{{ $b->nama }}</span></td>
                        <td>{{ $b->nik ?? '-' }}</td>
                        <td>{{ $b->nisn ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $b->jenis_kelamin === 'L' ? 'badge-l' : 'badge-p' }}">
                                {{ $b->jenis_kelamin }}
                            </span>
                        </td>
                        <td><span class="badge badge-kategori">{{ $b->kategori_label }}</span></td>
                        <td>{{ $b->kelas_label }}</td>
                        <td>
                            <span class="badge badge-{{ $b->status }}">
                                {{ $b->status_label }}
                            </span>
                        </td>
                        <td class="left">{{ $b->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="left">Subtotal {{ $groupLabel }}</td>
                    <td colspan="2">
                        L: {{ $items->where('jenis_kelamin', 'L')->count() }}
                        / P: {{ $items->where('jenis_kelamin', 'P')->count() }}
                    </td>
                    <td colspan="3"><strong>{{ $items->count() }} orang</strong></td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    <h2>Rekap Jenis Kelamin</h2>
    <table class="summary-box">
        <tr>
            <td style="width:50%"><span class="label">Laki-laki</span><span class="value" style="float:right">{{ $jmlLaki }}</span></td>
            <td style="width:50%"><span class="label">Perempuan</span><span class="value" style="float:right">{{ $jmlPerempuan }}</span></td>
        </tr>
        <tr class="total">
            <td colspan="2"><span class="label">TOTAL PENERIMA MANFAAT</span><span class="value" style="float:right">{{ $total }} orang</span></td>
        </tr>
    </table>

    <div class="legend">
        Keterangan: L = Laki-laki, P = Perempuan. Data dikelompokkan berdasarkan lembaga.
    </div>

    <div class="footer">
        Dicetak {{ $dicetak }} oleh {{ auth()->user()?->name ?? 'Sistem' }}.
        <span class="right">{{ $profile->nama }}</span>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mingguan - {{ $profile->nama }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 12mm 14mm 12mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #1f2937;
            margin: 0;
        }
        .head {
            border-bottom: 2pt solid #059669;
            padding-bottom: 6pt;
            margin-bottom: 10pt;
        }
        .org { font-size: 13pt; font-weight: bold; color: #065f46; }
        .addr { font-size: 7.6pt; color: #6b7280; margin-top: 2pt; }
        .doc-title { font-size: 12pt; font-weight: bold; color: #111827; margin: 0 0 3pt; }
        .doc-meta { font-size: 8.2pt; color: #4b5563; margin-bottom: 10pt; }
        .doc-meta strong { color: #065f46; }
        h2 { font-size: 9.5pt; color: #065f46; margin: 10pt 0 5pt; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10pt; }
        table.data thead { display: table-header-group; }
        table.data th {
            background: #ecfdf5;
            color: #065f46;
            border: 0.6pt solid #a7f3d0;
            padding: 4pt;
            text-align: left;
            font-size: 7.8pt;
            font-weight: bold;
        }
        table.data th.right { text-align: right; }
        table.data td {
            border: 0.6pt solid #e5e7eb;
            padding: 4pt;
            font-size: 8pt;
            vertical-align: middle;
        }
        table.data td.right { text-align: right; }
        table.data td.center { text-align: center; }
        table.data tbody tr:nth-child(even) td { background: #f9fafb; }
        table.data tr { page-break-inside: avoid; }
        .badge {
            display: inline-block;
            padding: 1pt 5pt;
            border-radius: 8pt;
            font-size: 7pt;
            font-weight: bold;
            background: #ecfdf5;
            color: #047857;
        }
        .badge-warn {
            background: #fffbeb;
            color: #b45309;
        }
        .stats {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }
        .stats td {
            border: 0.6pt solid #e5e7eb;
            padding: 6pt;
            width: 33.33%;
            background: #f9fafb;
        }
        .stats .label { font-size: 7.2pt; color: #6b7280; font-weight: bold; text-transform: uppercase; }
        .stats .value { font-size: 13pt; font-weight: bold; color: #111827; margin-top: 2pt; }
        .footer {
            margin-top: 15pt;
            border-top: 0.6pt solid #d1d5db;
            padding-top: 5pt;
            font-size: 7pt;
            color: #9ca3af;
        }
        .footer .right { float: right; }
    </style>
</head>
<body>
    <div class="head">
        <div class="org">{{ $profile->nama }}</div>
        <div class="addr">
            @if ($profile->alamat) {{ $profile->alamat }} @endif
            @if ($profile->kecamatan) - Kec. {{ $profile->kecamatan }} @endif
            @if ($profile->kabupaten) , {{ $profile->kabupaten }} @endif
            @if ($profile->provinsi) , {{ $profile->provinsi }} @endif
        </div>
    </div>

    <div class="doc-title">Laporan Mingguan Penerima Manfaat</div>
    <div class="doc-meta">
        Periode: <strong>{{ $mingguMulai->locale('id')->translatedFormat('d M Y') }} &ndash; {{ $akhir_minggu->locale('id')->translatedFormat('d M Y') }}</strong>
    </div>

    <table class="stats">
        <tr>
            <td>
                <div class="label">Hari Sudah Diupdate</div>
                <div class="value">{{ $sudah_update }} / 5</div>
            </td>
            <td>
                <div class="label">Hari Otomatis</div>
                <div class="value">{{ $otomatis }} / 5</div>
            </td>
            <td>
                <div class="label">Rata-rata Total PM</div>
                <div class="value">
                    {{ collect($days)->whereNotNull('total')->avg('total') ? round(collect($days)->whereNotNull('total')->avg('total')) : 0 }}
                </div>
            </td>
        </tr>
    </table>

    <h2>1. Status Senin &ndash; Jumat</h2>
    <table class="data">
        <thead>
            <tr>
                <th style="width:25%">Hari &amp; Tanggal</th>
                <th style="width:20%">Status</th>
                <th style="width:25%">Sumber</th>
                <th class="right" style="width:30%">Total Penerima Manfaat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($days as $day)
                <tr>
                    <td>
                        <strong>{{ $day['hari'] }}</strong><br>
                        <span style="color: #6b7280; font-size: 7.2pt;">{{ $day['tanggal']->locale('id')->translatedFormat('d M Y') }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $day['sumber'] === 'manual' ? '' : ($day['sumber'] === 'otomatis' ? 'badge-warn' : '') }}">
                            {{ $day['status'] }}
                        </span>
                    </td>
                    <td>{{ $day['sumber'] ? ucfirst($day['sumber']) : '-' }}</td>
                    <td class="right"><strong>{{ $day['total'] ?? '—' }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>2. 20 Hari Kerja Terakhir</h2>
    <table class="data">
        <thead>
            <tr>
                <th style="width:25%">Tanggal</th>
                <th style="width:20%">Hari</th>
                <th style="width:25%">Status</th>
                <th class="right" style="width:15%">Total PM</th>
                <th class="right" style="width:15%">Perubahan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($workdays as $workday)
                <tr>
                    <td>{{ $workday['tanggal']->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ $workday['hari'] }}</td>
                    <td>
                        <span class="badge {{ $workday['sumber'] === 'manual' ? '' : 'badge-warn' }}">
                            {{ $workday['status'] }}
                        </span>
                    </td>
                    <td class="right"><strong>{{ $workday['total'] }}</strong></td>
                    <td class="right">
                        @if ($workday['perubahan'] === null)
                            &mdash;
                        @elseif ($workday['perubahan'] > 0)
                            <span style="color: #047857; font-weight: bold;">+{{ $workday['perubahan'] }}</span>
                        @elseif ($workday['perubahan'] < 0)
                            <span style="color: #b91c1c; font-weight: bold;">{{ $workday['perubahan'] }}</span>
                        @else
                            <span style="color: #6b7280;">0</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak {{ $dicetak }} oleh {{ auth()->user()?->name ?? 'Sistem' }}.
        <span class="right">{{ $profile->nama }}</span>
    </div>
</body>
</html>

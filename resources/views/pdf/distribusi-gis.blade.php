<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta & Distribusi SPPG - {{ $profile->nama }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 8.5pt; line-height: 1.4; color: #1f2937; margin: 0; }
        .head { border-bottom: 2pt solid #059669; padding-bottom: 6pt; margin-bottom: 10pt; }
        .org { font-size: 13pt; font-weight: bold; color: #065f46; }
        .addr { font-size: 7.6pt; color: #6b7280; margin-top: 2pt; }
        .doc-title { font-size: 12pt; font-weight: bold; color: #111827; margin: 0 0 3pt; }
        .doc-meta { font-size: 8.2pt; color: #4b5563; margin-bottom: 10pt; }
        h2 { font-size: 9.5pt; color: #065f46; margin: 10pt 0 5pt; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10pt; }
        table.data thead { display: table-header-group; }
        table.data th { background: #ecfdf5; color: #065f46; border: 0.6pt solid #a7f3d0; padding: 4pt; text-align: left; font-size: 7.8pt; font-weight: bold; }
        table.data th.right { text-align: right; }
        table.data td { border: 0.6pt solid #e5e7eb; padding: 4pt; font-size: 8pt; vertical-align: middle; }
        table.data td.right { text-align: right; }
        table.data td.center { text-align: center; }
        table.data tbody tr:nth-child(even) td { background: #f9fafb; }
        table.data tr { page-break-inside: avoid; }
        .stats { width: 100%; border-collapse: collapse; margin-bottom: 10pt; }
        .stats td { border: 0.6pt solid #e5e7eb; padding: 6pt; width: 25%; background: #f9fafb; }
        .stats .label { font-size: 7.0pt; color: #6b7280; font-weight: bold; text-transform: uppercase; }
        .stats .value { font-size: 11pt; font-weight: bold; color: #111827; margin-top: 2pt; }
        .footer { margin-top: 15pt; border-top: 0.6pt solid #d1d5db; padding-top: 5pt; font-size: 7pt; color: #9ca3af; }
        .footer .right { float: right; }
    </style>
</head>
<body>
    <div class="head">
        <div class="org">{{ $profile->nama }}</div>
        <div class="addr">
            Pusat SPPG: {{ $profile->alamat ?? 'Samarang, Garut' }} (Lat: {{ $profile->latitude ?? '-' }}, Lng: {{ $profile->longitude ?? '-' }})
        </div>
    </div>

    <div class="doc-title">Laporan Peta &amp; Jalur Distribusi Pengantaran SPPG</div>
    <div class="doc-meta">
        Dicetak pada: <strong>{{ $dicetak }}</strong>
    </div>

    <table class="stats">
        <tr>
            <td>
                <div class="label">Total Lembaga</div>
                <div class="value">{{ $totalInstitutions }}</div>
            </td>
            <td>
                <div class="label">Total Penerima</div>
                <div class="value">{{ $totalBeneficiaries }}</div>
            </td>
            <td>
                <div class="label">Jarak Terjauh</div>
                <div class="value">{{ $maxDistance }} KM</div>
            </td>
            <td>
                <div class="label">Rata-rata Jarak</div>
                <div class="value">{{ $avgDistance }} KM</div>
            </td>
        </tr>
    </table>

    <h2>Daftar Jarak &amp; Rute Cabang Pengantaran</h2>
    <table class="data">
        <thead>
            <tr>
                <th style="width:7mm">No</th>
                <th style="width:48mm">Nama Lembaga</th>
                <th style="width:18mm">Tipe</th>
                <th style="width:35mm">Koordinat</th>
                <th class="right" style="width:22mm">Jarak (KM)</th>
                <th class="right" style="width:25mm">Penerima Aktif</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($institutions as $inst)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $inst['nama'] }}</strong>
                        @if ($inst['jenjang'])
                            <br><span style="color:#6b7280;font-size:7pt;">{{ $inst['jenjang'] }}</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($inst['tipe']) }}</td>
                    <td>
                        @if ($inst['latitude'] && $inst['longitude'])
                            {{ $inst['latitude'] }}, {{ $inst['longitude'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="right"><strong>{{ $inst['distance'] !== null ? $inst['distance'] . ' KM' : '-' }}</strong></td>
                    <td class="right"><strong>{{ $inst['beneficiaries_count'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Belum ada data lembaga.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen resmi jalur distribusi pengantaran SPPG.
        <span class="right">{{ $profile->nama }}</span>
    </div>
</body>
</html>

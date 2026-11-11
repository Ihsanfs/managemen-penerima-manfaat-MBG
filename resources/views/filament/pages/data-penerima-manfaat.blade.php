<x-filament-panels::page>
    @php
        $kategoriLabels = \App\Models\DailyReport::KATEGORI;
        $totalSekolah = collect($sekolah)->sum('total');
        $totalPosyandu = collect($posyandu)->sum('total');
    @endphp

    <style>
        .pm-wrap { display: grid; gap: 1.25rem; }
        @media (min-width: 1280px) {
            .pm-wrap { grid-template-columns: minmax(0, 3fr) minmax(320px, 1.15fr); align-items: start; }
        }
        .pm-stack { display: grid; gap: 1.25rem; }
        .pm-card { background: #fff; border-radius: .75rem; box-shadow: 0 0 0 1px rgba(17,24,39,.06), 0 1px 2px rgba(17,24,39,.06); overflow: hidden; }
        .dark .pm-card { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgba(255,255,255,.1); }
        .pm-card-header { padding: .8rem 1rem; border-bottom: 1px solid rgb(var(--gray-200)); display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .dark .pm-card-header { border-color: rgba(255,255,255,.1); }
        .pm-card-title { font-size: .875rem; font-weight: 700; color: rgb(var(--gray-950)); }
        .dark .pm-card-title { color: #fff; }
        .pm-card-body { padding: 1rem; }
        .pm-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
        .pm-table th { background: rgb(var(--gray-50)); padding: .5rem .45rem; text-align: center; font-weight: 700; color: rgb(var(--gray-600)); white-space: nowrap; border-bottom: 1px solid rgb(var(--gray-200)); }
        .dark .pm-table th { background: rgba(255,255,255,.04); color: rgb(var(--gray-300)); border-color: rgba(255,255,255,.1); }
        .pm-table td { padding: .48rem .45rem; text-align: center; border-bottom: 1px solid rgb(var(--gray-100)); color: rgb(var(--gray-700)); white-space: nowrap; }
        .dark .pm-table td { color: rgb(var(--gray-200)); border-color: rgba(255,255,255,.08); }
        .pm-table td.pm-left { text-align: left; }
        .pm-table tbody tr:hover td { background: rgb(var(--gray-50)); }
        .dark .pm-table tbody tr:hover td { background: rgba(255,255,255,.04); }
        .pm-table tfoot td { font-weight: 700; background: rgb(var(--gray-50)); color: rgb(var(--gray-950)); border-top: 2px solid rgb(var(--gray-200)); }
        .dark .pm-table tfoot td { background: rgba(255,255,255,.05); color: #fff; border-color: rgba(255,255,255,.15); }
        .pm-name { font-weight: 600; color: rgb(var(--gray-950)); }
        .dark .pm-name { color: #fff; }
        .pm-badge { display: inline-flex; align-items: center; border-radius: 9999px; padding: .1rem .45rem; font-size: .65rem; font-weight: 700; margin-left: .35rem; }
        .pm-badge-info { background: rgb(var(--info-50)); color: rgb(var(--info-700)); }
        .pm-badge-success { background: rgb(var(--success-50)); color: rgb(var(--success-700)); }
        .pm-badge-warning { background: rgb(var(--warning-50)); color: rgb(var(--warning-700)); }
        .dark .pm-badge-info { background: rgba(59,130,246,.2); color: #93c5fd; }
        .dark .pm-badge-success { background: rgba(34,197,94,.2); color: #86efac; }
        .dark .pm-badge-warning { background: rgba(245,158,11,.2); color: #fcd34d; }
        .pm-cal-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: .2rem; }
        .pm-cal-dow { text-align: center; font-size: .65rem; font-weight: 700; color: rgb(var(--gray-400)); padding: .25rem 0; }
        .pm-cal-day { position: relative; display: flex; align-items: center; justify-content: center; height: 2.1rem; width: 100%; border-radius: .5rem; font-size: .8rem; color: rgb(var(--gray-700)); border: 0; background: transparent; cursor: pointer; }
        .dark .pm-cal-day { color: rgb(var(--gray-200)); }
        .pm-cal-day:hover { background: rgb(var(--gray-100)); }
        .dark .pm-cal-day:hover { background: rgba(255,255,255,.1); }
        .pm-cal-day.is-today { box-shadow: inset 0 0 0 2px rgb(var(--primary-500)); color: rgb(var(--primary-600)); font-weight: 700; }
        .pm-cal-day.is-selected { background: rgb(var(--primary-600)); color: #fff; font-weight: 700; box-shadow: none; }
        .pm-cal-day.is-future { color: rgb(var(--gray-300)); cursor: not-allowed; }
        .dark .pm-cal-day.is-future { color: rgb(var(--gray-600)); }
        .pm-dot { position: absolute; bottom: .18rem; left: 50%; transform: translateX(-50%); height: .25rem; width: .25rem; border-radius: 9999px; background: rgb(var(--success-500)); }
        .pm-cal-day.is-selected .pm-dot { background: #fff; }
        .pm-summary-row { display: flex; align-items: center; justify-content: space-between; padding: .35rem 0; border-bottom: 1px dashed rgb(var(--gray-200)); font-size: .8rem; }
        .dark .pm-summary-row { border-color: rgba(255,255,255,.1); }
        .pm-summary-row:last-child { border-bottom: 0; }
        .pm-summary-label { color: rgb(var(--gray-600)); }
        .dark .pm-summary-label { color: rgb(var(--gray-300)); }
        .pm-summary-value { font-weight: 700; color: rgb(var(--gray-950)); font-variant-numeric: tabular-nums; }
        .dark .pm-summary-value { color: #fff; }
        .pm-total-box { display: flex; align-items: center; justify-content: space-between; margin-top: .6rem; padding: .65rem .8rem; border-radius: .5rem; background: rgb(var(--success-50)); }
        .dark .pm-total-box { background: rgba(34,197,94,.15); }
        .pm-total-box .label { font-size: .8rem; font-weight: 700; color: rgb(var(--success-800)); }
        .dark .pm-total-box .label { color: #86efac; }
        .pm-total-box .value { font-size: 1.4rem; font-weight: 800; color: rgb(var(--success-700)); }
        .dark .pm-total-box .value { color: #bbf7d0; }
        .pm-alert { border-radius: .6rem; padding: .7rem .9rem; font-size: .8rem; line-height: 1.4; }
        .pm-alert-warning { background: rgb(var(--warning-50)); color: rgb(var(--warning-800)); }
        .pm-alert-success { background: rgb(var(--success-50)); color: rgb(var(--success-800)); }
        .pm-alert-danger { background: rgb(var(--danger-50)); color: rgb(var(--danger-800)); }
        .dark .pm-alert-warning { background: rgba(245,158,11,.15); color: #fde68a; }
        .dark .pm-alert-success { background: rgba(34,197,94,.15); color: #bbf7d0; }
        .dark .pm-alert-danger { background: rgba(239,68,68,.15); color: #fecaca; }
        .pm-toolbar { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; }
        .pm-toolbar-actions { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
        .pm-title { font-size: 1.4rem; font-weight: 800; color: rgb(var(--gray-950)); letter-spacing: -.02em; }
        .dark .pm-title { color: #fff; }
        .pm-subtitle { margin-top: .2rem; font-size: .85rem; color: rgb(var(--gray-500)); }
        .dark .pm-subtitle { color: rgb(var(--gray-400)); }
        .pm-muted { color: rgb(var(--gray-500)); font-size: .72rem; line-height: 1.45; }
        .dark .pm-muted { color: rgb(var(--gray-400)); }
        .pm-table-scroll { overflow-x: auto; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            .fi-sidebar, .fi-topbar, .fi-header, .fi-page-header, .fi-breadcrumbs, .no-print { display: none !important; }
            .fi-main, .fi-page { padding: 0 !important; max-width: none !important; }
            .pm-card { box-shadow: none !important; border: 1px solid #d1d5db !important; break-inside: auto; }
            .pm-table-scroll { overflow: visible !important; }
            .pm-wrap { display: block; }
            .pm-stack { gap: .5rem; }
            body { background: #fff !important; }
            .pm-table { font-size: .68rem; width: 100% !important; }
            .pm-table th, .pm-table td { padding: .3rem .28rem; }
            .pm-table thead { display: table-header-group; }
            .pm-table tr { break-inside: avoid; }
        }
    </style>

    <div class="pm-stack">
        <div class="pm-toolbar">
            <div>
                <div class="pm-title">{{ $profile->nama }}</div>
                <div class="pm-subtitle">
                    {{ $tanggalAktif->locale('id')->translatedFormat('l, d F Y') }}
                    @if ($profile->kecamatan)
                        &middot; Kec. {{ $profile->kecamatan }}, {{ $profile->kabupaten }}
                    @endif
                </div>
            </div>

            <div class="pm-toolbar-actions no-print">
                <x-filament::badge :color="$isToday ? 'success' : 'warning'">
                    {{ $isToday ? 'LIVE' : 'ARSIP' }}
                </x-filament::badge>

                <x-filament::input.wrapper>
                    <x-filament::input
                        type="date"
                        wire:model.live="tanggal"
                        max="{{ today()->toDateString() }}"
                    />
                </x-filament::input.wrapper>

                <x-filament::button color="gray" icon="heroicon-o-arrow-uturn-left" wire:click="kembaliHariIni" size="sm">
                    Hari Ini
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    icon="heroicon-o-printer"
                    size="sm"
                    wire:click="cetakPdf"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="cetakPdf">Cetak PDF</span>
                    <span wire:loading wire:target="cetakPdf">Memproses...</span>
                </x-filament::button>
            </div>
        </div>

        @if ($data['noData'])
            <div class="pm-alert pm-alert-danger">
                Belum ada data tersimpan untuk tanggal ini dan tidak ada data sebelumnya yang dapat ditampilkan.
            </div>
        @elseif ($data['fallback'])
            <div class="pm-alert pm-alert-warning">
                Tidak ada data tersimpan untuk tanggal {{ $tanggalAktif->locale('id')->translatedFormat('d F Y') }}.
                Menampilkan data terakhir tanggal {{ $data['fallbackDate']->locale('id')->translatedFormat('d F Y') }} (carry-forward).
            </div>
        @elseif ($isToday && $savedToday)
            <div class="pm-alert pm-alert-success">
                Data hari ini sudah tersimpan sebagai <strong>{{ $savedToday->sumber_label }}</strong>
                pada {{ $savedToday->saved_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}.
                Menyimpan ulang akan memperbarui data (tidak membuat baris ganda).
            </div>
        @elseif ($isToday)
            <div class="pm-alert pm-alert-warning">
                Data hari ini belum disimpan ke riwayat. Periksa angka lalu klik <strong>Simpan Data Hari Ini</strong>.
            </div>
        @elseif ($data['report'])
            <div class="pm-alert pm-alert-success">
                Menampilkan data arsip tanggal {{ $tanggalAktif->locale('id')->translatedFormat('d F Y') }}
                yang tersimpan sebagai <strong>{{ $data['report']->sumber_label }}</strong>
                pada {{ $data['report']->saved_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}.
                Data arsip hanya untuk dilihat.
            </div>
        @endif

        <div class="pm-wrap">
            <div class="pm-stack">
                <div class="pm-card">
                    <div class="pm-card-header">
                        <div class="pm-card-title">Sekolah</div>
                        <div class="pm-muted">{{ $sekolah->count() }} lembaga &middot; {{ $totalSekolah }} penerima</div>
                    </div>
                    <div class="pm-table-scroll">
                        <table class="pm-table">
                            <thead>
                                <tr>
                                    <th style="width:2.2rem">No</th>
                                    <th style="text-align:left;min-width:12rem">Nama Sekolah</th>
                                    <th>TK/PAUD</th>
                                    <th>K1</th>
                                    <th>K2</th>
                                    <th>K3</th>
                                    <th>K4</th>
                                    <th>K5</th>
                                    <th>K6</th>
                                    <th>SMK</th>
                                    <th>Guru</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sekolah as $row)
                                    @php
                                        $encUrl = \App\Filament\Resources\BeneficiaryResource::getUrl('index', [
                                            'enc' => encrypt(json_encode(['institution_id' => $row['id']]))
                                        ]);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="pm-left">
                                            <a href="{{ $encUrl }}" class="pm-name" style="color: rgb(var(--primary-600)); text-decoration: none; cursor: pointer;" title="Klik untuk melihat daftar penerima manfaat lembaga ini" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                {{ $row['nama'] }}
                                            </a>
                                            @if ($row['jenjang'])
                                                <span class="pm-badge pm-badge-info">{{ \App\Models\Institution::JENJANG[$row['jenjang']] ?? $row['jenjang'] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['jenjang'] === 'tk_paud' ? ($row['rincian']['tk_paud'] ?: '-') : '-' }}</td>
                                        @foreach ([1, 2, 3, 4, 5, 6] as $kelas)
                                            <td>{{ $row['jenjang'] === 'sd' ? ($row['rincian']['kelas_' . $kelas] ?: '-') : '-' }}</td>
                                        @endforeach
                                        <td>{{ $row['jenjang'] === 'smk' ? ($row['rincian']['smk'] ?: '-') : '-' }}</td>
                                        <td>{{ $row['rincian']['guru'] ?: '-' }}</td>
                                        <td style="font-weight:800">{{ $row['total'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="12" class="pm-muted">Belum ada data sekolah.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="pm-left">JUMLAH SEKOLAH</td>
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
                    </div>
                </div>

                <div class="pm-card">
                    <div class="pm-card-header">
                        <div class="pm-card-title">Posyandu</div>
                        <div class="pm-muted">{{ $posyandu->count() }} posyandu &middot; {{ $totalPosyandu }} penerima</div>
                    </div>
                    <div class="pm-table-scroll">
                        <table class="pm-table">
                            <thead>
                                <tr>
                                    <th style="width:2.2rem">No</th>
                                    <th style="text-align:left;min-width:12rem">Nama Posyandu</th>
                                    <th>Balita</th>
                                    <th>Bumil</th>
                                    <th>Busui</th>
                                    <th>Kader</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posyandu as $row)
                                    @php
                                        $encUrl = \App\Filament\Resources\BeneficiaryResource::getUrl('index', [
                                            'enc' => encrypt(json_encode(['institution_id' => $row['id']]))
                                        ]);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="pm-left">
                                            <a href="{{ $encUrl }}" class="pm-name" style="color: rgb(var(--primary-600)); text-decoration: none; cursor: pointer;" title="Klik untuk melihat daftar penerima manfaat lembaga ini" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                {{ $row['nama'] }}
                                            </a>
                                            <span class="pm-badge pm-badge-success">Posyandu</span>
                                        </td>
                                        <td>{{ $row['rincian']['balita'] ?: '-' }}</td>
                                        <td>{{ $row['rincian']['bumil'] ?: '-' }}</td>
                                        <td>{{ $row['rincian']['busui'] ?: '-' }}</td>
                                        <td>{{ $row['rincian']['kader'] ?: '-' }}</td>
                                        <td style="font-weight:800">{{ $row['total'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="pm-muted">Belum ada data posyandu.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="pm-left">JUMLAH POSYANDU</td>
                                    <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['balita']) ?: '-' }}</td>
                                    <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['bumil']) ?: '-' }}</td>
                                    <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['busui']) ?: '-' }}</td>
                                    <td>{{ collect($posyandu)->sum(fn ($row) => $row['rincian']['kader']) ?: '-' }}</td>
                                    <td>{{ $totalPosyandu }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="pm-card">
                    <div class="pm-card-body" style="display:flex;align-items:center;justify-content:space-between;gap:1rem">
                        <div>
                            <div class="pm-muted">TOTAL PENERIMA MANFAAT</div>
                            <div style="font-size:1.6rem;font-weight:800;color:rgb(var(--gray-950))" class="pm-name">
                                {{ $data['snapshot']['total'] }} orang
                            </div>
                        </div>
                        <div class="pm-muted" style="text-align:right">
                            {{ collect($institusi)->count() }} lembaga<br>
                            {{ $tanggalAktif->locale('id')->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>

                <div class="pm-card">
                    <div class="pm-card-header">
                        <div class="pm-card-title">Jenis Kelamin (Aktif)</div>
                        <div class="pm-muted">{{ $tanggalAktif->locale('id')->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="pm-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;text-align:center">
                        <div style="border:1px solid #e5e7eb;border-radius:.5rem;padding:.75rem">
                            <div class="pm-muted" style="font-size:.7rem">Laki-laki</div>
                            <div style="font-size:1.5rem;font-weight:800;color:#2563eb">{{ $jmlLaki }}</div>
                        </div>
                        <div style="border:1px solid #e5e7eb;border-radius:.5rem;padding:.75rem">
                            <div class="pm-muted" style="font-size:.7rem">Perempuan</div>
                            <div style="font-size:1.5rem;font-weight:800;color:#be185d">{{ $jmlPerempuan }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pm-stack">
                <div class="pm-card no-print">
                    <div class="pm-card-header">
                        <div class="pm-card-title">Kalender</div>
                        <div class="no-print" style="display:flex;gap:.25rem">
                            <x-filament::button color="gray" size="xs" wire:click="geserBulan(-1)" icon="heroicon-m-chevron-left" />
                            <x-filament::button color="gray" size="xs" wire:click="geserBulan(1)" icon="heroicon-m-chevron-right" />
                        </div>
                    </div>
                    <div class="pm-card-body">
                        <div style="text-align:center;font-weight:700;font-size:.85rem;margin-bottom:.5rem">{{ $calendar['label'] }}</div>
                        <div class="pm-cal-grid">
                            @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dow)
                                <div class="pm-cal-dow">{{ $dow }}</div>
                            @endforeach

                            @foreach ($calendar['weeks'] as $week)
                                @foreach ($week as $cell)
                                    @if ($cell === null)
                                        <div></div>
                                    @else
                                        <button
                                            type="button"
                                            class="pm-cal-day {{ $cell['is_selected'] ? 'is-selected' : '' }} {{ $cell['is_today'] ? 'is-today' : '' }} {{ $cell['is_future'] ? 'is-future' : '' }}"
                                            @if (! $cell['is_future']) wire:click="pilihTanggal('{{ $cell['tanggal'] }}')" @endif
                                            @if ($cell['is_future']) disabled @endif
                                        >
                                            {{ $cell['hari'] }}
                                            @if ($cell['has_report'])
                                                <span class="pm-dot"></span>
                                            @endif
                                        </button>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                        <div style="display:flex;align-items:center;gap:1rem;margin-top:.75rem;font-size:.65rem;color:rgb(var(--gray-500))">
                            <span style="display:inline-flex;align-items:center;gap:.25rem"><span style="height:.35rem;width:.35rem;border-radius:9999px;background:rgb(var(--success-500))"></span> Tersimpan</span>
                            <span style="display:inline-flex;align-items:center;gap:.25rem"><span style="height:.55rem;width:.55rem;border-radius:9999px;border:2px solid rgb(var(--primary-500))"></span> Hari ini</span>
                        </div>
                    </div>
                </div>

                <div class="pm-card">
                    <div class="pm-card-header">
                        <div class="pm-card-title">Rekap Kategori</div>
                        <div class="pm-muted">{{ $tanggalAktif->locale('id')->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="pm-card-body">
                        @php
                            $kategoriJson = $data['snapshot']['kategori_json'] ?? [];
                            $kategoriTampil = [];
                            foreach (\App\Services\DailyReportService::GRUP_URUTAN as $grupKey) {
                                if (! isset($kategoriJson[$grupKey])) {
                                    continue;
                                }
                                $kategoriTampil[$grupKey] = [
                                    'label' => \App\Models\Category::grupLabels()[$grupKey] ?? $grupKey,
                                    'nilai' => (int) $kategoriJson[$grupKey],
                                ];
                            }
                            if ($kategoriTampil === []) {
                                foreach ($kategoriLabels as $key => $label) {
                                    $kategoriTampil[$key] = ['label' => $label, 'nilai' => (int) ($totals[$key] ?? 0)];
                                }
                            }
                        @endphp
                        @foreach ($kategoriTampil as $grupKey => $item)
                            <div class="pm-summary-row">
                                <span class="pm-summary-label">{{ $item['label'] }}</span>
                                <span class="pm-summary-value">{{ $item['nilai'] }}</span>
                            </div>
                        @endforeach

                        <div class="pm-total-box">
                            <span class="label">TOTAL</span>
                            <span class="value">{{ $data['snapshot']['total'] }}</span>
                        </div>

                        <div class="no-print" style="margin-top:1rem;display:grid;gap:.5rem">
                            <x-filament::button
                                color="success"
                                icon="heroicon-o-check-circle"
                                wire:click="simpanHariIni"
                                wire:confirm="Simpan data penerima manfaat hari ini? Total {{ $data['snapshot']['total'] }} orang."
                                wire:loading.attr="disabled"
                                style="width:100%"
                            >
                                Simpan Data Hari Ini
                            </x-filament::button>
                            <div class="pm-muted" style="text-align:center">
                                Tombol ini selalu menyimpan data LIVE hari ini, bukan tanggal arsip yang sedang dilihat.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pm-card no-print">
                    <div class="pm-card-header">
                        <div class="pm-card-title">Simpan untuk Tanggal Tertentu</div>
                    </div>
                    <div class="pm-card-body">
                        <div class="pm-muted" style="margin-bottom:.6rem">
                            Menyimpan data LIVE saat ini sebagai tanggal pilihan (misalnya tanggal terlewat).
                        </div>
                        <x-filament::input.wrapper>
                            <x-filament::input type="date" wire:model="tanggalSimpan" max="{{ today()->toDateString() }}" />
                        </x-filament::input.wrapper>
                        @error('tanggalSimpan')
                            <div style="margin-top:.35rem;font-size:.72rem;color:rgb(var(--danger-600))">{{ $message }}</div>
                        @enderror
                        <x-filament::button
                            color="warning"
                            icon="heroicon-o-calendar-days"
                            wire:click="simpanTanggal"
                            wire:loading.attr="disabled"
                            style="width:100%;margin-top:.6rem"
                        >
                            Simpan untuk Tanggal Ini
                        </x-filament::button>
                    </div>
                </div>

                <div class="pm-card no-print">
                    <div class="pm-card-header">
                        <div class="pm-card-title">Informasi</div>
                    </div>
                    <div class="pm-card-body pm-muted">
                        <p style="margin-bottom:.4rem"><strong>LIVE</strong> = data langsung dari data penerima aktif saat ini.</p>
                        <p style="margin-bottom:.4rem"><strong>ARSIP</strong> = data tanggal lampau yang sudah pernah disimpan.</p>
                        <p style="margin-bottom:.4rem"><strong>Simpan</strong> = memotret data LIVE ke Riwayat Harian, Riwayat Detail, dan Log Audit.</p>
                        <p>Tanggal dengan titik hijau di kalender sudah memiliki data tersimpan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

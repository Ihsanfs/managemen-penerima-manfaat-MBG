<x-filament-panels::page>
    <style>
        .pmw-stack { display: grid; gap: 1.25rem; }
        .pmw-card { background: #fff; border-radius: .75rem; box-shadow: 0 0 0 1px rgba(17,24,39,.06), 0 1px 2px rgba(17,24,39,.06); overflow: hidden; }
        .dark .pmw-card { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgba(255,255,255,.1); }
        .pmw-card-header { padding: .8rem 1rem; border-bottom: 1px solid rgb(var(--gray-200)); display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .dark .pmw-card-header { border-color: rgba(255,255,255,.1); }
        .pmw-card-body { padding: 1rem; }
        .pmw-card-title { font-size: .875rem; font-weight: 700; color: rgb(var(--gray-950)); }
        .dark .pmw-card-title { color: #fff; }
        .pmw-toolbar { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; }
        .pmw-title { font-size: 1.4rem; font-weight: 800; color: rgb(var(--gray-950)); letter-spacing: -.02em; }
        .dark .pmw-title { color: #fff; }
        .pmw-subtitle { margin-top: .2rem; font-size: .85rem; color: rgb(var(--gray-500)); }
        .dark .pmw-subtitle { color: rgb(var(--gray-400)); }
        .pmw-days { display: grid; gap: .75rem; grid-template-columns: repeat(5, minmax(0, 1fr)); }
        @media (max-width: 900px) { .pmw-days { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 480px) { .pmw-days { grid-template-columns: 1fr; } }
        .pmw-day { border: 1px solid rgb(var(--gray-200)); border-radius: .6rem; padding: .8rem; }
        .dark .pmw-day { border-color: rgba(255,255,255,.1); }
        .pmw-day-name { font-size: .72rem; font-weight: 700; color: rgb(var(--gray-500)); text-transform: uppercase; letter-spacing: .04em; }
        .pmw-day-date { font-size: .8rem; color: rgb(var(--gray-950)); font-weight: 700; margin-top: .1rem; }
        .dark .pmw-day-date { color: #fff; }
        .pmw-day-total { font-size: 1.6rem; font-weight: 800; color: rgb(var(--gray-950)); margin-top: .5rem; }
        .dark .pmw-day-total { color: #fff; }
        .pmw-day-status { margin-top: .4rem; font-size: .68rem; }
        .pmw-badge { display: inline-flex; border-radius: 9999px; padding: .12rem .5rem; font-weight: 700; font-size: .65rem; }
        .pmw-badge-success { background: rgb(var(--success-50)); color: rgb(var(--success-700)); }
        .pmw-badge-warning { background: rgb(var(--warning-50)); color: rgb(var(--warning-700)); }
        .pmw-badge-gray { background: rgb(var(--gray-100)); color: rgb(var(--gray-600)); }
        .dark .pmw-badge-success { background: rgba(34,197,94,.2); color: #86efac; }
        .dark .pmw-badge-warning { background: rgba(245,158,11,.2); color: #fcd34d; }
        .dark .pmw-badge-gray { background: rgba(255,255,255,.08); color: rgb(var(--gray-300)); }
        .pmw-stats { display: grid; gap: .75rem; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        @media (max-width: 640px) { .pmw-stats { grid-template-columns: 1fr; } }
        .pmw-stat { border-radius: .6rem; padding: .8rem 1rem; background: rgb(var(--gray-50)); }
        .dark .pmw-stat { background: rgba(255,255,255,.04); }
        .pmw-stat-label { font-size: .68rem; color: rgb(var(--gray-500)); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .pmw-stat-value { font-size: 1.5rem; font-weight: 800; color: rgb(var(--gray-950)); }
        .dark .pmw-stat-value { color: #fff; }
        .pmw-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
        .pmw-table th { background: rgb(var(--gray-50)); padding: .55rem .6rem; text-align: left; font-weight: 700; color: rgb(var(--gray-600)); white-space: nowrap; border-bottom: 1px solid rgb(var(--gray-200)); }
        .dark .pmw-table th { background: rgba(255,255,255,.04); color: rgb(var(--gray-300)); border-color: rgba(255,255,255,.1); }
        .pmw-table td { padding: .5rem .6rem; border-bottom: 1px solid rgb(var(--gray-100)); color: rgb(var(--gray-700)); }
        .dark .pmw-table td { color: rgb(var(--gray-200)); border-color: rgba(255,255,255,.08); }
        .pmw-right { text-align: right; }
        .pmw-center { text-align: center; }
        @media print {
            .fi-sidebar, .fi-topbar, .fi-header, .fi-page-header, .fi-breadcrumbs, .no-print { display: none !important; }
            .fi-main, .fi-page { padding: 0 !important; }
            .pmw-card { box-shadow: none !important; border: 1px solid #d1d5db !important; }
        }
    </style>

    <div class="pmw-stack">
        <div class="pmw-toolbar">
            <div>
                <div class="pmw-title">Laporan Mingguan</div>
                <div class="pmw-subtitle">
                    {{ $mingguMulai->locale('id')->translatedFormat('d M Y') }}
                    &ndash;
                    {{ $akhir_minggu->locale('id')->translatedFormat('d M Y') }}
                </div>
            </div>

            <div class="no-print" style="display:flex;flex-wrap:wrap;align-items:center;gap:.5rem">
                <x-filament::button color="gray" size="sm" icon="heroicon-m-chevron-left" wire:click="geserMinggu(-1)">
                    Minggu Lalu
                </x-filament::button>
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model.live="minggu" max="{{ today()->toDateString() }}" />
                </x-filament::input.wrapper>
                <x-filament::button color="gray" size="sm" wire:click="kembaliMingguIni">
                    Minggu Ini
                </x-filament::button>
                <x-filament::button color="gray" size="sm" icon="heroicon-m-chevron-right" wire:click="geserMinggu(1)">
                    Minggu Depan
                </x-filament::button>
                <x-filament::button color="gray" size="sm" icon="heroicon-o-printer" onclick="window.print()">
                    Cetak
                </x-filament::button>
            </div>
        </div>

        <div class="pmw-stats">
            <div class="pmw-stat">
                <div class="pmw-stat-label">Hari Sudah Diupdate</div>
                <div class="pmw-stat-value">{{ $sudah_update }} / 5</div>
            </div>
            <div class="pmw-stat">
                <div class="pmw-stat-label">Hari Otomatis</div>
                <div class="pmw-stat-value">{{ $otomatis }} / 5</div>
            </div>
            <div class="pmw-stat">
                <div class="pmw-stat-label">Rata-rata Total PM</div>
                <div class="pmw-stat-value">
                    {{ collect($days)->whereNotNull('total')->avg('total') ? round(collect($days)->whereNotNull('total')->avg('total')) : 0 }}
                </div>
            </div>
        </div>

        <div class="pmw-card">
            <div class="pmw-card-header">
                <div class="pmw-card-title">Status Senin - Jumat</div>
                <div class="pmw-subtitle">Minggu {{ $mingguMulai->locale('id')->translatedFormat('d M Y') }}</div>
            </div>
            <div class="pmw-card-body">
                <div class="pmw-days">
                    @foreach ($days as $day)
                        <div class="pmw-day">
                            <div class="pmw-day-name">{{ $day['hari'] }}</div>
                            <div class="pmw-day-date">{{ $day['tanggal']->locale('id')->translatedFormat('d M Y') }}</div>
                            <div class="pmw-day-total">{{ $day['total'] ?? '—' }}</div>
                            <div class="pmw-day-status">
                                <span class="pmw-badge {{ $day['sumber'] === 'manual' ? 'pmw-badge-success' : ($day['sumber'] === 'otomatis' ? 'pmw-badge-warning' : 'pmw-badge-gray') }}">
                                    {{ $day['status'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="pmw-card">
            <div class="pmw-card-header">
                <div class="pmw-card-title">20 Hari Kerja Terakhir</div>
                <div class="pmw-subtitle">Perubahan dihitung dari hari kerja sebelumnya</div>
            </div>
            <div style="overflow-x:auto">
                <table class="pmw-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Status</th>
                            <th class="pmw-right">Total PM</th>
                            <th class="pmw-right">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($workdays as $workday)
                            <tr>
                                <td>{{ $workday['tanggal']->locale('id')->translatedFormat('d M Y') }}</td>
                                <td>{{ $workday['hari'] }}</td>
                                <td>
                                    <span class="pmw-badge {{ $workday['sumber'] === 'manual' ? 'pmw-badge-success' : 'pmw-badge-warning' }}">
                                        {{ $workday['status'] }}
                                    </span>
                                </td>
                                <td class="pmw-right" style="font-weight:700">{{ $workday['total'] }}</td>
                                <td class="pmw-right">
                                    @if ($workday['perubahan'] === null)
                                        &mdash;
                                    @elseif ($workday['perubahan'] > 0)
                                        <span style="color:rgb(var(--success-600));font-weight:700">+{{ $workday['perubahan'] }}</span>
                                    @elseif ($workday['perubahan'] < 0)
                                        <span style="color:rgb(var(--danger-600));font-weight:700">{{ $workday['perubahan'] }}</span>
                                    @else
                                        <span style="color:rgb(var(--gray-500))">0</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="pmw-center" style="color:rgb(var(--gray-500))">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>

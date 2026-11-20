<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Data penerima manfaat {{ $profile->nama }} hari ini.">
    <title>Data Penerima Manfaat - {{ $profile->nama }}</title>
    @if ($cssFile)
        <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
    @endif
</head>
<body class="min-h-screen bg-gray-100 text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-emerald-600 text-sm font-bold text-white">
                    SPPG
                </div>
                <div>
                    <div class="text-base font-bold leading-tight">{{ $profile->nama }}</div>
                    <div class="text-xs text-gray-500">
                        @if ($profile->kecamatan)
                            Kec. {{ $profile->kecamatan }}, {{ $profile->kabupaten }}
                        @else
                            Data penerima manfaat
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    LIVE
                </span>
                <a href="/admin"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">
                    Masuk Admin
                </a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">Data Penerima Manfaat</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $tanggal->locale('id')->translatedFormat('l, d F Y') }}
                    &middot; Data langsung dari penerima aktif
                </p>
            </div>
            @if ($savedToday?->saved_at)
                <p class="text-xs text-gray-500">
                    Terakhir disimpan {{ $savedToday->saved_at->locale('id')->translatedFormat('d F Y H:i') }}
                </p>
            @endif
        </div>

        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <div class="text-xs font-medium text-gray-500">Total Penerima</div>
                <div class="mt-1 text-2xl font-extrabold text-gray-900">{{ $snapshot['total'] }}</div>
                <div class="text-xs text-gray-400">orang</div>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <div class="text-xs font-medium text-gray-500">Laki-laki</div>
                <div class="mt-1 text-2xl font-extrabold text-blue-600">{{ $jumlahLaki }}</div>
                <div class="text-xs text-gray-400">orang</div>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <div class="text-xs font-medium text-gray-500">Perempuan</div>
                <div class="mt-1 text-2xl font-extrabold text-pink-600">{{ $jumlahPerempuan }}</div>
                <div class="text-xs text-gray-400">orang</div>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <div class="text-xs font-medium text-gray-500">Sekolah</div>
                <div class="mt-1 text-2xl font-extrabold text-gray-900">{{ $sekolah->count() }}</div>
                <div class="text-xs text-gray-400">{{ $totalSekolah }} penerima</div>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <div class="text-xs font-medium text-gray-500">Posyandu</div>
                <div class="mt-1 text-2xl font-extrabold text-gray-900">{{ $posyandu->count() }}</div>
                <div class="text-xs text-gray-400">{{ $totalPosyandu }} penerima</div>
            </div>
        </div>

        <div class="space-y-6">
            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-4 py-3">
                    <h2 class="text-sm font-bold text-gray-900">Sekolah</h2>
                    <span class="text-xs text-gray-500">
                        {{ $sekolah->count() }} lembaga &middot; {{ $totalSekolah }} penerima
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-3 py-3 text-center font-bold">No</th>
                                <th class="px-3 py-3 text-left font-bold">Nama Sekolah</th>
                                <th class="px-3 py-3 text-center font-bold">TK/PAUD</th>
                                @foreach ([1, 2, 3, 4, 5, 6] as $kelas)
                                    <th class="px-3 py-3 text-center font-bold">K{{ $kelas }}</th>
                                @endforeach
                                <th class="px-3 py-3 text-center font-bold">SMK</th>
                                <th class="px-3 py-3 text-center font-bold">Guru</th>
                                <th class="px-3 py-3 text-center font-bold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($sekolah as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2.5 text-center text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-2.5 font-semibold text-gray-900">
                                        {{ $row['nama'] }}
                                        @if ($row['jenjang'])
                                            <span class="ml-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                                                {{ \App\Models\Institution::JENJANG[$row['jenjang']] ?? $row['jenjang'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center">{{ $row['jenjang'] === 'tk_paud' ? ($row['rincian']['tk_paud'] ?: '-') : '-' }}</td>
                                    @foreach ([1, 2, 3, 4, 5, 6] as $kelas)
                                        <td class="px-3 py-2.5 text-center">{{ $row['jenjang'] === 'sd' ? ($row['rincian']['kelas_' . $kelas] ?: '-') : '-' }}</td>
                                    @endforeach
                                    <td class="px-3 py-2.5 text-center">{{ $row['jenjang'] === 'smk' ? ($row['rincian']['smk'] ?: '-') : '-' }}</td>
                                    <td class="px-3 py-2.5 text-center">{{ $row['rincian']['guru'] ?: '-' }}</td>
                                    <td class="px-3 py-2.5 text-center font-extrabold text-gray-900">{{ $row['total'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-3 py-6 text-center text-sm text-gray-400">Belum ada data sekolah.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm font-bold text-gray-900">
                            <tr>
                                <td colspan="2" class="px-3 py-3 text-left">JUMLAH SEKOLAH</td>
                                <td class="px-3 py-3 text-center">{{ $sekolah->sum(fn ($row) => $row['rincian']['tk_paud']) ?: '-' }}</td>
                                @foreach ([1, 2, 3, 4, 5, 6] as $kelas)
                                    <td class="px-3 py-3 text-center">{{ $sekolah->sum(fn ($row) => $row['rincian']['kelas_' . $kelas]) ?: '-' }}</td>
                                @endforeach
                                <td class="px-3 py-3 text-center">{{ $sekolah->sum(fn ($row) => $row['rincian']['smk']) ?: '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $sekolah->sum(fn ($row) => $row['rincian']['guru']) ?: '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $totalSekolah }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-4 py-3">
                    <h2 class="text-sm font-bold text-gray-900">Posyandu</h2>
                    <span class="text-xs text-gray-500">
                        {{ $posyandu->count() }} posyandu &middot; {{ $totalPosyandu }} penerima
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-3 py-3 text-center font-bold">No</th>
                                <th class="px-3 py-3 text-left font-bold">Nama Posyandu</th>
                                <th class="px-3 py-3 text-center font-bold">Balita</th>
                                <th class="px-3 py-3 text-center font-bold">Bumil</th>
                                <th class="px-3 py-3 text-center font-bold">Busui</th>
                                <th class="px-3 py-3 text-center font-bold">Kader</th>
                                <th class="px-3 py-3 text-center font-bold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($posyandu as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2.5 text-center text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-2.5 font-semibold text-gray-900">
                                        {{ $row['nama'] }}
                                        <span class="ml-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                            Posyandu
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center">{{ $row['rincian']['balita'] ?: '-' }}</td>
                                    <td class="px-3 py-2.5 text-center">{{ $row['rincian']['bumil'] ?: '-' }}</td>
                                    <td class="px-3 py-2.5 text-center">{{ $row['rincian']['busui'] ?: '-' }}</td>
                                    <td class="px-3 py-2.5 text-center">{{ $row['rincian']['kader'] ?: '-' }}</td>
                                    <td class="px-3 py-2.5 text-center font-extrabold text-gray-900">{{ $row['total'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-400">Belum ada data posyandu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm font-bold text-gray-900">
                            <tr>
                                <td colspan="2" class="px-3 py-3 text-left">JUMLAH POSYANDU</td>
                                <td class="px-3 py-3 text-center">{{ $posyandu->sum(fn ($row) => $row['rincian']['balita']) ?: '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $posyandu->sum(fn ($row) => $row['rincian']['bumil']) ?: '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $posyandu->sum(fn ($row) => $row['rincian']['busui']) ?: '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $posyandu->sum(fn ($row) => $row['rincian']['kader']) ?: '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $totalPosyandu }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 lg:col-span-2">
                    <div class="border-b border-gray-200 px-4 py-3">
                        <h2 class="text-sm font-bold text-gray-900">Rekap Kategori</h2>
                    </div>
                    <div class="grid gap-x-8 gap-y-1 p-4 sm:grid-cols-2">
                        @foreach ($rekap as $item)
                            <div class="flex items-center justify-between border-b border-dashed border-gray-200 py-1.5 text-sm last:border-0">
                                <span class="text-gray-600">{{ $item['label'] }}</span>
                                <span class="font-bold text-gray-900">{{ $item['nilai'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="flex flex-col justify-between rounded-xl bg-emerald-600 p-5 text-white shadow-sm">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-100">Total Penerima Manfaat</div>
                        <div class="mt-2 text-5xl font-extrabold">{{ $snapshot['total'] }}</div>
                        <div class="text-sm text-emerald-100">orang</div>
                    </div>
                    <div class="mt-4 text-xs text-emerald-100">
                        {{ $tanggal->locale('id')->translatedFormat('d F Y') }} &middot;
                        {{ $sekolah->count() + $posyandu->count() }} lembaga
                    </div>
                </section>
            </div>
        </div>

        <footer class="mt-8 border-t border-gray-200 pt-4 text-xs text-gray-500">
            <p>Halaman publik ini hanya menampilkan data agregat penerima manfaat. Tidak ada data pribadi yang ditampilkan.</p>
            <p class="mt-1">&copy; {{ date('Y') }} {{ $profile->nama }}.</p>
        </footer>
    </main>
</body>
</html>

<x-filament-panels::page>
    <style>
        .ie-wrap { display: grid; gap: 1.25rem; }
        .ie-head { display: grid; gap: .35rem; }
        .ie-title { font-size: 1.4rem; font-weight: 800; color: rgb(var(--gray-950)); letter-spacing: -.02em; }
        .dark .ie-title { color: #fff; }
        .ie-subtitle { font-size: .85rem; color: rgb(var(--gray-500)); line-height: 1.5; max-width: 78ch; }
        .dark .ie-subtitle { color: rgb(var(--gray-400)); }
        .ie-grid { display: grid; gap: 1.25rem; }
        @media (min-width: 1100px) { .ie-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); align-items: start; } }
        .ie-card { background: #fff; border-radius: .75rem; box-shadow: 0 0 0 1px rgba(17,24,39,.06), 0 1px 2px rgba(17,24,39,.06); overflow: hidden; }
        .dark .ie-card { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgba(255,255,255,.1); }
        .ie-card-head { padding: .9rem 1rem; border-bottom: 1px solid rgb(var(--gray-200)); display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; }
        .dark .ie-card-head { border-color: rgba(255,255,255,.1); }
        .ie-card-body { padding: 1rem; display: grid; gap: .9rem; }
        .ie-card-title { font-size: .9rem; font-weight: 700; color: rgb(var(--gray-950)); }
        .dark .ie-card-title { color: #fff; }
        .ie-muted { margin-top: .15rem; font-size: .75rem; color: rgb(var(--gray-500)); line-height: 1.5; }
        .dark .ie-muted { color: rgb(var(--gray-400)); }
        .ie-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
        .ie-drop { position: relative; display: flex; flex-direction: column; align-items: center; gap: .35rem; padding: 1.1rem 1rem; border: 1.5px dashed rgb(var(--gray-300)); border-radius: .65rem; background: rgb(var(--gray-50)); cursor: pointer; text-align: center; transition: border-color .15s, background .15s; }
        .ie-drop:hover { border-color: rgb(var(--primary-500)); background: rgb(var(--primary-50)); }
        .dark .ie-drop { border-color: rgb(var(--gray-600)); background: rgba(255,255,255,.03); }
        .dark .ie-drop:hover { border-color: rgb(var(--primary-500)); background: rgba(var(--primary-500), .08); }
        .ie-drop input[type="file"] { position: absolute; width: 1px; height: 1px; opacity: 0; overflow: hidden; }
        .ie-drop-icon { height: 1.6rem; width: 1.6rem; color: rgb(var(--primary-600)); }
        .ie-drop-title { font-size: .82rem; font-weight: 700; color: rgb(var(--gray-800)); }
        .dark .ie-drop-title { color: rgb(var(--gray-100)); }
        .ie-drop-note { font-size: .7rem; color: rgb(var(--gray-500)); }
        .dark .ie-drop-note { color: rgb(var(--gray-400)); }
        .ie-chosen { display: flex; align-items: center; gap: .4rem; font-size: .75rem; color: rgb(var(--success-700)); background: rgb(var(--success-50)); border-radius: .45rem; padding: .45rem .6rem; word-break: break-all; }
        .dark .ie-chosen { background: rgba(34,197,94,.15); color: #86efac; }
        .ie-error { font-size: .75rem; color: rgb(var(--danger-700)); background: rgb(var(--danger-50)); border-radius: .45rem; padding: .45rem .6rem; }
        .dark .ie-error { background: rgba(239,68,68,.15); color: #fecaca; }
        .ie-hint { font-size: .72rem; color: rgb(var(--gray-500)); line-height: 1.55; border-top: 1px solid rgb(var(--gray-100)); padding-top: .8rem; }
        .dark .ie-hint { color: rgb(var(--gray-400)); border-color: rgba(255,255,255,.08); }
        .ie-hint strong { color: rgb(var(--gray-700)); }
        .dark .ie-hint strong { color: rgb(var(--gray-200)); }
        .ie-result { border-radius: .6rem; border: 1px solid rgb(var(--gray-200)); overflow: hidden; }
        .dark .ie-result { border-color: rgba(255,255,255,.1); }
        .ie-result-head { display: flex; align-items: center; justify-content: space-between; gap: .5rem; padding: .55rem .7rem; background: rgb(var(--gray-50)); }
        .dark .ie-result-head { background: rgba(255,255,255,.04); }
        .ie-result-title { font-size: .75rem; font-weight: 700; color: rgb(var(--gray-700)); }
        .dark .ie-result-title { color: rgb(var(--gray-200)); }
        .ie-close { border: 0; background: transparent; font-size: .7rem; font-weight: 600; color: rgb(var(--gray-500)); cursor: pointer; padding: .1rem .25rem; }
        .ie-close:hover { color: rgb(var(--gray-800)); }
        .ie-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .4rem; padding: .7rem; }
        @media (max-width: 520px) { .ie-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        .ie-stat { border-radius: .45rem; padding: .45rem .55rem; background: rgb(var(--gray-50)); }
        .dark .ie-stat { background: rgba(255,255,255,.04); }
        .ie-stat-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: rgb(var(--gray-500)); }
        .ie-stat-value { font-size: 1.15rem; font-weight: 800; color: rgb(var(--gray-950)); line-height: 1.2; }
        .dark .ie-stat-value { color: #fff; }
        .ie-stat.is-created .ie-stat-value { color: rgb(var(--success-600)); }
        .ie-stat.is-updated .ie-stat-value { color: rgb(var(--info-600)); }
        .ie-stat.is-failed .ie-stat-value { color: rgb(var(--danger-600)); }
        .ie-errors { max-height: 15rem; overflow: auto; border-top: 1px solid rgb(var(--gray-100)); }
        .dark .ie-errors { border-color: rgba(255,255,255,.08); }
        .ie-errors table { width: 100%; border-collapse: collapse; font-size: .72rem; }
        .ie-errors th { position: sticky; top: 0; background: rgb(var(--gray-50)); text-align: left; padding: .4rem .6rem; font-weight: 700; color: rgb(var(--gray-600)); }
        .dark .ie-errors th { background: rgb(var(--gray-800)); color: rgb(var(--gray-300)); }
        .ie-errors td { padding: .4rem .6rem; border-top: 1px solid rgb(var(--gray-100)); color: rgb(var(--gray-700)); vertical-align: top; }
        .dark .ie-errors td { border-color: rgba(255,255,255,.08); color: rgb(var(--gray-200)); }
        .ie-errors td:first-child { white-space: nowrap; font-weight: 700; width: 4.5rem; }
        .ie-ok { padding: .6rem .7rem; font-size: .75rem; color: rgb(var(--success-700)); background: rgb(var(--success-50)); }
        .dark .ie-ok { color: #86efac; background: rgba(34,197,94,.12); }
        .ie-spin { font-size: .72rem; color: rgb(var(--gray-500)); }
        .ie-riwayat { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .9rem; padding: 1rem; }
    </style>

    <div class="ie-wrap">
        <div class="ie-head">
            <div class="ie-title">Impor &amp; Ekspor Data</div>
            <div class="ie-subtitle">
                Unduh template, isi datanya, lalu unggah kembali. Template bisa dipakai berulang kali.
                Impor menerima file <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong>.
            </div>
        </div>

        <div class="ie-grid">
            <div class="ie-card">
                <div class="ie-card-head">
                    <div>
                        <div class="ie-card-title">Penerima Manfaat</div>
                        <div class="ie-muted">Tambah atau perbarui data penerima manfaat sekaligus.</div>
                    </div>
                    <x-filament::badge color="success">
                        {{ number_format($jumlahPenerima, 0, ',', '.') }} data
                    </x-filament::badge>
                </div>

                <div class="ie-card-body">
                    <div class="ie-actions">
                        <x-filament::button
                            tag="a"
                            href="{{ route('unduhan.template-penerima') }}"
                            icon="heroicon-o-arrow-down-tray"
                            color="gray"
                            size="sm"
                            outlined
                        >
                            Template Impor
                        </x-filament::button>

                        <x-filament::button
                            tag="a"
                            href="{{ route('unduhan.export-penerima') }}"
                            icon="heroicon-o-document-arrow-down"
                            color="gray"
                            size="sm"
                        >
                            Ekspor Excel
                        </x-filament::button>
                    </div>

                    <form wire:submit="importPenerima" style="display:grid;gap:.6rem">
                        <label class="ie-drop">
                            <x-filament::icon icon="heroicon-o-cloud-arrow-up" class="ie-drop-icon" />
                            <span class="ie-drop-title">Pilih file penerima manfaat</span>
                            <span class="ie-drop-note">.xlsx, .xls, atau .csv &middot; maksimal {{ $maksimalMb }} MB</span>
                            <input type="file" wire:model="filePenerima" accept=".xlsx,.xls,.csv">
                        </label>

                        @if ($filePenerima)
                            <div class="ie-chosen">
                                <x-filament::icon icon="heroicon-m-document-check" style="height:1rem;width:1rem" />
                                <span>{{ $filePenerima->getClientOriginalName() }}</span>
                            </div>
                        @endif

                        @error('filePenerima')
                            <div class="ie-error">{{ $message }}</div>
                        @enderror

                        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
                            <x-filament::button
                                type="submit"
                                icon="heroicon-o-arrow-up-tray"
                                size="sm"
                                wire:loading.attr="disabled"
                                wire:target="importPenerima"
                            >
                                Impor Penerima Manfaat
                            </x-filament::button>

                            <span class="ie-spin" wire:loading wire:target="importPenerima">Memproses file...</span>
                        </div>
                    </form>

                    <div class="ie-hint">
                        <strong>Kolom wajib:</strong> nama_lembaga, nama, kategori, jenis_kelamin.
                        Pencocokan data: NIK &rarr; NISN &rarr; lembaga + nama + kategori + kelas.
                        Baris yang belum ada akan dibuat, yang sudah ada akan diperbarui.
                    </div>

                    @if ($hasilPenerima)
                        <div class="ie-result">
                            <div class="ie-result-head">
                                <div class="ie-result-title">Hasil impor penerima manfaat</div>
                                <button type="button" class="ie-close" wire:click="hapusHasilPenerima">Tutup</button>
                            </div>

                            <div class="ie-stats">
                                <div class="ie-stat is-created">
                                    <div class="ie-stat-label">Dibuat</div>
                                    <div class="ie-stat-value">{{ $hasilPenerima['dibuat'] }}</div>
                                </div>
                                <div class="ie-stat is-updated">
                                    <div class="ie-stat-label">Diperbarui</div>
                                    <div class="ie-stat-value">{{ $hasilPenerima['diperbarui'] }}</div>
                                </div>
                                <div class="ie-stat is-failed">
                                    <div class="ie-stat-label">Gagal</div>
                                    <div class="ie-stat-value">{{ $hasilPenerima['gagal'] }}</div>
                                </div>
                                <div class="ie-stat">
                                    <div class="ie-stat-label">Total Baris</div>
                                    <div class="ie-stat-value">{{ $hasilPenerima['total'] }}</div>
                                </div>
                            </div>

                            @if (count($hasilPenerima['errors']) > 0)
                                <div class="ie-errors">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Baris</th>
                                                <th>Pesan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hasilPenerima['errors'] as $error)
                                                <tr>
                                                    <td>{{ $error['baris'] > 0 ? '#' . $error['baris'] : 'File' }}</td>
                                                    <td>{{ $error['pesan'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="ie-ok">Semua baris berhasil diproses.</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="ie-card">
                <div class="ie-card-head">
                    <div>
                        <div class="ie-card-title">Lembaga</div>
                        <div class="ie-muted">Tambah atau perbarui sekolah dan posyandu.</div>
                    </div>
                    <x-filament::badge color="info">
                        {{ number_format($jumlahLembaga, 0, ',', '.') }} lembaga
                    </x-filament::badge>
                </div>

                <div class="ie-card-body">
                    <div class="ie-actions">
                        <x-filament::button
                            tag="a"
                            href="{{ route('unduhan.template-lembaga') }}"
                            icon="heroicon-o-arrow-down-tray"
                            color="gray"
                            size="sm"
                            outlined
                        >
                            Template Impor
                        </x-filament::button>

                        <x-filament::button
                            tag="a"
                            href="{{ route('unduhan.export-lembaga') }}"
                            icon="heroicon-o-document-arrow-down"
                            color="gray"
                            size="sm"
                        >
                            Ekspor Excel
                        </x-filament::button>
                    </div>

                    <form wire:submit="importLembaga" style="display:grid;gap:.6rem">
                        <label class="ie-drop">
                            <x-filament::icon icon="heroicon-o-cloud-arrow-up" class="ie-drop-icon" />
                            <span class="ie-drop-title">Pilih file lembaga</span>
                            <span class="ie-drop-note">.xlsx, .xls, atau .csv &middot; maksimal {{ $maksimalMb }} MB</span>
                            <input type="file" wire:model="fileLembaga" accept=".xlsx,.xls,.csv">
                        </label>

                        @if ($fileLembaga)
                            <div class="ie-chosen">
                                <x-filament::icon icon="heroicon-m-document-check" style="height:1rem;width:1rem" />
                                <span>{{ $fileLembaga->getClientOriginalName() }}</span>
                            </div>
                        @endif

                        @error('fileLembaga')
                            <div class="ie-error">{{ $message }}</div>
                        @enderror

                        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
                            <x-filament::button
                                type="submit"
                                icon="heroicon-o-arrow-up-tray"
                                size="sm"
                                wire:loading.attr="disabled"
                                wire:target="importLembaga"
                            >
                                Impor Lembaga
                            </x-filament::button>

                            <span class="ie-spin" wire:loading wire:target="importLembaga">Memproses file...</span>
                        </div>
                    </form>

                    <div class="ie-hint">
                        <strong>Kolom wajib:</strong> nama, tipe.
                        Impor mencocokkan nama lembaga tanpa membedakan huruf besar/kecil.
                        Nama yang sudah ada akan diperbarui, nama baru akan dibuat.
                    </div>

                    @if ($hasilLembaga)
                        <div class="ie-result">
                            <div class="ie-result-head">
                                <div class="ie-result-title">Hasil impor lembaga</div>
                                <button type="button" class="ie-close" wire:click="hapusHasilLembaga">Tutup</button>
                            </div>

                            <div class="ie-stats">
                                <div class="ie-stat is-created">
                                    <div class="ie-stat-label">Dibuat</div>
                                    <div class="ie-stat-value">{{ $hasilLembaga['dibuat'] }}</div>
                                </div>
                                <div class="ie-stat is-updated">
                                    <div class="ie-stat-label">Diperbarui</div>
                                    <div class="ie-stat-value">{{ $hasilLembaga['diperbarui'] }}</div>
                                </div>
                                <div class="ie-stat is-failed">
                                    <div class="ie-stat-label">Gagal</div>
                                    <div class="ie-stat-value">{{ $hasilLembaga['gagal'] }}</div>
                                </div>
                                <div class="ie-stat">
                                    <div class="ie-stat-label">Total Baris</div>
                                    <div class="ie-stat-value">{{ $hasilLembaga['total'] }}</div>
                                </div>
                            </div>

                            @if (count($hasilLembaga['errors']) > 0)
                                <div class="ie-errors">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Baris</th>
                                                <th>Pesan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hasilLembaga['errors'] as $error)
                                                <tr>
                                                    <td>{{ $error['baris'] > 0 ? '#' . $error['baris'] : 'File' }}</td>
                                                    <td>{{ $error['pesan'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="ie-ok">Semua baris berhasil diproses.</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="ie-card">
            <div class="ie-riwayat">
                <div>
                    <div class="ie-card-title">Riwayat Harian</div>
                    <div class="ie-muted">
                        Ekspor seluruh laporan harian yang sudah tersimpan beserta rincian per lembaga.
                        Gunakan untuk arsip, pemeriksaan, atau pelaporan bulanan.
                    </div>
                </div>

                <x-filament::button
                    tag="a"
                    href="{{ route('unduhan.export-riwayat') }}"
                    icon="heroicon-o-document-arrow-down"
                    color="gray"
                >
                    Ekspor Riwayat Harian
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>

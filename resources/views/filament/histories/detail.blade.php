<style>
    .bh-wrap { display: grid; gap: 1rem; font-size: .82rem; }
    .bh-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .6rem; }
    .bh-cell { border: 1px solid rgb(var(--gray-200)); border-radius: .55rem; padding: .6rem .7rem; }
    .dark .bh-cell { border-color: rgba(255,255,255,.12); }
    .bh-label { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: rgb(var(--gray-500)); }
    .bh-value { margin-top: .15rem; font-weight: 600; color: rgb(var(--gray-900)); }
    .dark .bh-value { color: rgb(var(--gray-100)); }
    .bh-reason { border-radius: .55rem; padding: .7rem .8rem; background: rgb(var(--warning-50)); color: rgb(var(--warning-800)); line-height: 1.55; }
    .dark .bh-reason { background: rgba(245,158,11,.14); color: #fde68a; }
    .bh-table-wrap { overflow-x: auto; border: 1px solid rgb(var(--gray-200)); border-radius: .55rem; }
    .dark .bh-table-wrap { border-color: rgba(255,255,255,.12); }
    .bh-table { width: 100%; border-collapse: collapse; }
    .bh-table th { text-align: left; padding: .45rem .6rem; background: rgb(var(--gray-50)); color: rgb(var(--gray-600)); font-size: .68rem; text-transform: uppercase; letter-spacing: .03em; }
    .dark .bh-table th { background: rgba(255,255,255,.04); color: rgb(var(--gray-300)); }
    .bh-table td { padding: .45rem .6rem; border-top: 1px solid rgb(var(--gray-100)); color: rgb(var(--gray-700)); vertical-align: top; }
    .dark .bh-table td { border-color: rgba(255,255,255,.08); color: rgb(var(--gray-200)); }
    .bh-old { color: rgb(var(--danger-700)); }
    .dark .bh-old { color: #fca5a5; }
    .bh-new { color: rgb(var(--success-700)); }
    .dark .bh-new { color: #86efac; }
    .bh-muted { color: rgb(var(--gray-500)); font-size: .75rem; line-height: 1.5; }
    .dark .bh-muted { color: rgb(var(--gray-400)); }
    @media (max-width: 560px) { .bh-grid { grid-template-columns: 1fr; } }
</style>

<div class="bh-wrap">
    <div class="bh-grid">
        <div class="bh-cell">
            <div class="bh-label">Penerima Manfaat</div>
            <div class="bh-value">{{ $record->beneficiary_nama }}</div>
        </div>
        <div class="bh-cell">
            <div class="bh-label">Lembaga</div>
            <div class="bh-value">{{ $record->institution_nama ?? '-' }}</div>
        </div>
        <div class="bh-cell">
            <div class="bh-label">Kategori</div>
            <div class="bh-value">{{ $record->kategori_label ?? '-' }}</div>
        </div>
        <div class="bh-cell">
            <div class="bh-label">Aksi</div>
            <div class="bh-value">{{ $record->aksi_label }}</div>
        </div>
        <div class="bh-cell">
            <div class="bh-label">Tanggal &amp; Hari</div>
            <div class="bh-value">{{ $record->tanggal_lengkap }}</div>
        </div>
        <div class="bh-cell">
            <div class="bh-label">Waktu Tercatat</div>
            <div class="bh-value">
                {{ $record->created_at?->locale('id')->translatedFormat('d F Y, H:i:s') ?? '-' }}
            </div>
        </div>
        <div class="bh-cell">
            <div class="bh-label">Dicatat Oleh</div>
            <div class="bh-value">{{ $record->user?->name ?? 'Sistem' }}</div>
        </div>
        <div class="bh-cell">
            <div class="bh-label">ID Riwayat</div>
            <div class="bh-value">#{{ $record->getKey() }}</div>
        </div>
    </div>

    <div>
        <div class="bh-label" style="margin-bottom:.35rem">Alasan Perubahan</div>
        <div class="bh-reason">{{ $record->alasan ?: '-' }}</div>
    </div>

    <div>
        <div class="bh-label" style="margin-bottom:.35rem">Rincian Perubahan</div>

        @if (! empty($record->perubahan))
            <div class="bh-table-wrap">
                <table class="bh-table">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Sebelum</th>
                            <th>Sesudah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->perubahan as $item)
                            <tr>
                                <td style="font-weight:600">{{ $item['label'] ?? $item['kolom'] ?? '-' }}</td>
                                <td class="bh-old">{{ $item['lama'] ?? '-' }}</td>
                                <td class="bh-new">{{ $item['baru'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bh-muted">
                Tidak ada rincian kolom pada catatan ini. Data baru atau data yang dihapus tetap tercatat
                lengkap pada ringkasan di atas.
            </div>
        @endif
    </div>
</div>

<x-filament-panels::page>
    <style>
        .mb-wrap { display: grid; gap: 1.25rem; }
        .mb-hero { position: relative; overflow: hidden; border-radius: .9rem; padding: 1.5rem; color: #fff; background: linear-gradient(135deg, #065f46 0%, #059669 55%, #34d399 100%); }
        .mb-hero::after { content: ""; position: absolute; right: -40px; top: -40px; width: 180px; height: 180px; border-radius: 9999px; background: rgba(255,255,255,.12); }
        .mb-eyebrow { font-size: .68rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; opacity: .85; }
        .mb-title { margin-top: .3rem; font-size: 1.65rem; font-weight: 800; letter-spacing: -.02em; line-height: 1.15; }
        .mb-subtitle { margin-top: .5rem; max-width: 62ch; font-size: .82rem; line-height: 1.55; opacity: .92; }
        .mb-hero-actions { margin-top: 1rem; display: flex; flex-wrap: wrap; gap: .5rem; }
        .mb-stats { display: grid; gap: .6rem; grid-template-columns: repeat(5, minmax(0, 1fr)); }
        @media (max-width: 900px) { .mb-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 480px) { .mb-stats { grid-template-columns: 1fr; } }
        .mb-stat { background: #fff; border-radius: .65rem; padding: .7rem .8rem; box-shadow: 0 0 0 1px rgba(17,24,39,.06); }
        .dark .mb-stat { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgba(255,255,255,.1); }
        .mb-stat-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: rgb(var(--gray-500)); }
        .mb-stat-value { margin-top: .15rem; font-size: 1.3rem; font-weight: 800; color: rgb(var(--gray-950)); }
        .dark .mb-stat-value { color: #fff; }
        .mb-grid { display: grid; gap: 1.15rem; }
        @media (min-width: 1000px) { .mb-grid { grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); align-items: start; } }
        .mb-card { background: #fff; border-radius: .75rem; box-shadow: 0 0 0 1px rgba(17,24,39,.06), 0 1px 2px rgba(17,24,39,.06); overflow: hidden; }
        .dark .mb-card { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgba(255,255,255,.1); }
        .mb-card-head { padding: .8rem 1rem; border-bottom: 1px solid rgb(var(--gray-200)); display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .dark .mb-card-head { border-color: rgba(255,255,255,.1); }
        .mb-card-title { font-size: .875rem; font-weight: 700; color: rgb(var(--gray-950)); }
        .dark .mb-card-title { color: #fff; }
        .mb-card-body { padding: 1rem; }
        .mb-muted { font-size: .78rem; color: rgb(var(--gray-500)); line-height: 1.55; }
        .dark .mb-muted { color: rgb(var(--gray-400)); }
        .mb-flow { display: grid; gap: .55rem; }
        .mb-step { display: grid; grid-template-columns: 1.6rem minmax(0, 1fr); gap: .6rem; align-items: start; }
        .mb-step-no { display: flex; align-items: center; justify-content: center; height: 1.6rem; width: 1.6rem; border-radius: 9999px; background: rgb(var(--primary-50)); color: rgb(var(--primary-700)); font-size: .72rem; font-weight: 800; }
        .dark .mb-step-no { background: rgba(var(--primary-500), .15); color: rgb(var(--primary-300)); }
        .mb-step-title { font-size: .78rem; font-weight: 700; color: rgb(var(--gray-900)); }
        .dark .mb-step-title { color: rgb(var(--gray-100)); }
        .mb-step-text { margin-top: .1rem; font-size: .74rem; color: rgb(var(--gray-500)); line-height: 1.5; }
        .dark .mb-step-text { color: rgb(var(--gray-400)); }
        .mb-toc { display: grid; gap: .35rem; }
        .mb-toc-item { display: grid; grid-template-columns: 1.45rem minmax(0, 1fr); gap: .5rem; padding: .42rem .45rem; border-radius: .45rem; }
        .mb-toc-item:hover { background: rgb(var(--gray-50)); }
        .dark .mb-toc-item:hover { background: rgba(255,255,255,.04); }
        .mb-toc-no { font-size: .68rem; font-weight: 800; color: rgb(var(--primary-600)); padding-top: .1rem; }
        .mb-toc-title { font-size: .78rem; font-weight: 600; color: rgb(var(--gray-800)); }
        .dark .mb-toc-title { color: rgb(var(--gray-200)); }
        .mb-toc-text { font-size: .7rem; color: rgb(var(--gray-500)); line-height: 1.45; }
        .dark .mb-toc-text { color: rgb(var(--gray-400)); }
        .mb-note { border-radius: .55rem; padding: .6rem .75rem; font-size: .74rem; line-height: 1.55; background: rgb(var(--info-50)); color: rgb(var(--info-800)); }
        .dark .mb-note { background: rgba(59,130,246,.14); color: #bfdbfe; }
        .mb-note-warning { background: rgb(var(--warning-50)); color: rgb(var(--warning-800)); }
        .dark .mb-note-warning { background: rgba(245,158,11,.14); color: #fde68a; }
        .mb-inline { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .6rem; }
        .mb-tag { display: inline-flex; padding: .15rem .5rem; border-radius: 9999px; background: rgb(var(--gray-100)); color: rgb(var(--gray-600)); font-size: .66rem; font-weight: 700; }
        .dark .mb-tag { background: rgba(255,255,255,.08); color: rgb(var(--gray-300)); }
    </style>

    <div class="mb-wrap">
        <div class="mb-hero">
            <div class="mb-eyebrow">Manual Book &middot; SPPG Samarang</div>
            <div class="mb-title">Panduan lengkap penggunaan aplikasi</div>
            <div class="mb-subtitle">
                Alur kerja data penerima manfaat, laporan harian, arsip kalender, laporan mingguan,
                impor &amp; ekspor Excel, serta jejak perubahan data. Unduh versi PDF untuk dibaca
                offline, dicetak, atau dibagikan kepada petugas.
            </div>

            <div class="mb-hero-actions">
                <x-filament::button
                    tag="a"
                    href="{{ route('unduhan.manual-book') }}"
                    icon="heroicon-o-arrow-down-tray"
                    color="white"
                >
                    Unduh Manual Book (PDF)
                </x-filament::button>
            </div>
        </div>

        <div class="mb-stats">
            <div class="mb-stat">
                <div class="mb-stat-label">Penerima Manfaat</div>
                <div class="mb-stat-value">{{ number_format($jumlahPenerima, 0, ',', '.') }}</div>
            </div>
            <div class="mb-stat">
                <div class="mb-stat-label">Status Aktif</div>
                <div class="mb-stat-value">{{ number_format($jumlahPenerimaAktif, 0, ',', '.') }}</div>
            </div>
            <div class="mb-stat">
                <div class="mb-stat-label">Lembaga</div>
                <div class="mb-stat-value">{{ number_format($jumlahLembaga, 0, ',', '.') }}</div>
            </div>
            <div class="mb-stat">
                <div class="mb-stat-label">Laporan Harian</div>
                <div class="mb-stat-value">{{ number_format($jumlahLaporan, 0, ',', '.') }}</div>
            </div>
            <div class="mb-stat">
                <div class="mb-stat-label">Riwayat Perubahan</div>
                <div class="mb-stat-value">{{ number_format($jumlahRiwayat, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="mb-grid">
            <div class="mb-card">
                <div class="mb-card-head">
                    <div class="mb-card-title">Alur Kerja Harian</div>
                    <div class="mb-muted">Senin &ndash; Jumat</div>
                </div>
                <div class="mb-card-body">
                    <div class="mb-flow">
                        <div class="mb-step">
                            <div class="mb-step-no">1</div>
                            <div>
                                <div class="mb-step-title">Periksa data master</div>
                                <div class="mb-step-text">Pastikan lembaga dan penerima manfaat sudah benar. Setiap perubahan wajib disertai alasan.</div>
                            </div>
                        </div>
                        <div class="mb-step">
                            <div class="mb-step-no">2</div>
                            <div>
                                <div class="mb-step-title">Buka Data Penerima Manfaat</div>
                                <div class="mb-step-text">Halaman LIVE menampilkan data hari ini per sekolah dan posyandu, lengkap dengan total kategori.</div>
                            </div>
                        </div>
                        <div class="mb-step">
                            <div class="mb-step-no">3</div>
                            <div>
                                <div class="mb-step-title">Simpan snapshot harian</div>
                                <div class="mb-step-text">Klik <strong>Simpan Data Hari Ini</strong>. Data tersimpan sebagai arsip tanggal tersebut.</div>
                            </div>
                        </div>
                        <div class="mb-step">
                            <div class="mb-step-no">4</div>
                            <div>
                                <div class="mb-step-title">Cek arsip dan laporan</div>
                                <div class="mb-step-text">Gunakan kalender untuk membuka tanggal lampau dan halaman Laporan Mingguan untuk rekap Senin&ndash;Jumat.</div>
                            </div>
                        </div>
                        <div class="mb-step">
                            <div class="mb-step-no">5</div>
                            <div>
                                <div class="mb-step-title">Ekspor bila diperlukan</div>
                                <div class="mb-step-text">Unduh Excel data penerima, lembaga, atau riwayat harian untuk arsip dan pelaporan.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-note" style="margin-top:.9rem">
                        <strong>Otomatis:</strong> setiap hari kerja pukul 01.00 WIB sistem membuat snapshot
                        carry-forward jika data hari tersebut belum ada. Sabtu dan Minggu dilewati.
                    </div>
                </div>
            </div>

            <div class="mb-card">
                <div class="mb-card-head">
                    <div class="mb-card-title">Isi Manual Book</div>
                    <div class="mb-muted">PDF &middot; A4</div>
                </div>
                <div class="mb-card-body">
                    <div class="mb-toc">
                        <div class="mb-toc-item">
                            <div class="mb-toc-no">01</div>
                            <div>
                                <div class="mb-toc-title">Mengenal aplikasi &amp; peran pengguna</div>
                                <div class="mb-toc-text">Tujuan, fitur utama, hak akses admin dan petugas.</div>
                            </div>
                        </div>
                        <div class="mb-toc-item">
                            <div class="mb-toc-no">02</div>
                            <div>
                                <div class="mb-toc-title">Login, dashboard, dan navigasi</div>
                                <div class="mb-toc-text">Cara masuk, membaca statistik, dan berpindah menu.</div>
                            </div>
                        </div>
                        <div class="mb-toc-item">
                            <div class="mb-toc-no">03</div>
                            <div>
                                <div class="mb-toc-title">Data penerima manfaat &amp; snapshot</div>
                                <div class="mb-toc-text">Alur LIVE, penyimpanan harian, arsip kalender, carry-forward.</div>
                            </div>
                        </div>
                        <div class="mb-toc-item">
                            <div class="mb-toc-no">04</div>
                            <div>
                                <div class="mb-toc-title">Laporan mingguan &amp; audit</div>
                                <div class="mb-toc-text">Rekap Senin&ndash;Jumat, 20 hari kerja, log audit laporan.</div>
                            </div>
                        </div>
                        <div class="mb-toc-item">
                            <div class="mb-toc-no">05</div>
                            <div>
                                <div class="mb-toc-title">Data master &amp; impor/ekspor Excel</div>
                                <div class="mb-toc-text">Template, kolom wajib, pencocokan data, penanganan error.</div>
                            </div>
                        </div>
                        <div class="mb-toc-item">
                            <div class="mb-toc-no">06</div>
                            <div>
                                <div class="mb-toc-title">Jejak perubahan penerima manfaat</div>
                                <div class="mb-toc-text">Alasan, tanggal, hari, pengguna, dan nilai sebelum/sesudah.</div>
                            </div>
                        </div>
                        <div class="mb-toc-item">
                            <div class="mb-toc-no">07</div>
                            <div>
                                <div class="mb-toc-title">Penjadwalan, troubleshooting, glosarium</div>
                                <div class="mb-toc-text">Scheduler, masalah umum, istilah, dan kontak pendukung.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-note mb-note-warning" style="margin-top:.9rem">
                        PDF dibuat dari data aplikasi saat ini: {{ $profile->nama }}.
                        Unduh ulang setelah ada perubahan besar agar isi manual tetap mutakhir.
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-card">
            <div class="mb-card-head">
                <div class="mb-card-title">Jejak Perubahan Penerima Manfaat</div>
                <x-filament::badge color="success">Aktif</x-filament::badge>
            </div>
            <div class="mb-card-body">
                <div class="mb-muted">
                    Setiap penambahan, perubahan, dan penghapusan penerima manfaat dicatat otomatis.
                    Catatan berisi tanggal, hari, waktu, pengguna, alasan, serta nilai sebelum dan sesudah.
                    Buka menu <strong>Laporan &rarr; Riwayat Perubahan</strong> untuk menelusuri semua catatan,
                    atau tab <strong>Riwayat Perubahan</strong> pada halaman edit penerima manfaat.
                </div>

                <div class="mb-inline">
                    <span class="mb-tag">Nama</span>
                    <span class="mb-tag">Lembaga</span>
                    <span class="mb-tag">Kategori</span>
                    <span class="mb-tag">Kelas</span>
                    <span class="mb-tag">Status</span>
                    <span class="mb-tag">NIK / NISN</span>
                    <span class="mb-tag">Alasan</span>
                    <span class="mb-tag">Tanggal &amp; hari</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

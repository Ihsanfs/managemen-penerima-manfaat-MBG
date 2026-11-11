<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryHistory;
use App\Models\Category;
use App\Models\DailyReport;
use App\Models\Institution;
use App\Models\SppgProfile;
use App\Services\DailyReportService;
use App\Services\SpreadsheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct(private readonly SpreadsheetService $spreadsheets) {}

    public function templatePenerima(): StreamedResponse
    {
        $institusi = Institution::query()
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(fn (Institution $institution): array => [
                $institution->nama,
                $institution->tipe,
                $institution->jenjang ?? '',
                $institution->is_active ? 'aktif' : 'non_aktif',
            ])
            ->all();

        $kategoriPenerima = Category::query()
            ->active()
            ->penerima()
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->mapWithKeys(fn (Category $category) => [$category->kode => $category->nama])
            ->all();

        if ($kategoriPenerima === []) {
            $kategoriPenerima = Beneficiary::KATEGORI;
        }

        $keteranganKategori = [
            'tk_paud' => 'TK / PAUD',
            'sd' => 'Sekolah Dasar',
            'smk' => 'SMK',
            'guru' => 'Guru',
            'balita' => 'Balita',
            'bumil' => 'Ibu Hamil',
            'busui' => 'Ibu Menyusui',
            'kader' => 'Kader Posyandu',
        ];

        $kelasWajib = [
            'tk_paud' => 'Tidak',
            'sd' => 'Ya (1-6)',
            'smk' => 'Ya (1-3 / X-XII)',
            'guru' => 'Tidak',
            'balita' => 'Tidak',
            'bumil' => 'Tidak',
            'busui' => 'Tidak',
            'kader' => 'Tidak',
        ];

        $kategoriList = implode(', ', array_keys($kategoriPenerima));

        $sampleInstitutionName = Institution::query()->value('nama') ?? 'SDN 1 Samarang';

        return $this->spreadsheets->download([
            [
                'title' => 'Data Penerima',
                'headings' => [
                    'nama_lembaga',
                    'nama',
                    'nik',
                    'nisn',
                    'jenis_kelamin',
                    'kategori',
                    'kelas',
                    'status',
                    'bulan',
                    'tahun',
                    'keterangan',
                ],
                'rows' => [
                    [
                        $sampleInstitutionName,
                        'Contoh Siswa Berhasil Diimpor',
                        '3200000000000001',
                        '0012345678',
                        'L',
                        'sd',
                        5,
                        'aktif',
                        (int) date('n'),
                        (int) date('Y'),
                        'Contoh baris data yang valid untuk diimpor. Hapus atau sesuaikan baris ini sebelum mengimpor file Anda.',
                    ],
                ],
                'text_columns' => [3, 4],
                'widths' => [3 => 26, 5 => 16, 6 => 14],
                'freeze' => true,
            ],
            [
                'title' => 'Petunjuk & Batas Ketentuan',
                'headings' => ['kolom', 'wajib', 'keterangan'],
                'rows' => [
                    ['nama_lembaga', 'Ya', 'Nama lembaga persis seperti pada sheet Referensi Lembaga.'],
                    ['nama', 'Ya', 'Nama lengkap penerima manfaat.'],
                    ['nik', 'Tidak', '16 digit. Format kolom sebagai Teks agar angka nol di depan tidak hilang.'],
                    ['nisn', 'Tidak', '10 digit. Format kolom sebagai Teks.'],
                    ['jenis_kelamin', 'Ya', 'L atau P.'],
                    ['kategori', 'Ya', 'Pilihan: ' . $kategoriList . '. Harus sesuai kategori yang telah dibuat di menu Kategori.'],
                    ['kelas', 'Untuk SD/SMP/SMA/SMK', 'SD: 1-6. SMP: 7-9. SMA/SMK: 1-3, X, XI, atau XII.'],
                    ['status', 'Tidak', 'aktif, non_aktif, atau pindah. Default aktif.'],
                    ['bulan', 'Tidak', '1-12. Default bulan berjalan.'],
                    ['tahun', 'Tidak', 'Contoh 2026. Default tahun berjalan.'],
                    ['keterangan', 'Tidak', 'Catatan tambahan.'],
                    ['Batas Ukuran File', 'Wajib', 'Maksimal 10 MB per unggahan file (.xlsx, .xls, atau .csv).'],
                    ['Pemrosesan Chunk', 'Otomatis', 'Data diproses secara bertahap (per 500 baris per batch) untuk mengoptimalkan memori server dan mencegah overload.'],
                    ['Sistem Anti-Duplikat', 'Otomatis', 'Data yang sudah ada (berdasarkan NIK, NISN, atau kombinasi lembaga + nama + kategori + kelas) akan dilewati dan dihitung sebagai duplikat tanpa menimpa data lama.'],
                    ['Baris Contoh', 'Perhatian', 'Hapus baris contoh di sheet Data Penerima sebelum melakukan impor data riil agar tidak ikut tersimpan.'],
                ],
                'widths' => [1 => 22, 2 => 16, 3 => 80],
                'wrap' => true,
            ],
            [
                'title' => 'Referensi Lembaga',
                'headings' => ['nama_lembaga', 'tipe', 'jenjang', 'status'],
                'rows' => $institusi,
                'widths' => [1 => 28, 2 => 14, 3 => 14, 4 => 14],
                'wrap' => true,
            ],
            [
                'title' => 'Referensi Kategori',
                'headings' => ['kategori', 'keterangan', 'kelas_wajib'],
                'rows' => collect($kategoriPenerima)
                    ->map(fn (string $nama, string $kode): array => [
                        $kode,
                        $keteranganKategori[$kode] ?? $nama,
                        $kelasWajib[$kode] ?? 'Tidak',
                    ])
                    ->values()
                    ->all(),
                'widths' => [1 => 16, 2 => 28, 3 => 20],
            ],
        ], 'template-impor-penerima-manfaat.xlsx');
    }

    public function exportPenerima(): StreamedResponse
    {
        $kategoriPenerima = Category::query()
            ->active()
            ->penerima()
            ->orderBy('urutan')
            ->orderBy('nama')
            ->pluck('nama', 'kode')
            ->all();

        if ($kategoriPenerima === []) {
            $kategoriPenerima = Beneficiary::KATEGORI;
        }

        $kategoriUrutan = array_flip(array_keys($kategoriPenerima));

        $beneficiaries = Beneficiary::query()
            ->with('institution')
            ->get()
            ->sortBy(fn (Beneficiary $beneficiary): string => sprintf(
                '%s-%05d-%02d-%02d-%s',
                $beneficiary->institution?->tipe === 'posyandu' ? '2' : '1',
                $beneficiary->institution?->urutan ?? 0,
                $kategoriUrutan[$beneficiary->kategori] ?? 99,
                $beneficiary->kelas ?? 0,
                mb_strtolower($beneficiary->nama),
            ))
            ->values();

        $rows = $beneficiaries->map(fn (Beneficiary $beneficiary): array => [
            $beneficiary->institution?->nama ?? '',
            $beneficiary->nama,
            $beneficiary->nik ?? '',
            $beneficiary->nisn ?? '',
            $beneficiary->jenis_kelamin,
            $beneficiary->kategori,
            $beneficiary->kelas ?? '',
            $beneficiary->status,
            $beneficiary->bulan,
            $beneficiary->tahun,
            $beneficiary->keterangan ?? '',
        ])->all();

        $ringkasan = Institution::query()
            ->withCount([
                'beneficiaries as aktif' => fn ($query) => $query->where('status', 'aktif'),
                'beneficiaries as non_aktif' => fn ($query) => $query->where('status', 'non_aktif'),
                'beneficiaries as pindah' => fn ($query) => $query->where('status', 'pindah'),
            ])
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->map(fn (Institution $institution): array => [
                $institution->nama,
                $institution->tipe,
                $institution->jenjang ?? '',
                $institution->aktif,
                $institution->non_aktif,
                $institution->pindah,
                $institution->aktif + $institution->non_aktif + $institution->pindah,
            ])
            ->all();

        $ringkasan[] = [
            'TOTAL',
            '',
            '',
            $beneficiaries->where('status', 'aktif')->count(),
            $beneficiaries->where('status', 'non_aktif')->count(),
            $beneficiaries->where('status', 'pindah')->count(),
            $beneficiaries->count(),
        ];

        return $this->spreadsheets->download([
            [
                'title' => 'Data Penerima',
                'headings' => [
                    'nama_lembaga',
                    'nama',
                    'nik',
                    'nisn',
                    'jenis_kelamin',
                    'kategori',
                    'kelas',
                    'status',
                    'bulan',
                    'tahun',
                    'keterangan',
                ],
                'rows' => $rows,
                'text_columns' => [3, 4],
                'widths' => [1 => 26, 2 => 28, 3 => 20, 4 => 16, 6 => 12, 11 => 36],
                'wrap' => true,
            ],
            [
                'title' => 'Ringkasan per Lembaga',
                'headings' => ['lembaga', 'tipe', 'jenjang', 'aktif', 'non_aktif', 'pindah', 'total'],
                'rows' => $ringkasan,
                'widths' => [1 => 28],
            ],
        ], 'data-penerima-manfaat-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function templateLembaga(): StreamedResponse
    {
        return $this->spreadsheets->download([
            [
                'title' => 'Data Lembaga',
                'headings' => [
                    'nama',
                    'tipe',
                    'jenjang',
                    'alamat',
                    'penanggung_jawab',
                    'telepon',
                    'urutan',
                    'is_active',
                ],
                'rows' => [
                    [
                        'SDN Contoh Baru',
                        'sekolah',
                        'sd',
                        'Jalan Contoh Raya No. 1, Samarang',
                        'Kepala Sekolah Contoh, S.Pd.',
                        '081234567890',
                        100,
                        'aktif',
                    ],
                ],
                'text_columns' => [6],
                'widths' => [1 => 28, 4 => 40, 5 => 24, 6 => 18],
                'freeze' => true,
            ],
            [
                'title' => 'Petunjuk & Batas Ketentuan',
                'headings' => ['kolom', 'wajib', 'keterangan'],
                'rows' => [
                    ['nama', 'Ya', 'Nama lembaga. Impor mencocokkan nama tanpa membedakan huruf besar/kecil.'],
                    ['tipe', 'Ya', 'sekolah atau posyandu.'],
                    ['jenjang', 'Tidak', 'Jenjang sekolah (tk_paud, sd, smp, sma, smk, slb, dst). Kosongkan untuk posyandu. Dapat dielola melalui menu Kategori.'],
                    ['alamat', 'Tidak', 'Alamat lembaga.'],
                    ['penanggung_jawab', 'Tidak', 'Nama penanggung jawab atau kepala sekolah.'],
                    ['telepon', 'Tidak', 'Nomor telepon / WhatsApp.'],
                    ['urutan', 'Tidak', 'Angka urutan tampilan. Jika kosong, otomatis di urutan berikutnya.'],
                    ['is_active', 'Tidak', 'aktif atau non_aktif. Default aktif.'],
                    ['Batas Ukuran File', 'Wajib', 'Maksimal 10 MB per unggahan file (.xlsx, .xls, atau .csv).'],
                    ['Pemrosesan Chunk', 'Otomatis', 'Data diproses secara bertahap (per 500 baris per batch) untuk mengoptimalkan memori server.'],
                    ['Sistem Anti-Duplikat', 'Otomatis', 'Lembaga dengan nama yang sudah ada akan dilewati dan dihitung sebagai duplikat tanpa menimpa data lama.'],
                    ['Baris Contoh', 'Perhatian', 'Hapus baris contoh di sheet Data Lembaga sebelum melakukan impor data riil agar tidak ikut tersimpan.'],
                ],
                'widths' => [1 => 22, 2 => 14, 3 => 80],
                'wrap' => true,
            ],
            [
                'title' => 'Referensi Nilai',
                'headings' => ['kolom', 'nilai', 'keterangan'],
                'rows' => array_merge(
                    [
                        ['tipe', 'sekolah', 'TK/PAUD, SD, SMP, SMA, SMK, SLB, dan lainnya.'],
                        ['tipe', 'posyandu', 'Posyandu balita, ibu hamil, ibu menyusui, dan kader.'],
                    ],
                    collect(Category::optionsJenjang())
                        ->map(fn (string $nama, string $kode): array => ['jenjang', $kode, 'Untuk lembaga ' . $nama . '.'])
                        ->values()
                        ->all(),
                    [
                        ['is_active', 'aktif', 'Lembaga ikut dihitung pada laporan harian.'],
                        ['is_active', 'non_aktif', 'Lembaga tidak ikut dihitung pada laporan harian.'],
                    ],
                ),
                'widths' => [1 => 16, 2 => 20, 3 => 70],
                'wrap' => true,
            ],
        ], 'template-impor-lembaga.xlsx');
    }

    public function exportLembaga(): StreamedResponse
    {
        $institusi = Institution::query()
            ->withCount([
                'beneficiaries as aktif' => fn ($query) => $query->where('status', 'aktif'),
                'beneficiaries as total_penerima',
            ])
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        $rows = $institusi->map(fn (Institution $institution): array => [
            $institution->nama,
            $institution->tipe,
            $institution->jenjang ?? '',
            $institution->alamat ?? '',
            $institution->penanggung_jawab ?? '',
            $institution->telepon ?? '',
            $institution->urutan,
            $institution->is_active ? 'aktif' : 'non_aktif',
        ])->all();

        $ringkasan = $institusi
            ->groupBy('tipe')
            ->map(fn ($group, string $tipe): array => [
                $tipe,
                $group->count(),
                $group->sum('aktif'),
                $group->sum('total_penerima'),
            ])
            ->values()
            ->all();

        $ringkasan[] = [
            'TOTAL',
            $institusi->count(),
            $institusi->sum('aktif'),
            $institusi->sum('total_penerima'),
        ];

        return $this->spreadsheets->download([
            [
                'title' => 'Data Lembaga',
                'headings' => [
                    'nama',
                    'tipe',
                    'jenjang',
                    'alamat',
                    'penanggung_jawab',
                    'telepon',
                    'urutan',
                    'is_active',
                ],
                'rows' => $rows,
                'text_columns' => [6],
                'widths' => [1 => 28, 4 => 40, 5 => 24, 6 => 18],
                'wrap' => true,
            ],
            [
                'title' => 'Ringkasan',
                'headings' => ['tipe', 'jumlah_lembaga', 'penerima_aktif', 'total_penerima'],
                'rows' => $ringkasan,
                'widths' => [1 => 18],
            ],
        ], 'data-lembaga-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function cetakPenerimaManfaat(): Response
    {
        $tanggal = request()->query('tanggal');

        try {
            $date = $tanggal ? Carbon::parse($tanggal)->startOfDay() : today();
        } catch (\Throwable) {
            $date = today();
        }

        if ($date->isAfter(today())) {
            $date = today();
        }

        $data = app(DailyReportService::class)->dataForDate($date);
        $institusi = collect($data['snapshot']['institusi']);

        $jmlLaki = Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
        $jmlPerempuan = Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'P')->count();

        $pdf = Pdf::loadView('pdf.data-penerima-manfaat', [
            'profile' => SppgProfile::current(),
            'tanggal' => $date,
            'data' => $data,
            'sekolah' => $institusi->where('tipe', 'sekolah')->values(),
            'posyandu' => $institusi->where('tipe', 'posyandu')->values(),
            'totals' => $data['snapshot']['totals'],
            'jmlLaki' => $jmlLaki,
            'jmlPerempuan' => $jmlPerempuan,
            'dicetak' => now()->locale('id')->translatedFormat('d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-penerima-manfaat-' . $date->format('Y-m-d') . '.pdf');
    }

    public function cetakDaftarPenerima(): Response
    {
        $status = request()->query('status');
        $kategori = request()->query('kategori');
        $institution = request()->query('institution_id');
        $jenisKelamin = request()->query('jenis_kelamin');

        $query = Beneficiary::query()->with('institution');

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($institution) {
            $query->where('institution_id', $institution);
        }

        if ($jenisKelamin) {
            $query->where('jenis_kelamin', $jenisKelamin);
        }

        $beneficiaries = $query
            ->orderBy('institution_id')
            ->orderBy('nama')
            ->get();

        $jmlLaki = $beneficiaries->where('jenis_kelamin', 'L')->count();
        $jmlPerempuan = $beneficiaries->where('jenis_kelamin', 'P')->count();

        // Group by institution name
        $groups = $beneficiaries->groupBy(fn (Beneficiary $b): string => $b->institution?->nama ?? 'Tanpa Lembaga');

        // Build filter label
        $filterParts = [];

        if ($status && $status !== 'semua') {
            $filterParts[] = 'Status: ' . (Beneficiary::STATUS[$status] ?? $status);
        }

        if ($kategori) {
            $filterParts[] = 'Kategori: ' . (Category::labelPenerima($kategori) ?? $kategori);
        }

        if ($institution) {
            $inst = Institution::query()->find($institution);
            $filterParts[] = 'Lembaga: ' . ($inst?->nama ?? '#' . $institution);
        }

        if ($jenisKelamin) {
            $filterParts[] = 'Jenis Kelamin: ' . (Beneficiary::JENIS_KELAMIN[$jenisKelamin] ?? $jenisKelamin);
        }

        $pdf = Pdf::loadView('pdf.daftar-penerima-manfaat', [
            'profile' => SppgProfile::current(),
            'beneficiaries' => $beneficiaries,
            'groups' => $groups,
            'total' => $beneficiaries->count(),
            'jmlLaki' => $jmlLaki,
            'jmlPerempuan' => $jmlPerempuan,
            'filterLabel' => $filterParts !== [] ? implode(' | ', $filterParts) : null,
            'dicetak' => now()->locale('id')->translatedFormat('d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('daftar-penerima-manfaat-' . now()->format('Ymd-His') . '.pdf');
    }

    public function manualBook(): Response
    {
        $pdf = Pdf::loadView('pdf.manual-book', $this->manualBookData())
            ->setPaper('a4', 'portrait');

        return $pdf->download('manual-book-sppg-samarang.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    public function manualBookData(): array
    {
        return [
            'profile' => SppgProfile::current(),
            'dicetak' => now()->locale('id')->translatedFormat('d F Y H:i') . ' WIB',
            'jumlahPenerima' => Beneficiary::query()->count(),
            'jumlahPenerimaAktif' => Beneficiary::query()->where('status', 'aktif')->count(),
            'jumlahLembaga' => Institution::query()->count(),
            'jumlahLaporan' => DailyReport::query()->count(),
            'jumlahRiwayat' => BeneficiaryHistory::query()->count(),
        ];
    }

    public function exportRiwayat(): StreamedResponse
    {
        $reports = DailyReport::query()
            ->with(['details.institution', 'user'])
            ->orderBy('tanggal')
            ->get();

        $riwayat = $reports->map(fn (DailyReport $report): array => [
            $report->tanggal->toDateString(),
            $report->hari,
            $report->tk_paud,
            $report->sd_1_3,
            $report->sd_4_6,
            $report->smk,
            $report->guru,
            $report->balita,
            $report->bumil,
            $report->busui,
            $report->kader,
            $report->total,
            $report->sumber_label,
            $report->user?->name ?? 'Sistem',
            $report->saved_at?->format('Y-m-d H:i:s') ?? '',
            $report->catatan ?? '',
        ])->all();

        $rincian = [];

        foreach ($reports as $report) {
            $details = $report->details
                ->sortBy(fn ($detail): string => sprintf(
                    '%s-%05d-%s',
                    $detail->institution?->tipe === 'posyandu' ? '2' : '1',
                    $detail->institution?->urutan ?? 0,
                    $detail->institution?->nama ?? '',
                ));

            foreach ($details as $detail) {
                $rincian[] = [
                    $report->tanggal->toDateString(),
                    $detail->institution?->nama ?? 'Lembaga #' . $detail->institution_id,
                    $detail->institution?->tipe ?? '',
                    $detail->institution?->jenjang ?? '',
                    $detail->tk_paud,
                    $detail->kelas_1,
                    $detail->kelas_2,
                    $detail->kelas_3,
                    $detail->kelas_4,
                    $detail->kelas_5,
                    $detail->kelas_6,
                    $detail->smk,
                    $detail->guru,
                    $detail->balita,
                    $detail->bumil,
                    $detail->busui,
                    $detail->kader,
                    $detail->total,
                ];
            }
        }

        return $this->spreadsheets->download([
            [
                'title' => 'Riwayat Harian',
                'headings' => [
                    'tanggal',
                    'hari',
                    ...array_values(DailyReport::KATEGORI),
                    'total',
                    'sumber',
                    'disimpan_oleh',
                    'disimpan_pada',
                    'catatan',
                ],
                'rows' => $riwayat,
                'widths' => [1 => 13, 2 => 11, 17 => 10, 19 => 22, 20 => 20, 21 => 36],
                'wrap' => true,
            ],
            [
                'title' => 'Rincian per Lembaga',
                'headings' => [
                    'tanggal',
                    'nama_lembaga',
                    'tipe',
                    'jenjang',
                    ...array_map(
                        fn (string $key): string => str_replace(' ', '_', strtolower($key)),
                        array_values(DailyReportService::DETAIL_KEYS),
                    ),
                    'total',
                ],
                'rows' => $rincian,
                'widths' => [1 => 13, 2 => 28, 3 => 12, 4 => 12],
                'wrap' => true,
            ],
        ], 'riwayat-harian-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function cetakLaporanMingguan(): Response
    {
        $minggu = request()->query('minggu');

        try {
            $start = $minggu ? Carbon::parse($minggu)->startOfWeek(Carbon::MONDAY) : today()->startOfWeek(Carbon::MONDAY);
        } catch (\Throwable) {
            $start = today()->startOfWeek(Carbon::MONDAY);
        }

        $data = app(DailyReportService::class)->weeklyData($start);

        $pdf = Pdf::loadView('pdf.laporan-mingguan', [
            'profile' => SppgProfile::current(),
            'mingguMulai' => $start,
            'akhir_minggu' => $start->copy()->addDays(4),
            ...$data,
            'dicetak' => now()->locale('id')->translatedFormat('d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-mingguan-' . $start->format('Y-m-d') . '.pdf');
    }

    public function cetakDistribusiGis(): Response
    {
        $profile = SppgProfile::current();
        $institutions = Institution::query()
            ->active()
            ->withCount('activeBeneficiaries')
            ->orderBy('urutan')
            ->get()
            ->map(function (Institution $institution) use ($profile) {
                $distance = $institution->distanceToSppg();
                return [
                    'id' => $institution->id,
                    'nama' => $institution->nama,
                    'tipe' => $institution->tipe,
                    'jenjang' => $institution->jenjang_label,
                    'alamat' => $institution->alamat,
                    'latitude' => $institution->latitude,
                    'longitude' => $institution->longitude,
                    'distance' => $distance,
                    'beneficiaries_count' => $institution->active_beneficiaries_count,
                ];
            })
            ->sortBy('distance')
            ->values();

        $maxDistance = $institutions->max('distance') ?? 0;
        $avgDistance = $institutions->whereNotNull('distance')->avg('distance') ?? 0;

        $pdf = Pdf::loadView('pdf.distribusi-gis', [
            'profile' => $profile,
            'institutions' => $institutions,
            'maxDistance' => round($maxDistance, 2),
            'avgDistance' => round($avgDistance, 2),
            'totalInstitutions' => $institutions->count(),
            'totalBeneficiaries' => $institutions->sum('beneficiaries_count'),
            'dicetak' => now()->locale('id')->translatedFormat('d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('peta-distribusi-sppg-' . now()->format('Ymd-His') . '.pdf');
    }
}

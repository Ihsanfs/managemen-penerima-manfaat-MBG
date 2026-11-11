<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\DailyReport;
use App\Models\DailyReportDetail;
use App\Models\Institution;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DailyReportService
{
    /**
     * Kolom rincian per lembaga (legacy, untuk kompatibilitas arsip lama).
     */
    public const DETAIL_KEYS = [
        'tk_paud',
        'kelas_1',
        'kelas_2',
        'kelas_3',
        'kelas_4',
        'kelas_5',
        'kelas_6',
        'smk',
        'guru',
        'balita',
        'bumil',
        'busui',
        'kader',
    ];

    /**
     * Pemetaan kolom legacy detail ke grup kategori laporan.
     *
     * @return array<string, string>
     */
    public const LEGACY_TO_GRUP = [
        'tk_paud' => 'tk_paud',
        'kelas_1' => 'sd_1_3',
        'kelas_2' => 'sd_1_3',
        'kelas_3' => 'sd_1_3',
        'kelas_4' => 'sd_4_6',
        'kelas_5' => 'sd_4_6',
        'kelas_6' => 'sd_4_6',
        'smk' => 'smk',
        'guru' => 'guru',
        'balita' => 'balita',
        'bumil' => 'bumil',
        'busui' => 'busui',
        'kader' => 'kader',
    ];

    /**
     * Daftar grup laporan dalam urutan standar.
     *
     * @return array<int, string>
     */
    public const GRUP_URUTAN = [
        'tk_paud',
        'sd_1_3',
        'sd_4_6',
        'smk',
        'guru',
        'balita',
        'bumil',
        'busui',
        'kader',
    ];

    /**
     * Hitung snapshot data penerima manfaat dari data penerima yang aktif.
     *
     * @return array{totals: array<string, int>, total: int, institusi: array<int, array>}
     */
    public function buildSnapshot(CarbonInterface $date): array
    {
        $institutions = Institution::query()
            ->active()
            ->with('category')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get()
            ->sortBy(fn (Institution $institution) => ($institution->tipe === 'sekolah' ? 0 : 1000) + $institution->urutan)
            ->values();

        $raw = DB::table('beneficiaries')
            ->join('institutions', 'institutions.id', '=', 'beneficiaries.institution_id')
            ->where('institutions.is_active', true)
            ->where('beneficiaries.status', 'aktif')
            ->select('beneficiaries.institution_id', 'beneficiaries.kategori', 'beneficiaries.kelas', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('beneficiaries.institution_id', 'beneficiaries.kategori', 'beneficiaries.kelas')
            ->get();

        $counts = [];
        foreach ($raw as $row) {
            $counts[$row->institution_id][$row->kategori][(string) $row->kelas] = (int) $row->jumlah;
        }

        $rows = [];
        $totals = $this->emptyTotals();
        $kategoriJson = $this->emptyKategoriJson();

        foreach ($institutions as $institution) {
            $detail = $this->emptyDetail();
            $institutionCounts = $counts[$institution->id] ?? [];

            foreach ($institutionCounts as $kategori => $kelasCounts) {
                foreach ($kelasCounts as $kelas => $jumlah) {
                    $this->applyCount($detail, $kategori, $kelas === '' ? null : (int) $kelas, $jumlah);
                }
            }

            $detail['total'] = array_sum($this->detailOnly($detail));

            $this->addDetailToTotals($totals, $detail);
            $this->addDetailToKategoriJson($kategoriJson, $detail);

            $rows[] = [
                'id' => $institution->id,
                'nama' => $institution->nama,
                'tipe' => $institution->tipe,
                'jenjang' => $institution->jenjang,
                'category_id' => $institution->category_id,
                'category' => $institution->category ? [
                    'kode' => $institution->category->kode,
                    'nama' => $institution->category->nama,
                    'jenis' => $institution->category->jenis,
                ] : null,
                'rincian' => $detail,
                'total' => $detail['total'],
            ];
        }

        return [
            'totals' => $totals,
            'total' => array_sum($totals),
            'kategori_json' => $kategoriJson,
            'institusi' => $rows,
        ];
    }

    /**
     * Simpan snapshot untuk tanggal tertentu.
     */
    public function save(CarbonInterface $date, string $sumber = 'manual', ?User $user = null, ?string $catatan = null): DailyReport
    {
        $date = Carbon::parse($date)->startOfDay();

        if ($date->isAfter(today())) {
            throw new InvalidArgumentException('Tanggal tidak boleh melebihi hari ini.');
        }

        return DB::transaction(function () use ($date, $sumber, $user, $catatan) {
            $snapshot = $this->buildSnapshot($date);
            $dateString = $date->toDateString();

            $existing = DailyReport::query()
                ->whereDate('tanggal', $dateString)
                ->first();

            $totalSebelum = $existing?->total ?? 0;

            if ($existing) {
                $existing->update([
                    ...$snapshot['totals'],
                    'total' => $snapshot['total'],
                    'sumber' => $sumber,
                    'saved_by' => $user?->id,
                    'saved_at' => now(),
                    'catatan' => $catatan,
                    'kategori_json' => $snapshot['kategori_json'],
                ]);
                $report = $existing;
            } else {
                $report = DailyReport::query()->create([
                    'tanggal' => $dateString,
                    ...$snapshot['totals'],
                    'total' => $snapshot['total'],
                    'sumber' => $sumber,
                    'saved_by' => $user?->id,
                    'saved_at' => now(),
                    'catatan' => $catatan,
                    'kategori_json' => $snapshot['kategori_json'],
                ]);
            }

            $report->details()->delete();

            foreach ($snapshot['institusi'] as $row) {
                $report->details()->create([
                    'institution_id' => $row['id'],
                    'tk_paud' => $row['rincian']['tk_paud'],
                    'kelas_1' => $row['rincian']['kelas_1'],
                    'kelas_2' => $row['rincian']['kelas_2'],
                    'kelas_3' => $row['rincian']['kelas_3'],
                    'kelas_4' => $row['rincian']['kelas_4'],
                    'kelas_5' => $row['rincian']['kelas_5'],
                    'kelas_6' => $row['rincian']['kelas_6'],
                    'smk' => $row['rincian']['smk'],
                    'guru' => $row['rincian']['guru'],
                    'balita' => $row['rincian']['balita'],
                    'bumil' => $row['rincian']['bumil'],
                    'busui' => $row['rincian']['busui'],
                    'kader' => $row['rincian']['kader'],
                    'total' => $row['total'],
                    'kategori_json' => $this->kategoriJsonFromDetail($row['rincian']),
                ]);
            }

            AuditLog::query()->create([
                'user_id' => $user?->id,
                'tanggal_data' => $date->toDateString(),
                'aksi' => $existing ? 'update' : 'simpan',
                'sumber' => $sumber,
                'total_sebelum' => $totalSebelum,
                'total_sesudah' => $snapshot['total'],
                'selisih' => $snapshot['total'] - $totalSebelum,
                'catatan' => $catatan,
            ]);

            return $report->fresh(['details.institution', 'user']);
        });
    }

    /**
     * Salin snapshot terakhir ke tanggal tertentu (carry-forward otomatis).
     */
    public function autoSnapshot(CarbonInterface $date): ?DailyReport
    {
        $date = Carbon::parse($date)->startOfDay();

        if ($date->isWeekend()) {
            return null;
        }

        if ($date->isAfter(today())) {
            return null;
        }

        if (DailyReport::query()->whereDate('tanggal', $date->toDateString())->exists()) {
            return null;
        }

        $previous = DailyReport::query()
            ->with('details')
            ->whereDate('tanggal', '<', $date->toDateString())
            ->orderByDesc('tanggal')
            ->first();

        if (! $previous) {
            return null;
        }

        return DB::transaction(function () use ($date, $previous) {
            $report = DailyReport::query()->create([
                'tanggal' => $date->toDateString(),
                'tk_paud' => $previous->tk_paud,
                'sd_1_3' => $previous->sd_1_3,
                'sd_4_6' => $previous->sd_4_6,
                'smk' => $previous->smk,
                'guru' => $previous->guru,
                'balita' => $previous->balita,
                'bumil' => $previous->bumil,
                'busui' => $previous->busui,
                'kader' => $previous->kader,
                'total' => $previous->total,
                'sumber' => 'otomatis',
                'saved_by' => null,
                'saved_at' => now(),
                'catatan' => 'Tidak diupdate, mengikuti data ' . $previous->tanggal->toDateString(),
                'kategori_json' => $previous->kategori_json,
            ]);

            foreach ($previous->details as $detail) {
                $report->details()->create([
                    'institution_id' => $detail->institution_id,
                    'tk_paud' => $detail->tk_paud,
                    'kelas_1' => $detail->kelas_1,
                    'kelas_2' => $detail->kelas_2,
                    'kelas_3' => $detail->kelas_3,
                    'kelas_4' => $detail->kelas_4,
                    'kelas_5' => $detail->kelas_5,
                    'kelas_6' => $detail->kelas_6,
                    'smk' => $detail->smk,
                    'guru' => $detail->guru,
                    'balita' => $detail->balita,
                    'bumil' => $detail->bumil,
                    'busui' => $detail->busui,
                    'kader' => $detail->kader,
                    'total' => $detail->total,
                    'kategori_json' => $detail->kategori_json,
                ]);
            }

            AuditLog::query()->create([
                'user_id' => null,
                'tanggal_data' => $date->toDateString(),
                'aksi' => 'otomatis',
                'sumber' => 'otomatis',
                'total_sebelum' => $previous->total,
                'total_sesudah' => $previous->total,
                'selisih' => 0,
                'catatan' => 'Tidak diupdate, mengikuti data ' . $previous->tanggal->toDateString(),
            ]);

            return $report->fresh(['details.institution', 'user']);
        });
    }

    /**
     * Ambil data untuk ditampilkan pada halaman utama.
     */
    public function dataForDate(CarbonInterface $date): array
    {
        $date = Carbon::parse($date)->startOfDay();

        if ($date->isToday()) {
            return [
                'mode' => 'live',
                'snapshot' => $this->buildSnapshot($date),
                'report' => DailyReport::query()->whereDate('tanggal', $date->toDateString())->first(),
                'fallback' => false,
                'fallbackDate' => null,
                'noData' => false,
            ];
        }

        $report = DailyReport::query()
            ->with(['details.institution', 'user'])
            ->whereDate('tanggal', $date->toDateString())
            ->first();

        if ($report) {
            return [
                'mode' => 'arsip',
                'snapshot' => $this->snapshotFromReport($report),
                'report' => $report,
                'fallback' => false,
                'fallbackDate' => null,
                'noData' => false,
            ];
        }

        $previous = DailyReport::query()
            ->with(['details.institution', 'user'])
            ->whereDate('tanggal', '<', $date->toDateString())
            ->orderByDesc('tanggal')
            ->first();

        if ($previous) {
            return [
                'mode' => 'arsip',
                'snapshot' => $this->snapshotFromReport($previous),
                'report' => null,
                'fallback' => true,
                'fallbackDate' => $previous->tanggal,
                'noData' => false,
            ];
        }

        return [
            'mode' => 'arsip',
            'snapshot' => $this->buildSnapshot($date),
            'report' => null,
            'fallback' => false,
            'fallbackDate' => null,
            'noData' => true,
        ];
    }

    /**
     * Ubah record laporan menjadi struktur snapshot.
     */
    public function snapshotFromReport(DailyReport $report): array
    {
        $totals = $report->kategori_totals;

        $rows = $report->details
            ->sortBy(fn (DailyReportDetail $detail) => ($detail->institution?->tipe === 'sekolah' ? 0 : 1000)
                + ($detail->institution?->urutan ?? 0))
            ->map(function (DailyReportDetail $detail) {
                $rincian = [
                    'tk_paud' => $detail->tk_paud,
                    'kelas_1' => $detail->kelas_1,
                    'kelas_2' => $detail->kelas_2,
                    'kelas_3' => $detail->kelas_3,
                    'kelas_4' => $detail->kelas_4,
                    'kelas_5' => $detail->kelas_5,
                    'kelas_6' => $detail->kelas_6,
                    'smk' => $detail->smk,
                    'guru' => $detail->guru,
                    'balita' => $detail->balita,
                    'bumil' => $detail->bumil,
                    'busui' => $detail->busui,
                    'kader' => $detail->kader,
                ];

                return [
                    'id' => $detail->institution_id,
                    'nama' => $detail->institution?->nama ?? 'Lembaga #' . $detail->institution_id,
                    'tipe' => $detail->institution?->tipe ?? 'sekolah',
                    'jenjang' => $detail->institution?->jenjang,
                    'category_id' => $detail->institution?->category_id,
                    'category' => $detail->institution?->category ? [
                        'kode' => $detail->institution->category->kode,
                        'nama' => $detail->institution->category->nama,
                        'jenis' => $detail->institution->category->jenis,
                    ] : null,
                    'rincian' => $rincian,
                    'total' => $detail->total,
                    'kategori_json' => $detail->kategori_json,
                ];
            })
            ->values()
            ->all();

        return [
            'totals' => $totals,
            'total' => $report->total,
            'kategori_json' => $report->kategori_json,
            'institusi' => $rows,
        ];
    }

    /**
     * Data laporan mingguan (Senin-Jumat) dan 20 hari kerja terakhir.
     */
    public function weeklyData(CarbonInterface $weekStart): array
    {
        $weekStart = Carbon::parse($weekStart)->startOfWeek(Carbon::MONDAY)->startOfDay();

        $days = collect(range(0, 4))
            ->map(function (int $offset) use ($weekStart) {
                $date = $weekStart->copy()->addDays($offset);
                $report = DailyReport::query()
                    ->whereDate('tanggal', $date->toDateString())
                    ->first();

                $status = match (true) {
                    $date->isAfter(today()) => 'Belum jatuh tempo',
                    $report?->sumber === 'manual' => 'Sudah diupdate',
                    $report?->sumber === 'otomatis' => 'Otomatis (carry-forward)',
                    default => 'Belum ada data',
                };

                return [
                    'tanggal' => $date,
                    'hari' => $date->locale('id')->translatedFormat('l'),
                    'status' => $status,
                    'sumber' => $report?->sumber,
                    'total' => $report?->total,
                    'report_id' => $report?->id,
                ];
            })
            ->all();

        $reports = DailyReport::query()
            ->whereDate('tanggal', '<=', today()->toDateString())
            ->orderByDesc('tanggal')
            ->limit(60)
            ->get()
            ->filter(fn (DailyReport $report) => ! $report->tanggal->isWeekend())
            ->take(20)
            ->values();

        $workdays = $reports->map(function (DailyReport $report, int $index) use ($reports) {
            $previous = $reports->get($index + 1);

            return [
                'tanggal' => $report->tanggal,
                'hari' => $report->tanggal->locale('id')->translatedFormat('l'),
                'status' => $report->sumber === 'manual' ? 'Sudah diupdate' : 'Otomatis',
                'sumber' => $report->sumber,
                'total' => $report->total,
                'perubahan' => $previous ? $report->total - $previous->total : null,
            ];
        })->all();

        return [
            'week_start' => $weekStart,
            'days' => $days,
            'workdays' => $workdays,
            'sudah_update' => collect($days)->where('sumber', 'manual')->count(),
            'otomatis' => collect($days)->where('sumber', 'otomatis')->count(),
        ];
    }

    private function emptyDetail(): array
    {
        return array_fill_keys([...self::DETAIL_KEYS, 'total'], 0);
    }

    private function emptyTotals(): array
    {
        return array_fill_keys(array_keys(DailyReport::KATEGORI), 0);
    }

    private function emptyKategoriJson(): array
    {
        return array_fill_keys(self::GRUP_URUTAN, 0);
    }

    private function detailOnly(array $detail): array
    {
        return array_intersect_key($detail, array_flip(self::DETAIL_KEYS));
    }

    private function applyCount(array &$detail, string $kategori, ?int $kelas, int $jumlah): void
    {
        // TK / PAUD masuk langsung ke kolom tk_paud.
        if ($kategori === 'tk_paud') {
            $detail['tk_paud'] += $jumlah;
            return;
        }

        // SD dibagi per kelas (1-3 → sd_1_3, 4-6 → sd_4_6).
        if ($kategori === 'sd' && $kelas !== null && $kelas >= 1 && $kelas <= 6) {
            $detail['kelas_' . $kelas] += $jumlah;
            return;
        }

        // Kategori lainnya ditentukan oleh grup laporan dari tabel categories.
        $category = Category::query()
            ->where('jenis', Category::JENIS_PENERIMA)
            ->where('kode', $kategori)
            ->first();

        $grup = $category?->grup;

        if ($grup === 'smk') {
            $detail['smk'] += $jumlah;
            return;
        }

        if ($grup === 'guru') {
            $detail['guru'] += $jumlah;
            return;
        }

        if ($grup === 'balita') {
            $detail['balita'] += $jumlah;
            return;
        }

        if ($grup === 'bumil') {
            $detail['bumil'] += $jumlah;
            return;
        }

        if ($grup === 'busui') {
            $detail['busui'] += $jumlah;
            return;
        }

        if ($grup === 'kader') {
            $detail['kader'] += $jumlah;
            return;
        }

        // Fallback untuk kategori legacy yang belum ada di DB.
        if (array_key_exists($kategori, $detail)) {
            $detail[$kategori] += $jumlah;
        }
    }

    private function addDetailToTotals(array &$totals, array $detail): void
    {
        $totals['tk_paud'] += $detail['tk_paud'];
        $totals['sd_1_3'] += $detail['kelas_1'] + $detail['kelas_2'] + $detail['kelas_3'];
        $totals['sd_4_6'] += $detail['kelas_4'] + $detail['kelas_5'] + $detail['kelas_6'];
        $totals['smk'] += $detail['smk'];
        $totals['guru'] += $detail['guru'];
        $totals['balita'] += $detail['balita'];
        $totals['bumil'] += $detail['bumil'];
        $totals['busui'] += $detail['busui'];
        $totals['kader'] += $detail['kader'];
    }

    /**
     * Akumulasi detail ke kategori_json (per grup laporan).
     */
    private function addDetailToKategoriJson(array &$kategoriJson, array $detail): void
    {
        foreach (self::LEGACY_TO_GRUP as $legacyKey => $grup) {
            if (isset($detail[$legacyKey])) {
                $kategoriJson[$grup] += (int) $detail[$legacyKey];
            }
        }
    }

    /**
     * Bangun kategori_json dari satu rincian detail (per grup).
     */
    private function kategoriJsonFromDetail(array $rincian): array
    {
        $json = $this->emptyKategoriJson();

        foreach (self::LEGACY_TO_GRUP as $legacyKey => $grup) {
            if (isset($rincian[$legacyKey])) {
                $json[$grup] = (int) $rincian[$legacyKey];
            }
        }

        return $json;
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Beneficiary;
use App\Models\DailyReport;
use App\Models\SppgProfile;
use App\Services\DailyReportService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DataPenerimaManfaat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Data Penerima Manfaat';

    protected static ?string $title = 'Data Penerima Manfaat';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.data-penerima-manfaat';

    public string $tanggal;

    public string $bulan;

    public string $tanggalSimpan;

    public function mount(): void
    {
        $this->tanggal = today()->toDateString();
        $this->bulan = today()->format('Y-m');
        $this->tanggalSimpan = today()->toDateString();
    }

    public function updatedTanggal(string $value): void
    {
        try {
            $date = Carbon::parse($value);
        } catch (\Throwable) {
            $this->tanggal = today()->toDateString();

            return;
        }

        if ($date->isAfter(today())) {
            $this->tanggal = today()->toDateString();
            $date = today();
        }

        $this->bulan = $date->format('Y-m');
    }

    public function pilihTanggal(string $tanggal): void
    {
        $date = Carbon::parse($tanggal);

        if ($date->isAfter(today())) {
            return;
        }

        $this->tanggal = $date->toDateString();
        $this->bulan = $date->format('Y-m');
    }

    public function kembaliHariIni(): void
    {
        $this->tanggal = today()->toDateString();
        $this->bulan = today()->format('Y-m');
        $this->tanggalSimpan = today()->toDateString();
    }

    public function geserBulan(int $delta): void
    {
        $this->bulan = Carbon::parse($this->bulan . '-01')
            ->addMonths($delta)
            ->format('Y-m');
    }

    public function simpanHariIni(): void
    {
        try {
            $report = app(DailyReportService::class)->save(today(), 'manual', auth()->user());

            $this->tanggal = today()->toDateString();
            $this->bulan = today()->format('Y-m');

            Notification::make()
                ->title('Data berhasil disimpan')
                ->body('Total ' . $report->total . ' penerima manfaat untuk ' . $report->tanggal->locale('id')->translatedFormat('d F Y') . '.')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Gagal menyimpan data')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function simpanTanggal(): void
    {
        $this->validate([
            'tanggalSimpan' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'tanggalSimpan.required' => 'Pilih tanggal yang ingin disimpan.',
            'tanggalSimpan.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ]);

        try {
            $date = Carbon::parse($this->tanggalSimpan);
            $report = app(DailyReportService::class)->save($date, 'manual', auth()->user(), 'Diisi untuk tanggal lain');

            $this->tanggal = $date->toDateString();
            $this->bulan = $date->format('Y-m');

            Notification::make()
                ->title('Data tersimpan untuk ' . $date->locale('id')->translatedFormat('d F Y'))
                ->body('Total ' . $report->total . ' penerima manfaat. Data LIVE saat ini disimpan sebagai tanggal tersebut.')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Gagal menyimpan data')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getViewData(): array
    {
        $date = Carbon::parse($this->tanggal);
        $service = app(DailyReportService::class);
        $data = $service->dataForDate($date);

        $aktif = Beneficiary::query()->where('status', 'aktif');
        $laki = $aktif->where('jenis_kelamin', 'L')->count();
        $perempuan = $aktif->where('jenis_kelamin', 'P')->count();

        return [
            'profile' => SppgProfile::current(),
            'calendar' => $this->buildCalendar(),
            'data' => $data,
            'totals' => $data['snapshot']['totals'],
            'institusi' => collect($data['snapshot']['institusi']),
            'sekolah' => collect($data['snapshot']['institusi'])->where('tipe', 'sekolah')->values(),
            'posyandu' => collect($data['snapshot']['institusi'])->where('tipe', 'posyandu')->values(),
            'tanggalAktif' => $date,
            'isToday' => $date->isToday(),
            'savedToday' => DailyReport::query()->whereDate('tanggal', today()->toDateString())->first(),
            'jmlLaki' => $laki,
            'jmlPerempuan' => $perempuan,
        ];
    }

    private function buildCalendar(): array
    {
        $month = Carbon::parse($this->bulan . '-01')->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $reportDates = DailyReport::query()
            ->whereBetween('tanggal', [$month->toDateString(), $end->toDateString()])
            ->get(['tanggal'])
            ->map(fn (DailyReport $report) => $report->tanggal->toDateString())
            ->all();

        $cells = [];

        for ($i = 0; $i < $month->dayOfWeekIso - 1; $i++) {
            $cells[] = null;
        }

        for ($day = 1; $day <= $end->day; $day++) {
            $date = $month->copy()->day($day);
            $cells[] = [
                'tanggal' => $date->toDateString(),
                'hari' => $day,
                'is_today' => $date->isToday(),
                'is_selected' => $date->toDateString() === $this->tanggal,
                'has_report' => in_array($date->toDateString(), $reportDates, true),
                'is_future' => $date->isAfter(today()),
            ];
        }

        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }

        return [
            'label' => $month->locale('id')->translatedFormat('F Y'),
            'weeks' => array_chunk($cells, 7),
        ];
    }
}

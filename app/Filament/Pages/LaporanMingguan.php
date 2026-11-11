<?php

namespace App\Filament\Pages;

use App\Services\DailyReportService;
use Carbon\Carbon;
use Filament\Pages\Page;

class LaporanMingguan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Mingguan';

    protected static ?string $title = 'Laporan Mingguan';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.laporan-mingguan';

    public string $minggu;

    public function mount(): void
    {
        $this->minggu = today()->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    public function updatedMinggu(string $value): void
    {
        try {
            $this->minggu = Carbon::parse($value)->startOfWeek(Carbon::MONDAY)->toDateString();
        } catch (\Throwable) {
            $this->minggu = today()->startOfWeek(Carbon::MONDAY)->toDateString();
        }
    }

    public function geserMinggu(int $delta): void
    {
        $this->minggu = Carbon::parse($this->minggu)
            ->addWeeks($delta)
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();
    }

    public function kembaliMingguIni(): void
    {
        $this->minggu = today()->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    protected function getViewData(): array
    {
        $start = Carbon::parse($this->minggu)->startOfWeek(Carbon::MONDAY);
        $data = app(DailyReportService::class)->weeklyData($start);

        return [
            ...$data,
            'mingguMulai' => $start,
            'akhir_minggu' => $start->copy()->addDays(4),
            'minggu_ini' => today()->startOfWeek(Carbon::MONDAY)->toDateString(),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Beneficiary;
use App\Models\DailyReport;
use App\Models\Institution;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Beneficiary::query()->where('status', 'aktif')->count();
        $laki = Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
        $perempuan = Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'P')->count();
        $sekolah = Institution::query()->where('tipe', 'sekolah')->where('is_active', true)->count();
        $posyandu = Institution::query()->where('tipe', 'posyandu')->where('is_active', true)->count();
        $todayReport = DailyReport::query()->whereDate('tanggal', today()->toDateString())->first();

        $recent = DailyReport::query()
            ->whereDate('tanggal', '<=', today()->toDateString())
            ->orderByDesc('tanggal')
            ->limit(7)
            ->get()
            ->reverse()
            ->pluck('total')
            ->all();

        return [
            Stat::make('Total Penerima Manfaat', number_format($total))
                ->description('Penerima aktif dari seluruh lembaga')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart($recent ?: [0]),
            Stat::make('Laki-laki', number_format($laki))
                ->description('Penerima aktif jenis kelamin Laki-laki')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),
            Stat::make('Perempuan', number_format($perempuan))
                ->description('Penerima aktif jenis kelamin Perempuan')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),
            Stat::make('Sekolah', number_format($sekolah))
                ->description('Sekolah & madrasah aktif')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('gray'),
            Stat::make('Posyandu', number_format($posyandu))
                ->description('Posyandu aktif')
                ->descriptionIcon('heroicon-m-heart')
                ->color('gray'),
            Stat::make('Status Hari Ini', $todayReport ? number_format($todayReport->total) . ' PM' : 'Belum disimpan')
                ->description($todayReport
                    ? 'Tersimpan ' . $todayReport->sumber_label . ' pukul ' . $todayReport->saved_at?->format('H:i')
                    : 'Klik Data Penerima Manfaat untuk menyimpan')
                ->descriptionIcon($todayReport ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($todayReport ? 'success' : 'danger'),
        ];
    }
}

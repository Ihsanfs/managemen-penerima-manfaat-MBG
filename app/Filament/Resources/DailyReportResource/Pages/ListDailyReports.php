<?php

namespace App\Filament\Resources\DailyReportResource\Pages;

use App\Filament\Resources\DailyReportResource;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDailyReports extends ListRecords
{
    protected static string $resource = DailyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('eksporRiwayat')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(fn (): string => route('unduhan.export-riwayat')),
            Actions\Action::make('simpanHariIni')
                ->label('Simpan Data Hari Ini')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Simpan data penerima manfaat hari ini?')
                ->modalDescription(fn (): string => 'Tanggal ' . today()->locale('id')->translatedFormat('d F Y') . '. Data yang sudah ada akan diperbarui.')
                ->action(function (): void {
                    $report = app(DailyReportService::class)->save(today(), 'manual', auth()->user());

                    Notification::make()
                        ->title('Data berhasil disimpan')
                        ->body('Total ' . $report->total . ' penerima manfaat untuk ' . $report->tanggal->locale('id')->translatedFormat('d F Y') . '.')
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => ! DailyReport::query()->whereDate('tanggal', today()->toDateString())->exists()),
        ];
    }
}

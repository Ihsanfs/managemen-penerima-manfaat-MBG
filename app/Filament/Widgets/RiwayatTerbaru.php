<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DailyReportResource;
use App\Models\DailyReport;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RiwayatTerbaru extends BaseWidget
{
    protected static ?string $heading = 'Riwayat Simpan Terbaru';

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(DailyReport::query()->orderByDesc('tanggal')->limit(6))
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn (DailyReport $record): string => $record->hari),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total PM')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('sumber')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DailyReport::SUMBER[$state] ?? $state)
                    ->color(fn (string $state): string => $state === 'manual' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('saved_at')
                    ->label('Disimpan')
                    ->dateTime('d M Y H:i')
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-m-eye')
                    ->url(fn (DailyReport $record): string => DailyReportResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false)
            ->defaultSort('tanggal', 'desc');
    }
}

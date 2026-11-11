<?php

namespace App\Filament\Resources\BeneficiaryResource\RelationManagers;

use App\Models\BeneficiaryHistory;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $title = 'Riwayat Perubahan';

    protected static ?string $modelLabel = 'Riwayat Perubahan';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('aksi')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('aksi')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => BeneficiaryHistory::AKSI[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'dibuat' => 'success',
                        'diperbarui' => 'info',
                        'dihapus' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('alasan')
                    ->label('Alasan')
                    ->wrap()
                    ->limit(100),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->placeholder('Sistem'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->headerActions([])
            ->actions([]);
    }
}

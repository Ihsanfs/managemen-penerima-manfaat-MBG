<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Log Audit';

    protected static ?string $modelLabel = 'Log Audit';

    protected static ?string $pluralModelLabel = 'Log Audit';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('tanggal_data')
                    ->label('Tanggal Data')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('aksi')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AuditLog::AKSI[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'simpan' => 'success',
                        'update' => 'info',
                        'otomatis' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sumber')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => $state === 'manual' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->placeholder('Sistem'),
                Tables\Columns\TextColumn::make('total_sebelum')
                    ->label('Sebelum')
                    ->numeric()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_sesudah')
                    ->label('Sesudah')
                    ->numeric()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('selisih')
                    ->label('Selisih')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? '+' . $state : (string) $state),
                Tables\Columns\TextColumn::make('catatan')
                    ->label('Catatan')
                    ->placeholder('-')
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('aksi')
                    ->label('Aksi')
                    ->options(AuditLog::AKSI),
                Tables\Filters\SelectFilter::make('sumber')
                    ->label('Sumber')
                    ->options([
                        'manual' => 'Manual',
                        'otomatis' => 'Otomatis',
                    ]),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_data', '>=', $date))
                            ->when($data['sampai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_data', '<=', $date));
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}

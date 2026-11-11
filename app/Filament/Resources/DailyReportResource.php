<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyReportResource\Pages;
use App\Models\DailyReport;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DailyReportResource extends Resource
{
    protected static ?string $model = DailyReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Riwayat Harian';

    protected static ?string $modelLabel = 'Riwayat Harian';

    protected static ?string $pluralModelLabel = 'Riwayat Harian';

    protected static ?int $navigationSort = 1;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Ringkasan')
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal')
                            ->label('Tanggal Data')
                            ->date('d F Y')
                            ->helperText(fn (DailyReport $record): string => $record->hari),
                        Infolists\Components\TextEntry::make('total')
                            ->label('Total Penerima Manfaat')
                            ->weight('bold')
                            ->size('lg')
                            ->color('success'),
                        Infolists\Components\TextEntry::make('sumber')
                            ->label('Sumber')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => DailyReport::SUMBER[$state] ?? $state)
                            ->color(fn (string $state): string => $state === 'manual' ? 'success' : 'warning'),
                        Infolists\Components\TextEntry::make('saved_at')
                            ->label('Waktu Simpan')
                            ->dateTime('d M Y H:i'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Disimpan Oleh')
                            ->placeholder('Sistem'),
                        Infolists\Components\TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Rekap Kategori')
                    ->schema([
                        Infolists\Components\TextEntry::make('tk_paud')->label('TK / PAUD')->badge()->color('info'),
                        Infolists\Components\TextEntry::make('sd_1_3')->label('Kelas 1-3 SD')->badge()->color('primary'),
                        Infolists\Components\TextEntry::make('sd_4_6')->label('Kelas 4-6 SD')->badge()->color('primary'),
                        Infolists\Components\TextEntry::make('smk')->label('SMK')->badge()->color('warning'),
                        Infolists\Components\TextEntry::make('guru')->label('Guru')->badge()->color('gray'),
                        Infolists\Components\TextEntry::make('balita')->label('Balita')->badge()->color('success'),
                        Infolists\Components\TextEntry::make('bumil')->label('Bumil')->badge()->color('danger'),
                        Infolists\Components\TextEntry::make('busui')->label('Busui')->badge()->color('warning'),
                        Infolists\Components\TextEntry::make('kader')->label('Kader')->badge()->color('gray'),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Rincian per Lembaga')
                    ->schema([
                        Infolists\Components\ViewEntry::make('details')
                            ->hiddenLabel()
                            ->view('filament.infolists.daily-report-details'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn (DailyReport $record): string => $record->hari)
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total PM')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sumber')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DailyReport::SUMBER[$state] ?? $state)
                    ->color(fn (string $state): string => $state === 'manual' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('saved_at')
                    ->label('Waktu Simpan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->placeholder('Sistem')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tk_paud')->label('TK/PAUD')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sd_1_3')->label('K1-3 SD')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sd_4_6')->label('K4-6 SD')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('smk')->label('SMK')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guru')->label('Guru')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('balita')->label('Balita')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bumil')->label('Bumil')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('busui')->label('Busui')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kader')->label('Kader')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sumber')
                    ->label('Sumber')
                    ->options(DailyReport::SUMBER),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '>=', $date))
                            ->when($data['sampai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyReports::route('/'),
            'view' => Pages\ViewDailyReport::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

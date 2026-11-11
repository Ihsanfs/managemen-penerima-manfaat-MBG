<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryHistoryResource\Pages;
use App\Models\Beneficiary;
use App\Models\BeneficiaryHistory;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BeneficiaryHistoryResource extends Resource
{
    protected static ?string $model = BeneficiaryHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Riwayat Perubahan';

    protected static ?string $modelLabel = 'Riwayat Perubahan';

    protected static ?string $pluralModelLabel = 'Riwayat Perubahan';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('beneficiary_nama')
                    ->label('Penerima Manfaat')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('institution_nama')
                    ->label('Lembaga')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? (Category::labelPenerima($state) ?? $state) : '-')
                    ->color(fn (?string $state): string => match (Category::labelPenerima($state ?? null) ?? null) {
                        'TK / PAUD' => 'info',
                        'SD' => 'primary',
                        'SMK' => 'warning',
                        'Guru' => 'gray',
                        'Balita' => 'success',
                        'Ibu Hamil' => 'danger',
                        'Ibu Menyusui' => 'warning',
                        'Kader' => 'gray',
                        default => 'gray',
                    }),
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
                    ->limit(90)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->placeholder('Sistem')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('aksi')
                    ->label('Aksi')
                    ->options(BeneficiaryHistory::AKSI),
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(Category::optionsPenerima()),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '>=', $date))
                            ->when($data['sampai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (BeneficiaryHistory $record): string => 'Riwayat perubahan — ' . $record->beneficiary_nama)
                    ->modalContent(fn (BeneficiaryHistory $record) => view('filament.histories.detail', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('3xl'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaryHistories::route('/'),
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
}

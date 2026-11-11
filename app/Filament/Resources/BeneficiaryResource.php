<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Filament\Resources\BeneficiaryResource\RelationManagers\HistoriesRelationManager;
use App\Models\Beneficiary;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Penerima Manfaat';

    protected static ?string $modelLabel = 'Penerima Manfaat';

    protected static ?string $pluralModelLabel = 'Penerima Manfaat';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Penerima Manfaat')
                    ->schema([
                        Forms\Components\Select::make('institution_id')
                            ->label('Lembaga')
                            ->relationship('institution', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->nullable()
                            ->stripCharacters([' ', '-', '.'])
                            ->length(16)
                            ->maxLength(16)
                            ->rules(['digits:16'])
                            ->unique(ignoreRecord: true)
                            ->inputMode('numeric')
                            ->placeholder('Boleh dikosongkan')
                            ->helperText('Opsional. Jika diisi harus 16 digit angka dan belum terdaftar.'),
                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->nullable()
                            ->stripCharacters([' ', '-', '.'])
                            ->length(10)
                            ->maxLength(10)
                            ->rules(['digits:10'])
                            ->unique(ignoreRecord: true)
                            ->inputMode('numeric')
                            ->placeholder('Boleh dikosongkan')
                            ->helperText('Opsional. Jika diisi harus 10 digit angka dan belum terdaftar.'),
                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(Beneficiary::JENIS_KELAMIN)
                            ->required()
                            ->default('L'),
                        Forms\Components\Select::make('kategori')
                            ->label('Kategori')
                            ->options(Category::optionsPenerima())
                            ->required()
                            ->live()
                            ->helperText('Pilih kategori penerima manfaat. Kategori dapat dielola melalui menu Kategori.'),
                        Forms\Components\Select::make('kelas')
                            ->label('Kelas')
                            ->options(fn (Get $get): array => match (true) {
                                in_array($get('kategori'), ['sma', 'smk'], true) => [10 => 'Kelas X', 11 => 'Kelas XI', 12 => 'Kelas XII'],
                                $get('kategori') === 'smp' => [7 => 'Kelas 7', 8 => 'Kelas 8', 9 => 'Kelas 9'],
                                $get('kategori') === 'sd' => [1 => 'Kelas 1', 2 => 'Kelas 2', 3 => 'Kelas 3', 4 => 'Kelas 4', 5 => 'Kelas 5', 6 => 'Kelas 6'],
                                default => [],
                            })
                            ->visible(fn (Get $get): bool => in_array($get('kategori'), ['sd', 'smp', 'sma', 'smk'], true))
                            ->required(fn (Get $get): bool => in_array($get('kategori'), ['sd', 'smp', 'sma', 'smk'], true)),
                        Forms\Components\TextInput::make('jurusan')
                            ->label('Jurusan')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => in_array($get('kategori'), ['sma', 'smk'], true)),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Beneficiary::STATUS)
                            ->required()
                            ->default('aktif'),
                        Forms\Components\TextInput::make('bulan')
                            ->label('Bulan Data')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->default(fn () => (int) now()->format('n')),
                        Forms\Components\TextInput::make('tahun')
                            ->label('Tahun Data')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->default(fn () => (int) now()->format('Y')),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Jejak Perubahan')
                    ->description('Catat alasan perubahan agar riwayat data penerima manfaat mudah ditelusuri.')
                    ->visible(fn (string $operation): bool => $operation === 'edit')
                    ->schema([
                        Forms\Components\Textarea::make('alasan_perubahan')
                            ->label('Alasan Perubahan')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Contoh: penerima pindah domisili, perbaikan NIK, atau perubahan status karena sudah lulus.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->getStateUsing(fn (Beneficiary $record, $livewire): string => 
                        (string) (((int) ($livewire->getTablePage() ?? 1) - 1) * ((int) ($livewire->getTableRecordsPerPage() ?? 10)) + 
                        (int) $livewire->getTableRecords()->search(fn ($item) => $item->getKey() === $record->getKey()) + 1)
                    )
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('institution.nama')
                    ->label('Lembaga')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Category::labelPenerima($state) ?? $state)
                    ->color(fn (string $state): string => match (Category::labelPenerima($state) ?? $state) {
                        'TK / PAUD' => 'info',
                        'SD' => 'primary',
                        'SMP' => 'warning',
                        'SMK' => 'warning',
                        'Guru' => 'gray',
                        'Balita' => 'success',
                        'Ibu Hamil' => 'danger',
                        'Ibu Menyusui' => 'warning',
                        'Kader' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state, Beneficiary $record): string => $record->kelas_label),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('L / P')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'L' ? 'info' : 'warning'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Beneficiary::STATUS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non_aktif' => 'danger',
                        'pindah' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Lembaga')
                    ->relationship('institution', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(Category::optionsPenerima()),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Beneficiary::STATUS),
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(Beneficiary::JENIS_KELAMIN),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Hapus penerima manfaat?')
                    ->modalDescription('Alasan penghapusan wajib diisi dan akan disimpan pada Riwayat Perubahan.')
                    ->form([
                        Forms\Components\Textarea::make('alasan')
                            ->label('Alasan Penghapusan')
                            ->required()
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->using(function (Beneficiary $record, array $data): bool {
                        $record->tracking_reason = $data['alasan'];

                        return (bool) $record->delete();
                    })
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Hapus beberapa penerima manfaat?')
                        ->modalDescription('Satu alasan akan dipakai untuk semua data yang dihapus.')
                        ->form([
                            Forms\Components\Textarea::make('alasan')
                                ->label('Alasan Penghapusan')
                                ->required()
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->using(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                if (! $record instanceof Beneficiary) {
                                    continue;
                                }

                                $record->tracking_reason = $data['alasan'];
                                $record->delete();
                            }
                        })
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ]),
            ])
            ->defaultSort('nama');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('institution');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Beneficiary::query()->where('status', 'aktif')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getRelations(): array
    {
        return [
            HistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'edit' => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }
}

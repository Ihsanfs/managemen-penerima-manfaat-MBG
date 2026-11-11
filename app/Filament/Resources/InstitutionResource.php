<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstitutionResource\Pages;
use App\Models\Category;
use App\Models\Institution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Lembaga';

    protected static ?string $modelLabel = 'Lembaga';

    protected static ?string $pluralModelLabel = 'Lembaga';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Lembaga')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lembaga')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('tipe')
                            ->label('Tipe')
                            ->options(Institution::TIPE)
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('jenjang')
                            ->label('Jenjang')
                            ->options(fn (): array => Category::optionsJenjang() ?: Institution::JENJANG)
                            ->visible(fn (Get $get): bool => $get('tipe') === 'sekolah')
                            ->required(fn (Get $get): bool => $get('tipe') === 'sekolah')
                            ->rules([
                                function () {
                                    return function ($attribute, $value, $fail) {
                                        if ($value === null || $value === '') {
                                            return;
                                        }

                                        $validJenjang = Category::query()
                                            ->where('jenis', Category::JENIS_JENJANG)
                                            ->pluck('kode')
                                            ->all();

                                        if ($validJenjang === []) {
                                            $validJenjang = array_keys(Institution::JENJANG);
                                        }

                                        if (! in_array($value, $validJenjang, true)) {
                                            $fail("Jenjang '{$value}' tidak valid. Pilihan: " . implode(', ', $validJenjang) . ".");
                                        }
                                    };
                                },
                            ]),
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori Lembaga')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'nama',
                                modifyQueryUsing: fn (Builder $query) => $query->where('jenis', Category::JENIS_LEMBAGA)
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('tipe') === 'lembaga' || true)
                            ->helperText('Pilih kategori jenis lembaga (TK, SD, SMP, SMK, Posyandu, dll). Dikelola melalui menu Kategori.')
                            ->rules([
                                function (Get $get) {
                                    return function ($attribute, $value, $fail) use ($get) {
                                        if ($value === null || $value === '') {
                                            return;
                                        }

                                        $category = Category::query()->find($value);

                                        if (! $category) {
                                            $fail('Kategori lembaga tidak ditemukan.');
                                            return;
                                        }

                                        if ($category->jenis !== Category::JENIS_LEMBAGA) {
                                            $jenisLabel = Category::jenisLabels()[$category->jenis] ?? $category->jenis;
                                            $fail("Kategori '{$category->nama}' adalah jenis {$jenisLabel}, bukan lembaga. Pilih kategori jenis lembaga.");
                                        }
                                    };
                                },
                            ]),
                        Forms\Components\TextInput::make('penanggung_jawab')
                            ->label('Penanggung Jawab')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('urutan')
                            ->label('Urutan Tampil')
                            ->options(function (?Institution $record) {
                                $used = Institution::query()
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->pluck('nama', 'urutan')
                                    ->all();

                                $max = max(30, (int) Institution::max('urutan') + 5);
                                $options = [];
                                for ($i = 1; $i <= $max; $i++) {
                                    if (isset($used[$i])) {
                                        $options[$i] = "Urutan {$i} — Terpakai ({$used[$i]})";
                                    } else {
                                        $options[$i] = "Urutan {$i} — Tersedia";
                                    }
                                }
                                return $options;
                            })
                            ->default(fn () => (int) (Institution::max('urutan') ?? 0) + 1)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->helperText('Pilih nomor urut tampil dari dropdown. Nomor yang sudah terpakai ditandai dengan nama lembaga.')
                            ->rules([
                                function ($get, ?Institution $record) {
                                    return function ($attribute, $value, $fail) use ($record) {
                                        if ($value === null || $value === '') {
                                            return;
                                        }

                                        $value = (int) $value;

                                        $query = Institution::query()->where('urutan', $value);
                                        if ($record) {
                                            $query->where('id', '!=', $record->id);
                                        }
                                        $existing = $query->first();

                                        if ($existing) {
                                            $available = self::suggestAvailableUrutan();
                                            $fail("⚠️ PERINGATAN DUPLIKAT: Urutan {$value} sudah digunakan oleh lembaga '{$existing->nama}'. Mohon pilih nomor urut lain yang masih tersedia (Rekomendasi: {$available}).");
                                        }
                                    };
                                },
                            ]),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->getStateUsing(fn (Institution $record, $livewire): string => 
                        (string) (((int) ($livewire->getTablePage() ?? 1) - 1) * ((int) ($livewire->getTableRecordsPerPage() ?? 10)) + 
                        (int) $livewire->getTableRecords()->search(fn ($item) => $item->getKey() === $record->getKey()) + 1)
                    )
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'sekolah' ? 'info' : 'success')
                    ->formatStateUsing(fn (string $state): string => Institution::TIPE[$state] ?? $state),
                Tables\Columns\TextColumn::make('jenjang')
                    ->label('Jenjang')
                    ->badge()
                    ->placeholder('-')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? (Category::labelJenjang($state) ?? Institution::JENJANG[$state] ?? $state) : null),
                Tables\Columns\TextColumn::make('category.nama')
                    ->label('Kategori Lembaga')
                    ->badge()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('active_beneficiaries_count')
                    ->label('Penerima Aktif')
                    ->counts('activeBeneficiaries')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('beneficiaries_laki_count')
                    ->label('L')
                    ->badge()
                    ->counts('activeLaki')
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('beneficiaries_perempuan_count')
                    ->label('P')
                    ->badge()
                    ->counts('activePerempuan')
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('urutan')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options(Institution::TIPE),
                Tables\Filters\SelectFilter::make('jenjang')
                    ->label('Jenjang')
                    ->options(fn (): array => Category::optionsJenjang() ?: Institution::JENJANG),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori Lembaga')
                    ->relationship('category', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('deactivate')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn (Institution $record): bool => $record->is_active && (auth()->user()?->isAdmin() ?? false))
                    ->requiresConfirmation()
                    ->modalHeading('Nonaktifkan lembaga?')
                    ->modalDescription('Lembaga yang dinonaktifkan tidak akan muncul sebagai pilihan pada form dan laporan harian.')
                    ->action(fn (Institution $record) => $record->update(['is_active' => false])),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn (Institution $record): bool => $record->beneficiaries()->count() === 0 && (auth()->user()?->isAdmin() ?? false))
                    ->requiresConfirmation()
                    ->modalHeading('Hapus lembaga?')
                    ->modalDescription('Lembaga yang masih memiliki penerima manfaat tidak dapat dihapus. Nonaktifkan saja jika ingin menyembunyikannya.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->beneficiaries()->count() === 0) {
                                    $record->delete();
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('urutan');
    }

    /**
     * Sarankan urutan yang belum digunakan (maksimal 3 saran).
     *
     * @return string
     */
    public static function suggestAvailableUrutan(): string
    {
        $used = Institution::query()->orderBy('urutan')->pluck('urutan')->all();
        $used = array_map('intval', $used);

        $suggestions = [];
        $candidate = 1;

        while (count($suggestions) < 3 && $candidate <= 100) {
            if (! in_array($candidate, $used, true)) {
                $suggestions[] = (string) $candidate;
            }

            $candidate++;
        }

        return implode(', ', $suggestions);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('activeBeneficiaries')
            ->withCount(['activeLaki as beneficiaries_laki_count'])
            ->withCount(['activePerempuan as beneficiaries_perempuan_count']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'edit' => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }
}

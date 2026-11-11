<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::withoutGlobalScopes()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kategori')
                    ->schema([
                        Forms\Components\TextInput::make('kode')
                            ->label('Kode Kategori')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Contoh: tk_paud, sd, smk, guru, balita, bumil, busui, kader. Digunakan untuk pencocokan data.'),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Contoh: TK / PAUD, Sekolah Dasar, Guru, Balita, dll.'),
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis')
                            ->options(Category::jenisLabels())
                            ->required()
                            ->default(Category::JENIS_PENERIMA)
                            ->live(),
                        Forms\Components\Select::make('grup')
                            ->label('Grup Laporan')
                            ->options(Category::grupLabels())
                            ->nullable()
                            ->visible(fn (Get $get): bool => $get('jenis') === Category::JENIS_PENERIMA)
                            ->helperText('Hanya untuk kategori penerima manfaat yang ingin dikomitasi pada laporan harian.'),
                        Forms\Components\Toggle::make('has_class')
                            ->label('Aktifkan Kelas')
                            ->default(false)
                            ->visible(fn (Get $get): bool => $get('jenis') === Category::JENIS_PENERIMA)
                            ->helperText('Aktifkan jika kategori ini menggunakan pembagian kelas (seperti SD, SMP, SMA, SMK).'),
                        Forms\Components\Select::make('urutan')
                            ->label('Urutan Tampil')
                            ->options(function (?Category $record) {
                                $used = Category::query()
                                    ->withoutGlobalScopes()
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->pluck('nama', 'urutan')
                                    ->all();

                                $max = max(30, (int) Category::query()->withoutGlobalScopes()->max('urutan') + 5);
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
                            ->default(fn () => (int) (Category::query()->withoutGlobalScopes()->max('urutan') ?? 0) + 1)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->helperText('Pilih nomor urut tampil dari dropdown. Nomor yang sudah terpakai ditandai dengan nama kategori.')
                            ->rules([
                                function ($get, ?Category $record) {
                                    return function ($attribute, $value, $fail) use ($record) {
                                        if ($value === null || $value === '') {
                                            return;
                                        }

                                        $value = (int) $value;

                                        $query = Category::query()->withoutGlobalScopes()->where('urutan', $value);
                                        if ($record) {
                                            $query->where('id', '!=', $record->id);
                                        }
                                        $existing = $query->first();

                                        if ($existing) {
                                            $fail("⚠️ PERINGATAN DUPLIKAT: Urutan {$value} sudah digunakan oleh kategori '{$existing->nama}'. Mohon pilih nomor urut lain yang masih tersedia.");
                                        }
                                    };
                                },
                            ]),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Category::jenisLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Category::JENIS_PENERIMA => 'success',
                        Category::JENIS_LEMBAGA => 'info',
                        Category::JENIS_JENJANG => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('grup')
                    ->label('Grup Laporan')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? (Category::grupLabels()[$state] ?? $state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Digunakan')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(fn (Category $record): int => $record->usageCount()),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('urutan')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis')
                    ->options(Category::jenisLabels()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Category $record): bool => $record->is_active || auth()->user()?->isAdmin() ?? false),
                Tables\Actions\Action::make('deactivate')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn (Category $record): bool => $record->is_active && auth()->user()?->isAdmin() ?? false)
                    ->requiresConfirmation()
                    ->modalHeading('Nonaktifkan kategori?')
                    ->modalDescription('Kategori yang dinonaktifkan tidak akan muncul sebagai pilihan pada form penerima manfaat dan lembaga.')
                    ->action(fn (Category $record) => $record->update(['is_active' => false])),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn (Category $record): bool => ! $record->isInUse() && auth()->user()?->isAdmin() ?? false)
                    ->requiresConfirmation()
                    ->modalHeading('Hapus kategori?')
                    ->modalDescription('Kategori yang sudah digunakan oleh penerima manfaat atau lembaga tidak dapat dihapus. Nonaktifkan saja jika ingin menarik dari pilihan.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ]),
            ])
            ->defaultSort('urutan');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record): bool
    {
        return ! $record->isInUse() && (auth()->user()?->isAdmin() ?? false);
    }

    public static function canEdit($record): bool
    {
        return $record->is_active || (auth()->user()?->isAdmin() ?? false);
    }
}

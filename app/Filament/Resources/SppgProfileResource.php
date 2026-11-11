<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SppgProfileResource\Pages;
use App\Models\SppgProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SppgProfileResource extends Resource
{
    protected static ?string $model = SppgProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil SPPG';

    protected static ?string $modelLabel = 'Profil SPPG';

    protected static ?string $pluralModelLabel = 'Profil SPPG';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profil SPPG')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama SPPG')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('kepala_sppg')
                            ->label('Kepala / Penanggung Jawab')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('kecamatan')
                            ->label('Kecamatan')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kabupaten')
                            ->label('Kabupaten')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('provinsi')
                            ->label('Provinsi')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama SPPG')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->label('Kabupaten')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('telepon')
                    ->label('Telepon')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSppgProfiles::route('/'),
            'edit' => Pages\EditSppgProfile::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
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

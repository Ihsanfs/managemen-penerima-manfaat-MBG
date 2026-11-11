<?php

namespace App\Filament\Resources\InstitutionResource\Pages;

use App\Filament\Pages\ImporEkspor;
use App\Filament\Resources\InstitutionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstitutions extends ListRecords
{
    protected static string $resource = InstitutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('imporEkspor')
                ->label('Impor / Ekspor')
                ->icon('heroicon-o-arrows-right-left')
                ->color('gray')
                ->url(fn (): string => ImporEkspor::getUrl()),
            Actions\CreateAction::make(),
        ];
    }
}

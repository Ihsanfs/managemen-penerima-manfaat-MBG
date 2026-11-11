<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Pages\ImporEkspor;
use App\Filament\Resources\BeneficiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBeneficiaries extends ListRecords
{
    protected static string $resource = BeneficiaryResource::class;

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

<?php

namespace App\Filament\Resources\BeneficiaryHistoryResource\Pages;

use App\Filament\Resources\BeneficiaryHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListBeneficiaryHistories extends ListRecords
{
    protected static string $resource = BeneficiaryHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

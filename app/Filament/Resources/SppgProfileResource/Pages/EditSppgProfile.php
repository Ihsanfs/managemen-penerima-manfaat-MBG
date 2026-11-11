<?php

namespace App\Filament\Resources\SppgProfileResource\Pages;

use App\Filament\Resources\SppgProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSppgProfile extends EditRecord
{
    protected static string $resource = SppgProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

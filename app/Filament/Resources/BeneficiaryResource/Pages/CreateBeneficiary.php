<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Resources\BeneficiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateBeneficiary extends CreateRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function onValidationError(ValidationException $exception): void
    {
        parent::onValidationError($exception);

        $this->dispatch(
            'beneficiary-validation-error',
            title: 'Data penerima manfaat belum valid',
            errors: collect($exception->errors())->flatten()->unique()->values()->all(),
        );
    }
}

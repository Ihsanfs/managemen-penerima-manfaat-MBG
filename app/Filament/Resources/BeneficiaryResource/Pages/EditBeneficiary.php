<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditBeneficiary extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
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
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $alasan = $data['alasan_perubahan'] ?? null;

        unset($data['alasan_perubahan']);

        $record->tracking_reason = filled($alasan)
            ? $alasan
            : 'Data penerima manfaat diperbarui melalui aplikasi.';

        return parent::handleRecordUpdate($record, $data);
    }

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

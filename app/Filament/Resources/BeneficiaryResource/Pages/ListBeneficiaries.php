<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Pages\ImporEkspor;
use App\Filament\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Institution;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListBeneficiaries extends ListRecords
{
    protected static string $resource = BeneficiaryResource::class;

    public function mount(): void
    {
        parent::mount();

        if (request()->has('enc')) {
            try {
                $decrypted = json_decode(decrypt(request()->query('enc')), true);
                if (is_array($decrypted)) {
                    foreach ($decrypted as $key => $value) {
                        $this->tableFilters[$key] = $value;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore decryption failure
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetakPdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(array_merge(['semua' => 'Semua Status'], Beneficiary::STATUS))
                        ->default('aktif'),
                    Forms\Components\Select::make('institution_id')
                        ->label('Lembaga')
                        ->options(Institution::query()->orderBy('nama')->pluck('nama', 'id')->all())
                        ->searchable()
                        ->placeholder('Semua Lembaga'),
                    Forms\Components\Select::make('kategori')
                        ->label('Kategori')
                        ->options(Category::optionsPenerima())
                        ->placeholder('Semua Kategori'),
                    Forms\Components\Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options(Beneficiary::JENIS_KELAMIN)
                        ->placeholder('Semua'),
                ])
                ->modalHeading('Cetak Daftar Penerima Manfaat')
                ->modalDescription('Pilih filter data yang akan dicetak. Kosongkan untuk mencetak semua data.')
                ->modalSubmitActionLabel('Cetak PDF')
                ->action(function (array $data): void {
                    $params = array_filter($data, fn ($v) => $v !== null && $v !== '');

                    $url = route('unduhan.cetak-daftar-penerima', $params);

                    $this->js("window.open('{$url}', '_blank')");
                }),
            Actions\Action::make('imporEkspor')
                ->label('Impor / Ekspor')
                ->icon('heroicon-o-arrows-right-left')
                ->color('gray')
                ->url(fn (): string => ImporEkspor::getUrl()),
            Actions\CreateAction::make(),
        ];
    }
}

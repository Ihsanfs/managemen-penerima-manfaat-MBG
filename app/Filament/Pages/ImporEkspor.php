<?php

namespace App\Filament\Pages;

use App\Models\Beneficiary;
use App\Models\Institution;
use App\Services\BeneficiaryImportService;
use App\Services\InstitutionImportService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\WithFileUploads;
use Throwable;

class ImporEkspor extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Impor & Ekspor';

    protected static ?string $title = 'Impor & Ekspor';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.impor-ekspor';

    public $filePenerima = null;

    public $fileLembaga = null;

    /** @var array{dibuat: int, diperbarui: int, gagal: int, total: int, errors: array<int, array{baris: int, pesan: string}>}|null */
    public ?array $hasilPenerima = null;

    /** @var array{dibuat: int, diperbarui: int, gagal: int, total: int, errors: array<int, array{baris: int, pesan: string}>}|null */
    public ?array $hasilLembaga = null;

    public function importPenerima(BeneficiaryImportService $service): void
    {
        $this->validateUpload('filePenerima');

        try {
            $namaFile = $this->filePenerima->getClientOriginalName();

            $this->hasilPenerima = $service->import(
                $this->filePenerima->getRealPath(),
                $this->filePenerima->getClientOriginalExtension(),
                'Impor penerima manfaat dari file "' . $namaFile . '".',
            );

            $this->filePenerima = null;

            $this->notifyResult('Penerima manfaat', $this->hasilPenerima);
        } catch (Throwable $exception) {
            $this->hasilPenerima = $this->failureResult($exception->getMessage());

            Notification::make()
                ->title('Impor penerima manfaat gagal')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function importLembaga(InstitutionImportService $service): void
    {
        $this->validateUpload('fileLembaga');

        try {
            $this->hasilLembaga = $service->import(
                $this->fileLembaga->getRealPath(),
                $this->fileLembaga->getClientOriginalExtension(),
            );

            $this->fileLembaga = null;

            $this->notifyResult('Lembaga', $this->hasilLembaga);
        } catch (Throwable $exception) {
            $this->hasilLembaga = $this->failureResult($exception->getMessage());

            Notification::make()
                ->title('Impor lembaga gagal')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function hapusHasilPenerima(): void
    {
        $this->hasilPenerima = null;
    }

    public function hapusHasilLembaga(): void
    {
        $this->hasilLembaga = null;
    }

    protected function getViewData(): array
    {
        return [
            'jumlahPenerima' => Beneficiary::query()->count(),
            'jumlahLembaga' => Institution::query()->count(),
            'maksimalMb' => 10,
        ];
    }

    private function validateUpload(string $property): void
    {
        $this->validate([
            $property => [
                'required',
                'max:10240',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $extension = strtolower((string) $value?->getClientOriginalExtension());

                    if (! in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
                        $fail('Format file harus .xlsx, .xls, atau .csv.');
                    }
                },
            ],
        ], [
            $property . '.required' => 'Pilih file yang ingin diimpor terlebih dahulu.',
            $property . '.max' => 'Ukuran file maksimal 10 MB.',
        ]);
    }

    /**
     * @param  array{dibuat: int, diperbarui: int, gagal: int, total: int, errors: array<int, array{baris: int, pesan: string}>}  $result
     */
    private function notifyResult(string $label, array $result): void
    {
        $notification = Notification::make()
            ->title('Impor ' . mb_strtolower($label) . ' selesai')
            ->body(sprintf(
                '%d dibuat, %d diperbarui, %d gagal dari %d baris.',
                $result['dibuat'],
                $result['diperbarui'],
                $result['gagal'],
                $result['total'],
            ));

        if ($result['gagal'] > 0) {
            $notification->warning();
        } else {
            $notification->success();
        }

        $notification->send();
    }

    /**
     * @return array{dibuat: int, diperbarui: int, gagal: int, total: int, errors: array<int, array{baris: int, pesan: string}>}
     */
    private function failureResult(string $message): array
    {
        return [
            'dibuat' => 0,
            'diperbarui' => 0,
            'gagal' => 0,
            'total' => 0,
            'errors' => [
                ['baris' => 0, 'pesan' => $message],
            ],
        ];
    }
}

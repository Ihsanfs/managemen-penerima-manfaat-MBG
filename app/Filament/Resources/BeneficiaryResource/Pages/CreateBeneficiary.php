<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateBeneficiary extends CreateRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (($data['input_mode'] ?? 'individu') === 'agregat') {
            $institutionId = $data['institution_id'];
            $kategori = $data['kategori'];
            $kelas = $data['kelas'] ?? null;
            $jumlah = (int) ($data['jumlah'] ?? 1);
            $status = $data['status'] ?? 'aktif';
            $bulan = $data['bulan'] ?? (int) now()->format('n');
            $tahun = $data['tahun'] ?? (int) now()->format('Y');
            $keterangan = $data['keterangan'] ?? 'Diinput via rekapitulasi jumlah/agregat.';

            $firstRecord = null;
            for ($i = 1; $i <= $jumlah; $i++) {
                $payload = [
                    'institution_id' => $institutionId,
                    'nama' => 'Penerima Rekap Kelas ' . ($kelas ?? 'Umur') . ' #' . $i,
                    'nik' => null,
                    'nisn' => null,
                    'jenis_kelamin' => 'L',
                    'kategori' => $kategori,
                    'kelas' => $kelas,
                    'status' => $status,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'keterangan' => $keterangan,
                ];

                $beneficiary = new Beneficiary($payload);
                $beneficiary->tracking_reason = 'Dibuat melalui input jumlah/agregat.';
                $beneficiary->save();

                if ($i === 1) {
                    $firstRecord = $beneficiary;
                }
            }

            return $firstRecord ?? Beneficiary::query()->latest()->first();
        }

        return parent::handleRecordCreation($data);
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


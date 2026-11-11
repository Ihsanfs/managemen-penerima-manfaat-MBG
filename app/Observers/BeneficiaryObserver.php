<?php

namespace App\Observers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryHistory;
use App\Models\Category;
use App\Models\Institution;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BeneficiaryObserver
{
    /**
     * Nilai sebelum perubahan, disimpan sementara sampai event updated selesai.
     *
     * @var array<int, array{old: array<string, mixed>, new: array<string, mixed>}>
     */
    private static array $pending = [];

    public function created(Beneficiary $beneficiary): void
    {
        if (! $this->shouldTrack($beneficiary)) {
            return;
        }

        $changes = [];

        foreach (['institution_id', 'nama', 'nik', 'nisn', 'jenis_kelamin', 'kategori', 'kelas', 'status'] as $field) {
            $value = $beneficiary->getAttribute($field);

            if ($value !== null && $value !== '') {
                $changes[] = $this->change($field, null, $value);
            }
        }

        $this->record(
            $beneficiary,
            'dibuat',
            'Data penerima manfaat ditambahkan melalui aplikasi.',
            $changes,
        );
    }

    public function updating(Beneficiary $beneficiary): void
    {
        $dirty = $beneficiary->getDirty();

        unset($dirty['created_at'], $dirty['updated_at']);

        if ($dirty === []) {
            return;
        }

        $old = [];

        foreach (array_keys($dirty) as $field) {
            $old[$field] = $beneficiary->getOriginal($field);
        }

        self::$pending[spl_object_id($beneficiary)] = [
            'old' => $old,
            'new' => $dirty,
        ];
    }

    public function updated(Beneficiary $beneficiary): void
    {
        $key = spl_object_id($beneficiary);
        $pending = self::$pending[$key] ?? null;

        unset(self::$pending[$key]);

        if (! $pending || ! $this->shouldTrack($beneficiary)) {
            return;
        }

        $changes = [];

        foreach ($pending['new'] as $field => $new) {
            $old = $pending['old'][$field] ?? null;

            if ($old === $new || (string) $old === (string) $new) {
                continue;
            }

            $changes[] = $this->change($field, $old, $new);
        }

        if ($changes === []) {
            return;
        }

        $this->record(
            $beneficiary,
            'diperbarui',
            'Data penerima manfaat diperbarui melalui aplikasi.',
            $changes,
        );
    }

    public function deleting(Beneficiary $beneficiary): void
    {
        if (! $this->shouldTrack($beneficiary)) {
            return;
        }

        $this->record(
            $beneficiary,
            'dihapus',
            'Data penerima manfaat dihapus melalui aplikasi.',
            [],
        );
    }

    private function shouldTrack(Beneficiary $beneficiary): bool
    {
        return filled($beneficiary->tracking_reason) || Auth::check();
    }

    /**
     * @param  array<int, array{kolom: string, label: string, lama: string, baru: string}>  $changes
     */
    private function record(Beneficiary $beneficiary, string $aksi, string $defaultReason, array $changes): void
    {
        $now = now();
        $institution = $beneficiary->relationLoaded('institution')
            ? $beneficiary->institution
            : Institution::query()->find($beneficiary->institution_id);

        BeneficiaryHistory::query()->create([
            'beneficiary_id' => $beneficiary->getKey(),
            'user_id' => $beneficiary->tracking_user_id ?? Auth::id(),
            'aksi' => $aksi,
            'beneficiary_nama' => $beneficiary->nama,
            'institution_nama' => $institution?->nama,
            'kategori' => $beneficiary->kategori,
            'tanggal' => $now->toDateString(),
            'hari' => $now->locale('id')->translatedFormat('l'),
            'alasan' => filled($beneficiary->tracking_reason)
                ? $beneficiary->tracking_reason
                : $defaultReason,
            'perubahan' => $changes !== [] ? $changes : null,
        ]);
    }

    /**
     * @return array{kolom: string, label: string, lama: string, baru: string}
     */
    private function change(string $field, mixed $old, mixed $new): array
    {
        return [
            'kolom' => $field,
            'label' => $this->label($field),
            'lama' => $this->formatValue($field, $old),
            'baru' => $this->formatValue($field, $new),
        ];
    }

    private function label(string $field): string
    {
        return match ($field) {
            'institution_id' => 'Lembaga',
            'nama' => 'Nama',
            'nik' => 'NIK',
            'nisn' => 'NISN',
            'jenis_kelamin' => 'Jenis Kelamin',
            'kategori' => 'Kategori',
            'kelas' => 'Kelas',
            'jurusan' => 'Jurusan',
            'status' => 'Status',
            'bulan' => 'Bulan Data',
            'tahun' => 'Tahun Data',
            'keterangan' => 'Keterangan',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }

    private function formatValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return match ($field) {
            'institution_id' => Institution::query()->find($value)?->nama ?? 'Lembaga #' . $value,
            'jenis_kelamin' => Beneficiary::JENIS_KELAMIN[$value] ?? (string) $value,
            'kategori' => Category::labelPenerima($value) ?? (string) $value,
            'status' => Beneficiary::STATUS[$value] ?? (string) $value,
            'bulan' => is_numeric($value)
                ? Carbon::createFromDate(2000, (int) $value, 1)->locale('id')->translatedFormat('F')
                : (string) $value,
            default => (string) $value,
        };
    }
}

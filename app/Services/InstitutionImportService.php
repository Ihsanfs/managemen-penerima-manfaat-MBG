<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Institution;
use InvalidArgumentException;

class InstitutionImportService
{
    private const TIPE_ALIASES = [
        'sekolah' => ['sekolah', 'school', 'pendidikan', 'sd', 'tk', 'paud', 'smk'],
        'posyandu' => ['posyandu', 'posyandu_balita', 'kesehatan'],
    ];

    private const JENJANG_ALIASES = [
        'tk_paud' => ['tk_paud', 'tk', 'paud', 'tkpaud'],
        'sd' => ['sd', 'sekolah_dasar', 'sd_1_3', 'sd_4_6'],
        'smp' => ['smp', 'smp_sederajat', 'menengah_pertama'],
        'sma' => ['sma', 'sma_sederajat', 'menengah_atas'],
        'smk' => ['smk', 'smk_sederajat', 'menengah_kejuruan'],
        'slb' => ['slb', 'sekolah_luar_biasa'],
        'lainnya' => ['lainnya', 'other', 'lain'],
    ];

    private const STATUS_ALIASES = [
        'aktif' => ['aktif', 'active', '1', 'ya', 'yes', 'y'],
        'non_aktif' => ['non_aktif', 'nonaktif', 'tidak_aktif', 'non_active', '0', 'tidak', 'no', 'n'],
    ];

    private const HEADER_ALIASES = [
        'nama' => ['nama', 'nama_lembaga', 'lembaga', 'sekolah', 'posyandu'],
        'tipe' => ['tipe', 'jenis', 'jenis_lembaga', 'type'],
        'jenjang' => ['jenjang', 'tingkat', 'jenjang_pendidikan'],
        'alamat' => ['alamat', 'address'],
        'penanggung_jawab' => ['penanggung_jawab', 'penanggungjawab', 'pj', 'nama_pj'],
        'telepon' => ['telepon', 'telp', 'no_hp', 'hp', 'phone', 'kontak'],
        'urutan' => ['urutan', 'no_urut', 'nomor_urut', 'sort'],
        'is_active' => ['is_active', 'aktif', 'status', 'status_aktif'],
        'category_id' => ['kategori_lembaga', 'kategori', 'kode_kategori'],
    ];

    public function __construct(private readonly SpreadsheetService $spreadsheets) {}

    /**
     * @return array{dibuat: int, duplikat: int, gagal: int, total: int, errors: array<int, array{baris: int, pesan: string}>}
     */
    public function import(string $path, ?string $extension = null): array
    {
        $parsed = $this->spreadsheets->read($path, $extension);

        if ($parsed['rows'] === []) {
            throw new InvalidArgumentException('File tidak memiliki baris data. Isi sheet Data Lembaga terlebih dahulu.');
        }

        $this->ensureRequiredHeaders($parsed['headers']);

        $result = [
            'dibuat' => 0,
            'duplikat' => 0,
            'gagal' => 0,
            'total' => count($parsed['rows']),
            'errors' => [],
        ];

        $nextUrutan = ((int) Institution::query()->max('urutan')) + 1;

        // Chunk rows (e.g. 500 per chunk) to protect server memory and prevent overload
        $chunks = array_chunk($parsed['rows'], 500);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $row) {
                $line = (int) ($row['__baris'] ?? 0);

                try {
                    $values = $this->mapRow($row, self::HEADER_ALIASES);

                    $nama = $this->text($values['nama'] ?? null);

                    if ($nama === '') {
                        throw new InvalidArgumentException('Kolom nama wajib diisi.');
                    }

                    $tipe = $this->normalizeAlias($values['tipe'] ?? null, self::TIPE_ALIASES);

                    if (! $tipe) {
                        throw new InvalidArgumentException("Tipe lembaga harus 'sekolah' atau 'posyandu'.");
                    }

                    $jenjang = null;

                    if ($this->text($values['jenjang'] ?? null) !== '') {
                        $jenjang = $this->resolveJenjang($values['jenjang'] ?? null);

                        if (! $jenjang) {
                            throw new InvalidArgumentException("Jenjang '" . $this->text($values['jenjang'] ?? null) . "' tidak dikenal. Pilihan: " . implode(', ', array_keys(Category::optionsJenjang() ?: Institution::JENJANG)) . ".");
                        }
                    }

                    $isActive = true;

                    if ($this->text($values['is_active'] ?? null) !== '') {
                        $isActive = $this->normalizeAlias($values['is_active'] ?? null, self::STATUS_ALIASES) === 'aktif';
                    }

                    $categoryId = $this->resolveCategory($values['category_id'] ?? null);

                    $urutan = $this->integerInRange($values['urutan'] ?? null, 0, 65535, 'Urutan');

                    $existing = $this->findExisting($nama);

                    if ($existing) {
                        // Anti-duplikat: Jika lembaga sudah ada, lewati dan hitung sebagai duplikat
                        $result['duplikat']++;
                    } else {
                        $payload = [
                            'nama' => $nama,
                            'tipe' => $tipe,
                            'jenjang' => $jenjang,
                            'category_id' => $categoryId,
                            'alamat' => $this->nullableText($values['alamat'] ?? null),
                            'penanggung_jawab' => $this->nullableText($values['penanggung_jawab'] ?? null),
                            'telepon' => $this->nullableText($values['telepon'] ?? null),
                            'urutan' => $urutan ?? $nextUrutan,
                            'is_active' => $isActive,
                        ];

                        Institution::query()->create($payload);
                        $nextUrutan++;
                        $result['dibuat']++;
                    }
                } catch (\Throwable $exception) {
                    $result['gagal']++;
                    $result['errors'][] = [
                        'baris' => $line,
                        'pesan' => $exception->getMessage(),
                    ];
                }
            }
        }

        if (count($result['errors']) > 200) {
            $result['errors'] = array_slice($result['errors'], 0, 200);
        }

        return $result;
    }

    /**
     * @param  array<int, string>  $headers
     */
    private function ensureRequiredHeaders(array $headers): void
    {
        $missing = [];

        foreach (['nama', 'tipe'] as $required) {
            $found = false;

            foreach (self::HEADER_ALIASES[$required] as $alias) {
                if (in_array($alias, $headers, true)) {
                    $found = true;

                    break;
                }
            }

            if (! $found) {
                $missing[] = $required;
            }
        }

        if ($missing !== []) {
            throw new InvalidArgumentException('Kolom wajib tidak ditemukan: ' . implode(', ', $missing) . '. Gunakan template impor yang disediakan.');
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, array<int, string>>  $aliases
     * @return array<string, mixed>
     */
    private function mapRow(array $row, array $aliases): array
    {
        $mapped = [];

        foreach ($aliases as $canonical => $variants) {
            $mapped[$canonical] = null;

            foreach ($variants as $variant) {
                if (array_key_exists($variant, $row) && $row[$variant] !== null && $row[$variant] !== '') {
                    $mapped[$canonical] = $row[$variant];

                    break;
                }
            }
        }

        return $mapped;
    }

    /**
     * @param  array<string, array<int, string>>  $aliases
     */
    private function normalizeAlias(mixed $value, array $aliases): ?string
    {
        $key = $this->key($value);

        if ($key === '') {
            return null;
        }

        foreach ($aliases as $canonical => $variants) {
            if ($key === $canonical || in_array($key, $variants, true)) {
                return $canonical;
            }
        }

        return null;
    }

    private function integerInRange(mixed $value, int $min, int $max, string $label): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (! is_numeric($value)) {
            throw new InvalidArgumentException("{$label} harus berupa angka.");
        }

        $value = (int) $value;

        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException("{$label} harus antara {$min} dan {$max}.");
        }

        return $value;
    }

    private function findExisting(string $nama): ?Institution
    {
        return Institution::query()
            ->whereRaw('LOWER(nama) = ?', [mb_strtolower(trim($nama))])
            ->first();
    }

    /**
     * Resolve kategori lembaga dari kode atau nama kategori yang ada di tabel categories.
     */
    private function resolveCategory(mixed $value): ?int
    {
        $text = trim((string) $value);

        if ($text === '') {
            return null;
        }

        $category = Category::query()
            ->where(function ($query) use ($text) {
                $query->where('kode', $text)
                    ->orWhereRaw('LOWER(nama) = ?', [mb_strtolower($text)]);
            })
            ->first();

        return $category?->id;
    }

    /**
     * Resolve jenjang sekolah dari kode atau nama yang ada di tabel categories (jenis jenjang),
     * fallback ke alias bawaan jika belum ada di DB.
     */
    private function resolveJenjang(mixed $value): ?string
    {
        $text = trim((string) $value);

        if ($text === '') {
            return null;
        }

        // Coba cari di tabel categories (jenis jenjang) terlebih dahulu.
        $category = Category::query()
            ->where('jenis', Category::JENIS_JENJANG)
            ->where(function ($query) use ($text) {
                $query->where('kode', $text)
                    ->orWhereRaw('LOWER(nama) = ?', [mb_strtolower($text)]);
            })
            ->first();

        if ($category) {
            return $category->kode;
        }

        // Fallback ke alias bawaan.
        return $this->normalizeAlias($text, self::JENJANG_ALIASES);
    }

    private function text(mixed $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value));
    }

    private function nullableText(mixed $value): ?string
    {
        $value = $this->text($value);

        return $value !== '' ? $value : null;
    }

    private function key(mixed $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = str_replace(['/', '-', ' '], '_', $value);

        return trim((string) preg_replace('/_+/', '_', $value), '_');
    }
}

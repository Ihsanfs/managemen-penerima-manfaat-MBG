<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Institution;
use InvalidArgumentException;

class BeneficiaryImportService
{
    private const KATEGORI_ALIASES = [
        'tk_paud' => ['tk_paud', 'tkpaud', 'tk', 'paud'],
        'sd' => ['sd', 'sekolah_dasar', 'sd_1_3', 'sd_4_6'],
        'smp' => ['smp', 'smp_sederajat', 'menengah_pertama'],
        'smk' => ['smk'],
        'guru' => ['guru', 'pendidik', 'teacher'],
        'balita' => ['balita', 'anak'],
        'bumil' => ['bumil', 'ibu_hamil', 'hamil'],
        'busui' => ['busui', 'ibu_menyusui', 'menyusui'],
        'kader' => ['kader'],
    ];

    private const STATUS_ALIASES = [
        'aktif' => ['aktif', 'active', '1', 'ya', 'yes'],
        'non_aktif' => ['non_aktif', 'nonaktif', 'tidak_aktif', 'non_active', '0', 'tidak', 'no'],
        'pindah' => ['pindah', 'pindah_keluar', 'keluar', 'mutasi'],
    ];

    private const JK_ALIASES = [
        'L' => ['l', 'laki_laki', 'lakilaki', 'pria', 'male', 'm'],
        'P' => ['p', 'perempuan', 'wanita', 'female', 'f'],
    ];

    private const HEADER_ALIASES = [
        'nama_lembaga' => ['nama_lembaga', 'lembaga', 'sekolah', 'posyandu', 'nama_sekolah', 'nama_posyandu'],
        'nama' => ['nama', 'nama_penerima', 'nama_lengkap'],
        'nik' => ['nik'],
        'nisn' => ['nisn'],
        'jenis_kelamin' => ['jenis_kelamin', 'jk', 'l_p', 'gender'],
        'kategori' => ['kategori', 'kelompok'],
        'kelas' => ['kelas', 'tingkat'],
        'status' => ['status', 'status_aktif'],
        'bulan' => ['bulan', 'bulan_data'],
        'tahun' => ['tahun', 'tahun_data'],
        'keterangan' => ['keterangan', 'catatan'],
    ];

    public function __construct(private readonly SpreadsheetService $spreadsheets) {}

    /**
     * @return array{dibuat: int, diperbarui: int, gagal: int, total: int, errors: array<int, array{baris: int, pesan: string}>}
     */
    public function import(string $path, ?string $extension = null, ?string $alasan = null): array
    {
        $parsed = $this->spreadsheets->read($path, $extension);

        if ($parsed['rows'] === []) {
            throw new InvalidArgumentException('File tidak memiliki baris data. Isi sheet Data Penerima terlebih dahulu.');
        }

        $this->ensureRequiredHeaders($parsed['headers']);

        $institutions = Institution::query()
            ->get()
            ->keyBy(fn (Institution $institution): string => $this->key($institution->nama));

        $result = [
            'dibuat' => 0,
            'diperbarui' => 0,
            'gagal' => 0,
            'total' => count($parsed['rows']),
            'errors' => [],
        ];

        foreach ($parsed['rows'] as $row) {
            $line = (int) ($row['__baris'] ?? 0);

            try {
                $values = $this->mapRow($row, self::HEADER_ALIASES);

                $institutionName = $this->text($values['nama_lembaga'] ?? null);

                if ($institutionName === '') {
                    throw new InvalidArgumentException('Kolom nama_lembaga wajib diisi.');
                }

                $institution = $institutions->get($this->key($institutionName));

                if (! $institution) {
                    throw new InvalidArgumentException("Lembaga '{$institutionName}' tidak ditemukan. Tambahkan lembaga terlebih dahulu atau gunakan nama dari sheet Referensi.");
                }

                $nama = $this->text($values['nama'] ?? null);

                if ($nama === '') {
                    throw new InvalidArgumentException('Kolom nama wajib diisi.');
                }

                $kategori = $this->normalizeAlias($values['kategori'] ?? null, self::KATEGORI_ALIASES);

                if (! $kategori || ! Category::query()->active()->penerima()->where('kode', $kategori)->exists()) {
                    $tersedia = implode(', ', Category::query()->active()->penerima()->pluck('kode')->all());

                    throw new InvalidArgumentException("Kategori '" . $this->text($values['kategori'] ?? null) . "' tidak dikenal atau tidak aktif. Gunakan: {$tersedia}.");
                }

                $jenisKelamin = $this->normalizeAlias($values['jenis_kelamin'] ?? null, self::JK_ALIASES);

                if (! $jenisKelamin) {
                    throw new InvalidArgumentException("Jenis kelamin harus L atau P.");
                }

                $status = $this->normalizeAlias($values['status'] ?? 'aktif', self::STATUS_ALIASES) ?? 'aktif';
                $kelas = $this->normalizeKelas($values['kelas'] ?? null, $kategori);
                $nik = $this->digits($values['nik'] ?? null, 16, 'NIK');
                $nisn = $this->digits($values['nisn'] ?? null, 10, 'NISN');
                $bulan = $this->integerInRange($values['bulan'] ?? null, 1, 12, 'Bulan', (int) now()->format('n'));
                $tahun = $this->integerInRange($values['tahun'] ?? null, 2000, 2100, 'Tahun', (int) now()->format('Y'));
                $keterangan = $this->text($values['keterangan'] ?? null);

                $payload = [
                    'institution_id' => $institution->id,
                    'nama' => $nama,
                    'nik' => $nik,
                    'nisn' => $nisn,
                    'jenis_kelamin' => $jenisKelamin,
                    'kategori' => $kategori,
                    'kelas' => $kelas,
                    'jurusan' => $kategori === 'smk' ? ($existing ?? null)?->jurusan : null,
                    'status' => $status,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'keterangan' => $keterangan !== '' ? $keterangan : null,
                ];

                $existing = $this->findExisting($institution, $nama, $kategori, $kelas, $nik, $nisn);

                $payload['jurusan'] = $existing?->jurusan;

                if ($existing) {
                    $existing->tracking_reason = $alasan ?? 'Data penerima manfaat diperbarui melalui impor.';
                    $existing->fill($payload)->save();
                    $result['diperbarui']++;
                } else {
                    $created = new Beneficiary($payload);
                    $created->tracking_reason = $alasan ?? 'Data penerima manfaat ditambahkan melalui impor.';
                    $created->save();
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

        foreach (['nama_lembaga', 'nama', 'kategori', 'jenis_kelamin'] as $required) {
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

    private function normalizeKelas(mixed $value, string $kategori): ?int
    {
        $raw = trim((string) $value);

        if (! in_array($kategori, ['sd', 'smp', 'sma', 'smk'], true)) {
            return null;
        }

        if ($raw === '') {
            throw new InvalidArgumentException("Kelas wajib diisi untuk kategori {$kategori}.");
        }

        $normalized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $raw));
        $normalized = preg_replace('/^(KELAS|KLS)/', '', $normalized);

        $roman = ['X' => 10, 'XI' => 11, 'XII' => 12];
        $number = $roman[$normalized] ?? (is_numeric($normalized) ? (int) $normalized : null);

        if ($number === null) {
            throw new InvalidArgumentException("Kelas '{$raw}' tidak dikenal. Gunakan 1-6 untuk SD, 7-9 untuk SMP, atau 1-3 / X-XII untuk SMA/SMK.");
        }

        if ($kategori === 'sd') {
            if ($number < 1 || $number > 6) {
                throw new InvalidArgumentException("Kelas SD harus 1 sampai 6.");
            }

            return $number;
        }

        if ($kategori === 'smp') {
            if ($number < 7 || $number > 9) {
                throw new InvalidArgumentException("Kelas SMP harus 7 sampai 9.");
            }

            return $number;
        }

        // SMA / SMK: kelas 10, 11, 12 (atau 1, 2, 3 yang akan diubah ke 10-12).
        if (in_array($number, [1, 2, 3], true)) {
            $number += 9;
        }

        if (! in_array($number, [10, 11, 12], true)) {
            throw new InvalidArgumentException("Kelas SMA/SMK harus 10-12 (atau 1-3 / X-XII).");
        }

        return $number;
    }

    private function digits(mixed $value, int $length, string $label): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            $value = number_format((float) $value, 0, '', '');
        }

        $value = preg_replace('/\s+/', '', ltrim((string) $value, "'"));

        if ($value === '') {
            return null;
        }

        if (! preg_match('/^\d+$/', $value)) {
            throw new InvalidArgumentException("{$label} harus berupa angka. Format kolom NIK/NISN sebagai Teks agar tidak berubah menjadi notasi ilmiah.");
        }

        if (strlen($value) !== $length) {
            throw new InvalidArgumentException("{$label} harus {$length} digit, ditemukan " . strlen($value) . ' digit.');
        }

        return $value;
    }

    private function integerInRange(mixed $value, int $min, int $max, string $label, int $default): int
    {
        if ($value === null || trim((string) $value) === '') {
            return $default;
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

    private function findExisting(
        Institution $institution,
        string $nama,
        string $kategori,
        ?int $kelas,
        ?string $nik,
        ?string $nisn,
    ): ?Beneficiary {
        if ($nik) {
            $existing = Beneficiary::query()->where('nik', $nik)->first();

            if ($existing) {
                return $existing;
            }
        }

        if ($nisn) {
            $existing = Beneficiary::query()->where('nisn', $nisn)->first();

            if ($existing) {
                return $existing;
            }
        }

        return Beneficiary::query()
            ->where('institution_id', $institution->id)
            ->whereRaw('LOWER(nama) = ?', [mb_strtolower(trim($nama))])
            ->where('kategori', $kategori)
            ->when(
                $kelas !== null,
                fn ($query) => $query->where('kelas', $kelas),
                fn ($query) => $query->whereNull('kelas'),
            )
            ->first();
    }

    private function text(mixed $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value));
    }

    private function key(mixed $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = str_replace(['/', '-', ' '], '_', $value);

        return trim((string) preg_replace('/_+/', '_', $value), '_');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    public const JENIS_PENERIMA = 'penerima';
    public const JENIS_LEMBAGA = 'lembaga';
    public const JENIS_JENJANG = 'jenjang';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'grup',
        'urutan',
        'is_active',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $query) {
            $query->orderBy('urutan')->orderBy('nama');
        });
    }

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'kategori', 'kode');
    }

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class, 'category_id');
    }

    public function scopePenerima(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_PENERIMA);
    }

    public function scopeLembaga(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_LEMBAGA);
    }

    public function scopeJenjang(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_JENJANG);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Ambil semua kategori aktif untuk penerima manfaat, dengan opsi untuk form/select.
     *
     * @return array<string, string>
     */
    public static function optionsPenerima(): array
    {
        return self::active()->penerima()->pluck('nama', 'kode')->all();
    }

    /**
     * Ambil semua kategori aktif untuk lembaga, dengan opsi untuk form/select.
     *
     * @return array<string, string>
     */
    public static function optionsLembaga(): array
    {
        return self::active()->lembaga()->pluck('nama', 'kode')->all();
    }

    /**
     * Ambil semua kategori aktif untuk jenjang sekolah, dengan opsi untuk form/select.
     *
     * @return array<string, string>
     */
    public static function optionsJenjang(): array
    {
        return self::active()->jenjang()->pluck('nama', 'kode')->all();
    }

    /**
     * Label untuk kode kategori penerima (fallback ke konstanta lama jika tidak ada di DB).
     *
     * @return array<string, string>
     */
    public static function labelsPenerima(): array
    {
        $db = self::optionsPenerima();

        return $db !== [] ? $db : Beneficiary::KATEGORI;
    }

    /**
     * Cari kategori penerima berdasarkan kode, dengan fallback ke konstanta Beneficiary::KATEGORI.
     */
    public static function labelPenerima(?string $kode): ?string
    {
        if ($kode === null || $kode === '') {
            return null;
        }

        $category = self::where('jenis', self::JENIS_PENERIMA)->where('kode', $kode)->first();

        if ($category) {
            return $category->nama;
        }

        return Beneficiary::KATEGORI[$kode] ?? null;
    }

    /**
     * Cari kategori lembeaga berdasarkan kode.
     */
    public static function labelLembaga(?string $kode): ?string
    {
        if ($kode === null || $kode === '') {
            return null;
        }

        $category = self::where('jenis', self::JENIS_LEMBAGA)->where('kode', $kode)->first();

        return $category?->nama;
    }

    /**
     * Cari kategori jenjang berdasarkan kode, dengan fallback ke konstanta Institution::JENJANG.
     */
    public static function labelJenjang(?string $kode): ?string
    {
        if ($kode === null || $kode === '') {
            return null;
        }

        $category = self::where('jenis', self::JENIS_JENJANG)->where('kode', $kode)->first();

        if ($category) {
            return $category->nama;
        }

        return Institution::JENJANG[$kode] ?? null;
    }

    /**
     * Daftar label jenis untuk ditampilkan di tabel/form kategori.
     *
     * @return array<string, string>
     */
    public static function jenisLabels(): array
    {
        return [
            self::JENIS_PENERIMA => 'Penerima Manfaat',
            self::JENIS_LEMBAGA => 'Lembaga',
            self::JENIS_JENJANG => 'Jenjang Sekolah',
        ];
    }

    /**
     * Hitung penggunaan kategori penerima pada beneficiary aktif.
     */
    public function usageCount(): int
    {
        return $this->beneficiaries()->where('status', 'aktif')->count();
    }

    /**
     * Apakah kategori masih digunakan (penerima, lembaga, atau sebagai jenjang lembaga).
     */
    public function isInUse(): bool
    {
        if ($this->jenis === self::JENIS_JENJANG) {
            return Institution::query()->where('jenjang', $this->kode)->exists();
        }

        return $this->beneficiaries()->exists() || $this->institutions()->exists();
    }

    /**
     * Buat opsi seleksi untuk laporan per grup (misalnya TK/PAUD, Kelas 1-3 SD, dst).
     *
     * @return array<string, string>
     */
    public static function optionsByGrup(?string $jenis = null): array
    {
        $query = self::active()->whereNotNull('grup');

        if ($jenis !== null) {
            $query->where('jenis', $jenis);
        }

        return $query->pluck('nama', 'grup')->all();
    }

    /**
     * Ambil daftar grup yang tersedia untuk laporan.
     *
     * @return array<int, string>
     */
    public static function grupList(): array
    {
        return self::query()
            ->whereNotNull('grup')
            ->distinct('grup')
            ->orderBy('grup')
            ->pluck('grup')
            ->all();
    }

    /**
     * Label grup untuk tampilan laporan.
     *
     * @return array<string, string>
     */
    public static function grupLabels(): array
    {
        return [
            'tk_paud' => 'TK / PAUD',
            'sd_1_3' => 'Kelas 1-3 SD',
            'sd_4_6' => 'Kelas 4-6 SD',
            'smk' => 'SMP / SMK',
            'guru' => 'Guru',
            'balita' => 'Balita',
            'bumil' => 'Ibu Hamil',
            'busui' => 'Ibu Menyusui',
            'kader' => 'Kader',
        ];
    }

    /**
     * Reset cache opsi jika diperlukan.
     */
    public static function clearOptionsCache(): void
    {
        Cache::forget('categories_options_penerima');
        Cache::forget('categories_options_lembaga');
    }
}

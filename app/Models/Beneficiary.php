<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiary extends Model
{
    use HasFactory;

    public const KATEGORI = [
        'tk_paud' => 'TK / PAUD',
        'sd' => 'SD',
        'smp' => 'SMP',
        'smk' => 'SMK',
        'guru' => 'Guru',
        'balita' => 'Balita',
        'bumil' => 'Ibu Hamil',
        'busui' => 'Ibu Menyusui',
        'kader' => 'Kader',
    ];

    public const STATUS = [
        'aktif' => 'Aktif',
        'non_aktif' => 'Non Aktif',
        'pindah' => 'Pindah / Keluar',
    ];

    public const JENIS_KELAMIN = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
    ];

    /**
     * Alasan perubahan untuk dicatat pada riwayat. Tidak disimpan ke tabel beneficiaries.
     */
    public ?string $tracking_reason = null;

    /**
     * Pengguna yang melakukan perubahan. Jika null, observer memakai pengguna yang sedang login.
     */
    public ?int $tracking_user_id = null;

    protected $fillable = [
        'institution_id',
        'nama',
        'nik',
        'nisn',
        'jenis_kelamin',
        'kategori',
        'kelas',
        'jurusan',
        'status',
        'bulan',
        'tahun',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'kelas' => 'integer',
            'bulan' => 'integer',
            'tahun' => 'integer',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori', 'kode')->where('jenis', Category::JENIS_PENERIMA);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BeneficiaryHistory::class)->latest('created_at');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function getKategoriLabelAttribute(): string
    {
        return Category::labelPenerima($this->kategori) ?? $this->kategori;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return self::JENIS_KELAMIN[$this->jenis_kelamin] ?? $this->jenis_kelamin;
    }

    public function getKelasLabelAttribute(): string
    {
        if ($this->kategori === 'smk' && $this->kelas) {
            return match ($this->kelas) {
                10 => 'Kelas X',
                11 => 'Kelas XI',
                12 => 'Kelas XII',
                default => 'Kelas ' . $this->kelas,
            };
        }

        if (in_array($this->kategori, ['sd', 'smp'], true) && $this->kelas) {
            return 'Kelas ' . $this->kelas;
        }

        return '-';
    }
}

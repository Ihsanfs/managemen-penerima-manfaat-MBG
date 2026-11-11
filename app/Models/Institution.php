<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    public const TIPE = [
        'sekolah' => 'Sekolah',
        'posyandu' => 'Posyandu',
    ];

    public const JENJANG = [
        'tk_paud' => 'TK / PAUD',
        'sd' => 'SD',
        'smp' => 'SMP',
        'sma' => 'SMA',
        'smk' => 'SMK',
        'slb' => 'SLB',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'nama',
        'tipe',
        'jenjang',
        'category_id',
        'alamat',
        'penanggung_jawab',
        'telepon',
        'urutan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function activeBeneficiaries(): HasMany
    {
        return $this->beneficiaries()->where('status', 'aktif');
    }

    public function activeLaki(): HasMany
    {
        return $this->beneficiaries()->where('status', 'aktif')->where('jenis_kelamin', 'L');
    }

    public function activePerempuan(): HasMany
    {
        return $this->beneficiaries()->where('status', 'aktif')->where('jenis_kelamin', 'P');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSekolah(Builder $query): Builder
    {
        return $query->where('tipe', 'sekolah');
    }

    public function scopePosyandu(Builder $query): Builder
    {
        return $query->where('tipe', 'posyandu');
    }

    public function getTipeLabelAttribute(): string
    {
        return self::TIPE[$this->tipe] ?? $this->tipe;
    }

    public function getJenjangLabelAttribute(): ?string
    {
        return $this->jenjang ? (Category::labelJenjang($this->jenjang) ?? self::JENJANG[$this->jenjang] ?? $this->jenjang) : null;
    }

    public function getJumlahPenerimaAttribute(): int
    {
        return $this->active_beneficiaries_count
            ?? $this->activeBeneficiaries()->count();
    }
}

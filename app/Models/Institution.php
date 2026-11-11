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
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'urutan' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function distanceToSppg(): ?float
    {
        $sppg = SppgProfile::current();
        if ($this->latitude === null || $this->longitude === null || $sppg->latitude === null || $sppg->longitude === null) {
            return null;
        }

        $earthRadius = 6371; // km
        $latFrom = deg2rad((float) $sppg->latitude);
        $lonFrom = deg2rad((float) $sppg->longitude);
        $latTo = deg2rad((float) $this->latitude);
        $lonTo = deg2rad((float) $this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($earthRadius * $angle, 2);
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

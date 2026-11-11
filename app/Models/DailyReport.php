<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    use HasFactory;

    public const KATEGORI = [
        'tk_paud' => 'TK / PAUD',
        'sd_1_3' => 'Kelas 1-3 SD',
        'sd_4_6' => 'Kelas 4-6 SD',
        'smk' => 'SMK',
        'guru' => 'Guru',
        'balita' => 'Balita',
        'bumil' => 'Bumil',
        'busui' => 'Busui',
        'kader' => 'Kader',
    ];

    public const SUMBER = [
        'manual' => 'Manual',
        'otomatis' => 'Otomatis',
    ];

    protected $fillable = [
        'tanggal',
        'tk_paud',
        'sd_1_3',
        'sd_4_6',
        'smk',
        'guru',
        'balita',
        'bumil',
        'busui',
        'kader',
        'total',
        'sumber',
        'saved_by',
        'saved_at',
        'catatan',
        'kategori_json',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'saved_at' => 'datetime',
            'tk_paud' => 'integer',
            'sd_1_3' => 'integer',
            'sd_4_6' => 'integer',
            'smk' => 'integer',
            'guru' => 'integer',
            'balita' => 'integer',
            'bumil' => 'integer',
            'busui' => 'integer',
            'kader' => 'integer',
            'total' => 'integer',
            'kategori_json' => 'array',
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(DailyReportDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saved_by');
    }

    public function scopeTanggal(Builder $query, string $tanggal): Builder
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function getSumberLabelAttribute(): string
    {
        return self::SUMBER[$this->sumber] ?? $this->sumber;
    }

    public function getHariAttribute(): string
    {
        return $this->tanggal?->locale('id')->translatedFormat('l') ?? '-';
    }

    /**
     * Ambil total per kategori. Prioritaskan kategori_json (dinamis),
     * fallback ke kolom legacy untuk arsip lama.
     *
     * @return array<string, int>
     */
    public function getKategoriTotalsAttribute(): array
    {
        if (is_array($this->kategori_json) && $this->kategori_json !== []) {
            return array_map('intval', $this->kategori_json);
        }

        return [
            'tk_paud' => (int) $this->tk_paud,
            'sd_1_3' => (int) $this->sd_1_3,
            'sd_4_6' => (int) $this->sd_4_6,
            'smk' => (int) $this->smk,
            'guru' => (int) $this->guru,
            'balita' => (int) $this->balita,
            'bumil' => (int) $this->bumil,
            'busui' => (int) $this->busui,
            'kader' => (int) $this->kader,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryHistory extends Model
{
    use HasFactory;

    public const AKSI = [
        'dibuat' => 'Dibuat',
        'diperbarui' => 'Diperbarui',
        'dihapus' => 'Dihapus',
    ];

    protected $fillable = [
        'beneficiary_id',
        'user_id',
        'aksi',
        'beneficiary_nama',
        'institution_nama',
        'kategori',
        'tanggal',
        'hari',
        'alasan',
        'perubahan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'perubahan' => 'array',
        ];
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAksiLabelAttribute(): string
    {
        return self::AKSI[$this->aksi] ?? $this->aksi;
    }

    public function getKategoriLabelAttribute(): ?string
    {
        return $this->kategori ? (Beneficiary::KATEGORI[$this->kategori] ?? $this->kategori) : null;
    }

    public function getTanggalLengkapAttribute(): string
    {
        return $this->tanggal
            ? $this->tanggal->locale('id')->translatedFormat('d F Y') . ' (' . $this->hari . ')'
            : '-';
    }
}

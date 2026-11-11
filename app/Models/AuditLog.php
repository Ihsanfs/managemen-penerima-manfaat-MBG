<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public const AKSI = [
        'simpan' => 'Simpan',
        'update' => 'Update',
        'otomatis' => 'Otomatis',
    ];

    protected $fillable = [
        'user_id',
        'tanggal_data',
        'aksi',
        'sumber',
        'total_sebelum',
        'total_sesudah',
        'selisih',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_data' => 'date',
            'total_sebelum' => 'integer',
            'total_sesudah' => 'integer',
            'selisih' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAksiLabelAttribute(): string
    {
        return self::AKSI[$this->aksi] ?? $this->aksi;
    }
}

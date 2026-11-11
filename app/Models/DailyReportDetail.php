<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'institution_id',
        'tk_paud',
        'kelas_1',
        'kelas_2',
        'kelas_3',
        'kelas_4',
        'kelas_5',
        'kelas_6',
        'smk',
        'guru',
        'balita',
        'bumil',
        'busui',
        'kader',
        'total',
        'kategori_json',
    ];

    protected function casts(): array
    {
        return [
            'tk_paud' => 'integer',
            'kelas_1' => 'integer',
            'kelas_2' => 'integer',
            'kelas_3' => 'integer',
            'kelas_4' => 'integer',
            'kelas_5' => 'integer',
            'kelas_6' => 'integer',
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

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}

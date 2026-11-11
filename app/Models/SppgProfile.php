<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppgProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'telepon',
        'email',
        'kepala_sppg',
        'logo',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? static::create([
            'nama' => 'SPPG Samarang',
            'kecamatan' => 'Samarang',
            'kabupaten' => 'Garut',
            'provinsi' => 'Jawa Barat',
        ]);
    }
}

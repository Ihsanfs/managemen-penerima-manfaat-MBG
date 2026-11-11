<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Snapshot otomatis 01:00 WIB
|--------------------------------------------------------------------------
| Setiap hari kerja, data penerima manfaat hari sebelumnya disalin menjadi
| arsip tanggal berjalan bila petugas belum menyimpan data secara manual.
| Akhir pekan dilewati oleh perintah pm:auto-snapshot.
*/
Schedule::command('pm:auto-snapshot')
    ->weekdays()
    ->at('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

<?php

use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'throttle:30,1'])->prefix('unduhan')->name('unduhan.')->group(function () {
    Route::get('template-penerima', [DownloadController::class, 'templatePenerima'])->name('template-penerima');
    Route::get('export-penerima', [DownloadController::class, 'exportPenerima'])->name('export-penerima');
    Route::get('template-lembaga', [DownloadController::class, 'templateLembaga'])->name('template-lembaga');
    Route::get('export-lembaga', [DownloadController::class, 'exportLembaga'])->name('export-lembaga');
    Route::get('export-riwayat', [DownloadController::class, 'exportRiwayat'])->name('export-riwayat');
    Route::get('manual-book', [DownloadController::class, 'manualBook'])->name('manual-book');
    Route::get('cetak-penerima', [DownloadController::class, 'cetakPenerimaManfaat'])->name('cetak-penerima');
    Route::get('cetak-daftar-penerima', [DownloadController::class, 'cetakDaftarPenerima'])->name('cetak-daftar-penerima');
    Route::get('cetak-laporan-mingguan', [DownloadController::class, 'cetakLaporanMingguan'])->name('cetak-laporan-mingguan');
});

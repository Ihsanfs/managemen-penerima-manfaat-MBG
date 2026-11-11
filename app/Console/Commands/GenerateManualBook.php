<?php

namespace App\Console\Commands;

use App\Http\Controllers\DownloadController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateManualBook extends Command
{
    protected $signature = 'manual:generate {--output= : Lokasi file PDF tujuan}';

    protected $description = 'Buat ulang file manual book SPPG Samarang dalam format PDF';

    public function handle(DownloadController $controller): int
    {
        $output = $this->option('output') ?: public_path('manual-book-sppg-samarang.pdf');

        File::ensureDirectoryExists(dirname($output));

        $pdf = Pdf::loadView('pdf.manual-book', $controller->manualBookData())
            ->setPaper('a4', 'portrait');

        $pdf->save($output);

        $this->info('Manual book berhasil dibuat: ' . $output);

        return self::SUCCESS;
    }
}

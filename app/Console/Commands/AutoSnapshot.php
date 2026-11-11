<?php

namespace App\Console\Commands;

use App\Services\DailyReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoSnapshot extends Command
{
    protected $signature = 'pm:auto-snapshot {tanggal? : Tanggal yang akan dibuatkan snapshot (YYYY-MM-DD)}';

    protected $description = 'Membuat snapshot otomatis data penerima manfaat (carry-forward) untuk hari kerja.';

    public function handle(DailyReportService $service): int
    {
        try {
            $tanggal = $this->argument('tanggal')
                ? Carbon::parse($this->argument('tanggal'))->startOfDay()
                : today();
        } catch (\Throwable) {
            $this->error('Format tanggal tidak valid. Gunakan format YYYY-MM-DD.');

            return self::FAILURE;
        }

        if ($tanggal->isWeekend()) {
            $this->info($tanggal->toDateString() . ' adalah akhir pekan, snapshot otomatis dilewati.');

            return self::SUCCESS;
        }

        if ($tanggal->isAfter(today())) {
            $this->warn('Tanggal ' . $tanggal->toDateString() . ' belum jatuh tempo, snapshot tidak dibuat.');

            return self::SUCCESS;
        }

        $existing = \App\Models\DailyReport::query()
            ->whereDate('tanggal', $tanggal->toDateString())
            ->first();

        if ($existing) {
            $this->info('Snapshot ' . $tanggal->toDateString() . ' sudah ada (sumber: ' . $existing->sumber . '), tidak diubah.');

            return self::SUCCESS;
        }

        $report = $service->autoSnapshot($tanggal);

        if (! $report) {
            $this->warn('Tidak ada data sebelumnya untuk dijadikan acuan snapshot ' . $tanggal->toDateString() . '.');

            return self::SUCCESS;
        }

        $this->info('Snapshot otomatis ' . $report->tanggal->toDateString() . ' dibuat dengan total ' . $report->total . ' penerima manfaat.');

        return self::SUCCESS;
    }
}

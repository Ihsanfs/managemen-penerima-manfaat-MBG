<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Filament\Widgets\ChartWidget;

class KategoriChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Penerima Manfaat';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $snapshot = app(DailyReportService::class)->buildSnapshot(today());

        $kategoriJson = $snapshot['kategori_json'] ?? $snapshot['totals'];

        $labels = [];
        $data = [];

        foreach (DailyReportService::GRUP_URUTAN as $grup) {
            if (! isset($kategoriJson[$grup])) {
                continue;
            }

            $labels[] = Category::grupLabels()[$grup] ?? $grup;
            $data[] = (int) $kategoriJson[$grup];
        }

        if ($data === []) {
            $labels = array_values(DailyReport::KATEGORI);
            $data = array_values($snapshot['totals']);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penerima',
                    'data' => $data,
                    'backgroundColor' => [
                        '#3b82f6',
                        '#6366f1',
                        '#8b5cf6',
                        '#f59e0b',
                        '#64748b',
                        '#22c55e',
                        '#ef4444',
                        '#f97316',
                        '#14b8a6',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 10,
                        'font' => ['size' => 11],
                    ],
                ],
            ],
            'cutout' => '60%',
        ];
    }
}

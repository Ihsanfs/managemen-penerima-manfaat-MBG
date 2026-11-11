<?php

namespace App\Filament\Pages;

use App\Models\Beneficiary;
use App\Models\BeneficiaryHistory;
use App\Models\DailyReport;
use App\Models\Institution;
use App\Models\SppgProfile;
use Filament\Pages\Page;

class ManualBook extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Manual Book';

    protected static ?string $title = 'Manual Book';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.manual-book';

    protected function getViewData(): array
    {
        return [
            'profile' => SppgProfile::current(),
            'jumlahPenerima' => Beneficiary::query()->count(),
            'jumlahPenerimaAktif' => Beneficiary::query()->where('status', 'aktif')->count(),
            'jumlahLembaga' => Institution::query()->count(),
            'jumlahLaporan' => DailyReport::query()->count(),
            'jumlahRiwayat' => BeneficiaryHistory::query()->count(),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Institution;
use App\Models\SppgProfile;
use Filament\Pages\Page;

class DistribusiGis extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Peta & Distribusi SPPG';

    protected static ?string $title = 'Peta & Jalur Distribusi SPPG';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.distribusi-gis';

    protected function getViewData(): array
    {
        $profile = SppgProfile::current();
        $institutions = Institution::query()
            ->active()
            ->withCount('activeBeneficiaries')
            ->orderBy('urutan')
            ->get()
            ->map(function (Institution $institution) use ($profile) {
                $distance = $institution->distanceToSppg();
                return [
                    'id' => $institution->id,
                    'nama' => $institution->nama,
                    'tipe' => $institution->tipe,
                    'jenjang' => $institution->jenjang_label,
                    'alamat' => $institution->alamat,
                    'latitude' => $institution->latitude,
                    'longitude' => $institution->longitude,
                    'distance' => $distance,
                    'beneficiaries_count' => $institution->active_beneficiaries_count,
                ];
            })
            ->sortBy('distance')
            ->values();

        $maxDistance = $institutions->max('distance') ?? 0;
        $avgDistance = $institutions->whereNotNull('distance')->avg('distance') ?? 0;

        return [
            'profile' => $profile,
            'institutions' => $institutions,
            'maxDistance' => round($maxDistance, 2),
            'avgDistance' => round($avgDistance, 2),
            'totalInstitutions' => $institutions->count(),
            'totalBeneficiaries' => $institutions->sum('beneficiaries_count'),
        ];
    }

    public function cetakPdf()
    {
        $url = route('unduhan.cetak-distribusi-gis');

        return redirect()->to($url);
    }
}

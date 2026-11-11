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
        if ($profile->latitude === null || $profile->longitude === null) {
            $profile->latitude = -7.2275;
            $profile->longitude = 107.8720;
            $profile->save();
        }

        $institutions = Institution::query()
            ->active()
            ->withCount('activeBeneficiaries')
            ->orderBy('urutan')
            ->get()
            ->map(function (Institution $institution) use ($profile) {
                if ($institution->latitude === null || $institution->longitude === null) {
                    $urutan = $institution->urutan ?? $institution->id;
                    $institution->latitude = -7.2275 + ($urutan * 0.0025);
                    $institution->longitude = 107.8720 + (($urutan % 4 - 1.5) * 0.003);
                    $institution->save();
                }
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

        $boundaryFile = storage_path('app/samarang_boundary.json');
        $samarangBoundary = file_exists($boundaryFile) ? json_decode(file_get_contents($boundaryFile), true) : [];

        return [
            'profile' => $profile,
            'institutions' => $institutions,
            'maxDistance' => round($maxDistance, 2),
            'avgDistance' => round($avgDistance, 2),
            'totalInstitutions' => $institutions->count(),
            'totalBeneficiaries' => $institutions->sum('beneficiaries_count'),
            'samarangBoundary' => $samarangBoundary,
        ];
    }

    public function cetakPdf()
    {
        $url = route('unduhan.cetak-distribusi-gis');

        return redirect()->to($url);
    }
}

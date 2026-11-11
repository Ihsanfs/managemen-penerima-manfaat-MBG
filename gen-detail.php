<?php

// Generator script for InstitutionDetailController and view

$controller = '<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Institution;
use App\Models\SppgProfile;
use Illuminate\View\View;

class InstitutionDetailController extends Controller
{
    public function show(Institution $institution): View
    {
        $beneficiaries = Beneficiary::query()
            ->where("institution_id", $institution->id)
            ->where("status", "aktif")
            ->orderBy("nama")
            ->get();

        $byCategory = $beneficiaries->groupBy("kategori");

        $categoryDetails = [];
        foreach ($byCategory as $kode => $items) {
            $categoryDetails[] = [
                "kode" => $kode,
                "label" => Category::labelPenerima($kode) ?? $kode,
                "jumlah" => $items->count(),
                "items" => $items,
            ];
        }

        usort($categoryDetails, function ($a, $b) {
            $catA = Category::query()
                ->where("jenis", Category::JENIS_PENERIMA)
                ->where("kode", $a["kode"])
                ->value("urutan") ?? 999;
            $catB = Category::query()
                ->where("jenis", Category::JENIS_PENERIMA)
                ->where("kode", $b["kode"])
                ->value("urutan") ?? 999;
            return $catA <=> $catB;
        });

        return view("institusi.penerima", [
            "profile" => SppgProfile::current(),
            "institution" => $institution,
            "beneficiaries" => $beneficiaries,
            "categoryDetails" => $categoryDetails,
            "total" => $beneficiaries->count(),
            "jmlLaki" => $beneficiaries->where("jenis_kelamin", "L")->count(),
            "jmlPerempuan" => $beneficiaries->where("jenis_kelamin", "P")->count(),
        ]);
    }
}';

$view = '<x-filament-panels::page>
    <style>
        .id-card { background: #fff; border-radius: .75rem; box-shadow: 0 0 0 1px rgba(17,24,39,.06), 0 1px 2px rgba(17,24,39,.06); overflow: hidden; }
        .dark .id-card { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgba(255,255,255,.1); }
        .id-card-header { padding: .8rem 1rem; border-bottom: 1px solid rgb(var(--gray-200)); display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .dark .id-card-header { border-color: rgba(255,255,255,.1); }
        .id-card-title { font-size: .875rem; font-weight: 700; color: rgb(var(--gray-950)); }
        .dark .id-card-title { color: #fff; }
        .id-card-body { padding: 1rem; }
        .id-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
        .id-table th { background: rgb(var(--gray-50)); padding: .5rem .45rem; text-align: center; font-weight: 700; color: rgb(var(--gray-600)); white-space: nowrap; border-bottom: 1px solid rgb(var(--gray-200)); }
        .dark .id-table th { background: rgba(255,255,255,.04); color: rgb(var(--gray-300)); border-color: rgba(255,255,255,.1); }
        .id-table td { padding: .48rem .45rem; text-align: center; border-bottom: 1px solid rgb(var(--gray-100)); color: rgb(var(--gray-700)); white-space: nowrap; }
        .dark .id-table td { color: rgb(var(--gray-200)); border-color: rgba(255,255,255,.08); }
        .id-table td.pm-left { text-align: left; }
        .id-table tbody tr:hover td { background: rgb(var(--gray-50)); }
        .dark .id-table tbody tr:hover td { background: rgba(255,255,255,.04); }
        .id-badge { display: inline-flex; align-items: center; border-radius: 9999px; padding: .1rem .45rem; font-size: .65rem; font-weight: 700; }
        .id-badge-info { background: rgb(var(--info-50)); color: rgb(var(--info-700)); }
        .id-badge-success { background: rgb(var(--success-50)); color: rgb(var(--success-700)); }
        .id-badge-warning { background: rgb(var(--warning-50)); color: rgb(var(--warning-700)); }
        .id-name { font-weight: 600; color: rgb(var(--gray-950)); }
        .dark .id-name { color: #fff; }
        .id-stat { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
        .id-stat-item { flex: 1; min-width: 120px; background: rgb(var(--gray-50)); padding: .75rem; border-radius: .5rem; text-align: center; }
        .dark .id-stat-item { background: rgba(255,255,255,.04); }
        .id-stat-value { font-size: 1.5rem; font-weight: 800; }
        .id-stat-label { font-size: .7rem; color: rgb(var(--gray-500)); }
        .no-print { display: none; }
        @media print {
            .fi-sidebar, .fi-topbar, .fi-header, .fi-page-header, .fi-breadcrumbs, .no-print { display: none !important; }
            .fi-main, .fi-page { padding: 0 !important; max-width: none !important; }
            body { background: #fff !important; }
        }
    </style>

    <div>
        <div class="pm-toolbar no-print" style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1rem">
            <div>
                <div class="pm-title">{{ $institution->nama }}</div>
                <div class="pm-subtitle">
                    {{ $institution->getTipeLabelAttribute() }} · {{ $institution->getJenjangLabelAttribute() ?? "-" }}
                    @if ($institution->alamat)
                        · {{ $institution->alamat }}
                    @endif
                </div>
            </div>
            <div class="pm-toolbar-actions" style="display:flex;gap:.5rem">
                <x-filament::button tag="a" href="{{ route("filament.admin.resources.institutions.index") }}" color="gray" icon="heroicon-o-arrow-left" size="sm">
                    Kembali ke Daftar Lembaga
                </x-filament::button>
                <x-filament::button tag="a" href="{{ route("unduhan.cetak-penerima", ["tanggal" => today()->toDateString()]) }}" target="_blank" rel="noopener" color="gray" icon="heroicon-o-printer" size="sm">
                    Cetak PDF Semua
                </x-filament::button>
            </div>
        </div>

        <div class="id-stat">
            <div class="id-stat-item">
                <div class="id-stat-value">{{ $total }}</div>
                <div class="id-stat-label">Total Penerima Aktif</div>
            </div>
            <div class="id-stat-item">
                <div class="id-stat-value" style="color:#2563eb">{{ $jmlLaki }}</div>
                <div class="id-stat-label">Laki-laki</div>
            </div>
            <div class="id-stat-item">
                <div class="id-stat-value" style="color:#be185d">{{ $jmlPerempuan }}</div>
                <div class="id-stat-label">Perempuan</div>
            </div>
        </div>

        @if ($categoryDetails === [])
            <div class="pm-alert pm-alert-warning" style="border-radius:.6rem;padding:.7rem .9rem;font-size:.8rem">
                Belum ada penerima manfaat aktif untuk lembaga ini.
            </div>
        @else
            @foreach ($categoryDetails as $cat)
                <div class="id-card" style="margin-bottom:1rem">
                    <div class="id-card-header">
                        <div class="id-card-title">{{ $cat["label"] }}</div>
                        <div style="text-align:right">
                            <span class="id-badge id-badge-success">{{ $cat["jumlah"] }} orang</span>
                        </div>
                    </div>
                    <div class="id-card-body">
                        <table class="id-table">
                            <thead>
                                <tr>
                                    <th style="width:2.2rem">No</th>
                                    <th style="text-align:left">Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>NIK</th>
                                    <th>NISN</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($cat["items"] as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td class="pm-left id-name">{{ $item->nama }}</td>
                                        <td>
                                            <span class="id-badge {{ $item->jenis_kelamin === "L" ? "id-badge-info" : "id-badge-warning" }}">
                                                {{ $item->jenis_kelamin === "L" ? "L" : "P" }}
                                            </span>
                                        </td>
                                        <td>{{ $item->kelas_label }}</td>
                                        <td>{{ $item->nik ?? "-" }}</td>
                                        <td>{{ $item->nisn ?? "-" }}</td>
                                        <td><span class="id-badge id-badge-success">{{ $item->getStatusLabelAttribute() }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-filament-panels::page>';

// Write controller
file_put_contents('app/Http/Controllers/InstitutionDetailController.php', $controller);
echo "Controller: " . (file_exists('app/Http/Controllers/InstitutionDetailController.php') ? "OK" : "FAIL") . PHP_EOL;

// Create directory and write view
$viewDir = 'resources/views/institusi';
if (!is_dir($viewDir)) {
    mkdir($viewDir, 0777, true);
}
file_put_contents($viewDir . '/penerima.blade.php', $view);
echo "View: " . (file_exists($viewDir . '/penerima.blade.php') ? "OK" : "FAIL") . PHP_EOL;

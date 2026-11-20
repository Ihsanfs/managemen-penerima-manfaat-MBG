<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\SppgProfile;
use App\Services\DailyReportService;
use Illuminate\Contracts\View\View;

class PublicDataController extends Controller
{
    /**
     * Halaman publik: menampilkan data penerima manfaat hari ini
     * (sekolah, posyandu, dan jumlah) tanpa perlu login.
     */
    public function __invoke(): View
    {
        $tanggal = today();
        $data = app(DailyReportService::class)->dataForDate($tanggal);

        $institusi = collect($data['snapshot']['institusi']);
        $sekolah = $institusi->where('tipe', 'sekolah')->values();
        $posyandu = $institusi->where('tipe', 'posyandu')->values();

        $kategoriJson = $data['snapshot']['kategori_json'] ?? [];
        $grupLabels = Category::grupLabels();

        $rekap = collect(DailyReportService::GRUP_URUTAN)
            ->map(fn (string $key) => [
                'label' => $grupLabels[$key] ?? $key,
                'nilai' => (int) ($kategoriJson[$key] ?? 0),
            ]);

        return view('public.data-penerima-manfaat', [
            'profile' => SppgProfile::current(),
            'tanggal' => $tanggal,
            'snapshot' => $data['snapshot'],
            'sekolah' => $sekolah,
            'posyandu' => $posyandu,
            'totalSekolah' => (int) $sekolah->sum('total'),
            'totalPosyandu' => (int) $posyandu->sum('total'),
            'jumlahLaki' => Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
            'jumlahPerempuan' => Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
            'savedToday' => $data['report'],
            'rekap' => $rekap,
            'cssFile' => $this->builtCssFile(),
        ]);
    }

    /**
     * Ambil nama file CSS hasil build (manifest Vite) agar halaman publik
     * tetap memakai aset produksi walau dev server Vite sedang tidak aktif.
     */
    private function builtCssFile(): ?string
    {
        $manifestPath = public_path('build/manifest.json');

        if (! is_file($manifestPath)) {
            return null;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);

        return $manifest['resources/css/app.css']['file'] ?? null;
    }
}

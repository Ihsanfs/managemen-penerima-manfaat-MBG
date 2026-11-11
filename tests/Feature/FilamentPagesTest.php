<?php

namespace Tests\Feature;

use App\Filament\Pages\DataPenerimaManfaat;
use App\Filament\Pages\LaporanMingguan;
use App\Models\DailyReport;
use App\Models\User;
use App\Services\DailyReportService;
use Carbon\Carbon;
use Database\Seeders\SppgSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SppgSeeder::class);

        $this->admin = User::query()->where('email', 'admin@sppg.test')->firstOrFail();
    }

    public function test_halaman_login_dapat_dibuka(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_dashboard_dapat_dibuka(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('SPPG Samarang');
    }

    public function test_halaman_data_penerima_manfaat_dapat_dibuka(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/data-penerima-manfaat')
            ->assertOk()
            ->assertSee('Data Penerima Manfaat')
            ->assertSee('Sekolah')
            ->assertSee('Posyandu');
    }

    public function test_halaman_laporan_mingguan_dapat_dibuka(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/laporan-mingguan')
            ->assertOk()
            ->assertSee('Laporan Mingguan');
    }

    public function test_resource_utama_dapat_dibuka(): void
    {
        foreach ([
            '/admin/institutions',
            '/admin/beneficiaries',
            '/admin/daily-reports',
            '/admin/audit-logs',
            '/admin/users',
            '/admin/sppg-profiles',
        ] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_detail_riwayat_harian_dapat_dibuka(): void
    {
        $report = DailyReport::query()->orderByDesc('tanggal')->firstOrFail();

        $this->actingAs($this->admin)
            ->get('/admin/daily-reports/' . $report->getKey())
            ->assertOk()
            ->assertSee('Rincian per Lembaga');
    }

    public function test_halaman_utama_bisa_memilih_tanggal_arsip(): void
    {
        $report = DailyReport::query()->orderByDesc('tanggal')->firstOrFail();

        Livewire::actingAs($this->admin)
            ->test(DataPenerimaManfaat::class)
            ->call('pilihTanggal', $report->tanggal->toDateString())
            ->assertSet('tanggal', $report->tanggal->toDateString())
            ->assertOk();
    }

    public function test_halaman_mingguan_bisa_berpindah_minggu(): void
    {
        Livewire::actingAs($this->admin)
            ->test(LaporanMingguan::class)
            ->call('geserMinggu', -1)
            ->assertOk();
    }

    public function test_petugas_tidak_bisa_membuka_menu_khusus_admin(): void
    {
        $petugas = User::query()->where('email', 'petugas@sppg.test')->firstOrFail();

        $this->actingAs($petugas)->get('/admin/users')->assertForbidden();
        $this->actingAs($petugas)->get('/admin/audit-logs')->assertForbidden();
        $this->actingAs($petugas)->get('/admin/sppg-profiles')->assertForbidden();

        $this->actingAs($petugas)->get('/admin/beneficiaries')->assertOk();
        $this->actingAs($petugas)->get('/admin/data-penerima-manfaat')->assertOk();
    }

    public function test_simpan_data_hari_ini_dari_halaman_utama(): void
    {
        DailyReport::query()->whereDate('tanggal', today()->toDateString())->delete();

        Livewire::actingAs($this->admin)
            ->test(DataPenerimaManfaat::class)
            ->call('simpanHariIni')
            ->assertOk();

        $report = DailyReport::query()->whereDate('tanggal', today()->toDateString())->first();

        $this->assertNotNull($report);
        $this->assertSame('manual', $report->sumber);
        $this->assertSame(96, $report->total);
    }

    public function test_snapshot_total_sesuai_referensi(): void
    {
        $snapshot = app(DailyReportService::class)->buildSnapshot(today());

        $this->assertSame(96, $snapshot['total']);
        $this->assertSame([
            'tk_paud' => 12,
            'sd_1_3' => 23,
            'sd_4_6' => 6,
            'smk' => 6,
            'guru' => 8,
            'balita' => 18,
            'bumil' => 6,
            'busui' => 12,
            'kader' => 5,
        ], $snapshot['totals']);
    }

    public function test_auto_snapshot_membuat_carry_forward(): void
    {
        DailyReport::query()->delete();

        $service = app(DailyReportService::class);
        $base = today()->copy()->subWeek()->startOfWeek(Carbon::MONDAY);

        $previous = $service->save($base, 'manual', $this->admin);
        $next = $base->copy()->addDay();

        $created = $service->autoSnapshot($next);

        $this->assertNotNull($created);
        $this->assertSame('otomatis', $created->sumber);
        $this->assertSame($previous->total, $created->total);
        $this->assertSame($previous->details()->count(), $created->details()->count());
    }

    public function test_cetak_pdf_dari_halaman_utama(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DataPenerimaManfaat::class)
            ->call('cetakPdf')
            ->assertRedirect();
    }

    public function test_cetak_laporan_mingguan_menghasilkan_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('unduhan.cetak-laporan-mingguan', ['minggu' => today()->startOfWeek(Carbon::MONDAY)->toDateString()]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_halaman_distribusi_gis_dapat_dibuka(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/distribusi-gis')
            ->assertOk()
            ->assertSee('Peta & Jalur Distribusi SPPG');
    }

    public function test_cetak_distribusi_gis_menghasilkan_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('unduhan.cetak-distribusi-gis'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }
}

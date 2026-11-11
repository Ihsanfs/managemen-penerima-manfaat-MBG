<?php

namespace Tests\Feature;

use App\Filament\Pages\ImporEkspor;
use App\Filament\Resources\BeneficiaryResource\Pages\CreateBeneficiary;
use App\Filament\Resources\BeneficiaryResource\Pages\EditBeneficiary;
use App\Filament\Resources\BeneficiaryResource\Pages\ListBeneficiaries;
use App\Models\Beneficiary;
use App\Models\BeneficiaryHistory;
use App\Models\Institution;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\SppgSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SppgSeeder::class);

        $this->admin = User::query()->where('email', 'admin@sppg.test')->firstOrFail();
    }

    public function test_template_dan_ekspor_excel_dapat_diunduh(): void
    {
        foreach ([
            'unduhan.template-penerima',
            'unduhan.export-penerima',
            'unduhan.template-lembaga',
            'unduhan.export-lembaga',
            'unduhan.export-riwayat',
        ] as $routeName) {
            $response = $this->actingAs($this->admin)->get(route($routeName));

            $response->assertOk();
            $this->assertStringContainsString(
                'spreadsheetml',
                (string) $response->headers->get('content-type'),
            );
            $this->assertStringStartsWith('PK', $response->streamedContent());
        }
    }

    public function test_manual_book_pdf_dapat_diunduh(): void
    {
        $response = $this->actingAs($this->admin)->get(route('unduhan.manual-book'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_halaman_impor_ekspor_riwayat_dan_manual_dapat_dibuka(): void
    {
        foreach ([
            '/admin/impor-ekspor',
            '/admin/beneficiary-histories',
            '/admin/manual-book',
        ] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_impor_penerima_dari_csv_membuat_data_dan_riwayat(): void
    {
        $csv = implode("\n", [
            'nama_lembaga,nama,nik,nisn,jenis_kelamin,kategori,kelas,status',
            'SDN 1 Samarang,Siswa Impor Satu,3200000000000091,,L,sd,5,aktif',
            'SDN 1 Samarang,Siswa Impor Gagal,,,P,kategori_tidak_ada,4,aktif',
        ]);

        $upload = UploadedFile::fake()->createWithContent('penerima-uji.csv', $csv);

        $component = Livewire::actingAs($this->admin)
            ->test(ImporEkspor::class)
            ->set('filePenerima', $upload)
            ->call('importPenerima');

        $result = $component->get('hasilPenerima');

        $this->assertSame(1, $result['dibuat']);
        $this->assertSame(0, $result['diperbarui']);
        $this->assertSame(1, $result['gagal']);
        $this->assertSame(2, $result['total']);

        $this->assertDatabaseHas('beneficiaries', [
            'nama' => 'Siswa Impor Satu',
            'nik' => '3200000000000091',
            'kategori' => 'sd',
            'kelas' => 5,
        ]);

        $this->assertDatabaseMissing('beneficiaries', [
            'nama' => 'Siswa Impor Gagal',
        ]);

        $this->assertDatabaseHas('beneficiary_histories', [
            'beneficiary_nama' => 'Siswa Impor Satu',
            'aksi' => 'dibuat',
            'user_id' => $this->admin->id,
        ]);

        $history = BeneficiaryHistory::query()
            ->where('beneficiary_nama', 'Siswa Impor Satu')
            ->firstOrFail();

        $this->assertStringContainsString('penerima-uji.csv', $history->alasan);
    }

    public function test_impor_ulang_penerima_memperbarui_data_dan_mencatat_perubahan(): void
    {
        $csvAwal = implode("\n", [
            'nama_lembaga,nama,nik,jenis_kelamin,kategori,kelas,status',
            'SDN 1 Samarang,Siswa Impor Ulang,3200000000000092,L,sd,5,aktif',
        ]);

        $this->importPenerimaCsv('awal.csv', $csvAwal);

        $csvPerbarui = implode("\n", [
            'nama_lembaga,nama,nik,jenis_kelamin,kategori,kelas,status',
            'SDN 1 Samarang,Siswa Impor Ulang,3200000000000092,L,sd,6,pindah',
        ]);

        $component = $this->importPenerimaCsv('perbarui.csv', $csvPerbarui);

        $result = $component->get('hasilPenerima');

        $this->assertSame(0, $result['dibuat']);
        $this->assertSame(1, $result['diperbarui']);
        $this->assertSame(1, $result['total']);

        $this->assertDatabaseHas('beneficiaries', [
            'nama' => 'Siswa Impor Ulang',
            'kelas' => 6,
            'status' => 'pindah',
        ]);

        $history = BeneficiaryHistory::query()
            ->where('beneficiary_nama', 'Siswa Impor Ulang')
            ->where('aksi', 'diperbarui')
            ->firstOrFail();

        $this->assertStringContainsString('perbarui.csv', $history->alasan);
        $this->assertNotEmpty($history->perubahan);
    }

    public function test_impor_lembaga_dari_csv_membuat_dan_memperbarui(): void
    {
        $csv = implode("\n", [
            'nama,tipe,jenjang,alamat,penanggung_jawab,telepon,urutan,is_active',
            'SDN Impor Baru,sekolah,sd,Jalan Baru 1,Bu Guru,08123456789,,aktif',
            'SDN 1 Samarang,sekolah,sd,Jalan Diperbarui,Kepala Baru,081200000000,,aktif',
        ]);

        $upload = UploadedFile::fake()->createWithContent('lembaga-uji.csv', $csv);

        $component = Livewire::actingAs($this->admin)
            ->test(ImporEkspor::class)
            ->set('fileLembaga', $upload)
            ->call('importLembaga');

        $result = $component->get('hasilLembaga');

        $this->assertSame(1, $result['dibuat']);
        $this->assertSame(1, $result['diperbarui']);
        $this->assertSame(0, $result['gagal']);

        $this->assertDatabaseHas('institutions', [
            'nama' => 'SDN Impor Baru',
            'tipe' => 'sekolah',
            'jenjang' => 'sd',
            'alamat' => 'Jalan Baru 1',
        ]);

        $this->assertDatabaseHas('institutions', [
            'nama' => 'SDN 1 Samarang',
            'alamat' => 'Jalan Diperbarui',
            'penanggung_jawab' => 'Kepala Baru',
        ]);
    }

    public function test_perubahan_penerima_tercatat_dengan_alasan_tanggal_dan_hari(): void
    {
        $this->actingAs($this->admin);

        Carbon::setTestNow(Carbon::parse('2026-11-04 09:30:00'));

        $institution = Institution::query()->where('nama', 'SDN 1 Samarang')->firstOrFail();

        $beneficiary = new Beneficiary([
            'institution_id' => $institution->id,
            'nama' => 'Penerima Uji Riwayat',
            'nik' => '3200000000000093',
            'jenis_kelamin' => 'L',
            'kategori' => 'sd',
            'kelas' => 4,
            'status' => 'aktif',
        ]);
        $beneficiary->tracking_reason = 'Data awal uji coba.';
        $beneficiary->save();

        $beneficiary->tracking_reason = 'Perbaikan kelas karena naik kelas.';
        $beneficiary->kelas = 5;
        $beneficiary->save();

        $beneficiary->tracking_reason = 'Penerima pindah domisili.';
        $beneficiary->status = 'pindah';
        $beneficiary->save();

        $histories = BeneficiaryHistory::query()
            ->where('beneficiary_nama', 'Penerima Uji Riwayat')
            ->orderBy('id')
            ->get();

        $this->assertCount(3, $histories);
        $this->assertSame(['dibuat', 'diperbarui', 'diperbarui'], $histories->pluck('aksi')->all());
        $this->assertSame('2026-11-04', $histories[1]->tanggal->toDateString());
        $this->assertSame('Rabu', $histories[1]->hari);
        $this->assertSame('Perbaikan kelas karena naik kelas.', $histories[1]->alasan);
        $this->assertSame('kelas', $histories[1]->perubahan[0]['kolom']);
        $this->assertSame('4', $histories[1]->perubahan[0]['lama']);
        $this->assertSame('5', $histories[1]->perubahan[0]['baru']);
        $this->assertSame($this->admin->id, $histories[1]->user_id);

        $beneficiary->tracking_reason = 'Data uji dihapus.';
        $beneficiary->delete();

        $deleted = BeneficiaryHistory::query()
            ->where('beneficiary_nama', 'Penerima Uji Riwayat')
            ->where('aksi', 'dihapus')
            ->firstOrFail();

        $this->assertSame('Data uji dihapus.', $deleted->alasan);
        $this->assertNull($deleted->beneficiary_id);
    }

    public function test_edit_penerima_lewat_filament_menyimpan_alasan(): void
    {
        $beneficiary = Beneficiary::query()->where('status', 'aktif')->firstOrFail();

        Livewire::actingAs($this->admin)
            ->test(EditBeneficiary::class, ['record' => $beneficiary->getRouteKey()])
            ->fillForm([
                'keterangan' => 'Diperbarui lewat pengujian.',
                'alasan_perubahan' => 'Perbaikan keterangan hasil verifikasi lapangan.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('beneficiary_histories', [
            'beneficiary_id' => $beneficiary->id,
            'aksi' => 'diperbarui',
            'alasan' => 'Perbaikan keterangan hasil verifikasi lapangan.',
        ]);
    }

    public function test_hapus_penerima_lewat_filament_menyimpan_alasan(): void
    {
        $beneficiary = Beneficiary::query()->where('status', 'aktif')->firstOrFail();
        $nama = $beneficiary->nama;
        $id = $beneficiary->id;

        Livewire::actingAs($this->admin)
            ->test(ListBeneficiaries::class)
            ->callTableAction(\Filament\Tables\Actions\DeleteAction::class, $beneficiary, [
                'alasan' => 'Data uji dihapus melalui daftar penerima.',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('beneficiaries', ['id' => $id]);

        $this->assertDatabaseHas('beneficiary_histories', [
            'beneficiary_nama' => $nama,
            'aksi' => 'dihapus',
            'alasan' => 'Data uji dihapus melalui daftar penerima.',
        ]);
    }

    public function test_nik_dan_nisn_boleh_kosong_saat_menyimpan_penerima(): void
    {
        $institution = Institution::query()->where('nama', 'SDN 1 Samarang')->firstOrFail();

        Livewire::actingAs($this->admin)
            ->test(CreateBeneficiary::class)
            ->fillForm([
                'institution_id' => $institution->id,
                'nama' => 'Penerima Tanpa NIK NISN',
                'nik' => '',
                'nisn' => '',
                'jenis_kelamin' => 'P',
                'kategori' => 'sd',
                'kelas' => 3,
                'status' => 'aktif',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('beneficiaries', [
            'nama' => 'Penerima Tanpa NIK NISN',
            'nik' => null,
            'nisn' => null,
        ]);
    }

    public function test_nik_dan_nisn_yang_tidak_valid_ditolak_dengan_modal_error(): void
    {
        $institution = Institution::query()->where('nama', 'SDN 1 Samarang')->firstOrFail();

        Livewire::actingAs($this->admin)
            ->test(CreateBeneficiary::class)
            ->fillForm([
                'institution_id' => $institution->id,
                'nama' => 'Penerima NIK Salah',
                'nik' => '12345',
                'nisn' => '123',
                'jenis_kelamin' => 'L',
                'kategori' => 'sd',
                'kelas' => 2,
                'status' => 'aktif',
            ])
            ->call('create')
            ->assertHasFormErrors(['nik', 'nisn'])
            ->assertDispatched('beneficiary-validation-error');

        $this->assertDatabaseMissing('beneficiaries', [
            'nama' => 'Penerima NIK Salah',
        ]);
    }

    public function test_nik_dan_nisn_duplikat_ditolak(): void
    {
        $institution = Institution::query()->where('nama', 'SDN 1 Samarang')->firstOrFail();

        Beneficiary::query()->create([
            'institution_id' => $institution->id,
            'nama' => 'Penerima Pertama',
            'nik' => '3200000000000101',
            'nisn' => '0000000101',
            'jenis_kelamin' => 'L',
            'kategori' => 'sd',
            'kelas' => 1,
            'status' => 'aktif',
        ]);

        Livewire::actingAs($this->admin)
            ->test(CreateBeneficiary::class)
            ->fillForm([
                'institution_id' => $institution->id,
                'nama' => 'Penerima Duplikat',
                'nik' => '3200000000000101',
                'nisn' => '0000000101',
                'jenis_kelamin' => 'P',
                'kategori' => 'sd',
                'kelas' => 4,
                'status' => 'aktif',
            ])
            ->call('create')
            ->assertHasFormErrors(['nik', 'nisn']);
    }

    public function test_cetak_data_penerima_manfaat_menghasilkan_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('unduhan.cetak-penerima', ['tanggal' => today()->toDateString()]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    private function importPenerimaCsv(string $filename, string $content): mixed
    {
        $upload = UploadedFile::fake()->createWithContent($filename, $content);

        return Livewire::actingAs($this->admin)
            ->test(ImporEkspor::class)
            ->set('filePenerima', $upload)
            ->call('importPenerima');
    }
}

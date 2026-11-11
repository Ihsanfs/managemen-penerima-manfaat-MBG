<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\DailyReport;
use App\Models\Institution;
use App\Models\SppgProfile;
use App\Models\User;
use App\Services\DailyReportService;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SppgSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        SppgProfile::query()->firstOrCreate([], [
            'nama' => 'SPPG Samarang',
            'alamat' => 'Kecamatan Samarang, Kabupaten Garut',
            'kecamatan' => 'Samarang',
            'kabupaten' => 'Garut',
            'provinsi' => 'Jawa Barat',
            'kepala_sppg' => 'Petugas SPPG Samarang',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $petugasRole = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);

        $this->seedCategories();

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@sppg.test'],
            ['name' => 'Administrator SPPG', 'password' => Hash::make('password')],
        );
        $admin->syncRoles([$adminRole]);

        $petugas = User::query()->firstOrCreate(
            ['email' => 'petugas@sppg.test'],
            ['name' => 'Petugas SPPG', 'password' => Hash::make('password')],
        );
        $petugas->syncRoles([$petugasRole]);

        $institutions = $this->institutions();

        $nikSequence = 1;
        $nisnSequence = 1;
        $nameSequence = 0;

        if (Beneficiary::query()->exists()) {
            $this->command?->warn('Data penerima manfaat sudah ada, seeding data master dilewati.');
        } else {
            foreach ($institutions as $data) {
                $institution = Institution::query()->updateOrCreate(
                    ['nama' => $data['nama']],
                    [
                        'tipe' => $data['tipe'],
                        'jenjang' => $data['jenjang'] ?? null,
                        'urutan' => $data['urutan'],
                        'is_active' => true,
                    ],
                );

                foreach ($data['counts'] as $key => $count) {
                    for ($i = 0; $i < $count; $i++) {
                        $nameSequence++;
                        [$kategori, $kelas] = $this->parseKey($key);
                        $gender = $this->genderFor($nameSequence);

                        Beneficiary::query()->create([
                            'institution_id' => $institution->id,
                            'nama' => $this->makeName($faker, $gender, $nameSequence),
                            'nik' => '3278' . str_pad((string) $nikSequence++, 12, '0', STR_PAD_LEFT),
                            'nisn' => in_array($kategori, ['tk_paud', 'sd', 'smk'], true)
                                ? str_pad((string) $nisnSequence++, 10, '0', STR_PAD_LEFT)
                                : null,
                            'jenis_kelamin' => $gender,
                            'kategori' => $kategori,
                            'kelas' => $kelas,
                            'jurusan' => $kategori === 'smk' ? 'Perhotelan' : null,
                            'status' => 'aktif',
                            'bulan' => 11,
                            'tahun' => (int) today()->format('Y'),
                        ]);
                    }
                }
            }

            // Contoh data non-aktif agar filter status bisa diuji.
            $this->seedInactive($faker, $nikSequence, $nisnSequence);
        }

        $this->seedHistory($admin);
    }

    private function seedCategories(): void
    {
        $categories = [
            // Kategori penerima manfaat (kode unik per jenis)
            ['kode' => 'tk_paud', 'nama' => 'TK / PAUD', 'jenis' => 'penerima', 'grup' => 'tk_paud', 'urutan' => 1, 'keterangan' => 'Taman Kanak-Kanak / PAUD'],
            ['kode' => 'sd', 'nama' => 'SD', 'jenis' => 'penerima', 'grup' => null, 'urutan' => 2, 'keterangan' => 'Sekolah Dasar (kelompokan kelas 1-3 dan 4-6 pada laporan)'],
            ['kode' => 'smk', 'nama' => 'SMK', 'jenis' => 'penerima', 'grup' => 'smk', 'urutan' => 3, 'keterangan' => 'Sekolah Menengah Kejuruan'],
            ['kode' => 'smp', 'nama' => 'SMP', 'jenis' => 'penerima', 'grup' => 'smk', 'urutan' => 3, 'keterangan' => 'Sekolah Menengah Pertama (dikategorikan bersama SMP/SMK pada laporan)'],
            ['kode' => 'guru', 'nama' => 'Guru', 'jenis' => 'penerima', 'grup' => 'guru', 'urutan' => 4, 'keterangan' => 'Guru / pendidik'],
            ['kode' => 'balita', 'nama' => 'Balita', 'jenis' => 'penerima', 'grup' => 'balita', 'urutan' => 5, 'keterangan' => 'Anak balita posyandu'],
            ['kode' => 'bumil', 'nama' => 'Ibu Hamil', 'jenis' => 'penerima', 'grup' => 'bumil', 'urutan' => 6, 'keterangan' => 'Ibu hamil posyandu'],
            ['kode' => 'busui', 'nama' => 'Ibu Menyusui', 'jenis' => 'penerima', 'grup' => 'busui', 'urutan' => 7, 'keterangan' => 'Ibu menyusui posyandu'],
            ['kode' => 'kader', 'nama' => 'Kader', 'jenis' => 'penerima', 'grup' => 'kader', 'urutan' => 8, 'keterangan' => 'Kader posyandu'],
            // Kategori lembaga
            ['kode' => 'tk_paud', 'nama' => 'TK / PAUD', 'jenis' => 'lembaga', 'grup' => null, 'urutan' => 9, 'keterangan' => 'Lembaga TK / PAUD'],
            ['kode' => 'sd', 'nama' => 'SD', 'jenis' => 'lembaga', 'grup' => null, 'urutan' => 10, 'keterangan' => 'Lembaga Sekolah Dasar'],
            ['kode' => 'smk', 'nama' => 'SMK', 'jenis' => 'lembaga', 'grup' => null, 'urutan' => 11, 'keterangan' => 'Lembaga SMK'],
            ['kode' => 'smp', 'nama' => 'SMP', 'jenis' => 'lembaga', 'grup' => null, 'urutan' => 11, 'keterangan' => 'Lembaga SMP'],
            ['kode' => 'posyandu', 'nama' => 'Posyandu', 'jenis' => 'lembaga', 'grup' => null, 'urutan' => 12, 'keterangan' => 'Lembaga Posyandu'],
            // Jenjang sekolah
            ['kode' => 'tk_paud', 'nama' => 'TK / PAUD', 'jenis' => 'jenjang', 'grup' => null, 'urutan' => 13, 'keterangan' => 'Jenjang Taman Kanak-Kanak / PAUD'],
            ['kode' => 'sd', 'nama' => 'SD', 'jenis' => 'jenjang', 'grup' => null, 'urutan' => 14, 'keterangan' => 'Jenjang Sekolah Dasar'],
            ['kode' => 'smp', 'nama' => 'SMP', 'jenis' => 'jenjang', 'grup' => null, 'urutan' => 15, 'keterangan' => 'Jenjang Sekolah Menengah Pertama'],
            ['kode' => 'sma', 'nama' => 'SMA', 'jenis' => 'jenjang', 'grup' => null, 'urutan' => 16, 'keterangan' => 'Jenjang Sekolah Menengah Atas'],
            ['kode' => 'smk', 'nama' => 'SMK', 'jenis' => 'jenjang', 'grup' => null, 'urutan' => 17, 'keterangan' => 'Jenjang Sekolah Menengah Kejuruan'],
            ['kode' => 'slb', 'nama' => 'SLB', 'jenis' => 'jenjang', 'grup' => null, 'urutan' => 18, 'keterangan' => 'Jenjang Sekolah Luar Biasa'],
            ['kode' => 'lainnya', 'nama' => 'Lainnya', 'jenis' => 'jenjang', 'grup' => null, 'urutan' => 19, 'keterangan' => 'Jenjang Lainnya'],
        ];

        foreach ($categories as $data) {
            Category::query()->updateOrCreate(
                ['kode' => $data['kode'], 'jenis' => $data['jenis']],
                [
                    'nama' => $data['nama'],
                    'grup' => $data['grup'],
                    'urutan' => $data['urutan'],
                    'is_active' => true,
                    'keterangan' => $data['keterangan'],
                ],
            );
        }
    }

    private function institutions(): array
    {
        return [
            ['nama' => 'Tk Pertiwi', 'tipe' => 'sekolah', 'jenjang' => 'tk_paud', 'urutan' => 1, 'counts' => ['tk_paud' => 6, 'guru' => 1]],
            ['nama' => 'RA Al Istiqomah', 'tipe' => 'sekolah', 'jenjang' => 'tk_paud', 'urutan' => 2, 'counts' => ['tk_paud' => 6, 'guru' => 1]],
            ['nama' => 'SDN 1 Sukarasa', 'tipe' => 'sekolah', 'jenjang' => 'sd', 'urutan' => 3, 'counts' => ['sd_1' => 2, 'sd_2' => 1, 'sd_3' => 1, 'sd_5' => 1, 'sd_6' => 1, 'guru' => 1]],
            ['nama' => 'SDN 3 Sukarasa', 'tipe' => 'sekolah', 'jenjang' => 'sd', 'urutan' => 4, 'counts' => ['sd_1' => 2, 'sd_2' => 1, 'sd_3' => 1, 'sd_5' => 1, 'sd_6' => 1, 'guru' => 1]],
            ['nama' => 'SDN 1 Samarang', 'tipe' => 'sekolah', 'jenjang' => 'sd', 'urutan' => 5, 'counts' => ['sd_1' => 5, 'sd_2' => 5, 'sd_3' => 1, 'guru' => 2]],
            ['nama' => 'SDN 3 Samarang', 'tipe' => 'sekolah', 'jenjang' => 'sd', 'urutan' => 6, 'counts' => ['sd_1' => 2, 'sd_2' => 1, 'sd_3' => 1, 'sd_5' => 1, 'sd_6' => 1, 'guru' => 1]],
            ['nama' => 'SMK Al Madani', 'tipe' => 'sekolah', 'jenjang' => 'smk', 'urutan' => 7, 'counts' => ['smk_10' => 3, 'smk_11' => 1, 'smk_12' => 2, 'guru' => 1]],
            ['nama' => 'Cempaka Putih 1', 'tipe' => 'posyandu', 'jenjang' => null, 'urutan' => 8, 'counts' => ['balita' => 3, 'bumil' => 1, 'busui' => 2, 'kader' => 0]],
            ['nama' => 'Cempaka Putih 2', 'tipe' => 'posyandu', 'jenjang' => null, 'urutan' => 9, 'counts' => ['balita' => 3, 'bumil' => 1, 'busui' => 2, 'kader' => 1]],
            ['nama' => 'Boegenville', 'tipe' => 'posyandu', 'jenjang' => null, 'urutan' => 10, 'counts' => ['balita' => 3, 'bumil' => 1, 'busui' => 2, 'kader' => 1]],
            ['nama' => 'Bunga Desember', 'tipe' => 'posyandu', 'jenjang' => null, 'urutan' => 11, 'counts' => ['balita' => 3, 'bumil' => 1, 'busui' => 2, 'kader' => 1]],
            ['nama' => 'posyandu sambok', 'tipe' => 'posyandu', 'jenjang' => null, 'urutan' => 12, 'counts' => ['balita' => 3, 'bumil' => 1, 'busui' => 2, 'kader' => 1]],
            ['nama' => 'posyandu sambok 2', 'tipe' => 'posyandu', 'jenjang' => null, 'urutan' => 13, 'counts' => ['balita' => 3, 'bumil' => 1, 'busui' => 2, 'kader' => 1]],
        ];
    }

    private function parseKey(string $key): array
    {
        if (str_starts_with($key, 'sd_')) {
            return ['sd', (int) substr($key, 3)];
        }

        if (str_starts_with($key, 'smk_')) {
            return ['smk', (int) substr($key, 4)];
        }

        return [$key, null];
    }

    private function genderFor(int $sequence): string
    {
        return $sequence % 2 === 0 ? 'P' : 'L';
    }

    private function makeName($faker, string $gender, int $sequence): string
    {
        $name = $gender === 'L' ? $faker->name('male') : $faker->name('female');

        return $sequence % 7 === 0 ? $name . ' ' . $faker->lastName() : $name;
    }

    private function seedInactive($faker, int $nikSequence, int $nisnSequence): void
    {
        $sdn1 = Institution::query()->where('nama', 'SDN 1 Samarang')->first();
        $smk = Institution::query()->where('nama', 'SMK Al Madani')->first();
        $cempaka = Institution::query()->where('nama', 'Cempaka Putih 1')->first();

        if ($sdn1) {
            Beneficiary::query()->create([
                'institution_id' => $sdn1->id,
                'nama' => $faker->name('male'),
                'nik' => '3278' . str_pad((string) $nikSequence++, 12, '0', STR_PAD_LEFT),
                'nisn' => str_pad((string) $nisnSequence++, 10, '0', STR_PAD_LEFT),
                'jenis_kelamin' => 'L',
                'kategori' => 'sd',
                'kelas' => 2,
                'status' => 'non_aktif',
                'bulan' => 11,
                'tahun' => (int) today()->format('Y'),
            ]);
        }

        if ($smk) {
            Beneficiary::query()->create([
                'institution_id' => $smk->id,
                'nama' => $faker->name('female'),
                'nik' => '3278' . str_pad((string) $nikSequence++, 12, '0', STR_PAD_LEFT),
                'nisn' => str_pad((string) $nisnSequence++, 10, '0', STR_PAD_LEFT),
                'jenis_kelamin' => 'P',
                'kategori' => 'smk',
                'kelas' => 11,
                'jurusan' => 'Perhotelan',
                'status' => 'pindah',
                'bulan' => 11,
                'tahun' => (int) today()->format('Y'),
            ]);
        }

        if ($cempaka) {
            Beneficiary::query()->create([
                'institution_id' => $cempaka->id,
                'nama' => $faker->name('female'),
                'nik' => '3278' . str_pad((string) $nikSequence++, 12, '0', STR_PAD_LEFT),
                'jenis_kelamin' => 'P',
                'kategori' => 'balita',
                'status' => 'non_aktif',
                'bulan' => 11,
                'tahun' => (int) today()->format('Y'),
            ]);
        }
    }

    private function seedHistory(User $admin): void
    {
        if (DailyReport::query()->exists()) {
            $this->command?->warn('Riwayat harian sudah ada, seeding riwayat dilewati.');

            return;
        }

        $service = app(DailyReportService::class);

        $start = Carbon::parse('2026-10-01')->startOfDay();
        $end = today()->startOfDay();

        if ($start->diffInDays($end) > 60) {
            $start = $end->copy()->subDays(60);
        }

        $first = true;

        for ($date = $start->copy(); $date->lessThanOrEqualTo($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $service->save(
                $date->copy(),
                $first ? 'manual' : 'otomatis',
                $first ? $admin : null,
                $first ? 'Data awal aplikasi' : 'Tidak diupdate, mengikuti data hari kerja sebelumnya',
            );

            $first = false;
        }
    }
}

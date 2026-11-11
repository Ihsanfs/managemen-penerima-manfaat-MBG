<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$total = \App\Models\Beneficiary::query()->where('status', 'aktif')->count();
$laki = \App\Models\Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
$perempuan = \App\Models\Beneficiary::query()->where('status', 'aktif')->where('jenis_kelamin', 'P')->count();

$beneficiaries = \App\Models\Beneficiary::query()->where('status', 'aktif')
    ->where(function ($q) {
        $q->whereNull('jenis_kelamin')->orWhere('jenis_kelamin', '');
    })
    ->get();

$nullCount = $beneficiaries->count();

echo "=== DATA CONSISTENCY CHECK ===\n";
echo "Total Aktif:   " . $total . "\n";
echo "Laki-laki:     " . $laki . "\n";
echo "Perempuan:     " . $perempuan . "\n";
echo "Null/Empty:    " . $nullCount . "\n";
echo "L + P:         " . ($laki + $perempuan) . "\n";
echo "Selisih:       " . ($total - ($laki + $perempuan)) . "\n";
echo ($total === ($laki + $perempuan)) ? "STATUS: SINKRON\n" : "STATUS: TIDAK SINKRON\n";

echo "\n=== BENEFICIARY WITHOUT GENDER ===\n";
foreach ($beneficiaries as $b) {
    echo "ID: " . $b->id . ", Nama: " . $b->nama . ", Lembaga: " . ($b->institution ? $b->institution->nama : '-') . ", JK: '" . ($b->jenis_kelamin ?? 'NULL') . "'\n";
}

$allHaveGender = $nullCount === 0;
if (!$allHaveGender) {
    echo "\nPERINGATAN: Ada " . $nullCount . " data penerima tanpa jenis kelamin!\n";
    echo "Perbaiki dengan update jenis_kelamin atau tambah validasi wajib isi.\n";
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (App\Models\Institution::query()->limit(15)->get(['id','nama','kategori']) as $i) {
    echo $i->id.' | '.$i->nama.' | '.$i->kategori.PHP_EOL;
}
echo 'beneficiaries: '.App\Models\Beneficiary::count().PHP_EOL;
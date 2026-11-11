<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Ubah jenjang dari enum menjadi string agar dapat menampung
            // semua jenjang yang dikelola melalui menu Kategori (smp, sma, slb, dst).
            $table->string('jenjang')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->enum('jenjang', ['tk_paud', 'sd', 'smk'])->nullable()->change();
        });
    }
};

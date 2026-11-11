<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Ubah jenis dari enum menjadi string agar bisa menampung 'jenjang' tanpa ALTER ENUM berulang.
            $table->string('jenis')->default('penerima')->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('jenis', ['penerima', 'lembaga'])->default('penerima')->change();
        });
    }
};

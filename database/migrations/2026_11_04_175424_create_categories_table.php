<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->enum('jenis', ['penerima', 'lembaga'])->default('penerima'); // penerima manfaat atau lembaga
            $table->string('grup')->nullable(); // bucket laporan: tk_paud, sd_1_3, sd_4_6, smk, guru, balita, bumil, busui, kader
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['kode', 'jenis']);
            $table->index(['jenis', 'is_active']);
            $table->index('grup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

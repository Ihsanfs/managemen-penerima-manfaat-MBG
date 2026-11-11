<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->char('nik', 16)->nullable()->unique();
            $table->char('nisn', 10)->nullable()->index();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->enum('kategori', ['tk_paud', 'sd', 'smk', 'guru', 'balita', 'bumil', 'busui', 'kader']);
            $table->unsignedTinyInteger('kelas')->nullable();
            $table->string('jurusan')->nullable();
            $table->enum('status', ['aktif', 'non_aktif', 'pindah'])->default('aktif');
            $table->unsignedTinyInteger('bulan')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'kategori', 'status']);
            $table->index(['kategori', 'kelas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};

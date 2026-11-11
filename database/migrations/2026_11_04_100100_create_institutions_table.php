<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['sekolah', 'posyandu']);
            $table->enum('jenjang', ['tk_paud', 'sd', 'smk'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('nama');
            $table->index(['tipe', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};

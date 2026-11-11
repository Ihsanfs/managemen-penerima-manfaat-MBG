<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiary_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('aksi', ['dibuat', 'diperbarui', 'dihapus']);
            $table->string('beneficiary_nama');
            $table->string('institution_nama')->nullable();
            $table->string('kategori', 20)->nullable();
            $table->date('tanggal');
            $table->string('hari', 12);
            $table->text('alasan')->nullable();
            $table->json('perubahan')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'created_at']);
            $table->index(['beneficiary_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_histories');
    }
};

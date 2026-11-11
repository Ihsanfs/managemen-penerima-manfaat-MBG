<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal_data');
            $table->enum('aksi', ['simpan', 'update', 'otomatis']);
            $table->enum('sumber', ['manual', 'otomatis']);
            $table->unsignedInteger('total_sebelum')->default(0);
            $table->unsignedInteger('total_sesudah')->default(0);
            $table->integer('selisih')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['tanggal_data', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->unsignedInteger('tk_paud')->default(0);
            $table->unsignedInteger('sd_1_3')->default(0);
            $table->unsignedInteger('sd_4_6')->default(0);
            $table->unsignedInteger('smk')->default(0);
            $table->unsignedInteger('guru')->default(0);
            $table->unsignedInteger('balita')->default(0);
            $table->unsignedInteger('bumil')->default(0);
            $table->unsignedInteger('busui')->default(0);
            $table->unsignedInteger('kader')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->enum('sumber', ['manual', 'otomatis'])->default('manual');
            $table->foreignId('saved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('saved_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'sumber']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};

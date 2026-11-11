<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('tk_paud')->default(0);
            $table->unsignedInteger('kelas_1')->default(0);
            $table->unsignedInteger('kelas_2')->default(0);
            $table->unsignedInteger('kelas_3')->default(0);
            $table->unsignedInteger('kelas_4')->default(0);
            $table->unsignedInteger('kelas_5')->default(0);
            $table->unsignedInteger('kelas_6')->default(0);
            $table->unsignedInteger('smk')->default(0);
            $table->unsignedInteger('guru')->default(0);
            $table->unsignedInteger('balita')->default(0);
            $table->unsignedInteger('bumil')->default(0);
            $table->unsignedInteger('busui')->default(0);
            $table->unsignedInteger('kader')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->timestamps();

            $table->unique(['daily_report_id', 'institution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_details');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->json('kategori_json')->nullable()->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->dropColumn('kategori_json');
        });
    }
};

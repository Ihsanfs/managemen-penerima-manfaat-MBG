<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('has_class')->default(false)->after('grup');
        });

        // Set existing school categories to have has_class = true
        \DB::table('categories')
            ->whereIn('kode', ['sd', 'smp', 'sma', 'smk'])
            ->update(['has_class' => true]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('has_class');
        });
    }
};

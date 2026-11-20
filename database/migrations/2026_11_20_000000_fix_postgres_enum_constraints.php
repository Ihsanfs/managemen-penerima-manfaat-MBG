<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * PostgreSQL menyimpan tipe enum sebagai VARCHAR + CHECK constraint.
     * Saat kolom diubah menjadi string (categories.jenis dan
     * institutions.jenjang), constraint lama tidak ikut terhapus sehingga
     * nilai baru seperti "jenjang" atau "smp" ditolak. Migration ini
     * membersihkan constraint tersebut khusus untuk PostgreSQL.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE categories DROP CONSTRAINT IF EXISTS categories_jenis_check');
        DB::statement('ALTER TABLE institutions DROP CONSTRAINT IF EXISTS institutions_jenjang_check');
    }

    public function down(): void
    {
        // Tidak ada yang perlu dikembalikan: constraint enum lama justru
        // membuat data valid ditolak.
    }
};

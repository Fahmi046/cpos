<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kartu_stok', function (Blueprint $table) {
            // Tambah kolom stok_awal & saldo_akhir setelah qty (biar rapi)
            $table->integer('stok_awal')->default(0)->after('qty');
            $table->integer('saldo_akhir')->default(0)->after('stok_awal');
        });
    }

    public function down(): void
    {
        Schema::table('kartu_stok', function (Blueprint $table) {
            $table->dropColumn(['stok_awal', 'saldo_akhir']);
        });
    }
};

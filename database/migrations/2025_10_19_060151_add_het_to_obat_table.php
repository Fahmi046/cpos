<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->decimal('het', 15, 2)->nullable()->after('harga_jual'); // Tambahkan setelah harga_jual
        });
    }

    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn('het');
        });
    }
};

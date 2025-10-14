<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kartu_stok', function (Blueprint $table) {
            if (Schema::hasColumn('kartu_stok', 'qty')) {
                $table->dropColumn('qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kartu_stok', function (Blueprint $table) {
            $table->integer('qty')->nullable(); // untuk rollback
        });
    }
};

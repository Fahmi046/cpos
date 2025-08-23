<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->foreignId('satuan_id')
                ->nullable()
                ->constrained('satuan_obat')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropConstrainedForeignId('satuan_id');
        });
    }
};

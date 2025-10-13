<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kartu_stok', function (Blueprint $table) {
            // hapus kolom status jika ada
            if (Schema::hasColumn('kartu_stok', 'status')) {
                $table->dropColumn('status');
            }

            // tambahkan kolom baru
            $table->decimal('masuk', 10, 2)->default(0)->after('qty');
            $table->decimal('keluar', 10, 2)->default(0)->after('masuk');
        });
    }

    public function down(): void
    {
        Schema::table('kartu_stok', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->dropColumn(['masuk', 'keluar']);
        });
    }
};

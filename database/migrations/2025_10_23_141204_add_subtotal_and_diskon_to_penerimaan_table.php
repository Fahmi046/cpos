<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->after('jenis_ppn');
            $table->decimal('diskon', 15, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'diskon']);
        });
    }
};

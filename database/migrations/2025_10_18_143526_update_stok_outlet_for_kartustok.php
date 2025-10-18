<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_outlet', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->integer('masuk')->default(0)->after('qty');
            $table->integer('keluar')->default(0)->after('masuk');
            $table->integer('stok_awal')->default(0)->after('utuhan');
            $table->string('keterangan')->nullable()->after('tanggal');

            // Optional: bisa hapus kolom qty lama kalau sudah tidak dipakai
            // $table->dropColumn('qty');
        });
    }

    public function down(): void
    {
        Schema::table('stok_outlet', function (Blueprint $table) {
            $table->dropColumn(['masuk', 'keluar', 'stok_awal', 'keterangan']);
            // Jika kolom qty dihapus di up(), bisa dikembalikan di sini
            // $table->integer('qty')->default(0)->after('jenis');
        });
    }
};

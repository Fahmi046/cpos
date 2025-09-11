<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('satuan_obat')->onDelete('cascade');
            $table->foreignId('sediaan_id')->constrained('bentuk_sediaans')->onDelete('cascade');
            $table->foreignId('pabrik_id')->constrained('pabrik')->onDelete('cascade');
            $table->string('batch', 100)->nullable();
            $table->date('ed')->nullable();

            $table->string('no_transaksi')->nullable();
            $table->enum('tipe_transaksi', ['Penerimaan', 'Mutasi', 'Penjualan'])->nullable();

            $table->decimal('stok_awal', 15, 2)->default(0);
            $table->decimal('masuk', 15, 2)->default(0);
            $table->decimal('keluar', 15, 2)->default(0);
            $table->decimal('stok_akhir', 15, 2)->default(0);

            $table->decimal('harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};

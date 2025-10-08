<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_stok', function (Blueprint $table) {
            $table->id();

            // Relasi utama
            $table->foreignId('obat_id')->constrained('obat')->cascadeOnDelete();
            $table->foreignId('satuan_id')->nullable()->constrained('satuan_obat')->nullOnDelete();
            $table->foreignId('sediaan_id')->nullable()->constrained('bentuk_sediaans')->nullOnDelete();
            $table->foreignId('pabrik_id')->nullable()->constrained('pabrik')->nullOnDelete();

            // Link ke transaksi
            $table->foreignId('penerimaan_id')->nullable()->constrained('penerimaan')->cascadeOnDelete();
            $table->foreignId('penerimaan_detail_id')->nullable()->constrained('penerimaan_detail')->cascadeOnDelete();

            $table->foreignId('mutasi_id')->nullable()->constrained('mutasi')->cascadeOnDelete();
            $table->foreignId('mutasi_detail_id')->nullable()->constrained('mutasi_detail')->cascadeOnDelete();

            // Informasi stok
            $table->enum('jenis', ['masuk', 'keluar', 'opname'])->index();
            $table->integer('qty'); // bisa positif (masuk) atau negatif (keluar)
            $table->boolean('utuhan')->default(true);
            $table->date('ed')->nullable();
            $table->string('batch')->nullable();

            $table->date('tanggal')->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_stok');
    }
};

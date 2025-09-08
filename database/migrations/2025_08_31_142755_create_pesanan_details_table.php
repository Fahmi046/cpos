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
        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pesanan_id');
            $table->unsignedBigInteger('obat_id');
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->unsignedBigInteger('sediaan_id')->nullable();
            $table->unsignedBigInteger('pabrik_id')->nullable();
            $table->unsignedBigInteger('kreditur_id'); // ✅ Tambahan kolom kreditur_id
            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 0)->default(0);
            $table->decimal('jumlah', 15, 0)->default(0);
            $table->boolean('utuhan')->default(0); // ✅ ceklist / boolean
            $table->timestamps();

            // foreign key
            $table->foreign('pesanan_id')->references('id')->on('pesanan')->onDelete('cascade');
            $table->foreign('obat_id')->references('id')->on('obat')->onDelete('cascade');
            $table->foreign('satuan_id')->references('id')->on('satuan_obat')->onDelete('set null');
            $table->foreign('sediaan_id')->references('id')->on('bentuk_sediaans')->onDelete('set null');
            $table->foreign('pabrik_id')->references('id')->on('pabrik')->onDelete('set null');
            $table->foreign('kreditur_id')->references('id')->on('kreditur')->onDelete('cascade'); // relasi kreditur
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_detail');
    }
};

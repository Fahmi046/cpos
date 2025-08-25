<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat', 50)->unique();
            $table->string('nama_obat', 150);

            // foreign key
            $table->unsignedBigInteger('kategori_id');
            $table->unsignedBigInteger('satuan_id');
            $table->unsignedBigInteger('sediaan_id');
            $table->unsignedBigInteger('pabrik_id');
            $table->unsignedBigInteger('komposisi_id');

            // detail obat
            $table->unsignedBigInteger('harga_beli')->default(0);
            $table->unsignedBigInteger('harga_jual')->default(0);
            $table->boolean('aktif')->default(true);

            $table->timestamps();

            // relasi
            $table->foreign('kategori_id')->references('id')->on('kategori_obat')->onDelete('cascade');
            $table->foreign('satuan_id')->references('id')->on('satuan_obat')->onDelete('cascade');
            $table->foreign('sediaan_id')->references('id')->on('bentuk_sediaans')->onDelete('cascade');
            $table->foreign('pabrik_id')->references('id')->on('pabrik')->onDelete('cascade');
            $table->foreign('komposisi_id')->references('id')->on('komposisi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};

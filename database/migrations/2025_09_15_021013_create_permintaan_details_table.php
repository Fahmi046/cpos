<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permintaan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('satuan_obat')->onDelete('cascade');
            $table->foreignId('sediaan_id')->constrained('bentuk_sediaans')->onDelete('cascade');
            $table->foreignId('pabrik_id')->constrained('pabrik')->onDelete('cascade');

            $table->integer('qty_minta')->default(0);   // jumlah diminta
            $table->integer('qty_mutasi')->default(0);  // jumlah yang dipenuhi dari mutasi
            $table->integer('qty_sisa')->default(0);  // jumlah yang dipenuhi dari sisa
            $table->boolean('utuhan')->default(true);
            $table->string('batch', 100)->nullable();
            $table->date('ed')->nullable();
            $table->decimal('harga', 15, 2)->default(0);

            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->string('keterangan', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_detail');
    }
};

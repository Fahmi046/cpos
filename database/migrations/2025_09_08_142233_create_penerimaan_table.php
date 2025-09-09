<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade');
            $table->foreignId('kreditur_id')->constrained('kreditur')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('no_penerimaan')->unique();
            $table->enum('jenis_bayar', ['Cash', 'Kredit']);
            $table->string('no_faktur')->nullable();
            $table->integer('tenor')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->enum('jenis_ppn', ['Include', 'Exclude'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan');
    }
};

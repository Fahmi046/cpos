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
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat', 20)->unique();
            $table->string('nama_obat', 150);
            $table->string('kategori', 100)->nullable();
            $table->string('bentuk_sediaan', 50)->nullable();   // tablet, kapsul, sirup
            $table->text('kandungan')->nullable();              // Paracetamol 500mg
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->unsignedInteger('stok')->default(0);
            $table->string('satuan', 50)->nullable();           // strip, botol, box
            $table->string('pabrik', 150)->nullable();
            $table->date('tgl_expired')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};

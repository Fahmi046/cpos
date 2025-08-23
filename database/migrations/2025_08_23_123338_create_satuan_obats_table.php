<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('satuan_obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_satuan', 20)->unique();   // contoh: PCS, STRIP, BOX
            $table->string('nama_satuan', 50);             // contoh: Piece, Strip, Box
            $table->string('deskripsi', 150)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuan_obat');
    }
};

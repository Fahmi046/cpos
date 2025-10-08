<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mutasi', function (Blueprint $table) {
            $table->id();
            $table->string('no_mutasi')->unique();
            $table->date('tanggal');

            // relasi opsional ke permintaan
            $table->foreignId('permintaan_id')
                ->nullable()
                ->constrained('permintaan')
                ->nullOnDelete(); // kalau permintaan dihapus, field ini otomatis null

            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi');
    }
};

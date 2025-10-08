<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id();
            $table->string('no_permintaan')->unique();
            $table->date('tanggal');
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');

            $table->enum('status', ['pending', 'sebagian', 'selesai'])->default('pending');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan');
    }
};

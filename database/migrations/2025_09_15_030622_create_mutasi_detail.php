<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mutasi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mutasi_id')->constrained('mutasi')->onDelete('cascade');
            $table->foreignId('permintaan_detail_id')->nullable()->constrained('permintaan_detail')->nullOnDelete(); // relasi opsional

            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('satuan_obat')->onDelete('cascade');
            $table->foreignId('sediaan_id')->constrained('bentuk_sediaans')->onDelete('cascade');
            $table->foreignId('pabrik_id')->constrained('pabrik')->onDelete('cascade');

            $table->integer('qty')->default(0);
            $table->boolean('utuhan')->default(true);
            $table->string('batch', 100)->nullable();
            $table->date('ed')->nullable();
            $table->decimal('harga', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_detail');
    }
};

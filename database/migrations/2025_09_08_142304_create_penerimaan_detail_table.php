<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_id')->constrained('penerimaan')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('satuan_obat')->onDelete('cascade');
            $table->foreignId('sediaan_id')->constrained('bentuk_sediaans')->onDelete('cascade');
            $table->foreignId('pabrik_id')->constrained('pabrik')->onDelete('cascade');
            $table->integer('qty');
            $table->boolean('utuhan')->default(true); // 1 = utuh, 0 = pecah
            $table->date('ed')->nullable();
            $table->string('batch', 100)->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('disc1', 8, 2)->default(0);
            $table->decimal('disc2', 8, 2)->default(0);
            $table->decimal('disc3', 8, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_detail');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();

            // relasi kasir/user
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // relasi ke tabel obat
            $table->foreignId('obat_id')
                ->constrained('obat')
                ->cascadeOnDelete();

            // relasi ke kategori harga
            $table->foreignId('kategori_harga_id')
                ->nullable()
                ->constrained('kategori_harga')
                ->nullOnDelete();

            // data transaksi
            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);

            // outlet (biar stok antar outlet tidak bentrok)
            $table->foreignId('outlet_id')
                ->nullable()
                ->constrained('outlets')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};

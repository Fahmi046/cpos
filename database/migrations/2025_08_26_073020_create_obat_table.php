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

            // foreign key shorthand
            $table->foreignId('kategori_id')->constrained('kategori_obat')->cascadeOnDelete();
            $table->foreignId('satuan_id')->constrained('satuan_obat')->cascadeOnDelete();
            $table->foreignId('sediaan_id')->constrained('bentuk_sediaans')->cascadeOnDelete();
            $table->foreignId('pabrik_id')->constrained('pabrik')->cascadeOnDelete();
            $table->foreignId('komposisi_id')->constrained('komposisi')->cascadeOnDelete();
            $table->foreignId('kreditur_id')->constrained('kreditur')->cascadeOnDelete(); // 🔥 tambahan relasi

            // detail obat
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);

            // tambahan sesuai permintaan
            $table->string('isi_obat', 100)->nullable();       // contoh: "10 tablet", "100 ml"
            $table->string('dosis', 150)->nullable();          // contoh: "3x1 sehari"
            $table->boolean('utuh_satuan')->default(false);    // true = hanya utuh, false = bisa satuan
            $table->boolean('prekursor')->default(false);      // true = obat prekursor
            $table->boolean('psikotropika')->default(false);   // true = obat psikotropika
            $table->boolean('resep_active')->default(false);   // true = wajib resep dokter

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};

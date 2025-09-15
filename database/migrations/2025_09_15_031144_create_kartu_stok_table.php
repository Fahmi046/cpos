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
        Schema::create('kartu_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->foreignId('satuan_id')->nullable()->constrained('satuan_obat')->nullOnDelete();
            $table->foreignId('sediaan_id')->nullable()->constrained('bentuk_sediaans')->nullOnDelete();
            $table->foreignId('pabrik_id')->nullable()->constrained('pabrik')->nullOnDelete();

            $table->foreignId('penerimaan_id')->nullable()->constrained('penerimaan')->onDelete('cascade');
            $table->foreignId('mutasi_id')->nullable()->constrained('mutasi')->onDelete('cascade');

            $table->enum('jenis', ['masuk', 'keluar']); // masuk = penerimaan, keluar = mutasi
            $table->integer('qty');                      // jumlah perubahan stok
            $table->boolean('utuhan')->default(true);    // utuh / tidak
            $table->date('ed')->nullable();              // expired date
            $table->string('batch')->nullable();         // nomor batch

            $table->date('tanggal');                     // tanggal transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_stok');
    }
};

<?php
// database/migrations/xxxx_xx_xx_create_stok_outlet_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokOutletTable extends Migration
{
    public function up()
    {
        Schema::create('stok_outlet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->unsignedBigInteger('obat_id');
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->unsignedBigInteger('sediaan_id')->nullable();
            $table->unsignedBigInteger('pabrik_id')->nullable();

            $table->string('batch')->nullable();
            $table->date('ed')->nullable();

            $table->enum('jenis', ['masuk', 'keluar', 'retur'])->default('masuk');
            // qty disimpan *signed* : masuk => positif, keluar/retur => negatif
            $table->integer('qty')->default(0);
            $table->integer('utuhan')->default(0);

            // stok akhir setelah baris ini diterapkan
            $table->integer('stok_akhir')->default(0);

            $table->date('tanggal')->nullable();

            // referensi ke modul lain
            $table->unsignedBigInteger('mutasi_id')->nullable();
            $table->unsignedBigInteger('mutasi_detail_id')->nullable();
            $table->unsignedBigInteger('penjualan_id')->nullable();
            $table->unsignedBigInteger('penjualan_detail_id')->nullable();
            $table->unsignedBigInteger('retur_id')->nullable();
            $table->unsignedBigInteger('retur_detail_id')->nullable();

            $table->timestamps();

            $table->foreign('outlet_id')->references('id')->on('outlets')->cascadeOnDelete();
            $table->foreign('obat_id')->references('id')->on('obat')->cascadeOnDelete();
            $table->foreign('satuan_id')->references('id')->on('satuan_obat')->nullOnDelete();
            $table->foreign('sediaan_id')->references('id')->on('bentuk_sediaans')->nullOnDelete();
            $table->foreign('pabrik_id')->references('id')->on('pabrik')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stok_outlet');
    }
}

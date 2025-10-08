<?php
// database/migrations/xxxx_xx_xx_create_penjualan_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualanTables extends Migration
{
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->date('tanggal');
            $table->string('no_nota')->unique();
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('outlet_id')->references('id')->on('outlets')->cascadeOnDelete();
        });

        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penjualan_id');
            $table->unsignedBigInteger('obat_id');
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->unsignedBigInteger('sediaan_id')->nullable();
            $table->unsignedBigInteger('pabrik_id')->nullable();
            $table->string('batch')->nullable();
            $table->date('ed')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('penjualan_id')->references('id')->on('penjualan')->cascadeOnDelete();
            $table->foreign('obat_id')->references('id')->on('obat')->cascadeOnDelete();
            $table->foreign('satuan_id')->references('id')->on('satuan_obat')->nullOnDelete();
            $table->foreign('sediaan_id')->references('id')->on('bentuk_sediaans')->nullOnDelete();
            $table->foreign('pabrik_id')->references('id')->on('pabrik')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualan_detail');
        Schema::dropIfExists('penjualan');
    }
}

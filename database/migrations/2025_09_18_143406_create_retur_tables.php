<?php
// database/migrations/xxxx_xx_xx_create_retur_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturTables extends Migration
{
    public function up()
    {
        Schema::create('retur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->date('tanggal');
            $table->string('no_retur')->unique();
            $table->enum('tipe', ['ke_gudang', 'dari_pelanggan'])->default('ke_gudang');
            $table->text('keterangan')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('outlet_id')->references('id')->on('outlets')->cascadeOnDelete();
        });

        Schema::create('retur_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retur_id');
            $table->unsignedBigInteger('obat_id');
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->unsignedBigInteger('sediaan_id')->nullable();
            $table->unsignedBigInteger('pabrik_id')->nullable();
            $table->string('batch')->nullable();
            $table->date('ed')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('retur_id')->references('id')->on('retur')->cascadeOnDelete();
            $table->foreign('obat_id')->references('id')->on('obat')->cascadeOnDelete();
            $table->foreign('satuan_id')->references('id')->on('satuan_obat')->nullOnDelete();
            $table->foreign('sediaan_id')->references('id')->on('bentuk_sediaans')->nullOnDelete();
            $table->foreign('pabrik_id')->references('id')->on('pabrik')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('retur_detail');
        Schema::dropIfExists('retur');
    }
}

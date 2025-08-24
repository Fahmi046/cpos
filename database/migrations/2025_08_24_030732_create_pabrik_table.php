<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pabrik', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pabrik')->unique();
            $table->string('nama_pabrik');
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pabrik');
    }
};

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
        if (!Schema::hasTable('barang')) {
            Schema::create('barang', function (Blueprint $table) {
                $table->id();
                $table->string('kode_barang', 30)->unique();
                $table->string('nama_barang', 150);
                $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
                $table->string('satuan', 20);
                $table->decimal('harga', 15, 2)->default(0);
                $table->decimal('stok', 12, 2)->default(0);
                $table->decimal('stok_minimum', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};

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
        if (!Schema::hasTable('transaksi_stok')) {
            Schema::create('transaksi_stok', function (Blueprint $table) {
                $table->id();
                $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('jenis_transaksi', ['masuk', 'keluar']);
                $table->decimal('jumlah', 12, 2);
                $table->decimal('stok_sebelum', 12, 2)->default(0);
                $table->decimal('stok_sesudah', 12, 2)->default(0);
                $table->string('keterangan', 255)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_stok');
    }
};

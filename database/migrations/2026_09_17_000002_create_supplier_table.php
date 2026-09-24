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
        if (!Schema::hasTable('supplier')) {
            Schema::create('supplier', function (Blueprint $table) {
                $table->id();
                $table->string('nama_supplier', 150);
                $table->string('kontak_person', 100)->nullable();
                $table->string('telepon', 30)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('alamat')->nullable();
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('barang', 'supplier_id')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->foreignId('supplier_id')->nullable()->after('kategori_id')->constrained('supplier')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('transaksi_stok', 'supplier_id')) {
            Schema::table('transaksi_stok', function (Blueprint $table) {
                $table->foreignId('supplier_id')->nullable()->after('user_id')->constrained('supplier')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transaksi_stok', 'supplier_id')) {
            Schema::table('transaksi_stok', function (Blueprint $table) {
                $table->dropConstrainedForeignId('supplier_id');
            });
        }

        if (Schema::hasColumn('barang', 'supplier_id')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->dropConstrainedForeignId('supplier_id');
            });
        }

        Schema::dropIfExists('supplier');
    }
};

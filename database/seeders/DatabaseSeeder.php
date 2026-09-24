<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pemilik@stokclean.com'],
            [
                'name' => 'Pemilik StokClean',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'pemilik',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pegawai@stokclean.com'],
            [
                'name' => 'Pegawai Gudang',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        $sapu = \App\Models\Kategori::firstOrCreate(
            ['nama_kategori' => 'Alat Sapu'],
            ['deskripsi' => 'Berbagai jenis alat untuk menyapu']
        );
        $pel = \App\Models\Kategori::firstOrCreate(
            ['nama_kategori' => 'Alat Pel'],
            ['deskripsi' => 'Berbagai jenis alat untuk mengepel']
        );
        $lap = \App\Models\Kategori::firstOrCreate(
            ['nama_kategori' => 'Kain Lap'],
            ['deskripsi' => 'Berbagai jenis kain untuk membersihkan']
        );
        $kimia = \App\Models\Kategori::firstOrCreate(
            ['nama_kategori' => 'Cairan Kimia'],
            ['deskripsi' => 'Cairan pembersih dan sanitasi']
        );
        $perlengkapan = \App\Models\Kategori::firstOrCreate(
            ['nama_kategori' => 'Perlengkapan'],
            ['deskripsi' => 'Perlengkapan pendukung kebersihan']
        );

        $supplier1 = \App\Models\Supplier::firstOrCreate(
            ['nama_supplier' => 'PT Higienis Mandiri Nusantara'],
            [
                'kontak_person' => 'Bambang Sugianto',
                'telepon' => '0812-8899-0011',
                'email' => 'sales@higienismandiri.co.id',
                'alamat' => 'Jl. Daan Mogot No. 128, Jakarta Barat',
                'deskripsi' => 'Supplier utama alat sapu, pel, dan perlengkapan kebersihan gedung',
            ]
        );

        $supplier2 = \App\Models\Supplier::firstOrCreate(
            ['nama_supplier' => 'CV Berkah Sanitasi Kimia'],
            [
                'kontak_person' => 'Ratna Dewi',
                'telepon' => '0857-1234-5678',
                'email' => 'order@berkahsanitasi.com',
                'alamat' => 'Kawasan Industri Rungkut Blok B-4, Surabaya',
                'deskripsi' => 'Produsen cairan karbol, pembersih lantai, hand sanitizer, dan sabun cuci tangan',
            ]
        );

        $supplier3 = \App\Models\Supplier::firstOrCreate(
            ['nama_supplier' => 'Distributor CleanPro Jaya'],
            [
                'kontak_person' => 'Hendra Kusuma',
                'telepon' => '0819-7654-3210',
                'email' => 'marketing@cleanprojaya.id',
                'alamat' => 'Jl. Raya Serpong KM 7, Tangerang Selatan',
                'deskripsi' => 'Distributor resmi lap microfiber, sarung tangan karet, dan trash bag',
            ]
        );

        \App\Models\Barang::updateOrCreate(
            ['kode_barang' => 'BRG001'],
            [
                'nama_barang' => 'Sapu Ijuk',
                'kategori_id' => $sapu->id,
                'supplier_id' => $supplier1->id,
                'satuan' => 'pcs',
                'harga' => 30000,
                'stok' => 7,
                'stok_minimum' => 5,
            ]
        );

        \App\Models\Barang::whereNull('supplier_id')->update(['supplier_id' => $supplier1->id]);
    }
}

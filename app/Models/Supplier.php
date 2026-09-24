<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'kontak_person',
        'telepon',
        'email',
        'alamat',
        'deskripsi',
    ];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }

    public function transaksiMasuk(): HasMany
    {
        return $this->hasMany(TransaksiStok::class, 'supplier_id');
    }
}

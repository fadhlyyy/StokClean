<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\TransaksiStok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $pemilik;
    protected User $pegawai;
    protected Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemilik = User::factory()->create(['role' => 'pemilik']);
        $this->pegawai = User::factory()->create(['role' => 'pegawai']);
        $this->kategori = Kategori::create([
            'nama_kategori' => 'Alat Pel',
            'deskripsi' => 'Alat untuk mengepel',
        ]);
    }

    public function test_pemilik_can_create_new_barang_with_initial_stock(): void
    {
        $response = $this->actingAs($this->pemilik)->post('/barang', [
            'kode_barang' => 'BRG-TEST-1',
            'nama_barang' => 'Kain Pel Premium',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 35000,
            'stok' => 15,
            'stok_minimum' => 5,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('barang', [
            'kode_barang' => 'BRG-TEST-1',
            'nama_barang' => 'Kain Pel Premium',
            'stok' => 15,
        ]);

        $this->assertDatabaseHas('transaksi_stok', [
            'jenis_transaksi' => 'masuk',
            'jumlah' => 15,
        ]);
    }

    public function test_pegawai_cannot_create_barang(): void
    {
        $response = $this->actingAs($this->pegawai)->post('/barang', [
            'kode_barang' => 'BRG-PEG-1',
            'nama_barang' => 'Barang Ilegal',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 10000,
            'stok' => 5,
            'stok_minimum' => 2,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('barang', ['kode_barang' => 'BRG-PEG-1']);
    }

    public function test_pemilik_can_update_barang(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-ED-1',
            'nama_barang' => 'Sapu Ijuk Lama',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 25000,
            'stok' => 10,
            'stok_minimum' => 3,
        ]);

        $response = $this->actingAs($this->pemilik)->put('/barang/' . $barang->id, [
            'kode_barang' => 'BRG-ED-1',
            'nama_barang' => 'Sapu Ijuk Baru Diperbarui',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 30000,
            'stok_minimum' => 5,
        ]);

        $response->assertRedirect('/');
        $this->assertEquals('Sapu Ijuk Baru Diperbarui', $barang->fresh()->nama_barang);
        $this->assertEquals(30000, $barang->fresh()->harga);
    }

    public function test_pegawai_cannot_update_barang(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-ED-2',
            'nama_barang' => 'Sapu Asli',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 25000,
            'stok' => 10,
            'stok_minimum' => 3,
        ]);

        $response = $this->actingAs($this->pegawai)->put('/barang/' . $barang->id, [
            'kode_barang' => 'BRG-ED-2',
            'nama_barang' => 'Sapu Diubah Pegawai',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 30000,
            'stok_minimum' => 5,
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Sapu Asli', $barang->fresh()->nama_barang);
    }

    public function test_pemilik_can_delete_barang(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-DEL-1',
            'nama_barang' => 'Barang Dihapus',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 10000,
            'stok' => 5,
            'stok_minimum' => 2,
        ]);

        $response = $this->actingAs($this->pemilik)->delete('/barang/' . $barang->id);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('barang', ['id' => $barang->id]);
    }

    public function test_pegawai_cannot_delete_barang(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-DEL-2',
            'nama_barang' => 'Barang Aman',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 10000,
            'stok' => 5,
            'stok_minimum' => 2,
        ]);

        $response = $this->actingAs($this->pegawai)->delete('/barang/' . $barang->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('barang', ['id' => $barang->id]);
    }

    public function test_pemilik_can_create_new_kategori(): void
    {
        $response = $this->actingAs($this->pemilik)->post('/kategori', [
            'nama_kategori' => 'Cairan Desinfektan',
            'deskripsi' => 'Cairan pembersih kuman',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'Cairan Desinfektan',
        ]);
    }

    public function test_pegawai_cannot_create_kategori(): void
    {
        $response = $this->actingAs($this->pegawai)->post('/kategori', [
            'nama_kategori' => 'Kategori Pegawai',
            'deskripsi' => 'Deskripsi',
        ]);

        $response->assertStatus(403);
    }

    public function test_pegawai_can_add_stok_masuk(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-M01',
            'nama_barang' => 'Sapu Lidi',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 20000,
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->pegawai)->post('/stok-masuk', [
            'barang_id' => $barang->id,
            'jumlah' => 5,
            'keterangan' => 'Pengadaan stok tambahan oleh pegawai',
        ]);

        $response->assertRedirect('/');
        $this->assertEquals(15, $barang->fresh()->stok);

        $this->assertDatabaseHas('transaksi_stok', [
            'barang_id' => $barang->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 5,
            'stok_sebelum' => 10,
            'stok_sesudah' => 15,
        ]);
    }

    public function test_pegawai_can_add_stok_keluar_when_stock_is_sufficient(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-K01',
            'nama_barang' => 'Sapu Lidi',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 20000,
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->pegawai)->post('/stok-keluar', [
            'barang_id' => $barang->id,
            'jumlah' => 4,
            'keterangan' => 'Pemakaian operasional kantor',
        ]);

        $response->assertRedirect('/');
        $this->assertEquals(6, $barang->fresh()->stok);

        $this->assertDatabaseHas('transaksi_stok', [
            'barang_id' => $barang->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => 4,
            'stok_sebelum' => 10,
            'stok_sesudah' => 6,
        ]);
    }

    public function test_stok_keluar_fails_if_stock_insufficient(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-K02',
            'nama_barang' => 'Sapu Lidi',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'pcs',
            'harga' => 20000,
            'stok' => 2,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->pegawai)->post('/stok-keluar', [
            'barang_id' => $barang->id,
            'jumlah' => 10,
            'keterangan' => 'Pengambilan berlebih',
        ]);

        $response->assertSessionHasErrors(['jumlah']);
        $this->assertEquals(2, $barang->fresh()->stok);
    }

    public function test_user_can_export_laporan_csv(): void
    {
        $response = $this->actingAs($this->pemilik)->get('/laporan/export');

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function test_pegawai_cannot_create_user(): void
    {
        $response = $this->actingAs($this->pegawai)->post('/users', [
            'name' => 'User Baru',
            'email' => 'userbaru@test.com',
            'role' => 'pegawai',
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
    }

    public function test_pemilik_can_create_supplier(): void
    {
        $response = $this->actingAs($this->pemilik)->post('/supplier', [
            'nama_supplier' => 'PT Mitra Sanitasi Sejahtera',
            'kontak_person' => 'Bpk. Rudi',
            'telepon' => '08123456789',
            'email' => 'rudi@mitrasanitasi.com',
            'alamat' => 'Jl. Kebersihan No. 10',
            'deskripsi' => 'Pemasok karbol dan deterjen',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('supplier', [
            'nama_supplier' => 'PT Mitra Sanitasi Sejahtera',
            'kontak_person' => 'Bpk. Rudi',
            'email' => 'rudi@mitrasanitasi.com',
        ]);
    }

    public function test_pegawai_cannot_create_supplier(): void
    {
        $response = $this->actingAs($this->pegawai)->post('/supplier', [
            'nama_supplier' => 'Supplier Ilegal',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('supplier', ['nama_supplier' => 'Supplier Ilegal']);
    }

    public function test_pemilik_can_update_supplier(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'CV Sumber Rejeki',
            'kontak_person' => 'Ibu Maya',
            'telepon' => '08111222333',
        ]);

        $response = $this->actingAs($this->pemilik)->put('/supplier/' . $supplier->id, [
            'nama_supplier' => 'CV Sumber Rejeki Abadi',
            'kontak_person' => 'Ibu Maya Santoso',
            'telepon' => '08111222999',
            'email' => 'maya@sumberrejeki.com',
            'alamat' => 'Surabaya',
            'deskripsi' => 'Supplier peralatan sapu & pel',
        ]);

        $response->assertRedirect('/');
        $this->assertEquals('CV Sumber Rejeki Abadi', $supplier->fresh()->nama_supplier);
        $this->assertEquals('08111222999', $supplier->fresh()->telepon);
    }

    public function test_pemilik_can_delete_supplier(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'Toko Alat Bersih Lama',
        ]);

        $response = $this->actingAs($this->pemilik)->delete('/supplier/' . $supplier->id);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('supplier', ['id' => $supplier->id]);
    }

    public function test_barang_can_be_linked_with_supplier(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'PT Bersih Kilat',
        ]);

        $response = $this->actingAs($this->pemilik)->post('/barang', [
            'kode_barang' => 'BRG-SUP-1',
            'nama_barang' => 'Spons Cuci Piring Super',
            'kategori_id' => $this->kategori->id,
            'supplier_id' => $supplier->id,
            'satuan' => 'pack',
            'harga' => 15000,
            'stok' => 20,
            'stok_minimum' => 5,
        ]);

        $response->assertRedirect('/');
        $barang = Barang::where('kode_barang', 'BRG-SUP-1')->first();
        $this->assertNotNull($barang);
        $this->assertEquals($supplier->id, $barang->supplier_id);
        $this->assertEquals('PT Bersih Kilat', $barang->supplier->nama_supplier);
    }

    public function test_stok_masuk_records_supplier(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'PT Pasokan Nusantara',
        ]);

        $barang = Barang::create([
            'kode_barang' => 'BRG-MSK-1',
            'nama_barang' => 'Hand Soap Cair',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'botol',
            'harga' => 25000,
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->pegawai)->post('/stok-masuk', [
            'barang_id' => $barang->id,
            'supplier_id' => $supplier->id,
            'jumlah' => 15,
            'keterangan' => 'Pengadaan stok bulanan',
        ]);

        $response->assertRedirect('/');
        $transaksi = TransaksiStok::where('barang_id', $barang->id)->latest()->first();
        $this->assertNotNull($transaksi);
        $this->assertEquals($supplier->id, $transaksi->supplier_id);
        $this->assertEquals('PT Pasokan Nusantara', $transaksi->supplier->nama_supplier);
        $this->assertEquals(25, $barang->fresh()->stok);
    }
}

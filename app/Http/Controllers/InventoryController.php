<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\TransaksiStok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();

        $totalBarang = Barang::count();
        $totalStok = (float) Barang::sum('stok');

        $stokMasukBulanIni = (float) TransaksiStok::where('jenis_transaksi', 'masuk')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('jumlah');

        $stokKeluarBulanIni = (float) TransaksiStok::where('jenis_transaksi', 'keluar')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('jumlah');

        $lowStockBarangs = Barang::with(['kategori', 'supplier'])
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok')
            ->get();

        $barangs = Barang::with(['kategori', 'supplier'])
            ->orderBy('nama_barang')
            ->get();

        $kategoris = Kategori::withCount('barangs')
            ->orderBy('nama_kategori')
            ->get();

        $suppliers = Supplier::withCount('barangs')
            ->orderBy('nama_supplier')
            ->get();

        $transaksiMasuk = TransaksiStok::with(['barang.kategori', 'supplier', 'user'])
            ->where('jenis_transaksi', 'masuk')
            ->latest('created_at')
            ->limit(500)
            ->get();

        $transaksiKeluar = TransaksiStok::with(['barang.kategori', 'user'])
            ->where('jenis_transaksi', 'keluar')
            ->latest('created_at')
            ->limit(500)
            ->get();

        $riwayatTransaksi = TransaksiStok::with(['barang.kategori', 'supplier', 'user'])
            ->latest('created_at')
            ->limit(500)
            ->get();

        $recentActivities = TransaksiStok::with(['barang.kategori', 'supplier', 'user'])
            ->latest('created_at')
            ->limit(6)
            ->get();

        $users = User::latest()->get();

        // 7 days trend for chart
        $chartLabels = [];
        $chartMasuk = [];
        $chartKeluar = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');

            $masuk = (float) TransaksiStok::where('jenis_transaksi', 'masuk')
                ->whereDate('created_at', $date->toDateString())
                ->sum('jumlah');
            $keluar = (float) TransaksiStok::where('jenis_transaksi', 'keluar')
                ->whereDate('created_at', $date->toDateString())
                ->sum('jumlah');

            $chartMasuk[] = $masuk;
            $chartKeluar[] = $keluar;
        }

        return view('dashboard', compact(
            'totalBarang',
            'totalStok',
            'stokMasukBulanIni',
            'stokKeluarBulanIni',
            'lowStockBarangs',
            'barangs',
            'kategoris',
            'suppliers',
            'transaksiMasuk',
            'transaksiKeluar',
            'riwayatTransaksi',
            'recentActivities',
            'users',
            'chartLabels',
            'chartMasuk',
            'chartKeluar'
        ));
    }

    public function storeBarang(Request $request): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang dapat menambah data master barang.');
        }

        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:30', 'unique:barang,kode_barang'],
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'supplier_id' => ['nullable', 'exists:supplier,id'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok' => ['nullable', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
        ], [
            'kode_barang.unique' => 'Kode barang ini sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_id.required' => 'Pilih kategori barang.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga.required' => 'Harga satuan wajib diisi.',
            'stok_minimum.required' => 'Batas stok minimum wajib diisi.',
        ]);

        $initialStock = (float) ($validated['stok'] ?? 0);

        DB::transaction(function () use ($validated, $initialStock) {
            $barang = Barang::create([
                'kode_barang' => strtoupper(trim($validated['kode_barang'])),
                'nama_barang' => trim($validated['nama_barang']),
                'kategori_id' => $validated['kategori_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'satuan' => strtolower(trim($validated['satuan'])),
                'harga' => $validated['harga'],
                'stok' => $initialStock,
                'stok_minimum' => $validated['stok_minimum'],
            ]);

            if ($initialStock > 0) {
                TransaksiStok::create([
                    'barang_id' => $barang->id,
                    'user_id' => Auth::id() ?? 1,
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $initialStock,
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $initialStock,
                    'keterangan' => 'Stok awal barang baru',
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Barang "' . $validated['nama_barang'] . '" berhasil ditambahkan ke database.');
    }

    public function updateBarang(Request $request, Barang $barang): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang dapat mengedit data master barang.');
        }

        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:30', 'unique:barang,kode_barang,' . $barang->id],
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'supplier_id' => ['nullable', 'exists:supplier,id'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
        ], [
            'kode_barang.unique' => 'Kode barang ini sudah digunakan oleh barang lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_id.required' => 'Pilih kategori barang.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga.required' => 'Harga satuan wajib diisi.',
            'stok_minimum.required' => 'Batas stok minimum wajib diisi.',
        ]);

        $barang->update([
            'kode_barang' => strtoupper(trim($validated['kode_barang'])),
            'nama_barang' => trim($validated['nama_barang']),
            'kategori_id' => $validated['kategori_id'],
            'supplier_id' => $validated['supplier_id'] ?? null,
            'satuan' => strtolower(trim($validated['satuan'])),
            'harga' => $validated['harga'],
            'stok_minimum' => $validated['stok_minimum'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Data barang "' . $barang->nama_barang . '" berhasil diperbarui.');
    }

    public function destroyBarang(Barang $barang): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang dapat menghapus data master barang.');
        }

        $nama = $barang->nama_barang;
        $barang->delete();

        return redirect()->route('dashboard')->with('success', 'Barang "' . $nama . '" berhasil dihapus dari database.');
    }

    public function storeKategori(Request $request): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang dapat menambah data kategori.');
        }

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
        ]);

        Kategori::create([
            'nama_kategori' => trim($validated['nama_kategori']),
            'deskripsi' => trim($validated['deskripsi'] ?? ''),
        ]);

        return redirect()->route('dashboard')->with('success', 'Kategori "' . $validated['nama_kategori'] . '" berhasil ditambahkan.');
    }

    public function storeSupplier(Request $request): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang dapat menambah data supplier.');
        }

        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:150'],
            'kontak_person' => ['nullable', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'email.email' => 'Format email supplier tidak valid.',
        ]);

        Supplier::create([
            'nama_supplier' => trim($validated['nama_supplier']),
            'kontak_person' => trim($validated['kontak_person'] ?? ''),
            'telepon' => trim($validated['telepon'] ?? ''),
            'email' => trim($validated['email'] ?? ''),
            'alamat' => trim($validated['alamat'] ?? ''),
            'deskripsi' => trim($validated['deskripsi'] ?? ''),
        ]);

        return redirect()->route('dashboard')->with('success', 'Supplier "' . $validated['nama_supplier'] . '" berhasil ditambahkan.');
    }

    public function updateSupplier(Request $request, Supplier $supplier): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang dapat memperbarui data supplier.');
        }

        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:150'],
            'kontak_person' => ['nullable', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'email.email' => 'Format email supplier tidak valid.',
        ]);

        $supplier->update([
            'nama_supplier' => trim($validated['nama_supplier']),
            'kontak_person' => trim($validated['kontak_person'] ?? ''),
            'telepon' => trim($validated['telepon'] ?? ''),
            'email' => trim($validated['email'] ?? ''),
            'alamat' => trim($validated['alamat'] ?? ''),
            'deskripsi' => trim($validated['deskripsi'] ?? ''),
        ]);

        return redirect()->route('dashboard')->with('success', 'Data supplier "' . $supplier->nama_supplier . '" berhasil diperbarui.');
    }

    public function destroySupplier(Supplier $supplier): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang dapat menghapus data supplier.');
        }

        $nama = $supplier->nama_supplier;
        $supplier->delete();

        return redirect()->route('dashboard')->with('success', 'Supplier "' . $nama . '" berhasil dihapus.');
    }

    public function storeStokMasuk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barang_id' => ['required', 'exists:barang,id'],
            'supplier_id' => ['nullable', 'exists:supplier,id'],
            'jumlah' => ['required', 'numeric', 'min:1', 'max:1000'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'barang_id.required' => 'Pilih barang yang akan ditambah stoknya.',
            'jumlah.required' => 'Jumlah stok masuk wajib diisi.',
            'jumlah.min' => 'Jumlah stok masuk minimal 1.',
            'jumlah.max' => 'Jumlah stok masuk maksimal 1.000.',
        ]);

        DB::transaction(function () use ($validated) {
            $barang = Barang::lockForUpdate()->findOrFail($validated['barang_id']);
            $sebelum = (float) $barang->stok;
            $jumlah = (float) $validated['jumlah'];
            $sesudah = $sebelum + $jumlah;

            $barang->update(['stok' => $sesudah]);

            TransaksiStok::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id() ?? 1,
                'supplier_id' => $validated['supplier_id'] ?? $barang->supplier_id,
                'jenis_transaksi' => 'masuk',
                'jumlah' => $jumlah,
                'stok_sebelum' => $sebelum,
                'stok_sesudah' => $sesudah,
                'keterangan' => $validated['keterangan'] ?: 'Penambahan stok masuk gudang',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Stok masuk berhasil dicatat ke database.');
    }

    public function storeStokKeluar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barang_id' => ['required', 'exists:barang,id'],
            'jumlah' => ['required', 'numeric', 'min:1', 'max:1000'],
            'keterangan' => ['required', 'string', 'max:255'],
        ], [
            'barang_id.required' => 'Pilih barang yang dikeluarkan.',
            'jumlah.required' => 'Jumlah stok keluar wajib diisi.',
            'jumlah.min' => 'Jumlah stok keluar minimal 1.',
            'jumlah.max' => 'Jumlah stok keluar maksimal 1.000.',
            'keterangan.required' => 'Tujuan / keterangan penggunaan wajib diisi.',
        ]);

        $errorMsg = null;

        DB::transaction(function () use ($validated, &$errorMsg) {
            $barang = Barang::lockForUpdate()->findOrFail($validated['barang_id']);
            $sebelum = (float) $barang->stok;
            $jumlah = (float) $validated['jumlah'];

            if ($sebelum < $jumlah) {
                $errorMsg = 'Stok ' . $barang->nama_barang . ' tidak mencukupi. Sisa stok saat ini: ' . number_format($sebelum, 0, ',', '.') . ' ' . $barang->satuan;
                return;
            }

            $sesudah = $sebelum - $jumlah;
            $barang->update(['stok' => $sesudah]);

            TransaksiStok::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id() ?? 1,
                'jenis_transaksi' => 'keluar',
                'jumlah' => $jumlah,
                'stok_sebelum' => $sebelum,
                'stok_sesudah' => $sesudah,
                'keterangan' => $validated['keterangan'],
                'created_at' => now(),
            ]);
        });

        if ($errorMsg) {
            return back()->withErrors(['jumlah' => $errorMsg]);
        }

        return redirect()->route('dashboard')->with('success', 'Pengeluaran stok berhasil dicatat ke database.');
    }

    public function exportLaporanCsv(): StreamedResponse
    {
        $barangs = Barang::with('kategori')->orderBy('nama_barang')->get();
        $filename = 'laporan-stokclean-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($barangs) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Harga Satuan (Rp)', 'Stok Saat Ini', 'Stok Minimum', 'Status Stok']);

            foreach ($barangs as $b) {
                $isLow = (float) $b->stok <= (float) $b->stok_minimum;
                fputcsv($handle, [
                    $b->kode_barang,
                    $b->nama_barang,
                    $b->kategori?->nama_kategori ?? '-',
                    $b->satuan,
                    $b->harga,
                    $b->stok,
                    $b->stok_minimum,
                    $isLow ? 'Stok Menipis' : 'Aman',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getKalenderData(Request $request): JsonResponse
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        $month = (int) $request->query('month', Carbon::now()->month);

        if ($year < 2000 || $year > 2100) {
            $year = Carbon::now()->year;
        }
        if ($month < 1 || $month > 12) {
            $month = Carbon::now()->month;
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $transaksis = TransaksiStok::with(['barang.kategori', 'supplier', 'user'])
            ->whereBetween('created_at', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->orderBy('created_at', 'asc')
            ->get();

        $days = [];
        $totalMasukJumlah = 0;
        $totalMasukTransaksi = 0;
        $totalKeluarJumlah = 0;
        $totalKeluarTransaksi = 0;
        $dayVolumes = [];

        foreach ($transaksis as $t) {
            $dateKey = $t->created_at->format('Y-m-d');
            if (!isset($days[$dateKey])) {
                $days[$dateKey] = [
                    'date' => $dateKey,
                    'day_name' => $t->created_at->translatedFormat('l'),
                    'formatted_date' => $t->created_at->translatedFormat('d F Y'),
                    'masuk_jumlah' => 0,
                    'masuk_count' => 0,
                    'keluar_jumlah' => 0,
                    'keluar_count' => 0,
                    'total_transaksi' => 0,
                    'items' => [],
                ];
            }

            $jumlah = (float) $t->jumlah;
            if ($t->jenis_transaksi === 'masuk') {
                $days[$dateKey]['masuk_jumlah'] += $jumlah;
                $days[$dateKey]['masuk_count'] += 1;
                $totalMasukJumlah += $jumlah;
                $totalMasukTransaksi += 1;
            } else {
                $days[$dateKey]['keluar_jumlah'] += $jumlah;
                $days[$dateKey]['keluar_count'] += 1;
                $totalKeluarJumlah += $jumlah;
                $totalKeluarTransaksi += 1;
            }
            $days[$dateKey]['total_transaksi'] += 1;

            if (!isset($dayVolumes[$dateKey])) {
                $dayVolumes[$dateKey] = 0;
            }
            $dayVolumes[$dateKey] += $jumlah;

            $days[$dateKey]['items'][] = [
                'id' => $t->id,
                'jenis_transaksi' => $t->jenis_transaksi,
                'waktu' => $t->created_at->format('H:i'),
                'kode_barang' => $t->barang?->kode_barang ?? '-',
                'nama_barang' => $t->barang?->nama_barang ?? 'Barang Dihapus',
                'kategori' => $t->barang?->kategori?->nama_kategori ?? '-',
                'satuan' => $t->barang?->satuan ?? 'unit',
                'jumlah' => $jumlah,
                'stok_sebelum' => (float) $t->stok_sebelum,
                'stok_sesudah' => (float) $t->stok_sesudah,
                'keterangan' => $t->keterangan ?? '-',
                'supplier' => $t->supplier?->nama_supplier ?? ($t->barang?->supplier?->nama_supplier ?? null),
                'petugas' => $t->user?->name ?? 'Sistem',
            ];
        }

        // Cari hari dengan aktivitas / volume tertinggi
        $busiestDay = null;
        $maxVolume = 0;
        foreach ($dayVolumes as $dateKey => $vol) {
            if ($vol > $maxVolume) {
                $maxVolume = $vol;
                $busiestDay = [
                    'date' => $dateKey,
                    'formatted_date' => Carbon::parse($dateKey)->translatedFormat('d F'),
                    'total_volume' => $vol,
                ];
            }
        }

        $monthName = $startDate->translatedFormat('F Y');

        return response()->json([
            'year' => $year,
            'month' => $month,
            'month_name' => $monthName,
            'summary' => [
                'total_masuk_jumlah' => $totalMasukJumlah,
                'total_masuk_transaksi' => $totalMasukTransaksi,
                'total_keluar_jumlah' => $totalKeluarJumlah,
                'total_keluar_transaksi' => $totalKeluarTransaksi,
                'net_movement' => $totalMasukJumlah - $totalKeluarJumlah,
                'total_transaksi' => $totalMasukTransaksi + $totalKeluarTransaksi,
                'busiest_day' => $busiestDay,
            ],
            'days' => $days,
        ]);
    }
}

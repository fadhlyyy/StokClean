<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokClean - Sistem Manajemen Inventaris Gudang</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- QRCode.js (Local Offline) -->
    <script src="{{ asset('js/qrcode.min.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .active-menu {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
        }
        .inactive-menu {
            color: #475569;
            font-weight: 600;
        }
        .inactive-menu:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98) translateY(-4px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col md:flex-row bg-slate-50 text-slate-800">

    @php
        $isPemilik = (Auth::user()?->role === 'pemilik');
    @endphp

    <!-- MOBILE TOP BAR -->
    <div class="md:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200 z-30">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="StokClean Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="font-bold text-slate-900 leading-tight">StokClean</h1>
                <p class="text-[11px] text-slate-500 font-medium">Manajemen Inventaris</p>
            </div>
        </div>
        <button id="mobileMenuBtn" aria-label="Buka menu navigasi" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- SIDEBAR NAV -->
    <aside id="sidebar" class="hidden md:flex flex-col w-64 bg-white border-r border-slate-200 h-screen shrink-0 transition-all duration-200 z-40 fixed inset-y-0 left-0 overflow-y-auto">
        
        <!-- App Logo & Title -->
        <div class="p-6 flex items-center gap-3.5 border-b border-slate-100">
            <div class="w-10 h-10 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="StokClean Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="font-bold text-base text-slate-900 tracking-tight leading-tight">StokClean</h1>
                <p class="text-[11px] text-slate-500 font-medium tracking-wide">Gudang &amp; Sanitasi</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <p class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">NAVIGASI UTAMA</p>

            <a href="#dashboard" onclick="switchTab('dashboard', event); return false;" id="nav-dashboard" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 active-menu">
                <i data-lucide="layout-grid" class="w-4 h-4 shrink-0"></i>
                <span>Dashboard</span>
            </a>
            <a href="#data-barang" onclick="switchTab('data-barang', event); return false;" id="nav-data-barang" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                <i data-lucide="package" class="w-4 h-4 shrink-0"></i>
                <span>Data Barang</span>
            </a>
            <a href="#kategori" onclick="switchTab('kategori', event); return false;" id="nav-kategori" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                <i data-lucide="tags" class="w-4 h-4 shrink-0"></i>
                <span>Kategori</span>
            </a>
            <a href="#supplier" onclick="switchTab('supplier', event); return false;" id="nav-supplier" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                <i data-lucide="truck" class="w-4 h-4 shrink-0"></i>
                <span>Supplier</span>
            </a>
            <a href="#stok-masuk" onclick="switchTab('stok-masuk', event); return false;" id="nav-stok-masuk" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                <i data-lucide="arrow-down-left" class="w-4 h-4 shrink-0"></i>
                <span>Stok Masuk</span>
            </a>
            <a href="#stok-keluar" onclick="switchTab('stok-keluar', event); return false;" id="nav-stok-keluar" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                <i data-lucide="arrow-up-right" class="w-4 h-4 shrink-0"></i>
                <span>Stok Keluar</span>
            </a>
            <a href="#riwayat-transaksi" onclick="switchTab('riwayat-transaksi', event); return false;" id="nav-riwayat-transaksi" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                <i data-lucide="history" class="w-4 h-4 shrink-0"></i>
                <span>Riwayat Transaksi</span>
            </a>

            <p class="px-3 pb-2 pt-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">MANAJEMEN</p>

            @if ($isPemilik)
                <a href="#pengguna" onclick="switchTab('pengguna', event); return false;" id="nav-pengguna" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                    <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                    <span>Pengguna</span>
                </a>
            @endif

            <a href="#laporan" onclick="switchTab('laporan', event); return false;" id="nav-laporan" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs leading-none transition-all duration-150 inactive-menu">
                <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                <span>Laporan</span>
            </a>
        </nav>

        <!-- Sidebar Footer Spacer -->
        <div class="p-4 mt-auto"></div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0 bg-slate-50 md:ml-64">
        
        <!-- TOP HEADER BAR -->
        <header class="h-16 bg-white sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between border-b border-slate-200">
            <div class="relative w-full max-w-md flex items-center">
                <!-- Global search removed per request -->
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <button type="button" onclick="toggleActionMenu(event, 'profile-dropdown')" class="flex items-center gap-1.5 focus:outline-none hover:bg-slate-50 pr-2 p-1 rounded-xl transition group">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-xs border border-slate-800 group-hover:scale-105 transition-transform duration-200 overflow-hidden">
                            @if(Auth::user()->foto_profil)
                                <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Profil" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                            @endif
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors"></i>
                    </button>

                    <!-- PROFILE DROPDOWN -->
                    <div id="profile-dropdown" class="action-dropdown hidden absolute right-0 mt-3 w-64 bg-white rounded-3xl shadow-[0_15px_40px_rgba(15,23,42,0.12)] border border-slate-200/80 p-2 z-50 animate-fadeIn">
                        <div class="px-4 pt-5 pb-5 border-b border-slate-100 flex flex-col items-center text-center">
                            <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold text-xl shadow-md mb-3 overflow-hidden">
                                @if(Auth::user()->foto_profil)
                                    <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Profil" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                                @endif
                            </div>
                            <p class="text-sm font-bold text-slate-900 mb-0.5">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                            <p class="text-[11px] text-slate-500 font-medium mb-4">{{ Auth::user()->email ?? 'Akun Pengguna' }}</p>
                            
                            <div class="inline-flex items-center gap-1.5 text-[10px] bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isPemilik ? 'bg-blue-600' : 'bg-emerald-600' }}"></span>
                                <span class="font-bold text-slate-700 uppercase tracking-wide">{{ $isPemilik ? 'Peran: Pemilik (Admin)' : 'Peran: Pegawai (Staf Gudang)' }}</span>
                            </div>
                        </div>
                        
                        <div class="p-1.5 mt-1">
                            <button type="button" onclick="openProfileSettingsModal()" class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition mb-1">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                                <span>Pengaturan Profil</span>
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition border border-transparent hover:border-rose-100">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Keluar Akun</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        <div class="px-4 sm:px-6 lg:px-8 pt-6">
            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1 shadow-xs">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        <span>Terjadi kesalahan saat memproses data:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- 1. DASHBOARD VIEW -->
        <!-- ========================================== -->
        <div id="view-dashboard" class="tab-view w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 animate-fadeIn">
            
            <!-- Welcome Header Card -->
            <div class="bg-slate-900 text-white p-6 sm:p-7 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-800 border border-slate-700 text-[10px] font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Data Terkini Gudang
                    </span>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Selamat Datang, {{ Auth::user()->name ?? 'Pengguna' }}</h1>
                    <p class="text-xs text-slate-400 font-normal mt-1 max-w-xl">
                        @if ($isPemilik)
                            Sebagai <strong>Pemilik (Admin)</strong>, Anda memiliki hak akses penuh untuk mengelola master barang, stok keluar-masuk, dan pengguna.
                        @else
                            Sebagai <strong>Pegawai (Staf Gudang)</strong>, Anda bertugas mencatat pergerakan stok masuk &amp; keluar harian dan memantau ketersediaan barang.
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    @if ($isPemilik)
                        <button onclick="openTambahBarangModal()" class="inline-flex items-center gap-2 bg-white hover:bg-slate-100 text-slate-900 font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition">
                            <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                            <span>Tambah Barang</span>
                        </button>
                    @endif
                    <button onclick="openStokMasukModal()" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition">
                        <i data-lucide="arrow-down-left" class="w-4 h-4 shrink-0"></i>
                        <span>Catat Masuk</span>
                    </button>
                    <button onclick="openStokKeluarModal()" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 shrink-0"></i>
                        <span>Catat Keluar</span>
                    </button>
                </div>
            </div>

            <!-- 4 Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: Total Jenis Barang -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i data-lucide="package" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">Aktif</span>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">TOTAL JENIS BARANG</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalBarang, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Item terdaftar di sistem</p>
                </div>

                <!-- Card 2: Stok Fisik Tersedia -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">Tersedia</span>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">STOK FISIK TERSEDIA</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalStok, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Total unit saat ini</p>
                </div>

                <!-- Card 3: Stok Masuk Bulan Ini -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i data-lucide="arrow-down-left" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">Bulan Ini</span>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">STOK MASUK BULAN INI</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stokMasukBulanIni, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Unit diterima ke gudang</p>
                </div>

                <!-- Card 4: Stok Keluar Bulan Ini -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">Bulan Ini</span>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">STOK KELUAR BULAN INI</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stokKeluarBulanIni, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Unit dikeluarkan</p>
                </div>

            </div>

            <!-- Charts & Recent Activities Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Stock Movement Chart (2 Cols) -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 gap-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Pergerakan Stok (7 Hari Terakhir)</h2>
                            <p class="text-xs text-slate-500 font-normal">Perbandingan arus barang masuk vs barang keluar gudang</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-md bg-slate-900 shrink-0"></span>
                                <span>Stok Masuk</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-md bg-slate-400 shrink-0"></span>
                                <span>Stok Keluar</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative h-64 w-full mt-4">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activities (1 Col) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                            <h2 class="text-sm font-bold text-slate-900">Aktivitas Transaksi Terbaru</h2>
                            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">Log Terkini</span>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse ($recentActivities as $act)
                                @php($isMasuk = $act->jenis_transaksi === 'masuk')
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg {{ $isMasuk ? 'bg-slate-100 text-slate-800' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="{{ $isMasuk ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4"></i>
                                    </div>
                                    <div class="text-xs leading-relaxed min-w-0 flex-1">
                                        <p class="text-slate-800">
                                            <span class="font-bold text-slate-900">{{ $act->user?->name ?? 'Petugas' }}</span> 
                                            {{ $isMasuk ? 'menambah stok' : 'mengeluarkan stok' }} 
                                            <strong>{{ number_format($act->jumlah, 0, ',', '.') }} {{ $act->barang?->satuan }}</strong>
                                            <span class="font-semibold text-slate-900">{{ $act->barang?->nama_barang ?? '-' }}</span>
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $act->created_at ? $act->created_at->diffForHumans() : '-' }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500 py-6 text-center">Belum ada aktivitas transaksi.</p>
                            @endforelse
                        </div>
                    </div>

                    <button onclick="switchTab('riwayat-transaksi')" class="w-full inline-flex items-center justify-center mt-5 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition">
                        Buka Riwayat Lengkap
                    </button>
                </div>

            </div>

            <!-- Low Stock Inventory Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Peringatan: Stok Mencapai Batas Minimum</h2>
                        <p class="text-xs text-slate-500 font-normal">Daftar barang yang jumlah fisiknya berada di bawah atau sama dengan stok minimum</p>
                    </div>
                    <button onclick="openStokMasukModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition">
                        <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                        <span>Restok Barang</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-5">KODE</th>
                                <th class="py-3.5 px-5">NAMA BARANG</th>
                                <th class="py-3.5 px-5">KATEGORI</th>
                                <th class="py-3.5 px-5">STOK SAAT INI</th>
                                <th class="py-3.5 px-5">MINIMUM</th>
                                <th class="py-3.5 px-5">STATUS</th>
                                <th class="py-3.5 px-5 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse ($lowStockBarangs as $barang)
                                @php($isCritical = (float) $barang->stok <= 0)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="showBarcodeModal({{ json_encode($barang) }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-900 hover:text-white border border-slate-200 text-slate-700 transition shadow-2xs group flex items-center justify-center shrink-0" title="Buka Barcode Kotak (QR Code)">
                                                <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <span class="font-bold text-slate-900 font-mono">{{ $barang->kode_barang }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-5 font-semibold text-slate-900">{{ $barang->nama_barang }}</td>
                                    <td class="py-3.5 px-5 text-slate-500">{{ $barang->kategori?->nama_kategori ?? '-' }}</td>
                                    <td class="py-3.5 px-5 font-bold {{ $isCritical ? 'text-rose-700' : 'text-slate-900' }}">{{ number_format($barang->stok, 0, ',', '.') }} {{ $barang->satuan }}</td>
                                    <td class="py-3.5 px-5 text-slate-500">{{ number_format($barang->stok_minimum, 0, ',', '.') }} {{ $barang->satuan }}</td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-flex items-center gap-1.5 {{ $isCritical ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200' }} border px-2.5 py-0.5 rounded-md text-[11px] font-bold">
                                            {{ $isCritical ? 'Habis (0)' : 'Menipis' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <div class="relative inline-block text-right">
                                            <button type="button" onclick="toggleActionMenu(event, 'action-dropdown-dash-{{ $barang->id }}')" class="inline-flex items-center justify-center p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Menu Aksi">
                                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                            </button>

                                            <!-- DROPDOWN MENU -->
                                            <div id="action-dropdown-dash-{{ $barang->id }}" class="action-dropdown hidden absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-left animate-fadeIn">
                                                <button type="button" onclick="showBarcodeModal({{ json_encode($barang) }}); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                    <i data-lucide="qr-code" class="w-3.5 h-3.5 text-indigo-600"></i>
                                                    <span>Barcode Kotak (QR)</span>
                                                </button>
                                                <button type="button" onclick="openStokMasukModalForBarang('{{ $barang->id }}'); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                    <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 text-emerald-600"></i>
                                                    <span>Catat Stok Masuk</span>
                                                </button>
                                                @if ((float) $barang->stok > 0)
                                                <button type="button" onclick="openStokKeluarModalForBarang('{{ $barang->id }}'); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-rose-600"></i>
                                                    <span>Catat Stok Keluar</span>
                                                </button>
                                                @endif
                                                
                                                @if ($isPemilik)
                                                    <div class="border-t border-slate-100 my-1"></div>
                                                    <button type="button" onclick="openEditBarangModal({{ json_encode($barang) }}); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-600"></i>
                                                        <span>Edit Data Barang</span>
                                                    </button>
                                                    <button type="button" onclick="openHapusBarangModal('{{ $barang->id }}', '{{ addslashes($barang->nama_barang) }}', '{{ $barang->kode_barang }}'); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                        <span>Hapus Barang</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 px-5 text-center text-xs text-slate-500">Semua stok barang berada dalam kondisi aman (di atas batas minimum).</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- 2. DATA BARANG VIEW -->
        <!-- ========================================== -->
        <div id="view-data-barang" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Katalog Data Barang</h1>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">
                        @if ($isPemilik)
                            Kelola seluruh master data persediaan alat kebersihan (tambah, edit, hapus, dan stok).
                        @else
                            Daftar ketersediaan seluruh alat dan produk kebersihan di gudang.
                        @endif
                    </p>
                </div>
                @if ($isPemilik)
                    <button onclick="openTambahBarangModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition">
                        <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                        <span>Tambah Barang Baru</span>
                    </button>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-slate-100 border border-slate-200 text-slate-600 px-3 py-1.5 rounded-xl text-xs font-semibold">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                        Mode Petugas: Hanya Lihat Master
                    </span>
                @endif
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </div>
                    <input type="text" id="searchDataBarang" onkeyup="filterDataBarangTable()" placeholder="Cari kode atau nama barang..." 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400">
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total: <strong class="text-slate-900">{{ $barangs->count() }}</strong> barang terdaftar
                </div>
            </div>
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs">
                <div class="overflow-x-auto overflow-y-visible">
                    <table class="w-full text-left border-collapse text-xs" id="tableDataBarang">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">KODE</th>
                                <th class="py-3.5 px-4">NAMA BARANG</th>
                                <th class="py-3.5 px-4">KATEGORI</th>
                                <th class="py-3.5 px-4">SUPPLIER</th>
                                <th class="py-3.5 px-4">HARGA SATUAN</th>
                                <th class="py-3.5 px-4">STOK SAAT INI</th>
                                <th class="py-3.5 px-4">MINIMUM</th>
                                <th class="py-3.5 px-4">STATUS</th>
                                <th class="py-3.5 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="tbodyDataBarang">
                            @forelse ($barangs as $barang)
                                @php($isLow = (float) $barang->stok <= (float) $barang->stok_minimum)
                                <tr class="hover:bg-slate-50/80 transition relative">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="showBarcodeModal({{ json_encode($barang) }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-900 hover:text-white border border-slate-200 text-slate-700 transition shadow-2xs group flex items-center justify-center shrink-0" title="Buka Barcode Kotak (QR Code)">
                                                <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <span class="font-bold text-slate-900 font-mono">{{ $barang->kode_barang }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $barang->nama_barang }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $barang->kategori?->nama_kategori ?? '-' }}</td>
                                    <td class="py-3.5 px-4">
                                        @if ($barang->supplier)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200">
                                                <i data-lucide="truck" class="w-3 h-3 text-slate-500"></i>
                                                {{ $barang->supplier->nama_supplier }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ number_format($barang->stok, 0, ',', '.') }} {{ $barang->satuan }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ number_format($barang->stok_minimum, 0, ',', '.') }} {{ $barang->satuan }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block {{ $isLow ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }} border px-2.5 py-0.5 rounded-md text-[11px] font-bold">
                                            {{ $isLow ? 'Stok Menipis' : 'Aman' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <!-- MENU AKSI TITIK TIGA (THREE-DOTS ACTION) -->
                                        <div class="relative inline-block text-right">
                                            <button type="button" onclick="toggleActionMenu(event, 'action-dropdown-{{ $barang->id }}')" class="inline-flex items-center justify-center p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Menu Aksi">
                                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                            </button>

                                            <!-- DROPDOWN MENU -->
                                            <div id="action-dropdown-{{ $barang->id }}" class="action-dropdown hidden absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-left animate-fadeIn">
                                                <button type="button" onclick="showBarcodeModal({{ json_encode($barang) }}); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                    <i data-lucide="qr-code" class="w-3.5 h-3.5 text-indigo-600"></i>
                                                    <span>Barcode Kotak (QR)</span>
                                                </button>
                                                <button type="button" onclick="openStokMasukModalForBarang('{{ $barang->id }}'); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                    <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 text-emerald-600"></i>
                                                    <span>Catat Stok Masuk</span>
                                                </button>
                                                @if ((float) $barang->stok > 0)
                                                <button type="button" onclick="openStokKeluarModalForBarang('{{ $barang->id }}'); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-rose-600"></i>
                                                    <span>Catat Stok Keluar</span>
                                                </button>
                                                @endif
                                                
                                                @if ($isPemilik)
                                                    <div class="border-t border-slate-100 my-1"></div>
                                                    <button type="button" onclick="openEditBarangModal({{ json_encode($barang) }}); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-600"></i>
                                                        <span>Edit Data Barang</span>
                                                    </button>
                                                    <button type="button" onclick="openHapusBarangModal('{{ $barang->id }}', '{{ addslashes($barang->nama_barang) }}', '{{ $barang->kode_barang }}'); closeAllActionMenus();" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                        <span>Hapus Barang</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 text-center text-xs text-slate-500">Belum ada data barang di database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 3. KATEGORI VIEW -->
        <!-- ========================================== -->
        <div id="view-kategori" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Kategori Barang</h1>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Klasifikasi persediaan alat kebersihan untuk mempermudah pencatatan.</p>
                </div>
                @if ($isPemilik)
                    <button onclick="openTambahKategoriModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition">
                        <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                        <span>Tambah Kategori Baru</span>
                    </button>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Kategori Aktif</h2>
                    <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-md border border-slate-200">
                        {{ $kategoris->count() }} Kategori
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($kategoris as $kategori)
                        <div class="flex items-start justify-between gap-4 p-5 hover:bg-slate-50 transition">
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="tag" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">{{ $kategori->nama_kategori }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $kategori->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 shrink-0">
                                {{ $kategori->barangs_count }} barang
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-500">Belum ada kategori terdaftar.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SUPPLIER VIEW -->
        <!-- ========================================== -->
        <div id="view-supplier" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Data Supplier &amp; Vendor</h1>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">
                        @if ($isPemilik)
                            Kelola rekanan pemasok persediaan perlengkapan dan alat sanitasi gudang (tambah, edit, dan hapus).
                        @else
                            Daftar mitra supplier dan kontak penanggung jawab perlengkapan kebersihan gudang.
                        @endif
                    </p>
                </div>
                @if ($isPemilik)
                    <button onclick="openTambahSupplierModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-xs">
                        <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                        <span>Tambah Supplier Baru</span>
                    </button>
                @endif
            </div>

            <!-- Search & Stats Toolbar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </div>
                    <input type="text" id="searchDataSupplier" onkeyup="filterDataSupplierCards()" placeholder="Cari nama supplier, kontak, alamat..." 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400">
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total: <strong class="text-slate-900">{{ $suppliers->count() }}</strong> supplier terdaftar
                </div>
            </div>

            <!-- Supplier Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="supplierCardsContainer">
                @forelse ($suppliers as $sup)
                    <div class="supplier-card bg-white rounded-2xl border border-slate-200 shadow-xs p-5 hover:border-slate-300 hover:shadow-md transition-all flex flex-col justify-between"
                        data-search="{{ strtolower($sup->nama_supplier . ' ' . ($sup->kontak_person ?? '') . ' ' . ($sup->telepon ?? '') . ' ' . ($sup->email ?? '') . ' ' . ($sup->alamat ?? '')) }}">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0">
                                        <i data-lucide="truck" class="w-5 h-5 text-amber-400"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900 leading-tight">{{ $sup->nama_supplier }}</h3>
                                        @if ($sup->kontak_person)
                                            <p class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                                <i data-lucide="user" class="w-3 h-3 text-slate-400"></i>
                                                {{ $sup->kontak_person }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg shrink-0">
                                    {{ $sup->barangs_count }} Barang
                                </span>
                            </div>

                            @if ($sup->deskripsi)
                                <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 mb-3 line-clamp-2">
                                    {{ $sup->deskripsi }}
                                </p>
                            @endif

                            <div class="space-y-2 text-xs text-slate-600 pt-1 border-t border-slate-100">
                                @if ($sup->telepon)
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                            <i data-lucide="phone" class="w-3 h-3"></i>
                                        </div>
                                        <a href="tel:{{ $sup->telepon }}" class="text-slate-800 font-medium hover:text-slate-900 hover:underline">
                                            {{ $sup->telepon }}
                                        </a>
                                    </div>
                                @endif

                                @if ($sup->email)
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                            <i data-lucide="mail" class="w-3 h-3"></i>
                                        </div>
                                        <a href="mailto:{{ $sup->email }}" class="text-slate-800 font-medium hover:text-slate-900 hover:underline truncate">
                                            {{ $sup->email }}
                                        </a>
                                    </div>
                                @endif

                                @if ($sup->alamat)
                                    <div class="flex items-start gap-2">
                                        <div class="w-5 h-5 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 mt-0.5">
                                            <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        </div>
                                        <span class="text-slate-600 line-clamp-2">{{ $sup->alamat }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($isPemilik)
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                                <button type="button" onclick="openEditSupplierModal({{ json_encode($sup) }})" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-500"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" onclick="openHapusSupplierModal('{{ $sup->id }}', '{{ addslashes($sup->nama_supplier) }}')" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-rose-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-500"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="truck" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Belum Ada Supplier Terdaftar</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tambahkan data supplier rekanan untuk mengelola sumber barang dan riwayat pasokan stok masuk.</p>
                        @if ($isPemilik)
                            <button onclick="openTambahSupplierModal()" class="mt-4 inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Tambah Supplier Sekarang</span>
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>
            
            <div id="noSupplierSearchResult" class="hidden bg-white rounded-2xl border border-slate-200 p-8 text-center text-xs text-slate-500">
                Tidak ada data supplier yang sesuai dengan kata kunci pencarian.
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 4. STOK MASUK VIEW -->
        <!-- ========================================== -->
        <div id="view-stok-masuk" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Riwayat Stok Masuk</h1>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Log pencatatan penambahan persediaan barang ke gudang.</p>
                </div>
                <button onclick="openStokMasukModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition">
                    <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                    <span>Catat Stok Masuk</span>
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">TANGGAL &amp; WAKTU</th>
                                <th class="py-3.5 px-4">KODE</th>
                                <th class="py-3.5 px-4">NAMA BARANG</th>
                                <th class="py-3.5 px-4">KATEGORI</th>
                                <th class="py-3.5 px-4">SUPPLIER</th>
                                <th class="py-3.5 px-4 text-center">JUMLAH MASUK</th>
                                <th class="py-3.5 px-4">PERUBAHAN STOK</th>
                                <th class="py-3.5 px-4">PETUGAS</th>
                                <th class="py-3.5 px-4">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="tbody-stok-masuk">
                            @forelse ($transaksiMasuk as $t)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ $t->barang?->kode_barang ?? '-' }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $t->barang?->nama_barang ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->barang?->kategori?->nama_kategori ?? '-' }}</td>
                                    <td class="py-3.5 px-4">
                                        @if ($t->supplier)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200">
                                                <i data-lucide="truck" class="w-3 h-3 text-slate-500"></i>
                                                {{ $t->supplier->nama_supplier }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-emerald-700">+{{ number_format($t->jumlah, 0, ',', '.') }} {{ $t->barang?->satuan }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ number_format($t->stok_sebelum, 0, ',', '.') }} &rarr; <strong class="text-slate-900">{{ number_format($t->stok_sesudah, 0, ',', '.') }}</strong></td>
                                    <td class="py-3.5 px-4 text-slate-800 font-semibold">{{ $t->user?->name ?? 'Petugas' }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 text-center text-xs text-slate-500">Belum ada data transaksi stok masuk di database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION BAR STOK MASUK -->
                <div id="pagination-stok-masuk" class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="text-slate-500 text-[11px] font-medium" id="info-pagination-stok-masuk">
                        Menampilkan <strong class="text-slate-900">0</strong> data
                    </div>
                    <div class="flex items-center gap-1.5" id="controls-pagination-stok-masuk"></div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 5. STOK KELUAR VIEW -->
        <!-- ========================================== -->
        <div id="view-stok-keluar" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Riwayat Stok Keluar</h1>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Log pencatatan penggunaan dan pengeluaran barang dari gudang.</p>
                </div>
                <button onclick="openStokKeluarModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition">
                    <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                    <span>Catat Stok Keluar</span>
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">TANGGAL &amp; WAKTU</th>
                                <th class="py-3.5 px-4">KODE</th>
                                <th class="py-3.5 px-4">NAMA BARANG</th>
                                <th class="py-3.5 px-4">KATEGORI</th>
                                <th class="py-3.5 px-4 text-center">JUMLAH KELUAR</th>
                                <th class="py-3.5 px-4">PERUBAHAN STOK</th>
                                <th class="py-3.5 px-4">PETUGAS</th>
                                <th class="py-3.5 px-4">TUJUAN / PENGGUNAAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="tbody-stok-keluar">
                            @forelse ($transaksiKeluar as $t)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ $t->barang?->kode_barang ?? '-' }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $t->barang?->nama_barang ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->barang?->kategori?->nama_kategori ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-center font-bold text-rose-700">-{{ number_format($t->jumlah, 0, ',', '.') }} {{ $t->barang?->satuan }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ number_format($t->stok_sebelum, 0, ',', '.') }} &rarr; <strong class="text-slate-900">{{ number_format($t->stok_sesudah, 0, ',', '.') }}</strong></td>
                                    <td class="py-3.5 px-4 text-slate-800 font-semibold">{{ $t->user?->name ?? 'Petugas' }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-xs text-slate-500">Belum ada data transaksi stok keluar di database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION BAR STOK KELUAR -->
                <div id="pagination-stok-keluar" class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="text-slate-500 text-[11px] font-medium" id="info-pagination-stok-keluar">
                        Menampilkan <strong class="text-slate-900">0</strong> data
                    </div>
                    <div class="flex items-center gap-1.5" id="controls-pagination-stok-keluar"></div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 6. RIWAYAT TRANSAKSI VIEW -->
        <!-- ========================================== -->
        <div id="view-riwayat-transaksi" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Riwayat Seluruh Transaksi</h1>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Semua catatan keluar-masuk stok barang secara kronologis.</p>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">WAKTU</th>
                                <th class="py-3.5 px-4">JENIS</th>
                                <th class="py-3.5 px-4">KODE &amp; BARANG</th>
                                <th class="py-3.5 px-4 text-center">JUMLAH</th>
                                <th class="py-3.5 px-4">SISA STOK</th>
                                <th class="py-3.5 px-4">PETUGAS</th>
                                <th class="py-3.5 px-4">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="tbody-riwayat-transaksi">
                            @forelse ($riwayatTransaksi as $t)
                                @php($isMasuk = $t->jenis_transaksi === 'masuk')
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1 {{ $isMasuk ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }} border px-2 py-0.5 rounded-md text-[11px] font-bold">
                                            {{ $isMasuk ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-slate-900">{{ $t->barang?->kode_barang ?? '-' }}</span>
                                        <span class="text-slate-500"> - {{ $t->barang?->nama_barang ?? '-' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold {{ $isMasuk ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $isMasuk ? '+' : '-' }}{{ number_format($t->jumlah, 0, ',', '.') }} {{ $t->barang?->satuan }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ number_format($t->stok_sesudah, 0, ',', '.') }} {{ $t->barang?->satuan }}</td>
                                    <td class="py-3.5 px-4 text-slate-800 font-semibold">{{ $t->user?->name ?? 'Petugas' }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $t->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-xs text-slate-500">Belum ada transaksi di database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION BAR RIWAYAT TRANSAKSI -->
                <div id="pagination-riwayat-transaksi" class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="text-slate-500 text-[11px] font-medium" id="info-pagination-riwayat-transaksi">
                        Menampilkan <strong class="text-slate-900">0</strong> data
                    </div>
                    <div class="flex items-center gap-1.5" id="controls-pagination-riwayat-transaksi"></div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 7. PENGGUNA VIEW (Hanya Pemilik) -->
        <!-- ========================================== -->
        @if ($isPemilik)
            <div id="view-pengguna" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Manajemen Pengguna</h1>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Kelola akun dan hak akses pengguna sistem StokClean.</p>
                    </div>
                    <button onclick="openTambahPenggunaModal()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Tambah Pengguna</span>
                    </button>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Daftar Akun Pengguna</h2>
                        <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-md border border-slate-200">
                            {{ $users->count() }} Pengguna
                        </span>
                    </div>
                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse ($users as $user)
                            <div class="flex items-center justify-between gap-4 p-5 hover:bg-slate-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 truncate">{{ $user->name }}</p>
                                        <p class="text-slate-500 truncate" title="{{ $user->masked_email }}">{{ $user->masked_email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span class="shrink-0 rounded-md bg-slate-100 border border-slate-200 px-3 py-1 font-bold uppercase text-slate-700 text-[11px]">
                                        {{ $user->role === 'pemilik' ? 'Pemilik (Admin)' : 'Pegawai (Staf)' }}
                                    </span>
                                    @if ($user->role !== 'pemilik')
                                        <button 
                                            type="button" 
                                            onclick='openEditPenggunaModal(@json($user))' 
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition shadow-2xs"
                                            title="Edit Akun Pengguna"
                                        >
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-500"></i>
                                            <span>Edit</span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-slate-400 bg-slate-50 rounded-lg border border-slate-200/60" title="Akun Pemilik Utama dilindungi dan tidak dapat diedit">
                                            <i data-lucide="lock" class="w-3 h-3 text-slate-400"></i>
                                            <span>Terkunci</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="p-6 text-center text-slate-500">Belum ada pengguna terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- 8. LAPORAN VIEW -->
        <!-- ========================================== -->
        <div id="view-laporan" class="tab-view hidden w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Laporan Inventaris</h1>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Ringkasan status persediaan dan ekspor data ke format CSV.</p>
                </div>
                <a href="{{ route('laporan.export') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition shadow-xs">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh Laporan CSV</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <p class="text-[11px] font-bold uppercase text-slate-500">TOTAL JENIS BARANG</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($totalBarang, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <p class="text-[11px] font-bold uppercase text-slate-500">TOTAL STOK FISIK</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($totalStok, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <p class="text-[11px] font-bold uppercase text-slate-500">STOK MASUK BULAN INI</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stokMasukBulanIni, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                    <p class="text-[11px] font-bold uppercase text-slate-500">STOK KELUAR BULAN INI</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stokKeluarBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Detailed Report Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-6 animate-fadeIn" style="animation-delay: 150ms;">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h2 class="text-sm font-bold text-slate-900">Ringkasan Estimasi Nilai Aset Inventaris</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-5">Kode Barang</th>
                                <th class="py-3.5 px-5">Nama Barang</th>
                                <th class="py-3.5 px-5 text-right">Harga Satuan</th>
                                <th class="py-3.5 px-5 text-right">Sisa Stok</th>
                                <th class="py-3.5 px-5 text-right">Total Nilai Aset</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse ($barangs as $barang)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-5 font-bold text-slate-900">{{ $barang->kode_barang }}</td>
                                    <td class="py-3.5 px-5 text-slate-900 font-semibold">{{ $barang->nama_barang }}</td>
                                    <td class="py-3.5 px-5 text-right text-slate-500">Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-5 text-right font-bold text-slate-900">{{ number_format($barang->stok, 0, ',', '.') }} <span class="font-medium text-slate-500">{{ $barang->satuan }}</span></td>
                                    <td class="py-3.5 px-5 text-right text-emerald-600 font-bold">Rp {{ number_format($barang->harga * $barang->stok, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 px-5 text-center text-xs text-slate-500">Tidak ada data barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200 font-bold text-slate-900">
                            <tr>
                                <td colspan="4" class="py-4 px-5 text-right text-sm">Total Estimasi Nilai Seluruh Aset:</td>
                                <td class="py-4 px-5 text-right text-emerald-700 text-base">
                                    Rp {{ number_format($barangs->sum(function($b) { return (float)$b->harga * (float)$b->stok; }), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- ========================================== -->
    <!-- MODALS SECTION -->
    <!-- ========================================== -->

    @if ($isPemilik)
        <!-- MODAL: TAMBAH BARANG (Hanya Pemilik) -->
        <div id="tambahBarangModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Tambah Data Barang Baru</h3>
                    <button onclick="closeTambahBarangModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form method="POST" action="{{ route('barang.store') }}" class="p-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Barang (SKU)</label>
                            <input type="text" name="kode_barang" required placeholder="Contoh: BRG011" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                            <select name="kategori_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" required placeholder="Contoh: Pembersih Lantai 1 Liter" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan</label>
                            <input type="text" name="satuan" required placeholder="pcs, kg, botol" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan (Rp)</label>
                            <input type="number" name="harga" required min="0" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Stok Minimum</label>
                            <input type="number" name="stok_minimum" required min="0" value="5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Supplier Utama (Opsional)</label>
                        <select name="supplier_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <option value="">-- Pilih Supplier Rekanan (Opsional) --</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Stok Awal Fisik (Opsional)</label>
                        <input type="number" name="stok" min="0" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeTambahBarangModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: EDIT BARANG (Hanya Pemilik) -->
        <div id="editBarangModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Edit Data Barang</h3>
                    <button onclick="closeEditBarangModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form id="editBarangForm" method="POST" action="" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Barang (SKU)</label>
                            <input type="text" id="edit_kode_barang" name="kode_barang" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                            <select id="edit_kategori_id" name="kategori_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang</label>
                        <input type="text" id="edit_nama_barang" name="nama_barang" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan</label>
                            <input type="text" id="edit_satuan" name="satuan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan (Rp)</label>
                            <input type="number" id="edit_harga" name="harga" required min="0" step="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Stok Minimum</label>
                            <input type="number" id="edit_stok_minimum" name="stok_minimum" required min="0" step="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Supplier Utama (Opsional)</label>
                        <select id="edit_supplier_id" name="supplier_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <option value="">-- Pilih Supplier Rekanan (Opsional) --</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeEditBarangModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: KONFIRMASI HAPUS BARANG (Hanya Pemilik) -->
        <div id="hapusBarangModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Konfirmasi Hapus Barang</h3>
                    </div>
                    <button onclick="closeHapusBarangModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form id="hapusBarangForm" method="POST" action="" class="p-5 space-y-4">
                    @csrf
                    @method('DELETE')
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Apakah Anda yakin ingin menghapus barang <strong id="hapus_barang_nama" class="text-slate-900"></strong> (<span id="hapus_barang_kode" class="font-mono text-slate-700"></span>)?
                    </p>
                    <p class="text-[11px] text-rose-600 bg-rose-50 p-3 rounded-xl border border-rose-200/60 leading-normal">
                        Perhatian: Seluruh riwayat transaksi yang terkait dengan barang ini akan ikut dihapus permanen.
                    </p>
                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeHapusBarangModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition">Ya, Hapus Barang</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: TAMBAH KATEGORI (Hanya Pemilik) -->
        <div id="tambahKategoriModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Tambah Kategori Baru</h3>
                    <button onclick="closeTambahKategoriModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form method="POST" action="{{ route('kategori.store') }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori</label>
                        <input type="text" name="nama_kategori" required placeholder="Contoh: Disinfektan & Sanitizer" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="3" placeholder="Keterangan singkat tentang kategori ini..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
                    </div>
                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeTambahKategoriModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: TAMBAH PENGGUNA (Hanya Pemilik) -->
        <div id="tambahPenggunaModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Tambah Pengguna Baru</h3>
                    <button onclick="closeTambahPenggunaModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form method="POST" action="{{ route('users.store') }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input name="name" required placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input name="email" type="email" required placeholder="budi@stokclean.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Peran / Role</label>
                        <select name="role" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <option value="pegawai">Pegawai (Staf Gudang)</option>
                            <option value="pemilik">Pemilik (Admin)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi (Min. 8 karakter)</label>
                        <input name="password" type="password" required minlength="8" placeholder="••••••••" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeTambahPenggunaModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 px-4 py-2 text-xs font-bold text-white transition">Tambah Pengguna</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: EDIT PENGGUNA (Hanya Pemilik) -->
        <div id="editPenggunaModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Edit Akun Pengguna</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Perbarui informasi akun pegawai / staf gudang</p>
                    </div>
                    <button type="button" onclick="closeEditPenggunaModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form id="editPenggunaForm" method="POST" action="" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input id="edit_user_name" name="name" required placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input id="edit_user_email" name="email" type="email" required placeholder="budi@gmail.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon (Opsional)</label>
                        <input id="edit_user_telepon" name="nomor_telepon" placeholder="Contoh: 08123456789" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Peran / Role</label>
                        <div class="px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-100 text-xs text-slate-600 font-semibold flex items-center justify-between">
                            <span>Pegawai (Staf Gudang)</span>
                            <span class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-bold uppercase">Tetap</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru (Opsional)</label>
                        <input id="edit_user_password" name="password" type="password" minlength="8" placeholder="Kosongkan jika tidak ingin mengubah sandi" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <p class="mt-1 text-[11px] text-slate-400">Minimal 8 karakter jika ingin mereset kata sandi.</p>
                    </div>
                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeEditPenggunaModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 px-4 py-2 text-xs font-bold text-white transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: TAMBAH SUPPLIER (Hanya Pemilik) -->
        <div id="tambahSupplierModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center">
                            <i data-lucide="truck" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Tambah Data Supplier Baru</h3>
                    </div>
                    <button onclick="closeTambahSupplierModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form method="POST" action="{{ route('supplier.store') }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / Supplier / Toko <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_supplier" required placeholder="Contoh: PT Clean Sentosa Abadi" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kontak Person (PIC)</label>
                            <input type="text" name="kontak_person" placeholder="Contoh: Bpk. Hendra Wijaya" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" name="telepon" placeholder="Contoh: 081234567890" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" placeholder="Contoh: sales@cleansentosa.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                        <textarea name="alamat" rows="2" placeholder="Contoh: Kawasan Industri Jababeka Blok B-12, Cikarang" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Catatan Barang Pasokan</label>
                        <textarea name="deskripsi" rows="2" placeholder="Contoh: Distributor resmi produk pembersih lantai & disinfektan medis" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeTambahSupplierModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Simpan Supplier</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: EDIT SUPPLIER (Hanya Pemilik) -->
        <div id="editSupplierModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Edit Data Supplier</h3>
                    </div>
                    <button onclick="closeEditSupplierModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form id="editSupplierForm" method="POST" action="" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / Supplier / Toko <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit_supplier_nama" name="nama_supplier" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kontak Person (PIC)</label>
                            <input type="text" id="edit_supplier_kontak" name="kontak_person" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" id="edit_supplier_telepon" name="telepon" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" id="edit_supplier_email" name="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                        <textarea id="edit_supplier_alamat" name="alamat" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Catatan Barang Pasokan</label>
                        <textarea id="edit_supplier_deskripsi" name="deskripsi" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeEditSupplierModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: HAPUS SUPPLIER (Hanya Pemilik) -->
        <div id="hapusSupplierModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Hapus Supplier</h3>
                    </div>
                    <button onclick="closeHapusSupplierModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <form id="hapusSupplierForm" method="POST" action="" class="p-5 space-y-4">
                    @csrf
                    @method('DELETE')
                    <div class="text-xs text-slate-600 space-y-2">
                        <p>Apakah Anda yakin ingin menghapus data supplier <strong id="hapus_supplier_nama" class="text-slate-900"></strong>?</p>
                        <p class="text-amber-700 bg-amber-50 p-2.5 rounded-xl border border-amber-200 leading-normal">
                            Barang yang sebelumnya ditautkan ke supplier ini tidak akan terhapus, namun relasi supplier pada barang terkait akan dikosongkan.
                        </p>
                    </div>
                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeHapusSupplierModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition">Hapus Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: CATAT STOK MASUK (Pemilik & Pegawai) -->
    <div id="stokMasukModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center">
                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Catat Penambahan Stok Masuk</h3>
                </div>
                <button onclick="closeStokMasukModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" action="{{ route('stok-masuk.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang</label>
                    <select name="barang_id" id="stokMasukBarangSelect" required onchange="onStokMasukBarangChange(this.value)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangs as $b)
                            <option value="{{ $b->id }}" 
                                data-supplier-id="{{ $b->supplier_id ?? '' }}"
                                data-supplier-name="{{ $b->supplier?->nama_supplier ?? '' }}"
                                data-supplier-pic="{{ $b->supplier?->kontak_person ?? '' }}"
                                data-supplier-phone="{{ $b->supplier?->telepon ?? '' }}">
                                {{ $b->kode_barang }} - {{ $b->nama_barang }} (Sisa: {{ number_format($b->stok, 0, ',', '.') }} {{ $b->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- KOTAK INFORMASI SUPPLIER OTOMATIS -->
                <div id="stokMasukSupplierAutoBox" class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                            <i data-lucide="truck" class="w-3.5 h-3.5 text-slate-500"></i>
                            Informasi Supplier Rekanan
                        </span>
                        <span id="stokMasukSupplierBadge" class="text-[10px] font-bold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-md transition-colors">
                            Pilih barang terlebih dahulu
                        </span>
                    </div>

                    <!-- Tampilan Identitas Supplier -->
                    <div class="flex items-center gap-3 p-2.5 bg-white rounded-xl border border-slate-200/80 shadow-2xs">
                        <div id="stokMasukSupplierIconWrapper" class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                            <i data-lucide="truck" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p id="stokMasukSupplierNameText" class="text-xs font-bold text-slate-800 truncate">
                                Belum memilih barang
                            </p>
                            <p id="stokMasukSupplierDetailText" class="text-[11px] text-slate-500 truncate">
                                Supplier otomatis tampil saat barang dipilih
                            </p>
                        </div>
                    </div>

                    <!-- Pilihan Ganti Supplier / Dropdown -->
                    <div>
                        <label class="block text-[11px] font-medium text-slate-500 mb-1">
                            Supplier Terpilih (dapat diganti jika pasokan dari vendor lain):
                        </label>
                        <select name="supplier_id" id="stokMasukSupplierSelect" onchange="onManualSupplierSelectChange(this.value)" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <option value="">-- Tanpa Supplier / Kosongkan --</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}" 
                                    data-name="{{ $sup->nama_supplier }}" 
                                    data-pic="{{ $sup->kontak_person ?? '' }}" 
                                    data-phone="{{ $sup->telepon ?? '' }}">
                                    {{ $sup->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Masuk</label>
                    <input type="number" step="any" name="jumlah" required min="1" max="1000" placeholder="Maks. 1000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Faktur</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Pembelian rutin supplier / No Faktur: INV-0921" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                </div>
                <div class="pt-2 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeStokMasukModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Simpan Stok Masuk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: CATAT STOK KELUAR (Pemilik & Pegawai) -->
    <div id="stokKeluarModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Catat Pengeluaran Stok</h3>
                </div>
                <button onclick="closeStokKeluarModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" action="{{ route('stok-keluar.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang</label>
                    <select name="barang_id" id="stokKeluarBarangSelect" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangs as $b)
                            <option value="{{ $b->id }}">{{ $b->kode_barang }} - {{ $b->nama_barang }} (Tersedia: {{ number_format($b->stok, 0, ',', '.') }} {{ $b->satuan }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Keluar</label>
                    <input type="number" step="any" name="jumlah" required min="1" max="1000" placeholder="Maks. 1000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tujuan / Keperluan Penggunaan</label>
                    <input type="text" name="keterangan" required placeholder="Contoh: Operasional pembersihan lantai 1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                </div>
                <div class="pt-2 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeStokKeluarModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
            initStockChart();
            initAllPaginations();

            // Hash based initial tab
            const hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById('view-' + hash)) {
                switchTab(hash);
            }

            // Close action dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.action-dropdown') && !e.target.closest('button[onclick*="toggleActionMenu"]')) {
                    closeAllActionMenus();
                }
            });
        });

        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }

        function switchTab(tabId, e) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            window.location.hash = tabId;
            document.querySelectorAll('.tab-view').forEach(view => view.classList.add('hidden'));
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active-menu');
                item.classList.add('inactive-menu');
            });

            const selectedView = document.getElementById('view-' + tabId);
            const selectedNav = document.getElementById('nav-' + tabId);
            
            if (selectedView) {
                selectedView.classList.remove('hidden');
            }
            if (selectedNav) {
                selectedNav.classList.add('active-menu');
                selectedNav.classList.remove('inactive-menu');
            }

            if (['stok-masuk', 'stok-keluar', 'riwayat-transaksi'].includes(tabId)) {
                initAllPaginations();
            }

            if (window.innerWidth < 768 && sidebar) {
                sidebar.classList.add('hidden');
            }
        }

        // ==========================================
        // SISTEM PAGINASI TABEL (9 ITEM PER HALAMAN)
        // ==========================================
        function setupTablePagination(config) {
            const tbody = document.getElementById(config.tbodyId);
            const controls = document.getElementById(config.controlsId);
            const info = document.getElementById(config.infoId);

            if (!tbody || !controls || !info) return;

            // Ambil hanya baris data (bukan baris pesan kosong bertanda colspan)
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
            const totalRows = rows.length;
            const pageSize = config.pageSize || 9;
            const totalPages = Math.ceil(totalRows / pageSize) || 1;
            let currentPage = 1;

            function renderPage(page) {
                if (page < 1) page = 1;
                if (page > totalPages) page = totalPages;
                currentPage = page;

                const startIdx = (currentPage - 1) * pageSize;
                const endIdx = startIdx + pageSize;

                // Tampilkan hanya baris pada halaman aktif
                rows.forEach((row, idx) => {
                    if (idx >= startIdx && idx < endIdx) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Teks informasi data yang sedang ditampilkan
                if (totalRows === 0) {
                    info.innerHTML = 'Belum ada catatan data transaksi.';
                } else {
                    const displayStart = startIdx + 1;
                    const displayEnd = Math.min(endIdx, totalRows);
                    info.innerHTML = `Menampilkan <strong class="text-slate-900 font-bold">${displayStart}</strong> - <strong class="text-slate-900 font-bold">${displayEnd}</strong> dari <strong class="text-slate-900 font-bold">${totalRows}</strong> transaksi (Hal. ${currentPage} dari ${totalPages})`;
                }

                // Render tombol navigasi halaman
                controls.innerHTML = '';

                // Jika data <= 9 (belum melebihi 9), tampilkan indikator halaman 1 tunggal
                if (totalPages <= 1) {
                    if (totalRows > 0) {
                        const singleBtn = document.createElement('span');
                        singleBtn.className = 'w-7 h-7 rounded-lg bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-2xs';
                        singleBtn.textContent = '1';
                        controls.appendChild(singleBtn);
                    }
                    return;
                }

                // Tombol Sebelumnya (Prev)
                const prevBtn = document.createElement('button');
                prevBtn.type = 'button';
                prevBtn.innerHTML = '<i data-lucide="chevron-left" class="w-3.5 h-3.5"></i><span>Sebelumnya</span>';
                prevBtn.className = `inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border text-xs font-semibold transition ${
                    currentPage === 1 
                        ? 'bg-slate-50 border-slate-200 text-slate-300 cursor-not-allowed' 
                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-slate-900 shadow-2xs'
                }`;
                prevBtn.disabled = (currentPage === 1);
                prevBtn.onclick = () => { if (currentPage > 1) renderPage(currentPage - 1); };
                controls.appendChild(prevBtn);

                // Tombol Angka Halaman (1, 2, 3, ...)
                for (let i = 1; i <= totalPages; i++) {
                    // Penanganan jika halaman sangat banyak (> 7 halaman)
                    if (totalPages > 7) {
                        if (i !== 1 && i !== totalPages && Math.abs(i - currentPage) > 1) {
                            if (i === 2 || i === totalPages - 1) {
                                const dots = document.createElement('span');
                                dots.className = 'px-1 text-xs text-slate-400 font-bold select-none';
                                dots.textContent = '...';
                                controls.appendChild(dots);
                            }
                            continue;
                        }
                    }

                    const pageBtn = document.createElement('button');
                    pageBtn.type = 'button';
                    pageBtn.textContent = i;
                    if (i === currentPage) {
                        pageBtn.className = 'w-7 h-7 rounded-lg bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-xs';
                    } else {
                        pageBtn.className = 'w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs flex items-center justify-center transition shadow-2xs';
                    }
                    pageBtn.onclick = () => renderPage(i);
                    controls.appendChild(pageBtn);
                }

                // Tombol Selanjutnya (Next)
                const nextBtn = document.createElement('button');
                nextBtn.type = 'button';
                nextBtn.innerHTML = '<span>Selanjutnya</span><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>';
                nextBtn.className = `inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border text-xs font-semibold transition ${
                    currentPage === totalPages 
                        ? 'bg-slate-50 border-slate-200 text-slate-300 cursor-not-allowed' 
                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-slate-900 shadow-2xs'
                }`;
                nextBtn.disabled = (currentPage === totalPages);
                nextBtn.onclick = () => { if (currentPage < totalPages) renderPage(currentPage + 1); };
                controls.appendChild(nextBtn);

                if (window.lucide) window.lucide.createIcons();
            }

            renderPage(1);
        }

        function initAllPaginations() {
            setupTablePagination({
                tbodyId: 'tbody-stok-masuk',
                controlsId: 'controls-pagination-stok-masuk',
                infoId: 'info-pagination-stok-masuk',
                pageSize: 9
            });

            setupTablePagination({
                tbodyId: 'tbody-stok-keluar',
                controlsId: 'controls-pagination-stok-keluar',
                infoId: 'info-pagination-stok-keluar',
                pageSize: 9
            });

            setupTablePagination({
                tbodyId: 'tbody-riwayat-transaksi',
                controlsId: 'controls-pagination-riwayat-transaksi',
                infoId: 'info-pagination-riwayat-transaksi',
                pageSize: 9
            });
        }

        // Global Search Input
        const globalSearchInput = document.getElementById('globalSearchInput');
        if (globalSearchInput) {
            globalSearchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                const activeView = document.querySelector('.tab-view:not(.hidden)');
                if (activeView) {
                    const rows = activeView.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(query) ? '' : 'none';
                    });
                }
            });
        }

        // Filter Data Barang Table
        function filterDataBarangTable() {
            const query = document.getElementById('searchDataBarang')?.value.toLowerCase().trim() || '';
            const rows = document.querySelectorAll('#tbodyDataBarang tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // Action Dropdowns (Three-dots menu)
        function toggleActionMenu(e, menuId) {
            e.stopPropagation();
            const targetMenu = document.getElementById(menuId);
            const isCurrentlyHidden = targetMenu ? targetMenu.classList.contains('hidden') : true;

            closeAllActionMenus();

            if (targetMenu && isCurrentlyHidden) {
                targetMenu.classList.remove('hidden');
            }
        }

        function closeAllActionMenus() {
            document.querySelectorAll('.action-dropdown').forEach(menu => menu.classList.add('hidden'));
        }

        // Modal Helpers
        function openTambahBarangModal() { document.getElementById('tambahBarangModal')?.classList.remove('hidden'); }
        function closeTambahBarangModal() { document.getElementById('tambahBarangModal')?.classList.add('hidden'); }

        function openTambahKategoriModal() { document.getElementById('tambahKategoriModal')?.classList.remove('hidden'); }
        function closeTambahKategoriModal() { document.getElementById('tambahKategoriModal')?.classList.add('hidden'); }

        function openTambahSupplierModal() { 
            document.getElementById('tambahSupplierModal')?.classList.remove('hidden'); 
            if (window.lucide) window.lucide.createIcons();
        }
        function closeTambahSupplierModal() { document.getElementById('tambahSupplierModal')?.classList.add('hidden'); }

        function openEditSupplierModal(sup) {
            const form = document.getElementById('editSupplierForm');
            if (form) {
                form.action = '{{ url("supplier") }}/' + sup.id;
            }
            document.getElementById('edit_supplier_nama').value = sup.nama_supplier || '';
            document.getElementById('edit_supplier_kontak').value = sup.kontak_person || '';
            document.getElementById('edit_supplier_telepon').value = sup.telepon || '';
            document.getElementById('edit_supplier_email').value = sup.email || '';
            document.getElementById('edit_supplier_alamat').value = sup.alamat || '';
            document.getElementById('edit_supplier_deskripsi').value = sup.deskripsi || '';

            document.getElementById('editSupplierModal')?.classList.remove('hidden');
            if (window.lucide) window.lucide.createIcons();
        }
        function closeEditSupplierModal() {
            document.getElementById('editSupplierModal')?.classList.add('hidden');
        }

        function openHapusSupplierModal(id, nama) {
            const form = document.getElementById('hapusSupplierForm');
            if (form) {
                form.action = '{{ url("supplier") }}/' + id;
            }
            const namaElem = document.getElementById('hapus_supplier_nama');
            if (namaElem) namaElem.textContent = nama;
            document.getElementById('hapusSupplierModal')?.classList.remove('hidden');
            if (window.lucide) window.lucide.createIcons();
        }
        function closeHapusSupplierModal() {
            document.getElementById('hapusSupplierModal')?.classList.add('hidden');
        }

        function filterDataSupplierCards() {
            const query = document.getElementById('searchDataSupplier')?.value.toLowerCase().trim() || '';
            const cards = document.querySelectorAll('.supplier-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const searchContent = card.getAttribute('data-search') || card.textContent.toLowerCase();
                if (searchContent.includes(query)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            const noResult = document.getElementById('noSupplierSearchResult');
            if (noResult) {
                noResult.classList.toggle('hidden', visibleCount > 0 || cards.length === 0);
            }
        }

        function onStokMasukBarangChange(barangId) {
            const select = document.getElementById('stokMasukBarangSelect');
            const box = document.getElementById('stokMasukSupplierAutoBox');
            const badge = document.getElementById('stokMasukSupplierBadge');
            const nameText = document.getElementById('stokMasukSupplierNameText');
            const detailText = document.getElementById('stokMasukSupplierDetailText');
            const iconWrapper = document.getElementById('stokMasukSupplierIconWrapper');
            const supplierSelect = document.getElementById('stokMasukSupplierSelect');

            if (!select || !supplierSelect) return;

            const selectedOption = select.options[select.selectedIndex];
            if (!barangId || !selectedOption || selectedOption.value === '') {
                supplierSelect.value = '';
                if (nameText) nameText.textContent = 'Belum memilih barang';
                if (detailText) detailText.textContent = 'Supplier otomatis tampil saat barang dipilih';
                if (badge) {
                    badge.textContent = 'Pilih barang terlebih dahulu';
                    badge.className = 'text-[10px] font-bold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-md transition-colors';
                }
                if (box) box.className = 'p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5 transition-all';
                if (iconWrapper) iconWrapper.className = 'w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors';
                if (window.lucide) window.lucide.createIcons();
                return;
            }

            const supplierId = selectedOption.getAttribute('data-supplier-id');
            const supplierName = selectedOption.getAttribute('data-supplier-name');
            const supplierPic = selectedOption.getAttribute('data-supplier-pic');
            const supplierPhone = selectedOption.getAttribute('data-supplier-phone');

            if (supplierId && supplierName) {
                supplierSelect.value = supplierId;
                if (nameText) nameText.textContent = supplierName;
                
                let details = [];
                if (supplierPic) details.push('PIC: ' + supplierPic);
                if (supplierPhone) details.push('Telp: ' + supplierPhone);
                if (detailText) detailText.textContent = details.length > 0 ? details.join(' • ') : 'Supplier resmi terdaftar untuk barang ini';

                if (badge) {
                    badge.textContent = 'Otomatis Terhubung';
                    badge.className = 'text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md transition-colors';
                }
                if (box) box.className = 'p-3.5 bg-emerald-50/40 border border-emerald-200/80 rounded-xl space-y-2.5 transition-all';
                if (iconWrapper) iconWrapper.className = 'w-9 h-9 rounded-lg bg-slate-900 text-amber-400 flex items-center justify-center shrink-0 transition-colors';
            } else {
                supplierSelect.value = '';
                if (nameText) nameText.textContent = 'Belum ada supplier default';
                if (detailText) detailText.textContent = 'Silakan pilih supplier di bawah ini jika barang didapat dari vendor tertentu';
                if (badge) {
                    badge.textContent = 'Opsional / Manual';
                    badge.className = 'text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md transition-colors';
                }
                if (box) box.className = 'p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5 transition-all';
                if (iconWrapper) iconWrapper.className = 'w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors';
            }
            if (window.lucide) window.lucide.createIcons();
        }

        function onManualSupplierSelectChange(supplierId) {
            const supplierSelect = document.getElementById('stokMasukSupplierSelect');
            const box = document.getElementById('stokMasukSupplierAutoBox');
            const badge = document.getElementById('stokMasukSupplierBadge');
            const nameText = document.getElementById('stokMasukSupplierNameText');
            const detailText = document.getElementById('stokMasukSupplierDetailText');
            const iconWrapper = document.getElementById('stokMasukSupplierIconWrapper');

            if (!supplierSelect) return;

            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
            if (supplierId && selectedOption && selectedOption.value !== '') {
                const name = selectedOption.getAttribute('data-name') || selectedOption.text;
                const pic = selectedOption.getAttribute('data-pic') || '';
                const phone = selectedOption.getAttribute('data-phone') || '';

                if (nameText) nameText.textContent = name;
                let details = [];
                if (pic) details.push('PIC: ' + pic);
                if (phone) details.push('Telp: ' + phone);
                if (detailText) detailText.textContent = details.length > 0 ? details.join(' • ') : 'Supplier dipilih secara manual';

                if (badge) {
                    badge.textContent = 'Dipilih Manual';
                    badge.className = 'text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md transition-colors';
                }
                if (box) box.className = 'p-3.5 bg-blue-50/40 border border-blue-200/80 rounded-xl space-y-2.5 transition-all';
                if (iconWrapper) iconWrapper.className = 'w-9 h-9 rounded-lg bg-slate-900 text-amber-400 flex items-center justify-center shrink-0 transition-colors';
            } else {
                if (nameText) nameText.textContent = 'Tanpa Supplier Terpilih';
                if (detailText) detailText.textContent = 'Transaksi stok masuk dicatat tanpa mengaitkan ke supplier tertentu';
                if (badge) {
                    badge.textContent = 'Tanpa Supplier';
                    badge.className = 'text-[10px] font-bold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-md transition-colors';
                }
                if (box) box.className = 'p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5 transition-all';
                if (iconWrapper) iconWrapper.className = 'w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors';
            }
            if (window.lucide) window.lucide.createIcons();
        }

        function openStokMasukModal() { 
            document.getElementById('stokMasukModal')?.classList.remove('hidden'); 
            const select = document.getElementById('stokMasukBarangSelect');
            if (select) {
                onStokMasukBarangChange(select.value);
            }
            if (window.lucide) window.lucide.createIcons();
        }
        function closeStokMasukModal() { document.getElementById('stokMasukModal')?.classList.add('hidden'); }

        function openStokKeluarModal() { document.getElementById('stokKeluarModal')?.classList.remove('hidden'); }
        function closeStokKeluarModal() { document.getElementById('stokKeluarModal')?.classList.add('hidden'); }

        function openTambahPenggunaModal() { document.getElementById('tambahPenggunaModal')?.classList.remove('hidden'); }
        function closeTambahPenggunaModal() { document.getElementById('tambahPenggunaModal')?.classList.add('hidden'); }

        function openEditPenggunaModal(user) {
            const form = document.getElementById('editPenggunaForm');
            if (form) {
                form.action = '{{ url("users") }}/' + user.id;
            }
            const nameInput = document.getElementById('edit_user_name');
            const emailInput = document.getElementById('edit_user_email');
            const teleponInput = document.getElementById('edit_user_telepon');
            const passwordInput = document.getElementById('edit_user_password');

            if (nameInput) nameInput.value = user.name || '';
            if (emailInput) emailInput.value = user.email || '';
            if (teleponInput) teleponInput.value = user.nomor_telepon || '';
            if (passwordInput) passwordInput.value = '';

            document.getElementById('editPenggunaModal')?.classList.remove('hidden');
            if (window.lucide) window.lucide.createIcons();
        }
        function closeEditPenggunaModal() {
            document.getElementById('editPenggunaModal')?.classList.add('hidden');
        }

        function openStokMasukModalForBarang(barangId) {
            const select = document.getElementById('stokMasukBarangSelect');
            if (select) {
                select.value = barangId;
                onStokMasukBarangChange(barangId);
            }
            document.getElementById('stokMasukModal')?.classList.remove('hidden');
            if (window.lucide) window.lucide.createIcons();
        }

        function openStokKeluarModalForBarang(barangId) {
            const select = document.getElementById('stokKeluarBarangSelect');
            if (select) select.value = barangId;
            openStokKeluarModal();
        }

        function openEditBarangModal(barang) {
            const form = document.getElementById('editBarangForm');
            if (form) {
                form.action = '{{ url("barang") }}/' + barang.id;
            }
            document.getElementById('edit_kode_barang').value = barang.kode_barang || '';
            document.getElementById('edit_nama_barang').value = barang.nama_barang || '';
            document.getElementById('edit_kategori_id').value = barang.kategori_id || '';
            document.getElementById('edit_satuan').value = barang.satuan || '';
            document.getElementById('edit_harga').value = parseInt(barang.harga) || 0;
            document.getElementById('edit_stok_minimum').value = parseInt(barang.stok_minimum) || 0;
            const supplierSelect = document.getElementById('edit_supplier_id');
            if (supplierSelect) {
                supplierSelect.value = barang.supplier_id || '';
            }

            document.getElementById('editBarangModal')?.classList.remove('hidden');
        }
        function closeEditBarangModal() {
            document.getElementById('editBarangModal')?.classList.add('hidden');
        }

        function openHapusBarangModal(id, nama, kode) {
            const form = document.getElementById('hapusBarangForm');
            if (form) {
                form.action = '{{ url("barang") }}/' + id;
            }
            const namaElem = document.getElementById('hapus_barang_nama');
            const kodeElem = document.getElementById('hapus_barang_kode');
            if (namaElem) namaElem.textContent = nama;
            if (kodeElem) kodeElem.textContent = kode;

            document.getElementById('hapusBarangModal')?.classList.remove('hidden');
        }
        function closeHapusBarangModal() {
            document.getElementById('hapusBarangModal')?.classList.add('hidden');
        }

        // CHART.JS INITIALIZATION WITH REAL DATABASE DATA
        function initStockChart() {
            const chartElem = document.getElementById('stockMovementChart');
            if (!chartElem) return;

            const ctx = chartElem.getContext('2d');
            const labels = @json($chartLabels);
            const dataMasuk = @json($chartMasuk);
            const dataKeluar = @json($chartKeluar);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Stok Masuk',
                            data: dataMasuk,
                            backgroundColor: '#0f172a',
                            borderRadius: 6,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Stok Keluar',
                            data: dataKeluar,
                            backgroundColor: '#94a3b8',
                            borderRadius: 6,
                            barPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { family: 'Plus Jakarta Sans', size: 12 },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' },
                            grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });
        }
    </script>
    <!-- ========================================== -->
    <!-- MODAL BARCODE KOTAK (QR CODE) BARANG -->
    <!-- ========================================== -->
    <div id="modalBarcodeBarang" class="fixed inset-0 z-[90] hidden">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="closeBarcodeModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-sm border border-slate-200/80 animate-fadeIn">
                    <!-- Header -->
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i data-lucide="qr-code" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Barcode Kotak Barang</h3>
                                <p class="text-[11px] text-slate-500">QR Code identifikasi &amp; scan stok</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeBarcodeModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Body / Printable Sticker Card -->
                    <div class="p-5 sm:p-6 flex flex-col items-center">
                        <!-- Sticker Label Card -->
                        <div id="printableBarcodeCard" class="w-full bg-white border-2 border-dashed border-slate-200 rounded-2xl p-5 flex flex-col items-center text-center shadow-xs">
                            <div class="flex items-center justify-center gap-1.5 mb-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                                <i data-lucide="box" class="w-3.5 h-3.5 text-slate-700"></i>
                                <span>StokClean Warehouse</span>
                            </div>

                            <!-- QR Code Container (Square Barcode) -->
                            <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-center">
                                <div id="qrcodeContainer" class="flex items-center justify-center min-w-[170px] min-h-[170px]"></div>
                            </div>

                            <!-- Kode Barang Badge -->
                            <div class="mt-3.5">
                                <span id="barcodeModalKode" class="font-mono font-extrabold text-base tracking-widest text-slate-900 px-3.5 py-1 bg-slate-100 rounded-xl border border-slate-200 inline-block">
                                    -
                                </span>
                            </div>

                            <!-- Detail Barang -->
                            <h4 id="barcodeModalNama" class="mt-2.5 font-bold text-slate-900 text-sm leading-tight line-clamp-2">
                                -
                            </h4>
                            <p id="barcodeModalKategori" class="text-xs text-slate-500 mt-0.5">
                                Kategori: -
                            </p>

                            <div class="mt-3.5 pt-3 border-t border-slate-100 w-full flex items-center justify-between text-xs text-slate-600 font-semibold px-2">
                                <span id="barcodeModalStok" class="text-slate-700 font-bold">Stok: -</span>
                                <span id="barcodeModalHarga" class="text-emerald-700 font-bold">Rp -</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-5 w-full space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="printBarcodeLabel()" class="flex items-center justify-center gap-2 px-3 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    <span>Cetak Label</span>
                                </button>
                                <button type="button" onclick="downloadBarcodeImage()" class="flex items-center justify-center gap-2 px-3 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    <span>Unduh PNG</span>
                                </button>
                            </div>
                            <button type="button" onclick="copyBarcodeKode()" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                <span id="copyBarcodeBtnText">Salin Kode Barang</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PENGATURAN PROFIL -->
    <div id="modalPengaturanProfil" class="fixed inset-0 z-[100] hidden">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity opacity-0" id="modalPengaturanProfilBackdrop" onclick="closeProfileSettingsModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div id="modalPengaturanProfilContent" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="bg-white px-5 sm:px-6 py-5">
                        <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Pengaturan Profil</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Perbarui informasi profil dan password Anda.</p>
                            </div>
                            <button type="button" onclick="closeProfileSettingsModal()" class="text-slate-400 hover:text-slate-500 bg-slate-50 hover:bg-slate-100 p-2 rounded-xl transition">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <form id="formPengaturanProfil" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <!-- Foto Profil -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">Foto Profil</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0 shadow-sm" id="profileImagePreviewContainer">
                                        @if(Auth::user()->foto_profil)
                                            <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" id="profileImagePreview" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="user" class="w-6 h-6 text-slate-400" id="profileImagePlaceholder"></i>
                                            <img src="" id="profileImagePreview" class="w-full h-full object-cover hidden">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="foto_profil" id="foto_profil_input" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition cursor-pointer" onchange="previewProfileImage(this)">
                                        <p class="text-[10px] text-slate-500 mt-1.5">Format JPG, PNG (maks 2MB).</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">Nama Lengkap *</label>
                                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">Email *</label>
                                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">Nomor Telepon</label>
                                <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', Auth::user()->nomor_telepon) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                            </div>

                            <!-- Ubah Password (Hanya muncul jika diaktifkan) -->
                            <div class="pt-2 border-t border-slate-100">
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-200/60">
                                    <div>
                                        <label for="toggleChangePassword" class="block text-xs font-bold text-slate-800 cursor-pointer">Ganti Password</label>
                                        <p class="text-[10px] text-slate-500">Centang jika Anda ingin memperbarui password akun.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" id="toggleChangePassword" class="sr-only peer" onchange="togglePasswordFields(this.checked)">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-slate-900"></div>
                                    </label>
                                </div>

                                <!-- Kolom Input Password Baru (Tersembunyi secara default) -->
                                <div id="sectionPasswordFields" class="hidden mt-3 space-y-3 p-3.5 bg-slate-50/70 rounded-2xl border border-slate-200/70">
                                    <div>
                                        <label for="inputPasswordBaru" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">Password Baru *</label>
                                        <div class="relative">
                                            <input 
                                                type="password" 
                                                name="password" 
                                                id="inputPasswordBaru" 
                                                disabled 
                                                autocomplete="new-password" 
                                                class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300" 
                                                placeholder="Minimal 8 karakter"
                                            >
                                            <button type="button" onclick="togglePasswordVisibility('inputPasswordBaru', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Tampilkan password">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="inputPasswordConfirmation" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">Konfirmasi Password Baru *</label>
                                        <div class="relative">
                                            <input 
                                                type="password" 
                                                name="password_confirmation" 
                                                id="inputPasswordConfirmation" 
                                                disabled 
                                                autocomplete="new-password" 
                                                class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-300" 
                                                placeholder="Ketik ulang password baru"
                                            >
                                            <button type="button" onclick="togglePasswordVisibility('inputPasswordConfirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Tampilkan password">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-2.5 pt-4 border-t border-slate-100">
                                <button type="button" onclick="closeProfileSettingsModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs transition">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordFields(show) {
            const section = document.getElementById('sectionPasswordFields');
            const passInput = document.getElementById('inputPasswordBaru');
            const confirmInput = document.getElementById('inputPasswordConfirmation');
            
            if (show) {
                section.classList.remove('hidden');
                passInput.disabled = false;
                passInput.value = '';
                confirmInput.disabled = false;
                confirmInput.value = '';
                passInput.focus();
            } else {
                section.classList.add('hidden');
                passInput.disabled = true;
                passInput.value = '';
                confirmInput.disabled = true;
                confirmInput.value = '';
            }
            if (window.lucide) window.lucide.createIcons();
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.innerHTML = `<i data-lucide="${isPass ? 'eye-off' : 'eye'}" class="w-4 h-4"></i>`;
            if (window.lucide) window.lucide.createIcons();
        }

        function openProfileSettingsModal() {
            closeAllActionMenus();
            const modal = document.getElementById('modalPengaturanProfil');
            const backdrop = document.getElementById('modalPengaturanProfilBackdrop');
            const content = document.getElementById('modalPengaturanProfilContent');
            
            // Reset toggle ganti password ke tidak aktif setiap kali modal dibuka
            const toggle = document.getElementById('toggleChangePassword');
            if (toggle) {
                toggle.checked = false;
                togglePasswordFields(false);
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                content.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            }, 10);
            if (window.lucide) window.lucide.createIcons();
        }

        function closeProfileSettingsModal() {
            const modal = document.getElementById('modalPengaturanProfil');
            const backdrop = document.getElementById('modalPengaturanProfilBackdrop');
            const content = document.getElementById('modalPengaturanProfilContent');
            
            backdrop.classList.add('opacity-0');
            content.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                const toggle = document.getElementById('toggleChangePassword');
                if (toggle) {
                    toggle.checked = false;
                    togglePasswordFields(false);
                }
            }, 300);
        }

        function previewProfileImage(input) {
            const preview = document.getElementById('profileImagePreview');
            const placeholder = document.getElementById('profileImagePlaceholder');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const validExtensions = ['image/jpeg', 'image/png', 'image/jpg'];
                const fileName = file.name.toLowerCase();
                const isValidExt = fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png');

                if (!validExtensions.includes(file.type.toLowerCase()) && !isValidExt) {
                    alert('Format file tidak didukung. Harap pilih foto berformat JPG atau PNG.');
                    input.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if(placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        // ==========================================
        // BARCODE KOTAK (QR CODE) FUNCTIONS
        // ==========================================
        let currentBarcodeData = null;

        function showBarcodeModal(barang) {
            currentBarcodeData = barang;
            const modal = document.getElementById('modalBarcodeBarang');
            if (!modal) return;

            document.getElementById('barcodeModalKode').textContent = barang.kode_barang || '-';
            document.getElementById('barcodeModalNama').textContent = barang.nama_barang || '-';
            document.getElementById('barcodeModalKategori').textContent = 'Kategori: ' + (barang.kategori ? barang.kategori.nama_kategori : (barang.kategori_nama || '-'));
            
            const stokFormatted = Number(barang.stok || 0).toLocaleString('id-ID');
            const satuan = barang.satuan || 'Pcs';
            document.getElementById('barcodeModalStok').textContent = `Stok: ${stokFormatted} ${satuan}`;

            const hargaFormatted = Number(barang.harga || 0).toLocaleString('id-ID');
            document.getElementById('barcodeModalHarga').textContent = `Rp ${hargaFormatted}`;

            const container = document.getElementById('qrcodeContainer');
            container.innerHTML = '';

            const qrValue = barang.kode_barang || '';
            
            if (typeof QRCode !== 'undefined') {
                new QRCode(container, {
                    text: qrValue,
                    width: 170,
                    height: 170,
                    colorDark: "#0f172a",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                const img = document.createElement('img');
                img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=170x170&data=' + encodeURIComponent(qrValue);
                img.alt = qrValue;
                img.className = 'w-[170px] h-[170px]';
                container.appendChild(img);
            }

            modal.classList.remove('hidden');
            if (window.lucide) window.lucide.createIcons();
        }

        function closeBarcodeModal() {
            const modal = document.getElementById('modalBarcodeBarang');
            if (modal) modal.classList.add('hidden');
        }

        function copyBarcodeKode() {
            if (!currentBarcodeData || !currentBarcodeData.kode_barang) return;
            navigator.clipboard.writeText(currentBarcodeData.kode_barang).then(() => {
                const btnText = document.getElementById('copyBarcodeBtnText');
                if (btnText) {
                    const original = btnText.textContent;
                    btnText.textContent = 'Kode Berhasil Disalin!';
                    setTimeout(() => {
                        btnText.textContent = original;
                    }, 2000);
                }
            });
        }

        function downloadBarcodeImage() {
            if (!currentBarcodeData) return;
            const container = document.getElementById('qrcodeContainer');
            const canvas = container.querySelector('canvas');
            const img = container.querySelector('img');
            
            let dataUrl = null;
            if (canvas) {
                dataUrl = canvas.toDataURL('image/png');
            } else if (img && img.src) {
                dataUrl = img.src;
            }

            if (dataUrl) {
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = `barcode-${currentBarcodeData.kode_barang}.png`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                alert('Gagal mengambil gambar barcode.');
            }
        }

        function printBarcodeLabel() {
            if (!currentBarcodeData) return;
            const printWindow = window.open('', '', 'width=450,height=550');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Cetak Barcode - ${currentBarcodeData.kode_barang}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                            margin: 0;
                            background: #fff;
                        }
                        .label-box {
                            border: 2px solid #0f172a;
                            border-radius: 14px;
                            padding: 16px;
                            text-align: center;
                            width: 250px;
                        }
                        .brand {
                            font-size: 11px;
                            font-weight: 800;
                            text-transform: uppercase;
                            letter-spacing: 1.5px;
                            margin-bottom: 10px;
                            color: #475569;
                        }
                        .qr-img {
                            display: block;
                            margin: 0 auto;
                            width: 170px;
                            height: 170px;
                        }
                        .code {
                            font-family: monospace;
                            font-size: 16px;
                            font-weight: 800;
                            margin-top: 10px;
                            letter-spacing: 2px;
                            background: #f1f5f9;
                            padding: 4px 8px;
                            border-radius: 6px;
                            display: inline-block;
                        }
                        .name {
                            font-size: 13px;
                            font-weight: 700;
                            margin-top: 8px;
                            color: #0f172a;
                        }
                        .details {
                            font-size: 11px;
                            color: #64748b;
                            margin-top: 8px;
                            border-top: 1px dashed #cbd5e1;
                            padding-top: 8px;
                            display: flex;
                            justify-content: space-between;
                            font-weight: 600;
                        }
                        @media print {
                            body { min-height: auto; margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    <div class="label-box">
                        <div class="brand">StokClean Warehouse</div>
                        <div id="printQr"></div>
                        <div class="code">${currentBarcodeData.kode_barang}</div>
                        <div class="name">${currentBarcodeData.nama_barang}</div>
                        <div class="details">
                            <span>${currentBarcodeData.satuan || 'Pcs'}</span>
                            <span>Rp ${Number(currentBarcodeData.harga || 0).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                    <script>
                        const canvas = window.opener.document.querySelector('#qrcodeContainer canvas');
                        const img = window.opener.document.querySelector('#qrcodeContainer img');
                        const target = document.getElementById('printQr');
                        if (canvas) {
                            const printImg = document.createElement('img');
                            printImg.src = canvas.toDataURL('image/png');
                            printImg.className = 'qr-img';
                            target.appendChild(printImg);
                        } else if (img) {
                            const printImg = document.createElement('img');
                            printImg.src = img.src;
                            printImg.className = 'qr-img';
                            target.appendChild(printImg);
                        }
                        setTimeout(() => {
                            window.print();
                            window.close();
                        }, 300);
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</body>
</html>

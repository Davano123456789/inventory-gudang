@extends('layouts.masterDashboard')

@section('title', 'Dashboard Inventory Gudang')
@section('page_title', 'Dashboard')

@section('content')
<!-- Welcome Banner Card -->
<div class="relative flex flex-col min-w-0 break-words bg-blue-900 shadow-soft-xl rounded-2xl bg-clip-border mb-6 text-white overflow-hidden p-6">
    <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl text-blue-300 shadow-soft-inner">
                <i class="fa fa-user-shield"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h5 class="text-xl font-bold text-white mb-0">Selamat Datang Kembali, {{ auth()->user()->name }}!</h5>
                    <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-blue-500/30 text-blue-200 rounded-full border border-blue-400/30">
                        {{ ucwords(str_replace('_', ' ', auth()->user()->role ?? 'Pengguna')) }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 mb-0">
                    Sistem Manajemen Inventaris & Mutasi Stok Gudang. Pantau pergerakan stok barang secara realtime.
                </p>
            </div>
        </div>

    </div>
</div>

<div class="flex flex-wrap -mx-3 removable">
    <!-- Card: Total Items -->
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 flex">
        <div class="relative flex flex-col justify-between min-w-0 w-full break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border p-5 border border-gray-100 hover:shadow-soft-2xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 font-sans font-semibold leading-none text-xs text-slate-500 uppercase tracking-wider">Total Jenis Barang</p>
                    <h4 class="mb-0 font-bold text-slate-800 text-xl">{{ number_format($totalBarang, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-500 ml-1">Item</span></h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tl from-blue-600 to-sky-400 shadow-soft-md flex items-center justify-center text-white text-lg flex-shrink-0">
                    <i class="fa fa-boxes"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Barang Masuk Hari Ini -->
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 flex">
        <div class="relative flex flex-col justify-between min-w-0 w-full break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border p-5 border border-gray-100 hover:shadow-soft-2xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 font-sans font-semibold leading-none text-xs text-slate-500 uppercase tracking-wider">Barang Masuk (Hari Ini)</p>
                    <h4 class="mb-0 font-bold text-slate-800 text-xl">{{ number_format($incomingToday, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-500 ml-1">Qty</span></h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tl from-emerald-600 to-teal-400 shadow-soft-md flex items-center justify-center text-white text-lg flex-shrink-0">
                    <i class="fa fa-arrow-down"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Barang Keluar Hari Ini -->
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 flex">
        <div class="relative flex flex-col justify-between min-w-0 w-full break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border p-5 border border-gray-100 hover:shadow-soft-2xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 font-sans font-semibold leading-none text-xs text-slate-500 uppercase tracking-wider">Barang Keluar (Hari Ini)</p>
                    <h4 class="mb-0 font-bold text-slate-800 text-xl">{{ number_format($outgoingToday, 0, ',', '.') }} <span class="text-xs font-bold text-amber-500 ml-1">Qty</span></h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tl from-orange-500 to-amber-400 shadow-soft-md flex items-center justify-center text-white text-lg flex-shrink-0">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Batas Stok Rendah -->
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 flex">
        <div class="relative flex flex-col justify-between min-w-0 w-full break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border p-5 border border-gray-100 hover:shadow-soft-2xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 font-sans font-semibold leading-none text-xs text-slate-500 uppercase tracking-wider">Batas Stok Rendah</p>
                    <h4 class="mb-0 font-bold text-slate-800 text-xl">&le; {{ number_format($minStokAlert, 0, ',', '.') }} <span class="text-xs font-bold text-rose-500 ml-1">Barang</span></h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tl from-rose-600 to-red-400 shadow-soft-md flex items-center justify-center text-white text-lg flex-shrink-0">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>@endsection

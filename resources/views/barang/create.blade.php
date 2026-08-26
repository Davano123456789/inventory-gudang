@extends('layouts.masterDashboard')

@section('title', 'Tambah Barang - Inventory Gudang')
@section('page_title', 'Tambah Barang')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Validation Errors -->
        @if($errors->any())
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <div class="flex items-center mb-1">
                <i class="fa fa-exclamation-circle mr-2 text-base"></i>
                <span class="font-bold">Terjadi kesalahan input:</span>
            </div>
            <ul class="list-disc list-inside pl-4">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <h6 class="font-bold text-slate-800 text-lg">Form Tambah Barang Baru</h6>
                <a href="{{ route('barang.index') }}" class="inline-block px-5 py-2.5 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="kode_barang" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Kode Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang') }}" required placeholder="Contoh: 11-254394100S" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div class="mb-4">
                        <label for="nama_barang" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: 254 x 394 [0.15] S" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div class="mb-4">
                        <label for="satuan_id" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Satuan Barang (Opsional)</label>
                        <select name="satuan_id" id="satuan_id" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                            <option value="">-- Tanpa Satuan / Pilih Nanti --</option>
                            @foreach($satuans as $satuan)
                                <option value="{{ $satuan->id }}" {{ old('satuan_id') == $satuan->id ? 'selected' : '' }}>
                                    {{ $satuan->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Kosongkan jika Anda belum mengidentifikasi satuan untuk barang ini. Satuan dapat dipilih nanti saat transaksi barang pertama kali.</p>
                    </div>

                    <div class="mb-4">
                        <label for="kode_gudang" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Gudang Tempat Penyimpanan (Opsional)</label>
                        @if(Auth::user() && Auth::user()->isSuperAdmin())
                            <select name="kode_gudang" id="kode_gudang" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                                <option value="">-- Tanpa Stok Awal / Tentukan Nanti --</option>
                                @foreach($gudangs as $gudang)
                                    <option value="{{ $gudang->kode_gudang }}" {{ old('kode_gudang') == $gudang->kode_gudang ? 'selected' : '' }}>
                                        {{ $gudang->nama_gudang }} ({{ $gudang->kode_gudang }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <select disabled class="w-full px-3 py-2 text-sm text-slate-500 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none cursor-not-allowed">
                                <option value="{{ Auth::user()->kode_gudang }}" selected>{{ Auth::user()->gudang ? Auth::user()->gudang->nama_gudang : 'Gudang Penugasan' }}</option>
                            </select>
                            <input type="hidden" name="kode_gudang" value="{{ Auth::user()->kode_gudang }}">
                        @endif
                        <p class="text-xs text-slate-400 mt-1">Pilih gudang jika Anda ingin langsung menginput stok awal untuk barang ini.</p>
                    </div>

                    <div class="mb-4">
                        <label for="stok_awal" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Stok Awal (Opsional)</label>
                        <input type="number" step="any" name="stok_awal" id="stok_awal" value="{{ old('stok_awal', 0) }}" placeholder="Contoh: 100" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        <p class="text-xs text-slate-400 mt-1">Stok awal barang di gudang yang dipilih di atas.</p>
                    </div>

                    <div class="mb-6">
                        <label for="stok_minimum" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Batas Stok Minimum <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="stok_minimum" id="stok_minimum" value="{{ old('stok_minimum', 0) }}" required placeholder="Contoh: 50" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        <p class="text-xs text-slate-400 mt-1">Sistem akan memberi alarm peringatan jika stok barang di gudang tersisa di bawah angka ini.</p>
                    </div>

                    <div class="flex justify-start gap-3 border-t pt-4">
                        <button type="submit" class="px-6 py-3 font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md transition-colors text-xs tracking-wider">
                            <i class="fa fa-save mr-1"></i> Simpan Barang
                        </button>
                        <a href="{{ route('barang.index') }}" class="px-6 py-3 font-bold text-slate-500 uppercase bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-xs tracking-wider">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

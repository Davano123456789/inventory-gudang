@extends('layouts.masterDashboard')

@section('title', 'Tambah Gudang - Inventory Gudang')
@section('page_title', 'Tambah Gudang')

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
                <h6 class="font-bold text-slate-800 text-lg">Form Tambah Gudang Baru</h6>
                <a href="{{ route('gudang.index') }}" class="inline-block px-5 py-2.5 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">
                <form action="{{ route('gudang.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="kode_gudang" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Kode Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_gudang" id="kode_gudang" value="{{ old('kode_gudang') }}" required placeholder="Contoh: 300003" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        <p class="text-xs text-slate-400 mt-1">Masukkan kode unik cabang/gudang.</p>
                    </div>

                    <div class="mb-4">
                        <label for="nama_gudang" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Nama Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_gudang" id="nama_gudang" value="{{ old('nama_gudang') }}" required placeholder="Contoh: Gudang Surabaya" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div class="mb-6">
                        <label for="alamat" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Alamat Gudang</label>
                        <textarea name="alamat" id="alamat" rows="4" placeholder="Alamat lengkap lokasi gudang..." class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="flex justify-start gap-3 border-t pt-4">
                        <button type="submit" class="px-6 py-3 font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md transition-colors text-xs tracking-wider">
                            <i class="fa fa-save mr-1"></i> Simpan Gudang
                        </button>
                        <a href="{{ route('gudang.index') }}" class="px-6 py-3 font-bold text-slate-500 uppercase bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-xs tracking-wider">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

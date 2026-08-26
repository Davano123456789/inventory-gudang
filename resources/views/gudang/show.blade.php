@extends('layouts.masterDashboard')

@section('title', 'Detail Gudang - Inventory Gudang')
@section('page_title', 'Detail Gudang')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <h6 class="font-bold text-slate-800 text-lg">Informasi Detail Gudang</h6>
                <a href="{{ route('gudang.index') }}" class="inline-block px-5 py-2.5 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <!-- Card Body (Read-only Form style) -->
            <div class="flex-auto p-6">
                
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-slate-700 uppercase">Kode Gudang</label>
                    <input type="text" value="{{ $gudang->kode_gudang }}" readonly class="w-full px-3 py-2 text-sm text-slate-500 bg-gray-50 border border-gray-300 rounded-lg cursor-not-allowed focus:outline-none">
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-slate-700 uppercase">Nama Gudang</label>
                    <input type="text" value="{{ $gudang->nama_gudang }}" readonly class="w-full px-3 py-2 text-sm text-slate-500 bg-gray-50 border border-gray-300 rounded-lg cursor-not-allowed focus:outline-none">
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-slate-700 uppercase">Alamat Gudang</label>
                    <textarea readonly rows="4" class="w-full px-3 py-2 text-sm text-slate-500 bg-gray-50 border border-gray-300 rounded-lg cursor-not-allowed focus:outline-none">{{ $gudang->alamat ?? '-' }}</textarea>
                </div>



            </div>
        </div>

    </div>
</div>
@endsection

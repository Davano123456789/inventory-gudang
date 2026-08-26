@extends('layouts.masterDashboard')

@section('title', 'Detail Stock Opname - Inventory Gudang')
@section('page_title', 'Stock Opname')

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printArea, #printArea * {
            visibility: visible;
        }
        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-6" id="printArea">
            
            <!-- Document Header Title -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <div>
                    <h6 class="font-bold text-slate-800 text-lg leading-none">Dokumen Penyesuaian Stok (Stock Opname)</h6>
                    <span class="text-xs text-slate-400 font-semibold mt-1 block">Nomor: {{ $stockOpname->no_opname }}</span>
                </div>
                
                <!-- Back button -->
                <div class="flex gap-2 no-print">
                    <a href="{{ route('stock-opname.index') }}" class="inline-block px-6 py-2.5 font-bold text-center text-slate-600 bg-gray-100 hover:bg-gray-200 uppercase align-middle transition-all rounded-lg cursor-pointer leading-pro text-xs ease-soft-in shadow-soft-xs hover:shadow-soft-md tracking-tight">
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">
                <!-- Metadata Info Rows -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-5 bg-slate-50 border border-slate-100 rounded-2xl mb-6">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Tanggal Perhitungan</span>
                        <span class="text-sm font-bold text-slate-700 block">{{ $stockOpname->tanggal_opname->format('d F Y') }}</span>
                    </div>
                    
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Gudang Target</span>
                        <span class="text-sm font-bold text-rose-600 block">{{ $stockOpname->gudang ? $stockOpname->gudang->nama_gudang : 'Gudang Terhapus' }} ({{ $stockOpname->gudang_kode }})</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Petugas Pencatat</span>
                        <span class="text-sm font-bold text-slate-700 block">{{ $stockOpname->user ? $stockOpname->user->name : 'System' }}</span>
                    </div>


                </div>

                <!-- Detail Table -->
                <h6 class="font-bold text-slate-800 text-xs uppercase mb-3 tracking-wide">Daftar Barang & Selisih Penyesuaian</h6>
                <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr class="bg-slate-50 text-xxs uppercase tracking-wider text-slate-400 font-bold border-b border-gray-200">
                                <th class="px-6 py-3 text-left">No</th>
                                <th class="px-6 py-3 text-left">Barang</th>
                                <th class="px-6 py-3 text-center">Stok Sistem</th>
                                <th class="px-6 py-3 text-center">Stok Fisik</th>
                                <th class="px-6 py-3 text-center">Selisih</th>
                                <th class="px-6 py-3 text-left">Keterangan / Alasan Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockOpname->details as $index => $detail)
                            <tr class="border-b border-slate-100 hover:bg-slate-50/20">
                                <td class="px-6 py-4 align-middle bg-transparent shadow-none">
                                    <span class="text-sm font-semibold leading-normal text-slate-600">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent shadow-none">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">{{ $detail->barang->nama_barang }}</span>
                                        <span class="text-[10px] text-slate-400">Kode: {{ $detail->barang->kode_barang }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent shadow-none">
                                    <span class="text-sm leading-normal text-slate-600 font-semibold">{{ number_format($detail->stok_sistem, 0, ',', '.') }} {{ $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : '' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent shadow-none">
                                    <span class="text-sm font-bold leading-normal text-slate-700">{{ number_format($detail->stok_fisik, 0, ',', '.') }} {{ $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : '' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent shadow-none">
                                    @if($detail->selisih < 0)
                                        <span class="text-xs font-bold leading-normal text-red-600 bg-red-50 px-2.5 py-1 rounded-lg">
                                            {{ number_format($detail->selisih, 0, ',', '.') }} {{ $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : '' }}
                                        </span>
                                    @elseif($detail->selisih > 0)
                                        <span class="text-xs font-bold leading-normal text-green-600 bg-green-50 px-2.5 py-1 rounded-lg">
                                            +{{ number_format($detail->selisih, 0, ',', '.') }} {{ $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : '' }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold leading-normal text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">
                                            0 {{ $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : '' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent shadow-none">
                                    <span class="text-sm leading-normal text-slate-600 font-medium">{{ $detail->keterangan ?: '-' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center align-middle bg-transparent shadow-none">
                                    <span class="text-sm text-slate-400 font-medium">Tidak ada detail barang dalam dokumen ini.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


            </div>
        </div>

    </div>
</div>
@endsection

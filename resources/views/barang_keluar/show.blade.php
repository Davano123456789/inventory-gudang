@extends('layouts.masterDashboard')

@section('title', 'Detail Surat Jalan Keluar - Inventory Gudang')
@section('page_title', 'Detail Barang Keluar')

@push('styles')
<style>
    /* Printing CSS overrides */
    @media print {
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        /* Enforce desktop-style flex layouts on print */
        .flex {
            display: flex !important;
        }
        .flex-wrap {
            flex-wrap: nowrap !important;
        }
        .justify-between {
            justify-content: space-between !important;
        }
        .items-start {
            align-items: flex-start !important;
        }
        .w-full {
            width: auto !important;
        }
        .md\:w-3\/5 {
            width: 60% !important;
        }
        .md\:w-2\/5 {
            width: 40% !important;
        }
        .md\:text-right {
            text-align: right !important;
        }
        .md\:items-end {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
        }
        .md\:justify-start {
            justify-content: flex-start !important;
        }
        .md\:mt-0 {
            margin-top: 0 !important;
        }
        /* Hide sidebar, navbar and other elements during printing */
        aside, nav, footer, button, .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-4">
            
            <!-- Card Header Actions -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center no-print">
                <h6 class="font-bold text-slate-800 text-lg">Informasi Surat Jalan Keluar</h6>
                <div class="flex gap-2">
                    <button type="button" onclick="window.print()" class="inline-block px-5 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-slate-600 to-slate-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-print mr-1"></i> Cetak / Print
                    </button>
                    <a href="{{ route('barang-keluar.index') }}" class="inline-block px-5 py-2.5 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight">
                        <i class="fa fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Surat Jalan Paper Template (Printable Area) -->
            <div class="flex-auto p-8" id="printableArea">
            
            <!-- Document Header -->
            <div class="flex flex-wrap justify-between items-start border-b pb-6 mb-6">
                <!-- Left: Sender Info & Document Details -->
                <div class="w-full md:w-1/2 pr-0 md:pr-4">
                    <h5 class="font-extrabold text-red-600 text-2xl tracking-wide uppercase m-0 leading-none">PT. BINTANG CAKRA KENCANA</h5>
                    <p class="text-[10px] text-slate-500 font-bold mt-1 mb-4">BANYUDONO JL. RAYA SOLO SEMARANG KM 15 BOYOLALI</p>
                    
                    <div class="flex flex-col gap-2 text-xs">
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">No Surat Jalan :</span>
                            <span class="text-slate-800 font-bold">{{ $barangKeluar->no_surat_jalan }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Tgl Surat Jalan :</span>
                            <span class="text-slate-600 font-medium">{{ $barangKeluar->tanggal_surat_jalan->format('d F Y') }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Tgl Pengiriman :</span>
                            <span class="text-slate-600 font-medium">{{ $barangKeluar->tanggal_keluar->format('d F Y') }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Petugas Pengirim :</span>
                            <span class="text-slate-700 font-semibold">{{ $barangKeluar->user ? $barangKeluar->user->name : '-' }}</span>
                        </div>
                        @if($barangKeluar->catatan)
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Catatan :</span>
                            <span class="text-slate-600 italic">{{ $barangKeluar->catatan }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Boxed Number & Badges -->
                <div class="w-full md:w-1/2 md:text-right mt-6 md:mt-0 flex flex-col md:items-end">
                    <div class="border-2 border-red-500 text-red-600 rounded-lg px-6 py-2 text-center font-bold text-lg tracking-wider mb-4 bg-red-50/50 w-44">
                        @php
                            $dashIndex = strpos($barangKeluar->no_surat_jalan, '/');
                            $cleanNum = $dashIndex !== false ? substr($barangKeluar->no_surat_jalan, 0, $dashIndex) : $barangKeluar->no_surat_jalan;
                        @endphp
                        {{ $cleanNum }}
                        <div class="text-[9px] uppercase tracking-widest text-slate-400 mt-0.5 border-t border-red-200 pt-0.5 font-bold">ASLI SURAT JALAN</div>
                    </div>

                    <div class="flex flex-col gap-2 text-xs w-full md:w-auto">
                        <div class="flex justify-between md:justify-end gap-3">
                            <span class="font-bold text-slate-700 uppercase">Jenis Transaksi :</span>
                            <span class="text-slate-600 font-semibold">
                                @php
                                    $jenisVal = (int)$barangKeluar->jenis;
                                    $jenisStr = strtolower($barangKeluar->jenis_text ?? '');
                                @endphp

                                @if($jenisVal === 2 || $jenisStr === 'mutasi')
                                    <span class="text-purple-600 bg-purple-50 px-2.5 py-1 rounded text-xxs font-bold uppercase">Mutasi</span>
                                @elseif($jenisVal === 3 || $jenisStr === 'retur' || $jenisStr === 'return')
                                    <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded text-xxs font-bold uppercase">Retur</span>
                                @elseif($jenisVal === 4 || $jenisStr === 'stock_opname')
                                    <span class="text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded text-xxs font-bold uppercase">Stock Opname</span>
                                @else
                                    <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded text-xxs font-bold uppercase">Reguler</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between md:justify-end gap-3">
                            <span class="font-bold text-slate-700 uppercase">Status Transaksi :</span>
                            <span class="text-slate-600 font-semibold">
                                @php
                                    $statusVal = (int)$barangKeluar->status;
                                    $statusStr = strtolower($barangKeluar->status_text ?? '');
                                @endphp

                                @if($statusVal === 2 || $statusStr === 'completed' || $statusStr === 'approved')
                                    <span class="text-green-600 bg-green-50 px-2.5 py-1 rounded text-xxs font-bold uppercase"><i class="fa fa-check mr-1"></i> Selesai</span>
                                @elseif($statusVal === 1 || $statusStr === 'pending')
                                    <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded text-xxs font-bold uppercase"><i class="fa fa-clock mr-1"></i> Menunggu (Pending)</span>
                                @elseif($statusVal === 3 || $statusStr === 'rejected')
                                    <span class="text-red-600 bg-red-50 px-2.5 py-1 rounded text-xxs font-bold uppercase"><i class="fa fa-times mr-1"></i> Ditolak (Rejected)</span>
                                @else
                                    <span class="text-slate-600 bg-slate-100 px-2.5 py-1 rounded text-xxs font-bold uppercase">{{ $barangKeluar->status_text }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between md:justify-end gap-3">
                            <span class="font-bold text-slate-700 uppercase">Gudang Pengirim :</span>
                            <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded text-xxs font-bold">{{ $barangKeluar->gudang ? $barangKeluar->gudang->nama_gudang : 'Gudang Terhapus' }} ({{ $barangKeluar->gudang_asal_kode }})</span>
                        </div>
                        @if(($jenisVal === 2 || $jenisStr === 'mutasi') && $barangKeluar->gudangTujuan)
                        <div class="flex justify-between md:justify-end gap-3">
                            <span class="font-bold text-slate-700 uppercase">Gudang Tujuan :</span>
                            <span class="text-sky-600 bg-sky-50 px-2.5 py-1 rounded text-xxs font-bold">{{ $barangKeluar->gudangTujuan->nama_gudang }} ({{ $barangKeluar->gudang_tujuan_kode }})</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto my-6">
                <table class="w-full border-collapse border border-slate-200">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xxs font-bold uppercase text-slate-600 tracking-wider">
                            <th class="p-3 border-r border-slate-200 text-center w-[5%]">No</th>
                            <th class="p-3 border-r border-slate-200 text-left w-[25%]">Kode Barang</th>
                            <th class="p-3 border-r border-slate-200 text-left w-[45%]">Nama Barang</th>
                            <th class="p-3 border-r border-slate-200 text-center w-[15%]">Quantity</th>
                            <th class="p-3 text-center w-[10%]">Satuan</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-slate-700">
                        @foreach($barangKeluar->details as $index => $detail)
                        <tr class="border-b border-slate-200 hover:bg-slate-50/50">
                            <td class="p-3 border-r border-slate-200 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="p-3 border-r border-slate-200 font-bold text-slate-800">{{ $detail->barang->kode_barang }}</td>
                            <td class="p-3 border-r border-slate-200 leading-normal">{{ $detail->barang->nama_barang }}</td>
                            <td class="p-3 border-r border-slate-200 text-center font-bold text-slate-800">{{ number_format($detail->qty, 0, ',', '.') }}</td>
                            <td class="p-3 text-center font-semibold">{{ $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 font-bold text-slate-800 text-xs">
                            <td colspan="3" class="p-3 border-r border-slate-200 text-right uppercase">Total Jumlah :</td>
                            <td class="p-3 border-r border-slate-200 text-center text-sm font-extrabold text-blue-600 bg-blue-50/30">
                                {{ number_format($barangKeluar->details->sum('qty'), 0, ',', '.') }}
                            </td>
                            <td class="p-3 bg-slate-50"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>







            </div>
        </div>
    </div>
</div>
@endsection

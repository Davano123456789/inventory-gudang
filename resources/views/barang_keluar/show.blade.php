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

        <!-- Action Panel -->
        <div class="mb-4 flex gap-2 no-print">
            <a href="{{ route('barang-keluar.index') }}" class="inline-block px-6 py-3 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-white leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl active:opacity-85 tracking-tight border">
                <i class="fa fa-arrow-left mr-1"></i> Kembali ke Riwayat
            </a>
            <button type="button" onclick="window.print()" class="inline-block px-6 py-3 font-bold text-center text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg cursor-pointer leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl active:opacity-85 tracking-tight">
                <i class="fa fa-print mr-1"></i> Cetak Surat Jalan
            </button>
        </div>

        <!-- Surat Jalan Paper Template -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border p-8" id="printableArea">
            
            <!-- Document Header -->
            <div class="flex flex-wrap justify-between items-start border-b pb-6 mb-6">
                <!-- Left: Sender Info & Recipient Info -->
                <div class="w-full md:w-3/5">
                    <h5 class="font-extrabold text-red-600 text-2xl tracking-wide uppercase m-0 leading-none">PT. BINTANG CAKRA KENCANA</h5>
                    <p class="text-[10px] text-slate-500 font-bold mt-1">BANYUDONO JL. RAYA SOLO SEMARANG KM 15 BOYOLALI</p>
                    

                </div>

                <!-- Right: Document Metadata & Boxed Number -->
                <div class="w-full md:w-2/5 md:text-right mt-6 md:mt-0 flex flex-col md:items-end">
                    <div class="border-2 border-red-500 text-red-600 rounded-lg px-6 py-2 text-center font-bold text-lg tracking-wider mb-2 bg-red-50/50 w-44">
                        @php
                            $dashIndex = strpos($barangKeluar->no_surat_jalan, '/');
                            $cleanNum = $dashIndex !== false ? substr($barangKeluar->no_surat_jalan, 0, $dashIndex) : $barangKeluar->no_surat_jalan;
                        @endphp
                        {{ $cleanNum }}
                        <div class="text-[9px] uppercase tracking-widest text-slate-400 mt-0.5 border-t border-red-200 pt-0.5 font-bold">ASLI SURAT JALAN</div>
                    </div>

                    <div class="flex flex-col gap-1.5 text-xs text-left w-full md:w-auto md:min-w-[280px] mt-4">
                        <div class="flex justify-between md:justify-start">
                            <span class="w-40 font-bold text-slate-700 uppercase md:text-right md:pr-3">No Surat Jalan :</span>
                            <span class="text-slate-800 font-bold">{{ $barangKeluar->no_surat_jalan }}</span>
                        </div>
                        <div class="flex justify-between md:justify-start">
                            <span class="w-40 font-bold text-slate-700 uppercase md:text-right md:pr-3">Tgl Surat Jalan :</span>
                            <span class="text-slate-600">{{ $barangKeluar->tanggal_surat_jalan->format('d-m-Y') }}</span>
                        </div>
                        <div class="flex justify-between md:justify-start">
                            <span class="w-40 font-bold text-slate-700 uppercase md:text-right md:pr-3">Tgl Pengiriman :</span>
                            <span class="text-slate-600">{{ $barangKeluar->tanggal_keluar->format('d-m-Y') }}</span>
                        </div>
                        <div class="flex justify-between md:justify-start">
                            <span class="w-40 font-bold text-slate-700 uppercase md:text-right md:pr-3">Jenis Transaksi :</span>
                            <span class="text-slate-600 font-semibold uppercase">{{ $barangKeluar->jenis }}</span>
                        </div>
                        <div class="flex justify-between md:justify-start">
                            <span class="w-40 font-bold text-slate-700 uppercase md:text-right md:pr-3">Status :</span>
                            @if($barangKeluar->status === 'completed')
                                <span class="text-green-600 font-bold uppercase">Selesai</span>
                            @elseif($barangKeluar->status === 'pending')
                                <span class="text-orange-600 font-bold uppercase">Menunggu (Pending)</span>
                            @elseif($barangKeluar->status === 'approved')
                                <span class="text-blue-600 font-bold uppercase">Diterima (Approved)</span>
                            @else
                                <span class="text-slate-600 font-bold uppercase">{{ $barangKeluar->status }}</span>
                            @endif
                        </div>
                        @if($barangKeluar->jenis === 'mutasi' && $barangKeluar->gudangTujuan)
                        <div class="flex justify-between md:justify-start">
                            <span class="w-40 font-bold text-slate-700 uppercase md:text-right md:pr-3">Gudang Tujuan :</span>
                            <span class="text-sky-600 font-bold">{{ $barangKeluar->gudangTujuan->nama_gudang }}</span>
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
@endsection

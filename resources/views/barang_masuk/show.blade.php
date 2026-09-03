@extends('layouts.masterDashboard')

@section('title', 'Detail Surat Jalan - Inventory Gudang')
@section('page_title', 'Detail Surat Jalan')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-4">
            
            <!-- Card Header Actions -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <h6 class="font-bold text-slate-800 text-lg">Informasi Surat Jalan</h6>
                <div class="flex gap-2">
                    <button type="button" onclick="window.print()" class="inline-block px-5 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-slate-600 to-slate-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-print mr-1"></i> Cetak / Print
                    </button>
                    <a href="{{ request('ref') === 'rekap' ? route('laporan.rekap') : route('barang-masuk.index') }}" class="inline-block px-5 py-2.5 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight">
                        <i class="fa fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Card Body (Printable Area) -->
            <div class="flex-auto p-6" id="printableArea">
                
                <!-- Surat Jalan Header Block -->
                <div class="mb-6 w-full flex flex-wrap justify-between items-start">
                    <!-- Left Block: Company Name -->
                    <div>
                        <h5 class="font-extrabold text-red-600 text-2xl tracking-wide uppercase m-0 leading-none">PT. BINTANG CAKRA KENCANA</h5>
                        <p class="text-[10px] text-slate-500 font-bold mt-1 mb-0">BANYUDONO JL. RAYA SOLO SEMARANG KM 15 BOYOLALI</p>
                    </div>
                    
                    <!-- Right Block: Surat Jalan Title -->
                    <div class="text-right md:text-right text-left mt-2 md:mt-0">
                        <h5 class="font-extrabold text-red-600 text-xl uppercase tracking-wider leading-none mb-1">Surat Jalan Masuk</h5>
                    </div>
                </div>

                <!-- Metadata Rows -->
                <div class="flex flex-wrap justify-between items-start border-b pb-6 mb-6">
                        <!-- Left Details -->
                        <div class="w-full md:w-1/2 pr-0 md:pr-4">
                            <div class="flex flex-col gap-2 text-xs">
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">No Surat Jalan :</span>
                                    <span class="text-slate-600 font-semibold">{{ $barangMasuk->no_surat_jalan }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">Tgl Surat Jalan :</span>
                                    <span class="text-slate-600">{{ $barangMasuk->tanggal_surat_jalan->format('d F Y') }}</span>
                                </div>

                                @if($barangMasuk->jenis_transaksi !== 'Mutasi')
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">Pengirim (Dari) :</span>
                                    <span class="text-slate-600">{{ $barangMasuk->pengirim ?: '-' }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right Details -->
                        <div class="w-full md:w-1/2 mt-6 md:mt-0 flex flex-col md:items-end">
                            <div class="flex flex-col gap-2 text-xs w-full md:w-auto text-left">
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">Jenis Transaksi :</span>
                                    <span class="text-slate-600 font-semibold">
                                        @if($barangMasuk->jenis_transaksi === 'Mutasi' || $barangMasuk->jenis_transaksi === 'Mutasi Ditolak')
                                            Mutasi
                                        @elseif($barangMasuk->jenis_transaksi === 'Retur' || $barangMasuk->jenis_transaksi === 'Return')
                                            Retur
                                        @elseif($barangMasuk->jenis_transaksi === 'Stock Opname')
                                            Stock Opname
                                        @else
                                            Reguler
                                        @endif
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">Status Transaksi :</span>
                                    <span class="text-slate-600 font-semibold">
                                        @if($barangMasuk->status === 'approved')
                                            Diterima
                                        @elseif($barangMasuk->status === 'rejected' || $barangMasuk->jenis_transaksi === 'Mutasi Ditolak')
                                            Ditolak
                                        @else
                                            Selesai
                                        @endif
                                    </span>
                                </div>
                                @if($barangMasuk->jenis_transaksi === 'Mutasi' && $barangMasuk->gudangAsal)
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">Gudang Asal :</span>
                                    <span class="text-slate-600 font-semibold">{{ $barangMasuk->gudangAsal->nama_gudang }} ({{ $barangMasuk->gudang_asal_kode }})</span>
                                </div>
                                @endif
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">Gudang Penerima :</span>
                                    <span class="text-slate-600 font-semibold">{{ $barangMasuk->gudang ? $barangMasuk->gudang->nama_gudang : 'Gudang Terhapus' }} ({{ $barangMasuk->gudang_tujuan_kode }})</span>
                                </div>
                                <div class="flex">
                                    <span class="w-36 font-bold text-slate-700 uppercase">Di-input Oleh :</span>
                                    <span class="text-slate-600">{{ $barangMasuk->user ? $barangMasuk->user->name : 'System' }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                <!-- Items Table -->
                <h6 class="font-bold text-slate-800 text-md mb-3 pl-1">Daftar Rincian Barang</h6>
                <div class="overflow-x-auto border border-slate-200 rounded-2xl mb-8">
                    <table class="items-center w-full mb-0 align-top text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-b border-slate-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-b border-slate-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Kode Barang</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-b border-slate-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Nama Barang</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-slate-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">BOX</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-slate-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">QTY (Total)</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-slate-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangMasuk->details as $index => $detail)
                            <tr>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-xs font-semibold leading-normal text-slate-600">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-xs font-bold leading-normal text-slate-700">{{ $detail->barang->kode_barang }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b shadow-none">
                                    <span class="text-xs leading-normal text-slate-600">{{ $detail->barang->nama_barang }}</span>
                                    @if($detail->barang->source === 'masuk_baru')
                                        <span class="ml-2 text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100 shadow-sm">Barang Baru</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-xs leading-normal text-slate-600">{{ number_format($detail->qty_box, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-xs font-bold leading-normal text-slate-700">{{ number_format($detail->qty_total, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($detail->barang->satuan)
                                        <span class="text-xs font-semibold leading-normal text-slate-700">{{ $detail->barang->satuan->nama_satuan }}</span>
                                    @else
                                        <span class="text-xs font-semibold leading-normal text-slate-700">Pcs</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            <!-- Total Row -->
                            <tr class="bg-slate-50/50">
                                <td colspan="3" class="px-6 py-4 font-bold text-slate-700 text-xs text-right uppercase">Total Jumlah :</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700 text-xs">
                                    {{ number_format($barangMasuk->details->sum('qty_box'), 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700 text-xs">
                                    {{ number_format($barangMasuk->details->sum('qty_total'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>



            </div>
        </div>

    </div>
</div>

<style>
    @media print {
        @page { margin: 0; }
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
            margin: 0;
            padding: 0;
            border: 0;
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
            width: 100% !important;
        }
        .md\:w-1\/2 {
            width: 50% !important;
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
        .rounded-2xl, .rounded-xl {
            border-radius: 0 !important;
        }
        .border-2 {
            border-width: 1px !important;
        }
        aside, nav, footer, button, .no-print {
            display: none !important;
        }
    }
</style>
@endsection

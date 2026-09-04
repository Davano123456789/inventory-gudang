@extends('layouts.masterDashboard')

@section('title', 'Detail Surat Jalan Keluar - Inventory Gudang')
@section('page_title', 'Detail Barang Keluar')

@push('styles')
<style>
    /* Printing CSS overrides */
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
                    <button type="button" onclick="window.print()" class="inline-block px-5 py-2.5 font-bold text-center text-white uppercase align-middle transition-colors rounded-lg cursor-pointer bg-slate-600 hover:bg-slate-700 leading-pro text-xs tracking-tight">
                        <i class="fa fa-print mr-1"></i> Cetak Surat Jalan
                    </button>
                    <a href="{{ request('ref') === 'rekap' ? route('laporan.rekap') : route('barang-keluar.index') }}" class="inline-block px-5 py-2.5 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight">
                        <i class="fa fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Surat Jalan Paper Template (Printable Area) -->
            <div class="flex-auto p-8" id="printableArea">
            
            <!-- Document Header -->
            <div class="w-full mb-6">
                <h5 class="font-extrabold text-red-600 text-2xl tracking-wide uppercase m-0 leading-none">PT. BINTANG CAKRA KENCANA</h5>
                <p class="text-[10px] text-slate-500 font-bold mt-1 mb-0">BANYUDONO JL. RAYA SOLO SEMARANG KM 15 BOYOLALI</p>
            </div>

            <div class="flex flex-wrap justify-between items-start border-b pb-6 mb-6">
                <!-- Left: Sender Info & Document Details -->
                <div class="w-full md:w-1/2 pr-0 md:pr-4">
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

                <!-- Right: Badges -->
                <div class="w-full md:w-1/2 md:text-right mt-6 md:mt-0 flex flex-col md:items-end">
                    <div class="flex flex-col gap-2 text-xs w-full md:w-auto text-left">
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Jenis Transaksi :</span>
                            <span class="text-slate-600 font-semibold">
                                @php
                                    $jenisVal = (int)$barangKeluar->jenis;
                                    $jenisStr = strtolower($barangKeluar->jenis_text ?? '');
                                @endphp

                                @if($jenisVal === 2 || $jenisStr === 'mutasi')
                                    Mutasi
                                @elseif($jenisVal === 3 || $jenisStr === 'retur' || $jenisStr === 'return')
                                    Retur
                                @elseif($jenisVal === 4 || $jenisStr === 'stock_opname')
                                    Stock Opname
                                @else
                                    Reguler
                                @endif
                            </span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Status Transaksi :</span>
                            <span class="text-slate-600 font-semibold">
                                @php
                                    $statusVal = (int)$barangKeluar->status;
                                    $statusStr = strtolower($barangKeluar->status_text ?? '');
                                @endphp

                                @if($statusVal === 2 || $statusStr === 'completed' || $statusStr === 'approved')
                                    Selesai
                                @elseif($statusVal === 1 || $statusStr === 'pending')
                                    Menunggu (Pending)
                                @elseif($statusVal === 3 || $statusStr === 'rejected')
                                    Ditolak (Rejected)
                                @else
                                    {{ $barangKeluar->status_text }}
                                @endif
                            </span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Gudang Pengirim :</span>
                            <span class="text-slate-600 font-semibold">{{ $barangKeluar->gudang ? $barangKeluar->gudang->nama_gudang : 'Gudang Terhapus' }} ({{ $barangKeluar->gudang_asal_kode }})</span>
                        </div>
                        @if(($jenisVal === 2 || $jenisStr === 'mutasi') && $barangKeluar->gudangTujuan)
                        <div class="flex">
                            <span class="w-36 font-bold text-slate-700 uppercase">Gudang Tujuan :</span>
                            <span class="text-slate-600 font-semibold">{{ $barangKeluar->gudangTujuan->nama_gudang }} ({{ $barangKeluar->gudang_tujuan_kode }})</span>
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
                            <td class="p-3 border-r border-slate-200 text-center text-sm font-extrabold text-slate-800">
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

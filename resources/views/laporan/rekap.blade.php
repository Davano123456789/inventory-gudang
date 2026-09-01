@extends('layouts.masterDashboard')

@section('title', 'Laporan Rekap Stok - Inventory Gudang')
@section('page_title', 'Laporan Rekap Stok')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-6">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex flex-wrap justify-between items-start gap-4 hide-on-print">
                <div class="flex flex-col gap-2">
                    <h6 class="font-bold text-slate-800 text-lg leading-none">Laporan Pergerakan Stok Gudang</h6>
                    <p class="text-xs text-slate-500 font-semibold mb-0">Lihat mutasi masuk dan keluar barang dalam periode tertentu.</p>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="exportTableToExcel('rekapTable', 'Laporan_Rekap_Stok_Gudang')" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-green-600 to-emerald-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-file-excel mr-1"></i> Cetak Excel
                    </button>
                    <button onclick="window.print()" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-slate-600 to-slate-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-print mr-1"></i> Cetak PDF
                    </button>
                </div>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">
                <!-- Filter Form -->
                <form action="{{ route('laporan.rekap') }}" method="GET" class="mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-wrap gap-4 items-end hide-on-print">
                    


                    <div class="flex-1 min-w-[150px]">
                        <label for="start_date" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Mulai Tanggal</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <label for="end_date" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div class="w-full md:w-auto mt-4 md:mt-0">
                        <button type="submit" class="w-full px-6 py-2.5 font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md transition-colors text-xs tracking-wider">
                            <i class="fa fa-filter mr-1"></i> Terapkan
                        </button>
                    </div>
                </form>

                <!-- Print Header (Hidden on screen, visible on print) -->
                <div class="hidden print-header mb-6 text-center">
                    <h2 class="text-2xl font-bold text-slate-800 uppercase">Laporan Pergerakan Stok Gudang</h2>
                    @php
                        $gudangObj = $gudangs->firstWhere('kode_gudang', $selectedGudang);
                    @endphp
                    <p class="text-sm text-slate-600 font-semibold mt-1">Gudang: {{ $gudangObj ? $gudangObj->nama_gudang : '-' }}</p>
                    <p class="text-sm text-slate-600 font-semibold">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                </div>

                @if(!$selectedGudang)
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300 hide-on-print">
                        <i class="fa fa-warehouse text-4xl text-slate-300 mb-3"></i>
                        <h6 class="text-slate-500 font-bold">Silakan pilih gudang</h6>
                        <p class="text-xs text-slate-400">Pilih gudang pada filter di atas untuk melihat laporan rekap stok.</p>
                    </div>
                @elseif($reportData->isEmpty())
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                        <i class="fa fa-box-open text-4xl text-slate-300 mb-3"></i>
                        <h6 class="text-slate-500 font-bold">Tidak Ada Data</h6>
                        <p class="text-xs text-slate-400">Tidak ada pergerakan stok atau saldo barang untuk periode ini.</p>
                    </div>
                @else
                    <!-- Search & Entries Limit (Client Side) -->
                    <div class="mb-3 flex justify-between items-center hide-on-print">
                        <div class="flex items-center gap-3">
                            <!-- Entries Limit -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-slate-500 font-semibold">Tampilkan:</span>
                                <select id="entriesLimit" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                    <option value="10" selected>10 Baris</option>
                                    <option value="25">25 Baris</option>
                                    <option value="50">50 Baris</option>
                                    <option value="all">Semua</option>
                                </select>
                            </div>
                            <!-- Transaction Type Filter -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-slate-500 font-semibold">Jenis:</span>
                                <select id="filterType" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                    <option value="all" selected>Semua Transaksi</option>
                                    <option value="reguler">Reguler</option>
                                    <option value="stock_opname">Stock Opname</option>
                                    <option value="mutasi">Mutasi Antar Gudang</option>
                                    <option value="retur">Return</option>
                                    <option value="saldo_awal">Saldo Awal</option>
                                </select>
                            </div>
                        </div>
                        <div class="relative flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 shadow-soft-xs w-64">
                            <i class="fa fa-search text-slate-400 text-sm mr-2"></i>
                            <input type="text" id="searchInput" placeholder="Cari barang..." class="text-xs text-slate-700 bg-transparent border-0 focus:outline-none w-full font-semibold">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto print-table">
                        <table id="rekapTable" class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                            <thead class="align-bottom">
                                <tr class="bg-slate-50">
                                    <th class="px-4 py-3 font-bold text-left uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-slate-400 rounded-tl-lg">Tanggal</th>
                                    <th class="px-4 py-3 font-bold text-left uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-slate-400">Barang</th>
                                    <th class="px-4 py-3 font-bold text-left uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-slate-400">Jenis Transaksi</th>
                                    <th class="px-4 py-3 font-bold text-left uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-slate-400">Referensi Dokumen</th>
                                    <th class="px-4 py-3 font-bold text-right uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-slate-400">Saldo Awal</th>
                                    <th class="px-4 py-3 font-bold text-right uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-green-500">Masuk (+)</th>
                                    <th class="px-4 py-3 font-bold text-right uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-red-500">Keluar (-)</th>
                                    <th class="px-4 py-3 font-bold text-right uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-slate-800">Saldo Akhir</th>
                                    <th class="px-4 py-3 font-bold text-center uppercase align-middle border-b border-gray-200 shadow-none text-xxs tracking-wider text-slate-400 rounded-tr-lg hide-on-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                @foreach($reportData as $row)
                                @php
                                    $satuan = $row->barang && $row->barang->satuan ? $row->barang->satuan->nama_satuan : 'Unit';
                                    $ref = '-';
                                    $jenisLabel = 'Reguler';
                                    $jenisClass = 'text-blue-600 bg-blue-50';
                                    $jenisCode = 'reguler';
                                    $detailLink = null;

                                    if ($row->barangMasuk) {
                                        $jenisTrans = $row->barangMasuk->jenis_transaksi;
                                        $ref = 'Masuk: ' . $row->barangMasuk->no_surat_jalan;
                                        $detailLink = route('barang-masuk.show', $row->barangMasuk->id);
                                        
                                        if ($row->barangMasuk->status === 'rejected' || $jenisTrans === 'Mutasi Ditolak') {
                                            $jenisLabel = 'Mutasi Ditolak';
                                            $jenisClass = 'text-red-600 bg-red-50';
                                            $jenisCode = 'mutasi';
                                        } elseif ($jenisTrans === 'Stock Opname' || str_contains($row->keterangan ?? '', 'Stock Opname')) {
                                            $jenisLabel = 'Stock Opname';
                                            $jenisClass = 'text-indigo-600 bg-indigo-50';
                                            $jenisCode = 'stock_opname';
                                        } elseif ($jenisTrans === 'Mutasi') {
                                            $jenisLabel = 'Mutasi Masuk';
                                            $jenisClass = 'text-purple-600 bg-purple-50';
                                            $jenisCode = 'mutasi';
                                        } elseif ($jenisTrans === 'Retur' || $jenisTrans === 'Return') {
                                            $jenisLabel = 'Return';
                                            $jenisClass = 'text-amber-600 bg-amber-50';
                                            $jenisCode = 'retur';
                                        } else {
                                            $jenisLabel = 'Reguler';
                                            $jenisClass = 'text-blue-600 bg-blue-50';
                                            $jenisCode = 'reguler';
                                        }
                                    } elseif ($row->barangKeluar) {
                                        $jenisTrans = $row->barangKeluar->jenis;
                                        $detailLink = route('barang-keluar.show', $row->barangKeluar->id);
                                        if ($row->status === 'pending') {
                                            $ref = 'Mutasi Masuk Pending: ' . $row->barangKeluar->no_surat_jalan;
                                            $jenisLabel = 'Mutasi Masuk Pending';
                                            $jenisClass = 'text-yellow-600 bg-yellow-50';
                                            $jenisCode = 'mutasi';
                                        } elseif ($row->masuk > 0) {
                                            $ref = 'Mutasi Ditolak: ' . $row->barangKeluar->no_surat_jalan;
                                            $jenisLabel = 'Mutasi Ditolak';
                                            $jenisClass = 'text-red-600 bg-red-50';
                                            $jenisCode = 'mutasi';
                                        } elseif ($jenisTrans === 'stock_opname' || str_contains($row->keterangan ?? '', 'Stock Opname')) {
                                            $ref = 'Keluar: ' . $row->barangKeluar->no_surat_jalan;
                                            $jenisLabel = 'Stock Opname';
                                            $jenisClass = 'text-indigo-600 bg-indigo-50';
                                            $jenisCode = 'stock_opname';
                                        } elseif ($jenisTrans === 'mutasi') {
                                            $ref = 'Keluar: ' . $row->barangKeluar->no_surat_jalan;
                                            $jenisLabel = 'Mutasi Keluar';
                                            $jenisClass = 'text-purple-600 bg-purple-50';
                                            $jenisCode = 'mutasi';
                                        } else {
                                            $ref = 'Keluar: ' . $row->barangKeluar->no_surat_jalan;
                                            $jenisLabel = 'Reguler';
                                            $jenisClass = 'text-blue-600 bg-blue-50';
                                            $jenisCode = 'reguler';
                                        }
                                    } else {
                                        if (str_contains($row->keterangan ?? '', 'Stock Opname')) {
                                            $jenisLabel = 'Stock Opname';
                                            $jenisClass = 'text-indigo-600 bg-indigo-50';
                                            $jenisCode = 'stock_opname';
                                        } elseif (str_contains($row->keterangan ?? '', 'Stok Awal') || str_contains($row->keterangan ?? '', 'Saldo Awal')) {
                                            $jenisLabel = 'Saldo Awal';
                                            $jenisClass = 'text-slate-600 bg-slate-100';
                                            $jenisCode = 'saldo_awal';
                                        } else {
                                            $jenisLabel = 'Lainnya';
                                            $jenisClass = 'text-gray-600 bg-gray-100';
                                            $jenisCode = 'lainnya';
                                        }
                                        $ref = $row->keterangan ?: '-';
                                    }
                                @endphp
                                <tr class="report-row border-b border-slate-100 hover:bg-slate-50/50 transition-colors {{ $row->status === 'pending' ? 'bg-yellow-50/50' : '' }}" 
                                    data-search="{{ strtolower(($row->barang ? $row->barang->nama_barang : '') . ' ' . ($row->barang ? $row->barang->kode_barang : '')) }}"
                                    data-type="{{ $jenisCode }}">
                                    <td class="px-4 py-3 align-middle bg-transparent shadow-none">
                                        <span class="text-sm font-semibold text-slate-600">{{ $row->tanggal->format('d M Y') }}</span>
                                        @if($row->status === 'pending')
                                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold text-yellow-700 bg-yellow-100 rounded-md">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle bg-transparent shadow-none">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700">{{ $row->barang ? $row->barang->nama_barang : 'Barang Dihapus' }}</span>
                                            <span class="text-[10px] text-slate-400">Kode: {{ $row->barang ? $row->barang->kode_barang : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle bg-transparent shadow-none">
                                        <span class="text-[10px] font-bold uppercase leading-none {{ $jenisClass }} px-2.5 py-1 rounded-lg inline-block whitespace-nowrap">{{ $jenisLabel }}</span>
                                    </td>
                                    <td class="px-4 py-3 align-middle bg-transparent shadow-none">
                                        <span class="text-xs font-semibold text-slate-600">{{ $ref }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right align-middle bg-transparent shadow-none">
                                        <span class="text-sm font-semibold text-slate-600">{{ number_format($row->saldo_awal, 0, ',', '.') }} <span class="text-xs text-slate-400">{{ $satuan }}</span></span>
                                    </td>
                                    <td class="px-4 py-3 text-right align-middle bg-transparent shadow-none">
                                        <span class="text-sm font-bold text-green-600">{{ number_format($row->masuk, 0, ',', '.') }} <span class="text-xs text-green-400">{{ $satuan }}</span></span>
                                    </td>
                                    <td class="px-4 py-3 text-right align-middle bg-transparent shadow-none">
                                        <span class="text-sm font-bold text-red-600">{{ number_format($row->keluar, 0, ',', '.') }} <span class="text-xs text-red-400">{{ $satuan }}</span></span>
                                    </td>
                                    <td class="px-4 py-3 text-right align-middle bg-transparent shadow-none">
                                        @if($row->status === 'pending')
                                            <span class="text-sm font-bold text-slate-400">? <span class="text-xs">{{ $satuan }}</span></span>
                                        @else
                                            <span class="text-sm font-bold text-slate-800">{{ number_format($row->saldo_akhir, 0, ',', '.') }} <span class="text-xs text-slate-400">{{ $satuan }}</span></span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center align-middle bg-transparent shadow-none hide-on-print">
                                        @if($detailLink)
                                            <a href="{{ $detailLink }}" class="inline-block px-3 py-1 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors" title="Lihat Detail Transaksi">
                                                <i class="fa fa-info-circle mr-1"></i> Detail
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Container -->
                    <div id="paginationContainer" class="hidden px-2 py-4 flex flex-wrap items-center justify-between gap-3 hide-on-print border-t border-gray-100 mt-4">
                        <div class="text-xs text-slate-500 font-semibold">
                            Menampilkan <span id="paginationInfoStart" class="text-slate-700">0</span> - <span id="paginationInfoEnd" class="text-slate-700">0</span> dari <span id="paginationInfoTotal" class="text-slate-700">0</span> baris
                        </div>
                        <div class="flex items-center gap-1.5" id="paginationBtns">
                            <!-- Dynamic page buttons -->
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<style>
    .search-hidden, .paginated-hidden {
        display: none !important;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .hide-on-print {
            display: none !important;
        }
        .print-table, .print-table *, .print-header, .print-header * {
            visibility: visible;
        }
        .print-header {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print-table {
            position: absolute;
            left: 0;
            top: 100px;
            width: 100%;
        }
        /* Ensure rows are expanded for printing */
        .report-row {
            display: table-row !important;
        }
        @page { size: landscape; }
    }
</style>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- SheetJS for proper Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        let currentPage = 1;

        function filterAndPaginate() {
            let searchQuery = $("#searchInput").val().toLowerCase().trim();
            let selectedType = $("#filterType").val();
            
            // 1. Filter by search query and transaction type first
            let $matchingRows = $(".report-row").filter(function() {
                let $row = $(this);
                let rowSearchText = String($row.attr("data-search") || '').toLowerCase();
                let rowType = $row.attr("data-type") || 'all';
                
                let matchSearch = rowSearchText.indexOf(searchQuery) > -1;
                let matchType = (selectedType === 'all') || (selectedType === rowType);
                
                if (matchSearch && matchType) {
                    $row.removeClass("search-hidden");
                    return true;
                } else {
                    $row.addClass("search-hidden").addClass("paginated-hidden");
                    return false;
                }
            });

            let totalItems = $matchingRows.length;
            let limitVal = $("#entriesLimit").val();
            let limit = limitVal === 'all' ? totalItems : parseInt(limitVal);
            let itemsPerPage = limit || 10;
            
            let totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            // 2. Hide all matching rows and only show active page slice
            $(".report-row").addClass("paginated-hidden");
            
            let startIndex = (currentPage - 1) * itemsPerPage;
            let endIndex = startIndex + itemsPerPage;
            
            $matchingRows.slice(startIndex, endIndex).removeClass("paginated-hidden");

            if (totalItems === 0) {
                $("#paginationContainer").addClass("hidden").removeClass("flex");
                if ($("#emptySearchPlaceholder").length === 0) {
                    $("#reportTableBody").append(`
                        <tr id="emptySearchPlaceholder">
                            <td colspan="8" class="px-6 py-10 text-center align-middle bg-transparent shadow-none">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa fa-search text-3xl text-slate-300 mb-2"></i>
                                    <span class="text-xs text-slate-400 font-medium">Tidak ada baris rekap yang cocok dengan pencarian Anda.</span>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            } else {
                $("#paginationContainer").removeClass("hidden").addClass("flex");
                $("#emptySearchPlaceholder").remove();
                
                $("#paginationInfoStart").text(startIndex + 1);
                $("#paginationInfoEnd").text(Math.min(endIndex, totalItems));
                $("#paginationInfoTotal").text(totalItems);
            }

            // Render buttons
            let $btnContainer = $("#paginationBtns");
            $btnContainer.empty();
            
            if (totalPages > 1) {
                let prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-gray-100';
                $btnContainer.append(`
                    <button type="button" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-gray-200 rounded-md transition-colors ${prevDisabled}" id="prevPageBtn">
                        <i class="fa fa-chevron-left mr-1"></i>
                    </button>
                `);
                
                for (let i = 1; i <= totalPages; i++) {
                    if (totalPages > 6) {
                        if (i === 1 || i === totalPages || Math.abs(currentPage - i) <= 1) {
                            appendPageBtn($btnContainer, i, currentPage);
                        } else if (i === 2 && currentPage > 3) {
                            $btnContainer.append(`<span class="text-slate-400 text-xs px-1">...</span>`);
                        } else if (i === totalPages - 1 && currentPage < totalPages - 2) {
                            $btnContainer.append(`<span class="text-slate-400 text-xs px-1">...</span>`);
                        }
                    } else {
                        appendPageBtn($btnContainer, i, currentPage);
                    }
                }
                
                let nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-gray-100';
                $btnContainer.append(`
                    <button type="button" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-gray-200 rounded-md transition-colors ${nextDisabled}" id="nextPageBtn">
                        <i class="fa fa-chevron-right ml-1"></i>
                    </button>
                `);
            }
        }

        function appendPageBtn($container, pageNum, activePage) {
            let activeClass = pageNum === activePage 
                ? 'bg-blue-600 text-white border-blue-600 shadow-soft-md' 
                : 'bg-white text-slate-600 border-gray-200 hover:bg-gray-100';
            $container.append(`
                <button type="button" class="page-num-btn px-3 py-1.5 text-xs font-semibold rounded-md border transition-all ${activeClass}" data-page="${pageNum}">
                    ${pageNum}
                </button>
            `);
        }

        $("#searchInput").on("keyup", function() {
            currentPage = 1;
            filterAndPaginate();
        });
        
        $("#entriesLimit").on("change", function() {
            currentPage = 1;
            filterAndPaginate();
        });

        $("#filterType").on("change", function() {
            currentPage = 1;
            filterAndPaginate();
        });

        $(document).on("click", "#prevPageBtn", function() {
            if (currentPage > 1) {
                currentPage--;
                filterAndPaginate();
            }
        });

        $(document).on("click", "#nextPageBtn", function() {
            let selectedType = $("#filterType").val();
            let totalMatching = $(".report-row").filter(function() {
                let $row = $(this);
                let rowSearchText = String($row.attr("data-search") || '').toLowerCase();
                let rowType = $row.attr("data-type") || 'all';
                
                let matchSearch = rowSearchText.indexOf($("#searchInput").val().toLowerCase().trim()) > -1;
                let matchType = (selectedType === 'all') || (selectedType === rowType);
                
                return matchSearch && matchType;
            }).length;
            
            let limitVal = $("#entriesLimit").val();
            let limit = limitVal === 'all' ? totalMatching : parseInt(limitVal);
            let itemsPerPage = limit || 10;
            let totalPages = Math.ceil(totalMatching / itemsPerPage) || 1;

            if (currentPage < totalPages) {
                currentPage++;
                filterAndPaginate();
            }
        });

        $(document).on("click", ".page-num-btn", function() {
            currentPage = parseInt($(this).data("page"));
            filterAndPaginate();
        });

        // Run initial pagination
        filterAndPaginate();
    });

    function exportTableToExcel(tableId, filename = ''){
        // Gunakan SheetJS untuk menghasilkan file .xlsx asli tanpa warning
        let table = document.getElementById(tableId);
        let clone = table.cloneNode(true);
        
        // Hapus baris yang disembunyikan oleh PENCARIAN (bukan oleh pagination)
        clone.querySelectorAll('tr.search-hidden').forEach(el => el.remove());
        
        // Hapus kolom Aksi (kolom terakhir di th dan td)
        clone.querySelectorAll('th:last-child, td:last-child').forEach(el => el.remove());
        
        // Konversi tabel HTML menjadi workbook Excel sejati
        let wb = XLSX.utils.table_to_book(clone, {sheet: "Rekap Stok"});
        
        // Unduh file dengan ekstensi .xlsx
        XLSX.writeFile(wb, (filename ? filename : 'Laporan_Rekap') + '.xlsx');
    }
</script>
@endpush
@endsection

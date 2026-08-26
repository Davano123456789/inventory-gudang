@extends('layouts.masterDashboard')

@section('title', 'Riwayat Input Manual - Inventory Gudang')
@section('page_title', 'Riwayat Input Manual')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container (Table) -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex flex-wrap justify-between items-start gap-4">
                <!-- Left: Title and Limit Filter -->
                <div class="flex flex-col gap-2">
                    <h6 class="font-bold text-slate-800 text-lg leading-none">Riwayat Barang Input Manual</h6>
                    
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <!-- Pagination Limit Select (Client-Side) -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-500 font-semibold">Tampilkan:</span>
                            <select id="perPageSelect" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                <option value="10" selected>10 Baris</option>
                                <option value="20">20 Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="all">Semua Baris</option>
                            </select>
                        </div>

                        <!-- Gudang Filter Select (Client-Side) -->
                        <!-- Hidden active warehouse selector for JQuery -->
                        <input type="hidden" id="gudangFilterSelect" value="{{ Auth::user()->getActiveGudangCode() }}">
                        
                        <!-- Active Warehouse Name Display -->
                        <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1">
                            <span class="text-xs text-slate-500 font-semibold">Gudang Aktif:</span>
                            @php
                                $currentGudang = $gudangs->firstWhere('kode_gudang', Auth::user()->getActiveGudangCode());
                            @endphp
                            <span class="text-xs text-blue-600 font-bold">{{ $currentGudang ? $currentGudang->nama_gudang : 'Gudang Penugasan' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Search Input & Add Button -->
                <div class="flex flex-wrap items-center gap-3 ml-auto">
                    <!-- Instant JQuery Search Input -->
                    <div class="relative flex items-center bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 shadow-soft-xs">
                        <i class="fa fa-search text-slate-400 text-xs mr-2"></i>
                        <input type="text" id="tableSearch" placeholder="Cari barang instan..." class="text-xs text-slate-700 bg-transparent border-0 focus:outline-none w-48 font-semibold">
                    </div>

                    <!-- Add Barang Button -->
                    <a href="{{ route('barang-manual.create') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-plus mr-1"></i> Tambah Barang
                    </a>
                </div>
            </div>

            <!-- Card Body (Table) -->
            <div class="flex-auto px-0 pt-0 pb-2">
                <div class="p-0 overflow-x-auto">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Kode Barang</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Nama Barang</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Satuan</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Stok</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Stok Min</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="barangTableBody">
                            @forelse($barangs as $index => $barang)
                            @php
                                $totalStok = $barang->stokGudangs->sum('stok_sekarang');
                            @endphp
                            <tr class="barang-row" data-stok-all="{{ $totalStok }}"
                                @foreach($gudangs as $g)
                                    @php
                                        $gStock = $barang->stokGudangs->firstWhere('kode_gudang', $g->kode_gudang);
                                        $gStockVal = $gStock ? $gStock->stok_sekarang : 0;
                                    @endphp
                                    data-stok-gudang-{{ $g->kode_gudang }}="{{ $gStockVal }}"
                                @endforeach
                            >
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-semibold leading-normal text-slate-600">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-bold leading-normal text-slate-700">{{ $barang->kode_barang }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ $barang->nama_barang }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($barang->satuan)
                                    <span class="text-xs font-bold leading-normal text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">{{ $barang->satuan->nama_satuan }}</span>
                                    @else
                                    <span class="text-xs font-semibold leading-normal text-slate-400 bg-gray-50 px-2.5 py-1 rounded-lg">Belum Set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="stok-display text-sm font-bold text-slate-700">{{ number_format($totalStok, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ number_format($barang->stok_minimum, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <a href="{{ route('barang.show', $barang->id) }}" class="text-xs font-semibold leading-normal text-slate-400 hover:text-blue-600 mr-3 transition-colors">
                                        <i class="fa fa-eye mr-1"></i> Detail
                                    </a>
                                    @if(Auth::user() && (Auth::user()->isSuperAdmin() || $barang->created_by_user_id == Auth::id()))
                                    <a href="{{ route('barang.edit', $barang->id) }}" class="text-xs font-semibold leading-normal text-slate-400 hover:text-amber-600 mr-3 transition-colors">
                                        <i class="fa fa-edit mr-1"></i> Edit
                                    </a>
                                    <button type="button" onclick="openDeleteModal('{{ route('barang.destroy', $barang->id) }}', '{{ $barang->nama_barang }}', 'barang')" class="text-xs font-semibold leading-normal text-slate-400 hover:text-red-600 transition-colors">
                                        <i class="fa fa-trash mr-1"></i> Hapus
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa fa-boxes text-4xl text-slate-300 mb-3"></i>
                                        <span class="text-sm text-slate-400 font-medium">Belum ada data barang.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Client-Side Pagination Container -->
                <div id="paginationContainer" class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="text-xs text-slate-500 font-semibold">
                        Menampilkan <span id="paginationInfoStart" class="text-slate-700">0</span> - <span id="paginationInfoEnd" class="text-slate-700">0</span> dari <span id="paginationInfoTotal" class="text-slate-700">0</span> barang
                    </div>
                    <div class="flex items-center gap-1.5" id="paginationBtns">
                        <!-- Dynamic page buttons -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        let $rows = $("#barangTableBody tr");
        let $search = $("#tableSearch");
        let $perPage = $("#perPageSelect");
        let $gudangFilter = $("#gudangFilterSelect");
        
        let currentPage = 1;
        let itemsPerPage = parseInt($perPage.val()) || 10;
        
        function getMatchedRows() {
            let searchValue = $search.val().toLowerCase();
            let selectedGudang = $gudangFilter.val();
            let matched = [];
            
            $rows.each(function() {
                if ($(this).find('td[colspan]').length) return;
                
                // Filter by search query
                var rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(searchValue) > -1) {
                    matched.push(this);
                } else {
                    $(this).hide();
                }
            });
            return $(matched);
        }
        
        function renderTable() {
            let selectedGudang = $gudangFilter.val();
            
            // First update stocks for all rows based on selected warehouse
            $rows.each(function() {
                if ($(this).find('td[colspan]').length) return;
                
                let $row = $(this);
                let stockVal = 0;
                
                if (selectedGudang === 'all') {
                    stockVal = parseFloat($row.attr('data-stok-all')) || 0;
                } else {
                    stockVal = parseFloat($row.attr('data-stok-gudang-' + selectedGudang)) || 0;
                }
                
                let formattedStock = stockVal.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
                $row.find('.stok-display').text(formattedStock);
            });
            
            let $matched = getMatchedRows();
            let totalItems = $matched.length;
            
            // Adjust "all" option
            let limitVal = $perPage.val();
            let limit = limitVal === 'all' ? totalItems : parseInt(limitVal);
            itemsPerPage = limit || 10;
            
            let totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }
            if (currentPage < 1) {
                currentPage = 1;
            }
            
            let startIndex = (currentPage - 1) * itemsPerPage;
            let endIndex = startIndex + itemsPerPage;
            
            $matched.each(function(index) {
                if (index >= startIndex && index < endIndex) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            // If no items matched at all, and table is empty
            if (totalItems === 0) {
                if ($("#noDataSearchRow").length === 0) {
                    $("#barangTableBody").append(`
                        <tr id="noDataSearchRow">
                            <td colspan="7" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa fa-search text-3xl text-slate-300 mb-2"></i>
                                    <span class="text-sm text-slate-400 font-medium">Barang tidak ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            } else {
                $("#noDataSearchRow").remove();
            }
            
            let displayStart = totalItems > 0 ? startIndex + 1 : 0;
            let displayEnd = Math.min(endIndex, totalItems);
            $("#paginationInfoStart").text(displayStart);
            $("#paginationInfoEnd").text(displayEnd);
            $("#paginationInfoTotal").text(totalItems);
            
            let $btnContainer = $("#paginationBtns");
            $btnContainer.empty();
            
            if (totalPages > 1) {
                let prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-gray-100';
                $btnContainer.append(`
                    <button type="button" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-gray-200 rounded-md transition-colors ${prevDisabled}" id="prevPageBtn">
                        <i class="fa fa-chevron-left mr-1"></i> Prev
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
                        Next <i class="fa fa-chevron-right ml-1"></i>
                    </button>
                `);
            }
        }
        
        function appendPageBtn($container, pageNum, activePage) {
            let activeClass = pageNum === activePage 
                ? 'bg-blue-600 text-white border-blue-600' 
                : 'bg-white text-slate-600 border-gray-200 hover:bg-gray-100';
            $container.append(`
                <button type="button" class="page-num-btn px-3 py-1.5 text-xs font-semibold rounded-md border transition-all ${activeClass}" data-page="${pageNum}">
                    ${pageNum}
                </button>
            `);
        }
        
        $(document).on("click", "#prevPageBtn", function() {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
        
        $(document).on("click", "#nextPageBtn", function() {
            let totalItems = getMatchedRows().length;
            let totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
        
        $(document).on("click", ".page-num-btn", function() {
            currentPage = parseInt($(this).data("page"));
            renderTable();
        });
        
        $perPage.on("change", function() {
            currentPage = 1;
            renderTable();
        });
        
        $gudangFilter.on("change", function() {
            currentPage = 1;
            renderTable();
        });
        
        $search.on("keyup", function() {
            currentPage = 1;
            renderTable();
        });
        
        // Initial render
        renderTable();
    });
</script>
@endpush
@endsection

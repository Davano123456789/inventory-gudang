@extends('layouts.masterDashboard')

@section('title', 'Catat Stock Opname - Inventory Gudang')
@section('page_title', 'Stock Opname')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-6">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <h6 class="font-bold text-slate-800 text-lg">Buat Dokumen Stock Opname (Bulk)</h6>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">

                <!-- Alert Validation Errors -->
                @if($errors->any())
                    <div class="mb-6 p-4 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200">
                        <div class="flex items-center gap-2 mb-2 font-bold">
                            <i class="fa fa-exclamation-triangle"></i> Gagal Menyimpan Opname!
                        </div>
                        <ul class="list-disc pl-5 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('stock-opname.store') }}" method="POST" id="stockOpnameForm">
                    @csrf
                    
                    <!-- Hidden Status Input -->
                    <input type="hidden" name="status" id="statusField" value="Draft">
                    <input type="hidden" name="items_json" id="itemsJsonField">

                    <!-- Header Inputs: Date & Warehouse -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="tanggal_opname" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Tanggal Perhitungan <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_opname" id="tanggal_opname" value="{{ old('tanggal_opname', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="gudang_kode" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Gudang Target <span class="text-red-500">*</span></label>
                            <select id="gudang_kode_display" disabled class="w-full px-3 py-2 text-sm text-slate-700 bg-slate-100 border border-gray-300 rounded-lg focus:outline-none transition-colors cursor-not-allowed font-semibold">
                                @foreach($gudangs as $g)
                                    <option value="{{ $g->kode_gudang }}" {{ $g->kode_gudang == Auth::user()->getActiveGudangCode() ? 'selected' : '' }}>
                                        {{ $g->nama_gudang }} ({{ $g->kode_gudang }})
                                    </option>
                                @endforeach
                            </select>
                            <!-- Hidden input to submit the actual value and be accessed by jQuery -->
                            <input type="hidden" name="gudang_kode" id="gudang_kode" value="{{ Auth::user()->getActiveGudangCode() }}">
                        </div>


                    </div>

                    <!-- Dynamic Bulk Items Table -->
                    <div id="bulkTableContainer" class="hidden mb-6">
                        <div class="flex justify-between items-center mb-3 border-b pb-2">
                            <h6 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Daftar Barang & Kuantitas Fisik</h6>
                            
                            <div class="flex items-center gap-3">
                                <!-- Search -->
                                <div class="relative flex items-center bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 shadow-soft-xs">
                                    <i class="fa fa-search text-slate-400 text-xs mr-2"></i>
                                    <input type="text" id="searchInput" placeholder="Cari nama/kode..." class="text-xs text-slate-700 bg-transparent border-0 focus:outline-none w-48 font-semibold">
                                </div>
                                <!-- Entries -->
                                <select id="entriesLimit" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                    <option value="10" selected>10 Baris</option>
                                    <option value="25">25 Baris</option>
                                    <option value="50">50 Baris</option>
                                    <option value="all">Semua</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto bg-slate-50/50 rounded-2xl border border-slate-100 p-2">
                            <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500" id="bulkTable">
                                <thead class="align-bottom">
                                    <tr class="bg-white text-xxs uppercase tracking-wider text-slate-400 font-bold border-b border-gray-100">
                                        <th class="px-4 py-3 text-left w-[40%]">Nama Barang</th>
                                        <th class="px-4 py-3 text-center w-[15%]">Stok Sistem</th>
                                        <th class="px-4 py-3 text-center w-[20%]">Stok Fisik</th>
                                        <th class="px-4 py-3 text-center w-[15%]">Selisih</th>
                                        <th class="px-4 py-3 text-left w-[20%]">Keterangan / Alasan Selisih</th>
                                    </tr>
                                </thead>
                                <tbody id="bulkItemsTableBody" class="bg-white">
                                    <!-- Loaded dynamic rows -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Container -->
                        <div id="paginationContainer" class="hidden px-2 py-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-xs text-slate-500 font-semibold">
                                Menampilkan <span id="paginationInfoStart" class="text-slate-700">0</span> - <span id="paginationInfoEnd" class="text-slate-700">0</span> dari <span id="paginationInfoTotal" class="text-slate-700">0</span> barang
                            </div>
                            <div class="flex items-center gap-1.5" id="paginationBtns">
                                <!-- Dynamic page buttons -->
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start items-center gap-3 border-t pt-4">
                        <!-- Submit as Selesai -->
                        <button type="submit" onclick="setStatus('Selesai')" class="px-6 py-3 font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors text-xs tracking-wider">
                            <i class="fa fa-check mr-1"></i> Selesaikan Opname
                        </button>
                        
                        <!-- Submit as Draft -->
                        <button type="submit" onclick="setStatus('Draft')" class="px-6 py-3 font-bold text-slate-600 uppercase bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-xs tracking-wider">
                            Simpan Draf
                        </button>

                        <a href="{{ route('stock-opname.index') }}" class="px-6 py-3 font-bold text-slate-400 uppercase bg-transparent hover:text-slate-600 rounded-lg transition-colors text-xs tracking-wider ml-auto">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    function setStatus(statusVal) {
        document.getElementById('statusField').value = statusVal;
    }

    $(document).ready(function() {
        let currentPage = 1;

        function filterAndPaginate() {
            let searchQuery = $("#searchInput").val().toLowerCase().trim();
            
            let $matchingRows = $(".item-row").filter(function() {
                let $row = $(this);
                let rowSearchText = String($row.attr("data-search") || '').toLowerCase();
                return rowSearchText.indexOf(searchQuery) > -1;
            });

            let totalItems = $matchingRows.length;
            let limitVal = $("#entriesLimit").val();
            let limit = limitVal === 'all' ? totalItems : parseInt(limitVal);
            let itemsPerPage = limit || 10;
            
            let totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            $(".item-row").addClass("hidden");
            
            let startIndex = (currentPage - 1) * itemsPerPage;
            let endIndex = startIndex + itemsPerPage;
            
            $matchingRows.slice(startIndex, endIndex).removeClass("hidden");

            if (totalItems === 0) {
                $("#paginationContainer").addClass("hidden").removeClass("flex");
                if ($("#emptySearchPlaceholder").length === 0) {
                    $("#bulkItemsTableBody").append(`
                        <tr id="emptySearchPlaceholder">
                            <td colspan="5" class="px-6 py-10 text-center align-middle bg-transparent shadow-none">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa fa-search text-3xl text-slate-300 mb-2"></i>
                                    <span class="text-xs text-slate-400 font-medium">Tidak ada barang yang cocok dengan pencarian Anda.</span>
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

        $(document).on("click", "#prevPageBtn", function() {
            if (currentPage > 1) {
                currentPage--;
                filterAndPaginate();
            }
        });

        $(document).on("click", "#nextPageBtn", function() {
            let totalMatching = $(".item-row").filter(function() {
                let $row = $(this);
                let rowSearchText = $row.data("search") || '';
                return rowSearchText.indexOf($("#searchInput").val().toLowerCase().trim()) > -1;
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

        
        // Fetch all items with current stock for the selected warehouse
        function loadWarehouseItems() {
            let gudangKode = $("#gudang_kode").val();
            let $tableBody = $("#bulkItemsTableBody");
            let $container = $("#bulkTableContainer");
            
            if (!gudangKode) {
                $container.addClass("hidden");
                $tableBody.empty();
                return;
            }

            $.ajax({
                url: "{{ route('stock-opname.warehouse-items') }}",
                type: "GET",
                data: {
                    gudang_kode: gudangKode
                },
                beforeSend: function() {
                    $container.removeClass("hidden");
                    $tableBody.html(`
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center align-middle bg-transparent shadow-none">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa fa-spinner fa-spin text-slate-400 text-lg"></i>
                                    <span class="text-xs text-slate-400 font-semibold">Memuat stok barang gudang...</span>
                                </div>
                            </td>
                        </tr>
                    `);
                },
                success: function(response) {
                    $tableBody.empty();
                    if (response.status === 'success' && response.items.length > 0) {
                        response.items.forEach(function(item, index) {
                            let namaBarang = item.nama_barang || '';
                            let kodeBarang = item.kode_barang || '';
                            let searchData = (namaBarang + ' ' + kodeBarang).toLowerCase();
                            
                            let rowHtml = `
                                <tr class="item-row border-b border-slate-100 hover:bg-slate-50/20" data-unit="${item.unit}" data-search="${searchData}">
                                    <!-- Barang Details -->
                                    <td class="px-4 py-3.5 align-middle bg-transparent shadow-none">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700">${namaBarang}</span>
                                            <span class="text-[10px] text-slate-400">Kode: ${kodeBarang}</span>
                                        </div>
                                        <input type="hidden" class="barang-id-input" value="${item.barang_id}">
                                    </td>
                                    
                                    <!-- System Stock (Read-Only) -->
                                    <td class="px-4 py-3.5 text-center align-middle bg-transparent shadow-none">
                                        <span class="text-sm font-semibold text-slate-600">${item.stok_sistem.toLocaleString('id-ID')}</span>
                                        <input type="hidden" class="stok-sistem-input" value="${item.stok_sistem}">
                                    </td>
                                    
                                    <!-- Physical Stock Input -->
                                    <td class="px-4 py-3.5 text-center align-middle bg-transparent shadow-none">
                                        <div class="relative flex items-center justify-center">
                                            <input type="number" step="any" placeholder="-" class="stok-fisik-input w-32 text-center px-2.5 py-1.5 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-all font-semibold">
                                        </div>
                                    </td>
                                    
                                    <!-- Difference Badge -->
                                    <td class="px-4 py-3.5 text-center align-middle bg-transparent shadow-none">
                                        <span class="diff-badge text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-1.5 rounded-lg block w-36 mx-auto transition-all">-</span>
                                    </td>
                                    
                                    <!-- Remarks Input -->
                                    <td class="px-4 py-3.5 align-middle bg-transparent shadow-none">
                                        <input type="text" placeholder="Catat alasan selisih..." class="keterangan-input w-full px-3 py-1.5 text-xs text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                                    </td>
                                </tr>
                            `;
                            $tableBody.append(rowHtml);
                        });
                        currentPage = 1;
                        filterAndPaginate();
                    } else {
                        $tableBody.html(`
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center align-middle bg-transparent shadow-none">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa fa-boxes text-3xl text-slate-300 mb-2"></i>
                                        <span class="text-xs text-slate-400 font-medium">Tidak ada barang yang terdaftar di sistem.</span>
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function() {
                    $tableBody.html(`
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center align-middle bg-transparent shadow-none">
                                <span class="text-xs text-red-500 font-semibold">Gagal memuat barang. Silakan hubungi admin atau muat ulang.</span>
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Live calculate difference for the specific row
        $(document).on("input", ".stok-fisik-input", function() {
            let $input = $(this);
            let $row = $input.closest(".item-row");
            
            let systemVal = parseFloat($row.find(".stok-sistem-input").val()) || 0;
            let physicalStr = $input.val();
            let $badge = $row.find(".diff-badge");

            if (physicalStr === "" || isNaN(physicalStr)) {
                $badge.text("-")
                      .removeClass("bg-red-50 text-red-600 bg-green-50 text-green-600 bg-blue-50 text-blue-600 border border-red-200 border-green-200 border-blue-200")
                      .addClass("text-slate-500");
                return;
            }

            let physicalVal = parseFloat(physicalStr);
            let diff = physicalVal - systemVal;

            $badge.removeClass("text-slate-500 bg-red-50 text-red-600 bg-green-50 text-green-600 bg-blue-50 text-blue-600 border border-red-200 border-green-200 border-blue-200");

            if (diff < 0) {
                $badge.text(diff.toLocaleString('id-ID') + " (Kurang)")
                      .addClass("bg-red-50 text-red-600 border border-red-200");
            } else if (diff > 0) {
                $badge.text("+" + diff.toLocaleString('id-ID') + " (Lebih)")
                      .addClass("bg-green-50 text-green-600 border border-green-200");
            } else {
                $badge.text("0 (Sesuai)")
                      .addClass("bg-blue-50 text-blue-600 border border-blue-200");
            }
        });

        // Trigger load on page load if already selected (e.g. locked/pre-selected)
        if ($("#gudang_kode").val()) {
            loadWarehouseItems();
        }

        // Event listener for warehouse change
        $("#gudang_kode").on("change", loadWarehouseItems);

        // Validate form before submission: must fill at least 1 physical stock input
        $("#stockOpnameForm").on("submit", function(e) {
            let statusVal = $("#statusField").val();
            let filledCount = 0;
            let itemsData = [];

            $(".item-row").each(function() {
                let $row = $(this);
                let barangId = $row.find(".barang-id-input").val();
                let stokSistem = $row.find(".stok-sistem-input").val();
                let stokFisik = $row.find(".stok-fisik-input").val().trim();
                let keterangan = $row.find(".keterangan-input").val().trim();

                if (stokFisik !== "") {
                    filledCount++;
                }

                // Push all rows so backend gets complete representation of counts entered
                itemsData.push({
                    barang_id: barangId,
                    stok_sistem: stokSistem,
                    stok_fisik: stokFisik !== "" ? parseFloat(stokFisik) : null,
                    keterangan: keterangan !== "" ? keterangan : null
                });
            });
            
            if (filledCount === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Kesalahan!',
                    text: 'Anda harus mengisi minimal 1 kolom stok fisik sebelum menyimpan!',
                    icon: 'error',
                    confirmButtonColor: '#ea0606',
                    customClass: {
                        popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                        confirmButton: 'text-white bg-red-600 hover:bg-red-700 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
                    }
                });
                return;
            }

            // Save JSON string in hidden field
            $("#itemsJsonField").val(JSON.stringify(itemsData));

            if (statusVal === 'Selesai') {
                e.preventDefault();
                Swal.fire({
                    title: 'Selesaikan Stock Opname?',
                    text: 'Dokumen yang sudah diselesaikan akan langsung memperbarui stok gudang dan tidak dapat diedit kembali!',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya, Selesaikan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                        confirmButton: 'text-white bg-blue-600 hover:bg-blue-700 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight mr-2',
                        cancelButton: 'text-slate-700 bg-gray-100 hover:bg-gray-200 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#stockOpnameForm").off("submit").submit();
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection

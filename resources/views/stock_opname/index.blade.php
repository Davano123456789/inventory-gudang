@extends('layouts.masterDashboard')

@section('title', 'Stock Opname - Inventory Gudang')
@section('page_title', 'Stock Opname')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex flex-wrap justify-between items-start gap-4">
                <!-- Left: Title and Limit Filter -->
                <div class="flex flex-col gap-2">
                    <h6 class="font-bold text-slate-800 text-lg leading-none">Daftar Dokumen Stock Opname</h6>
                    
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <!-- Entries per Page Dropdown (Client-Side) -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-500 font-semibold">Tampilkan:</span>
                            <select id="entriesLimit" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                <option value="10" selected>10 Baris</option>
                                <option value="25">25 Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="100">100 Baris</option>
                                <option value="all">Semua Baris</option>
                            </select>
                        </div>

                        <!-- Gudang Filter Dropdown (Client-Side) -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-500 font-semibold">Gudang:</span>
                            <select id="filterGudang" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                <option value="" selected>Semua Gudang</option>
                                @foreach($gudangs as $g)
                                    <option value="{{ $g->kode_gudang }}">{{ $g->nama_gudang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Right: Search Input & Add Button -->
                <div class="flex flex-wrap items-center gap-3 ml-auto">
                    <!-- Instant JQuery Search Input -->
                    <div class="relative flex items-center bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 shadow-soft-xs">
                        <i class="fa fa-search text-slate-400 text-xs mr-2"></i>
                        <input type="text" id="searchInput" placeholder="Cari nomor dokumen, keterangan..." class="text-xs text-slate-700 bg-transparent border-0 focus:outline-none w-48 font-semibold">
                    </div>

                    <!-- Add Button -->
                    <a href="{{ route('stock-opname.create') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-plus mr-1"></i> Buat Dokumen Opname
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="flex-auto px-0 pt-0 pb-2">
                <div class="p-0 overflow-x-auto">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No Dokumen</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Tanggal Perhitungan</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Gudang</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Status</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Petugas</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($opnames as $index => $op)
                            <tr class="op-row" data-gudang="{{ $op->gudang_kode }}" data-search="{{ strtolower($op->no_opname) }} {{ strtolower($op->user ? $op->user->name : 'system') }}">
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-semibold leading-normal text-slate-600">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-bold leading-normal text-slate-700">{{ $op->no_opname }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ $op->tanggal_opname->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-xs font-bold leading-normal text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg">{{ $op->gudang ? $op->gudang->nama_gudang : 'Gudang Terhapus' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($op->status === 'Draft')
                                        <span class="text-xs font-bold leading-normal text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">Draft</span>
                                    @else
                                        <span class="text-xs font-bold leading-normal text-white bg-gradient-to-tl from-blue-600 to-sky-400 px-2.5 py-1 rounded-lg shadow-soft-md">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ $op->user ? $op->user->name : 'System' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($op->status === 'Draft')
                                        @if(Auth::user() && Auth::user()->isSuperAdmin())
                                        <a href="{{ route('stock-opname.edit', $op->id) }}" class="text-xs font-semibold leading-normal text-slate-400 hover:text-amber-600 mr-3 transition-colors">
                                            <i class="fa fa-edit mr-1"></i> Edit (Lanjutkan)
                                        </a>
                                        <button type="button" onclick="openDeleteModal('{{ route('stock-opname.destroy', $op->id) }}', '{{ $op->no_opname }}', false)" class="text-xs font-semibold leading-normal text-slate-400 hover:text-red-600 transition-colors">
                                            <i class="fa fa-trash mr-1"></i> Hapus
                                        </button>
                                        @else
                                        <span class="text-xs font-semibold leading-normal text-slate-400">Tidak ada aksi</span>
                                        @endif
                                    @else
                                        <a href="{{ route('stock-opname.show', $op->id) }}" class="text-xs font-semibold leading-normal text-slate-400 hover:text-blue-600 mr-3 transition-colors">
                                            <i class="fa fa-eye mr-1"></i> Detail
                                        </a>
                                        @if(Auth::user() && Auth::user()->isSuperAdmin())
                                        <button type="button" onclick="openDeleteModal('{{ route('stock-opname.destroy', $op->id) }}', '{{ $op->no_opname }}', true)" class="text-xs font-semibold leading-normal text-slate-400 hover:text-red-600 transition-colors">
                                            <i class="fa fa-trash mr-1"></i> Hapus
                                        </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa fa-clipboard-check text-4xl text-slate-300 mb-3"></i>
                                        <span class="text-sm text-slate-400 font-medium">Belum ada riwayat dokumen stock opname.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Client-Side Pagination Container -->
            <div id="paginationContainer" class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500 font-semibold">
                    Menampilkan <span id="paginationInfoStart" class="text-slate-700">0</span> - <span id="paginationInfoEnd" class="text-slate-700">0</span> dari <span id="paginationInfoTotal" class="text-slate-700">0</span> dokumen
                </div>
                <div class="flex items-center gap-1.5" id="paginationBtns">
                    <!-- Dynamic page buttons -->
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Delete Confirmation (Form) -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    function openDeleteModal(actionUrl, identifierName, isFinalized) {
        let warningText = isFinalized 
            ? `Menghapus dokumen opname "${identifierName}" yang sudah selesai akan MENGEMBALIKAN penyesuaian stok gudang ke nilai sistem semula!`
            : `Menghapus draf dokumen opname "${identifierName}" tidak akan mempengaruhi stok gudang karena belum diposting.`;
            
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: warningText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea0606',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                confirmButton: 'text-white bg-red-600 hover:bg-red-700 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight mr-2',
                cancelButton: 'text-slate-700 bg-gray-100 hover:bg-gray-200 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = actionUrl;
                form.submit();
            }
        });
    }

    // Success Alerts and Search Filter Pagination
    document.addEventListener("DOMContentLoaded", function() {
        let currentPage = 1;

        function filterAndPaginate() {
            let searchQuery = $("#searchInput").val().toLowerCase().trim();
            let selectedGudang = $("#filterGudang").val();
            
            // Filter rows
            let $matchingRows = $(".op-row").filter(function() {
                let $row = $(this);
                let rowSearchText = $row.data("search") || '';
                let rowGudang = $row.data("gudang") || '';

                let matchSearch = rowSearchText.indexOf(searchQuery) > -1;
                let matchGudang = selectedGudang === "" || rowGudang === selectedGudang;

                return matchSearch && matchGudang;
            });

            let totalItems = $matchingRows.length;
            let limitVal = $("#entriesLimit").val();
            let limit = limitVal === 'all' ? totalItems : parseInt(limitVal);
            let itemsPerPage = limit || 10;
            
            let totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }
            if (currentPage < 1) {
                currentPage = 1;
            }

            // Hide all rows, show only current page rows
            $(".op-row").hide();
            
            let startIndex = (currentPage - 1) * itemsPerPage;
            let endIndex = startIndex + itemsPerPage;
            
            $matchingRows.slice(startIndex, endIndex).show();

            // Update Pagination Info Text and Footer Visibility
            if (totalItems === 0) {
                $("#paginationContainer").hide();
                
                // Show empty placeholder row
                if ($("#emptySearchPlaceholder").length === 0) {
                    $("tbody").append(`
                        <tr id="emptySearchPlaceholder">
                            <td colspan="8" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa fa-search text-3xl text-slate-300 mb-2"></i>
                                    <span class="text-xs text-slate-400 font-medium">Tidak ada dokumen yang cocok dengan kriteria Anda.</span>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            } else {
                $("#paginationContainer").show();
                $("#emptySearchPlaceholder").remove();
                
                let displayStart = startIndex + 1;
                let displayEnd = Math.min(endIndex, totalItems);
                $("#paginationInfoStart").text(displayStart);
                $("#paginationInfoEnd").text(displayEnd);
                $("#paginationInfoTotal").text(totalItems);
            }

            // Render Page Number Buttons
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
                ? 'bg-blue-600 text-white border-blue-600 shadow-soft-md' 
                : 'bg-white text-slate-600 border-gray-200 hover:bg-gray-100';
            $container.append(`
                <button type="button" class="page-num-btn px-3 py-1.5 text-xs font-semibold rounded-md border transition-all ${activeClass}" data-page="${pageNum}">
                    ${pageNum}
                </button>
            `);
        }

        // Event Handlers
        $("#searchInput").on("keyup", function() {
            currentPage = 1;
            filterAndPaginate();
        });
        
        $("#filterGudang").on("change", function() {
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
            let totalMatching = $(".op-row").filter(function() {
                let $row = $(this);
                let rowSearchText = $row.data("search") || '';
                let rowGudang = $row.data("gudang") || '';
                return (rowSearchText.indexOf($("#searchInput").val().toLowerCase().trim()) > -1) && ($("#filterGudang").val() === "" || rowGudang === $("#filterGudang").val());
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

        // Run on load
        filterAndPaginate();

        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'font-poppins rounded-2xl shadow-soft-2xl'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#ea0606',
                customClass: {
                    popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                    confirmButton: 'font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
                }
            });
        @endif
    });
</script>
@endpush
@endsection

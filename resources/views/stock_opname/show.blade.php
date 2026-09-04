@extends('layouts.masterDashboard')

@section('title', 'Detail Stock Opname - Inventory Gudang')
@section('page_title', 'Stock Opname')

@push('styles')
<style>
    @media print {
        @page { margin: 10mm; }
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
                
                <!-- Back button & Quick Adjustment Links -->
                <div class="flex flex-wrap items-center gap-2 no-print">

                    <a href="{{ route('stock-opname.index') }}" class="inline-block px-4 py-2 font-bold text-center text-slate-600 bg-gray-100 hover:bg-gray-200 uppercase align-middle transition-all rounded-lg cursor-pointer leading-pro text-xs ease-soft-in shadow-soft-xs hover:shadow-soft-md tracking-tight">
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

                <!-- Detail Table Header & Search -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                    <div class="flex items-center gap-4">
                        <h6 class="font-bold text-slate-800 text-xs uppercase tracking-wide m-0">Daftar Barang & Selisih Penyesuaian</h6>
                        <div class="flex items-center gap-1.5 no-print">
                            <span class="text-[10px] text-slate-500 font-semibold uppercase">Tampilkan:</span>
                            <select id="entriesLimit" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                <option value="10" selected>10 Baris</option>
                                <option value="25">25 Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="all">Semua Baris</option>
                            </select>
                        </div>
                    </div>
                    <div class="relative w-full sm:w-64 no-print">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-search text-slate-400 text-xs"></i>
                        </div>
                        <input type="text" id="searchTable" placeholder="Cari nama atau kode barang..." class="block w-full pl-9 pr-3 py-2 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>
                </div>
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
                            <tr class="border-b border-slate-100 hover:bg-slate-50/20 data-row">
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
                                    <span class="text-sm leading-normal text-slate-600 font-semibold">{{ number_format($detail->stok_sistem, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent shadow-none">
                                    <span class="text-sm font-bold leading-normal text-slate-700">{{ number_format($detail->stok_fisik, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent shadow-none">
                                    @if($detail->selisih < 0)
                                        <span class="text-xs font-bold leading-normal text-red-600 bg-red-50 px-2.5 py-1 rounded-lg">
                                            {{ number_format($detail->selisih, 0, ',', '.') }}
                                        </span>
                                    @elseif($detail->selisih > 0)
                                        <span class="text-xs font-bold leading-normal text-green-600 bg-green-50 px-2.5 py-1 rounded-lg">
                                            +{{ number_format($detail->selisih, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold leading-normal text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">
                                            0
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

                <!-- Pagination Container -->
                <div id="paginationControls" class="flex justify-between items-center mt-4 px-2 no-print">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <span id="pageStart">0</span> - <span id="pageEnd">0</span> dari <span id="totalItems">0</span> barang
                    </div>
                    <div class="flex gap-1" id="paginationButtons">
                        <!-- Buttons injected via JS -->
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchTable');
        const rows = Array.from(document.querySelectorAll('.data-row'));
        const paginationButtons = document.getElementById('paginationButtons');
        const pageStart = document.getElementById('pageStart');
        const pageEnd = document.getElementById('pageEnd');
        const totalItemsLabel = document.getElementById('totalItems');
        const noDataRow = document.getElementById('noDataRow'); // we need to add this ID if it exists, or handle it
        const entriesLimitSelect = document.getElementById('entriesLimit');
        
        let itemsPerPage = 10;
        let currentPage = 1;
        let filteredRows = rows;

        function renderTable() {
            // Hide all rows first
            rows.forEach(row => row.style.display = 'none');
            
            // Calculate limits
            const totalItems = filteredRows.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);

            // Show current page rows
            for (let i = startIndex; i < endIndex; i++) {
                filteredRows[i].style.display = '';
            }

            // Update Labels
            totalItemsLabel.textContent = totalItems;
            pageStart.textContent = totalItems > 0 ? startIndex + 1 : 0;
            pageEnd.textContent = endIndex;

            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            paginationButtons.innerHTML = '';
            if (totalPages <= 1) return;

            // Prev Button
            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '<i class="fa fa-chevron-left text-xs"></i>';
            prevBtn.className = `px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors ${currentPage === 1 ? 'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed' : 'bg-white text-slate-600 border-gray-200 hover:bg-gray-50'}`;
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => { currentPage--; renderTable(); };
            paginationButtons.appendChild(prevBtn);

            // Page Numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = `px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors ${i === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-gray-200 hover:bg-gray-50'}`;
                pageBtn.onclick = () => { currentPage = i; renderTable(); };
                paginationButtons.appendChild(pageBtn);
            }

            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '<i class="fa fa-chevron-right text-xs"></i>';
            nextBtn.className = `px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed' : 'bg-white text-slate-600 border-gray-200 hover:bg-gray-50'}`;
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => { currentPage++; renderTable(); };
            paginationButtons.appendChild(nextBtn);
        }

        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                const term = e.target.value.toLowerCase();
                filteredRows = rows.filter(row => row.textContent.toLowerCase().includes(term));
                currentPage = 1;
                renderTable();
            });
        }
        
        if (entriesLimitSelect) {
            entriesLimitSelect.addEventListener('change', function(e) {
                const val = e.target.value;
                if (val === 'all') {
                    itemsPerPage = filteredRows.length > 0 ? filteredRows.length : 1;
                } else {
                    itemsPerPage = parseInt(val, 10);
                }
                currentPage = 1;
                renderTable();
            });
        }
        
        // Initial render
        renderTable();
    });
</script>
@endpush
@endsection

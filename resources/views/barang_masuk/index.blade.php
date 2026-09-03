@extends('layouts.masterDashboard')

@section('title', 'Barang Masuk - Inventory Gudang')
@section('page_title', 'Barang Masuk')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        @if(isset($pendingMutasi) && count($pendingMutasi) > 0)
        <!-- Pending Mutasi Card -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-6 border-l-4 border-orange-400">
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl">
                <h6 class="font-bold text-orange-600 text-lg leading-none mb-1"><i class="fa fa-bell mr-2"></i> Persetujuan Mutasi Masuk</h6>
                <p class="text-sm text-slate-500 mb-0">Terdapat {{ count($pendingMutasi) }} mutasi barang yang dikirim ke gudang Anda dan menunggu persetujuan (Terima Barang).</p>
            </div>
            <div class="flex-auto px-0 pt-4 pb-2">
                <div class="p-0 overflow-x-auto">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-y border-gray-200 shadow-none text-xxs text-slate-400 opacity-70">Surat Jalan</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-y border-gray-200 shadow-none text-xxs text-slate-400 opacity-70">Gudang Pengirim</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-y border-gray-200 shadow-none text-xxs text-slate-400 opacity-70">Tanggal Kirim</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-y border-gray-200 shadow-none text-xxs text-slate-400 opacity-70">Barang & Qty</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-y border-gray-200 shadow-none text-xxs text-slate-400 opacity-70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingMutasi as $mutasi)
                            <tr>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-bold leading-normal text-slate-700">{{ $mutasi->no_surat_jalan }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-xs font-bold leading-normal text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg">{{ $mutasi->gudang ? $mutasi->gudang->nama_gudang : 'Gudang Terhapus' }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ $mutasi->tanggal_keluar->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b shadow-none">
                                    <ul class="list-disc pl-4 text-xs text-slate-600">
                                        @foreach($mutasi->details->take(2) as $det)
                                            <li>{{ $det->barang->nama_barang }} ({{ $det->qty }} {{ $det->barang->satuan ? $det->barang->satuan->nama_satuan : '' }})</li>
                                        @endforeach
                                        @if($mutasi->details->count() > 2)
                                            <li class="italic text-slate-400">+ {{ $mutasi->details->count() - 2 }} barang lainnya</li>
                                        @endif
                                    </ul>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none text-center">
                                    @if(Auth::user() && Auth::user()->role !== 'staff_gudang')
                                    <form action="{{ route('mutasi.approve', $mutasi->id) }}" method="POST" class="inline-block mr-1">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded shadow-md transition-colors" onclick="confirmTerima(event, this.closest('form'))">
                                            <i class="fa fa-check mr-1"></i> Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('mutasi.reject', $mutasi->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white uppercase bg-red-600 hover:bg-red-700 rounded shadow-md transition-colors" onclick="confirmTolak(event, this.closest('form'))">
                                            <i class="fa fa-times mr-1"></i> Tolak
                                        </button>
                                    </form>
                                    @else
                                        <span class="text-xs font-semibold leading-normal text-slate-400">Menunggu Approval Admin/Kepala</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex flex-wrap justify-between items-start gap-4">
                <!-- Left: Title and Limit Filter -->
                <div class="flex flex-col gap-2">
                    <h6 class="font-bold text-slate-800 text-lg leading-none">Riwayat Surat Jalan Barang Masuk</h6>
                    
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

                        <!-- Filter Dropdown (Jenis Transaksi) -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-500 font-semibold">Jenis:</span>
                            <select id="filterJenis" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                                <option value="" selected>Semua Jenis</option>
                                <option value="1">Reguler</option>
                                <option value="3">Retur</option>
                                <option value="4">Stock Opname</option>
                            </select>
                        </div>
                        
                        <!-- Filter Tanggal -->
                        <div class="flex items-center gap-1.5 ml-2">
                            <span class="text-xs text-slate-500 font-semibold">Dari:</span>
                            <input type="date" id="filterDateStart" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-500 font-semibold">Sampai:</span>
                            <input type="date" id="filterDateEnd" class="text-xs text-slate-600 bg-white border border-gray-200 rounded-lg p-1.5 focus:outline-none cursor-pointer font-semibold shadow-soft-xs">
                        </div>
                    </div>
                </div>

                <!-- Right: Search Input & Add Button -->
                <div class="flex flex-wrap items-center gap-3 ml-auto">
                    <!-- Instant JQuery Search Input -->
                    <div class="relative flex items-center bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 shadow-soft-xs">
                        <i class="fa fa-search text-slate-400 text-xs mr-2"></i>
                        <input type="text" id="searchInput" placeholder="Cari surat jalan..." class="text-xs text-slate-700 bg-transparent border-0 focus:outline-none w-48 font-semibold">
                    </div>

                    <!-- Export Excel Button -->
                    <button onclick="exportTableToExcel('masukTable', 'Riwayat_Barang_Masuk')" class="inline-block px-4 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-green-600 to-emerald-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-file-excel mr-1"></i> Cetak Excel
                    </button>
                    <!-- Add Button -->
                    <a href="{{ route('barang-masuk.create') }}" class="inline-block px-4 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                        <i class="fa fa-plus mr-1"></i> Catat Masuk
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="flex-auto px-0 pt-0 pb-2">
                <div class="p-0 overflow-x-auto">
                    <table id="masukTable" class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No Surat Jalan</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Jenis</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Status</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Tanggal Masuk</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Pengirim</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Gudang Penerima</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">User Input</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $index => $tx)
                            <tr class="tx-row" data-date="{{ $tx->tanggal_masuk->format('Y-m-d') }}" data-jenis="{{ $tx->jenis_transaksi }}" data-search="{{ strtolower($tx->no_surat_jalan) }} {{ strtolower($tx->pengirim) }} {{ strtolower($tx->gudang ? $tx->gudang->nama_gudang : '') }} {{ strtolower($tx->user ? $tx->user->name : 'system') }}">
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-semibold leading-normal text-slate-600">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-bold leading-normal text-slate-700">{{ $tx->no_surat_jalan }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($tx->jenis_transaksi == \App\Models\BarangMasuk::JENIS_MUTASI)
                                        <span class="text-xs font-bold leading-normal text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg">Mutasi</span>
                                    @elseif($tx->jenis_transaksi == \App\Models\BarangMasuk::JENIS_RETURN)
                                        <span class="text-xs font-bold leading-normal text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Retur</span>
                                    @elseif($tx->jenis_transaksi == \App\Models\BarangMasuk::JENIS_STOCK_OPNAME)
                                        <span class="text-xs font-bold leading-normal text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Stock Opname</span>
                                    @else
                                        <span class="text-xs font-bold leading-normal text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Reguler</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($tx->status == \App\Models\BarangMasuk::STATUS_COMPLETED)
                                        @if($tx->jenis_transaksi == \App\Models\BarangMasuk::JENIS_MUTASI)
                                            <span class="text-xs font-bold leading-normal text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg"><i class="fa fa-check-double mr-1"></i> Diterima</span>
                                        @else
                                            <span class="text-xs font-bold leading-normal text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg"><i class="fa fa-check mr-1"></i> Selesai</span>
                                        @endif
                                    @elseif($tx->status == \App\Models\BarangMasuk::STATUS_REJECTED)
                                        <span class="text-xs font-bold leading-normal text-red-600 bg-red-50 px-2.5 py-1 rounded-lg"><i class="fa fa-times mr-1"></i> Ditolak</span>
                                    @elseif($tx->status == \App\Models\BarangMasuk::STATUS_PENDING)
                                        <span class="text-xs font-bold leading-normal text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg"><i class="fa fa-clock mr-1"></i> Menunggu</span>
                                    @else
                                        <span class="text-xs font-bold leading-normal text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg uppercase">{{ $tx->status_text }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ $tx->tanggal_masuk->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ $tx->pengirim ?: '-' }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($tx->gudang)
                                        <span class="text-xs font-bold leading-normal text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">{{ $tx->gudang->nama_gudang }}</span>
                                    @else
                                        <span class="text-xs font-semibold leading-normal text-slate-400 bg-gray-50 px-2.5 py-1 rounded-lg">Gudang Terhapus</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600">{{ $tx->user ? $tx->user->name : 'System' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <a href="{{ route('barang-masuk.show', $tx->id) }}" class="text-xs font-semibold leading-normal text-slate-400 hover:text-blue-600 mr-3 transition-colors">
                                        <i class="fa fa-eye mr-1"></i> Detail
                                    </a>
                                    @if(Auth::user() && Auth::user()->isSuperAdmin())
                                    <button type="button" onclick="openDeleteModal('{{ route('barang-masuk.destroy', $tx->id) }}', '{{ $tx->no_surat_jalan }}', 'barang-masuk')" class="text-xs font-semibold leading-normal text-slate-400 hover:text-red-600 transition-colors">
                                        <i class="fa fa-trash mr-1"></i> Hapus
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa fa-file-invoice text-4xl text-slate-300 mb-3"></i>
                                        <span class="text-sm text-slate-400 font-medium">Belum ada riwayat transaksi barang masuk.</span>
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
                    Menampilkan <span id="paginationInfoStart" class="text-slate-700">0</span> - <span id="paginationInfoEnd" class="text-slate-700">0</span> dari <span id="paginationInfoTotal" class="text-slate-700">0</span> transaksi
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
<!-- SheetJS for proper Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function confirmTerima(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Terima Mutasi?',
            text: 'Apakah Anda yakin barang ini sudah diterima dan akan dimasukkan ke stok?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, Terima!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                confirmButton: 'text-white bg-blue-600 hover:bg-blue-700 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight mr-2',
                cancelButton: 'text-slate-700 bg-gray-100 hover:bg-gray-200 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    function confirmTolak(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Tolak Mutasi?',
            text: 'Apakah Anda yakin ingin menolak mutasi ini? Stok akan dikembalikan ke gudang pengirim.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea0606',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                confirmButton: 'text-white bg-red-600 hover:bg-red-700 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight mr-2',
                cancelButton: 'text-slate-700 bg-gray-100 hover:bg-gray-200 font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    function exportTableToExcel(tableId, filename = ''){
        let table = document.getElementById(tableId);
        let clone = table.cloneNode(true);
        
        // Hapus kolom Aksi (kolom terakhir di th dan td)
        clone.querySelectorAll('th:last-child, td:last-child').forEach(el => el.remove());
        
        // Evaluasi ulang filter agar semua baris yang cocok (bukan hanya halaman ini) ikut terekspor
        let rows = clone.querySelectorAll('.tx-row');
        rows.forEach(row => {
            let rowSearchText = row.getAttribute('data-search') || '';
            let rowJenis = row.getAttribute('data-jenis') || '';
            let rowDate = row.getAttribute('data-date') || '';
            
            let searchQuery = document.getElementById("searchInput") ? document.getElementById("searchInput").value.toLowerCase().trim() : "";
            let selectedJenis = document.getElementById("filterJenis") ? document.getElementById("filterJenis").value : '';
            let dateStart = document.getElementById("filterDateStart") ? document.getElementById("filterDateStart").value : '';
            let dateEnd = document.getElementById("filterDateEnd") ? document.getElementById("filterDateEnd").value : '';
            
            let matchSearch = rowSearchText.indexOf(searchQuery) > -1;
            let matchJenis = selectedJenis === "" || rowJenis === selectedJenis;
            let matchDate = true;
            if (dateStart && rowDate < dateStart) matchDate = false;
            if (dateEnd && rowDate > dateEnd) matchDate = false;
            
            if (!(matchSearch && matchJenis && matchDate)) {
                row.remove(); // Buang baris yang tidak cocok filter
            } else {
                row.style.display = ''; // Tampilkan baris yang cocok (walau tersembunyi karena pagination)
            }
        });
        
        let wb = XLSX.utils.table_to_book(clone, {sheet: "Riwayat"});
        XLSX.writeFile(wb, filename + '.xlsx');
    }

    function openDeleteModal(actionUrl, identifierName, itemType) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: `Menghapus transaksi Surat Jalan "${identifierName}" akan MEMBATALKAN penambahan stok barang di gudang terkait!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea0606',
            cancelButtonColor: '#82d616',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                confirmButton: 'font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight mr-2',
                cancelButton: 'font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
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
            let selectedJenis = $("#filterJenis").val();
            let dateStart = $("#filterDateStart").val();
            let dateEnd = $("#filterDateEnd").val();
            
            // 1. Filter rows
            let $matchingRows = $(".tx-row").filter(function() {
                let $row = $(this);
                let rowSearchText = $row.data("search") || '';
                let rowJenis = $row.data("jenis") || '';
                let rowDate = $row.data("date") || '';

                let matchSearch = rowSearchText.indexOf(searchQuery) > -1;
                let matchJenis = selectedJenis === "" || rowJenis === selectedJenis;
                
                let matchDate = true;
                if (dateStart && rowDate < dateStart) matchDate = false;
                if (dateEnd && rowDate > dateEnd) matchDate = false;

                return matchSearch && matchJenis && matchDate;
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

            // 2. Hide all rows, show only current page rows
            $(".tx-row").hide();
            
            let startIndex = (currentPage - 1) * itemsPerPage;
            let endIndex = startIndex + itemsPerPage;
            
            $matchingRows.slice(startIndex, endIndex).show();

            // 3. Update Pagination Info Text and Footer Visibility
            if (totalItems === 0) {
                $("#paginationContainer").hide();
                
                // Show empty placeholder row
                if ($("#emptySearchPlaceholder").length === 0) {
                    $("tbody").append(`
                        <tr id="emptySearchPlaceholder">
                            <td colspan="8" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa fa-search text-3xl text-slate-300 mb-2"></i>
                                    <span class="text-xs text-slate-400 font-medium">Tidak ada transaksi yang cocok dengan pencarian / filter Anda.</span>
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

            // 4. Render Page Number Buttons
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
        
        $("#filterJenis").on("change", function() {
            currentPage = 1;
            filterAndPaginate();
        });
        
        $("#entriesLimit, #filterDateStart, #filterDateEnd").on("change", function() {
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
            let totalMatching = $(".tx-row").filter(function() {
                let $row = $(this);
                let rowSearchText = $row.data("search") || '';
                let rowJenis = $row.data("jenis") || '';
                let rowDate = $row.data("date") || '';
                
                let matchSearch = rowSearchText.indexOf($("#searchInput").val().toLowerCase().trim()) > -1;
                let matchJenis = ($("#filterJenis").val() === "" || rowJenis === $("#filterJenis").val());
                
                let dateStart = $("#filterDateStart").val();
                let dateEnd = $("#filterDateEnd").val();
                let matchDate = true;
                if (dateStart && rowDate < dateStart) matchDate = false;
                if (dateEnd && rowDate > dateEnd) matchDate = false;
                
                return matchSearch && matchJenis && matchDate;
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

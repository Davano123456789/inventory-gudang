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
                            <label for="gudang_kode" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Pilih Gudang Target <span class="text-red-500">*</span></label>
                            <select name="gudang_kode" id="gudang_kode" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                @if(count($gudangs) > 1)
                                    <option value="">-- Pilih Gudang Opname --</option>
                                @endif
                                @foreach($gudangs as $g)
                                    <option value="{{ $g->kode_gudang }}" {{ (old('gudang_kode') == $g->kode_gudang || count($gudangs) == 1) ? 'selected' : '' }}>
                                        {{ $g->nama_gudang }} ({{ $g->kode_gudang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                    <!-- Dynamic Bulk Items Table -->
                    <div id="bulkTableContainer" class="hidden mb-6">
                        <h6 class="font-bold text-slate-800 text-xs uppercase mb-3 tracking-wide border-b pb-2">Daftar Barang & Kuantitas Fisik</h6>
                        
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
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start items-center gap-3 border-t pt-4">
                        <!-- Submit as Selesai -->
                        <button type="submit" onclick="setStatus('Selesai')" class="px-6 py-3 font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md transition-colors text-xs tracking-wider">
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
        
        // Fetch all items with current stock for the selected warehouse
        function loadWarehouseItems() {
            let gudangKode = $("#gudang_kode").val();
            let $tableBody = $("#bulkItemsTableBody");
            let $container = $("#bulkTableContainer");
            
            if (!gudangKode) {
                $container.hide();
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
                    $container.show();
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
                            let rowHtml = `
                                <tr class="item-row border-b border-slate-100 hover:bg-slate-50/20" data-unit="${item.unit}">
                                    <!-- Barang Details -->
                                    <td class="px-4 py-3.5 align-middle bg-transparent shadow-none">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700">${item.nama_barang}</span>
                                            <span class="text-[10px] text-slate-400">Kode: ${item.kode_barang}</span>
                                        </div>
                                        <input type="hidden" class="barang-id-input" value="${item.barang_id}">
                                    </td>
                                    
                                    <!-- System Stock (Read-Only) -->
                                    <td class="px-4 py-3.5 text-center align-middle bg-transparent shadow-none">
                                        <span class="text-sm font-semibold text-slate-600">${item.stok_sistem.toLocaleString('id-ID')} ${item.unit}</span>
                                        <input type="hidden" class="stok-sistem-input" value="${item.stok_sistem}">
                                    </td>
                                    
                                    <!-- Physical Stock Input -->
                                    <td class="px-4 py-3.5 text-center align-middle bg-transparent shadow-none">
                                        <div class="relative flex items-center justify-center gap-1.5">
                                            <input type="number" step="any" placeholder="-" class="stok-fisik-input w-28 text-center px-2.5 py-1.5 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-all font-semibold">
                                            <span class="text-xxs font-bold text-slate-400 uppercase w-10 text-left">${item.unit}</span>
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
            let unit = $row.data("unit") || 'Unit';
            
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
                $badge.text(diff.toLocaleString('id-ID') + " " + unit + " (Kurang)")
                      .addClass("bg-red-50 text-red-600 border border-red-200");
            } else if (diff > 0) {
                $badge.text("+" + diff.toLocaleString('id-ID') + " " + unit + " (Lebih)")
                      .addClass("bg-green-50 text-green-600 border border-green-200");
            } else {
                $badge.text("0 " + unit + " (Sesuai)")
                      .addClass("bg-blue-50 text-blue-600 border border-blue-200");
            }
        });

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

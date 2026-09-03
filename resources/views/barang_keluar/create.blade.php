@extends('layouts.masterDashboard')

@section('title', 'Catat Barang Keluar - Inventory Gudang')
@section('page_title', 'Barang Keluar')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Soft UI styling overrides */
    .select2-container--default .select2-selection--single {
        border-color: #d2d6da !important;
        border-radius: 0.5rem !important;
        height: 38px !important;
        padding: 5px 12px !important;
        background-color: #fff !important;
        transition: all 0.2s ease;
    }
    .select2-container--default .select2-selection--single:focus-within {
        border-color: #596cff !important;
        outline: none !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057 !important;
        font-size: 0.75rem !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #cb0c9f !important;
        background-image: linear-gradient(310deg, #7928ca 0%, #ff0080 100%) !important;
    }
    .select2-dropdown {
        border-color: #d2d6da !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden;
    }
    .select2-search__field {
        border-radius: 0.375rem !important;
        border-color: #d2d6da !important;
        font-size: 0.75rem !important;
    }
</style>
@endpush

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-6">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <h6 class="font-bold text-slate-800 text-lg">Catat Transaksi Barang Keluar</h6>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">

                <!-- Alert Validation Errors -->
                @if($errors->any())
                    <div class="mb-6 p-4 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200">
                        <div class="flex items-center gap-2 mb-2 font-bold">
                            <i class="fa fa-exclamation-triangle"></i> Gagal Menyimpan Transaksi!
                        </div>
                        <ul class="list-disc pl-5 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('barang-keluar.store') }}" method="POST" id="barangKeluarForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Row 1 -->
                        <div>
                            <label for="no_surat_jalan" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Nomor Surat Jalan <span class="text-red-500">*</span></label>
                            <input type="text" name="no_surat_jalan" id="no_surat_jalan" value="{{ old('no_surat_jalan') }}" required placeholder="Contoh: 3073154/26" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="jenis" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Jenis Transaksi <span class="text-red-500">*</span></label>
                            <select name="jenis" id="jenis" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                <option value="reguler" {{ old('jenis') == 'reguler' ? 'selected' : '' }}>Reguler</option>
                                <option value="mutasi" {{ old('jenis') == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                                <option value="stock_opname" {{ old('jenis') == 'stock_opname' ? 'selected' : '' }}>Penyesuaian Stock Opname</option>
                            </select>
                        </div>

                        <!-- Row 2 -->
                        <div>
                            <label for="tanggal_surat_jalan" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Tanggal Surat Jalan <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_surat_jalan" id="tanggal_surat_jalan" value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="tanggal_keluar" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Tanggal Pengiriman / Keluar <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_keluar" id="tanggal_keluar" value="{{ old('tanggal_keluar', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <!-- Row 3 -->
                        <div class="md:col-span-2">
                            <label for="gudang_asal_kode" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Gudang Asal (Dikurangi Dari) <span class="text-red-500">*</span></label>
                            <select id="gudang_asal_kode_display" disabled class="w-full px-3 py-2 text-sm text-slate-700 bg-slate-100 border border-gray-300 rounded-lg focus:outline-none transition-colors cursor-not-allowed font-semibold">
                                @foreach($gudangs as $g)
                                    <option value="{{ $g->kode_gudang }}" {{ $g->kode_gudang == Auth::user()->getActiveGudangCode() ? 'selected' : '' }}>
                                        {{ $g->nama_gudang }} ({{ $g->kode_gudang }})
                                    </option>
                                @endforeach
                            </select>
                            <!-- Hidden input to submit the actual value since disabled selects aren't sent by the browser -->
                            <input type="hidden" name="gudang_asal_kode" value="{{ Auth::user()->getActiveGudangCode() }}">
                        </div>

                        <div class="md:col-span-2 hidden" id="gudangTujuanContainer">
                            <label for="gudang_tujuan_kode" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Gudang Tujuan (Untuk Mutasi) <span class="text-red-500">*</span></label>
                            <select name="gudang_tujuan_kode" id="gudang_tujuan_kode" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                <option value="">-- Pilih Gudang Tujuan --</option>
                                @foreach($allGudangs as $g)
                                    <option value="{{ $g->kode_gudang }}" {{ old('gudang_tujuan_kode') == $g->kode_gudang ? 'selected' : '' }}>
                                        {{ $g->nama_gudang }} ({{ $g->kode_gudang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-6 border-slate-200">

                    <!-- Detail Items Section -->
                    <div class="flex justify-between items-center mb-4">
                        <h6 class="font-bold text-slate-800 text-md">Daftar Barang Keluar</h6>
                        <button type="button" id="addRowBtn" class="inline-block px-4 py-2 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                            <i class="fa fa-plus mr-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500" id="itemsTable">
                            <thead class="align-bottom">
                                <tr>
                                    <th class="px-3 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[50%]">Barang</th>
                                    <th class="px-3 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[20%]">Quantity</th>
                                    <th class="px-3 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[20%]">Satuan</th>
                                    <th class="px-3 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[10%]">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Dynamic rows go here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start gap-3 border-t pt-4">
                        <button type="submit" class="px-6 py-3 font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md transition-colors text-xs tracking-wider">
                            <i class="fa fa-save mr-1"></i> Simpan Transaksi
                        </button>
                        <a href="{{ route('barang-keluar.index') }}" class="px-6 py-3 font-bold text-slate-500 uppercase bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-xs tracking-wider">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        let rowIndex = 0;
        
        // Toggle Gudang Tujuan based on Jenis
        function toggleGudangTujuan() {
            if ($('#jenis').val() === 'mutasi') {
                $('#gudangTujuanContainer').removeClass('hidden');
                $('#gudang_tujuan_kode').prop('required', true);
            } else {
                $('#gudangTujuanContainer').addClass('hidden');
                $('#gudang_tujuan_kode').prop('required', false);
            }
        }

        $('#jenis').on('change', toggleGudangTujuan);
        toggleGudangTujuan(); // Run on load
        
        // List of all units passed from DB
        const satuansData = [
            @foreach($satuans as $s)
            {
                id: "{{ $s->id }}",
                nama: "{{ $s->nama_satuan }}"
            },
            @endforeach
        ];
        
        // List of items passed to JS
        const itemsData = [
            @foreach($barangs as $b)
            {
                id: "{{ $b->id }}",
                kode: "{{ $b->kode_barang }}",
                nama: "{{ $b->nama_barang }}",
                satuan_id: "{{ $b->satuan_id }}",
                satuan: "{{ $b->satuan ? $b->satuan->nama_satuan : '' }}"
            },
            @endforeach
        ];

        // Function to create a new row HTML
        function createRow(index) {
            let options = '';
            itemsData.forEach(function(item) {
                options += `<option value="${item.id}">${item.kode} - ${item.nama}</option>`;
            });

            let satuanOptions = '<option value="">-- Pilih Satuan --</option>';
            satuansData.forEach(function(s) {
                satuanOptions += `<option value="${s.id}">${s.nama}</option>`;
            });

            return `
                <tr class="item-row">
                    <td class="p-3 align-middle bg-transparent border-b shadow-none">
                        <select name="items[${index}][barang_id]" required class="barang-select w-full px-3 py-2 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                            ${options}
                        </select>
                    </td>
                    <td class="p-3 align-middle bg-transparent border-b shadow-none text-center">
                        <input type="number" step="any" name="items[${index}][qty]" required placeholder="Quantity" class="w-full text-center px-3 py-2 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </td>
                    <td class="p-3 align-middle bg-transparent border-b shadow-none text-left">
                        <input type="hidden" name="items[${index}][satuan_id]" class="satuan-hidden-input">
                        <select name="items[${index}][satuan_select]" required class="satuan-select w-full px-2.5 py-2 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                            ${satuanOptions}
                        </select>
                    </td>
                    <td class="p-3 align-middle bg-transparent border-b shadow-none text-center">
                        <button type="button" class="remove-row-btn text-slate-400 hover:text-red-600 transition-colors bg-transparent border-0">
                            <i class="fa fa-trash text-sm"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Add row on button click
        $("#addRowBtn").on("click", function() {
            let $row = $(createRow(rowIndex));
            $("#itemsTableBody").append($row);
            
            // Initialize Select2 on the new dropdown
            $row.find('.barang-select').select2({
                width: '100%'
            }).trigger('change');
            
            rowIndex++;
        });

        // Delete row on trash icon click
        $(document).on("click", ".remove-row-btn", function() {
            $(this).closest(".item-row").remove();
            checkEmptyTable();
        });

        // Update Satuan dropdown when item changes
        $(document).on("change", ".barang-select", function() {
            let $select = $(this);
            let itemVal = $select.val();
            let itemData = itemsData.find(x => x.id == itemVal);
            
            let $row = $select.closest(".item-row");
            let $satuanSelect = $row.find(".satuan-select");
            let $satuanHidden = $row.find(".satuan-hidden-input");
            
            if (itemData && itemData.satuan_id) {
                $satuanSelect.val(itemData.satuan_id).prop('disabled', true).addClass('bg-gray-100 cursor-not-allowed');
                $satuanHidden.val(itemData.satuan_id);
            } else {
                $satuanSelect.val("").prop('disabled', false).removeClass('bg-gray-100 cursor-not-allowed');
                $satuanHidden.val("");
            }
        });

        // Update hidden input when user manually changes the enabled unit dropdown
        $(document).on("change", ".satuan-select", function() {
            let $select = $(this);
            let $row = $select.closest(".item-row");
            $row.find(".satuan-hidden-input").val($select.val());
        });

        // Check if table is empty and add a placeholder or initial row
        function checkEmptyTable() {
            if ($("#itemsTableBody tr").length === 0) {
                $("#itemsTableBody").append(`
                    <tr id="emptyPlaceholderRow">
                        <td colspan="4" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa fa-boxes text-3xl text-slate-300 mb-2"></i>
                                <span class="text-xs text-slate-400 font-medium">Belum ada barang ditambahkan. Klik "Tambah Baris" di atas.</span>
                            </div>
                        </td>
                    </tr>
                `);
            } else {
                $("#emptyPlaceholderRow").remove();
            }
        }

        // Add initial row
        $("#addRowBtn").trigger("click");

        // Validate form before submission
        $("#barangKeluarForm").on("submit", function(e) {
            let rowCount = $("#itemsTableBody tr.item-row").length;
            if (rowCount === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Kesalahan!',
                    text: 'Anda harus menambahkan minimal 1 barang sebelum menyimpan transaksi!',
                    icon: 'error',
                    confirmButtonColor: '#ea0606',
                    customClass: {
                        popup: 'font-poppins rounded-2xl shadow-soft-2xl',
                        confirmButton: 'font-bold px-6 py-2.5 rounded-lg text-xs leading-pro uppercase tracking-tight'
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection

@extends('layouts.masterDashboard')

@section('title', 'Catat Barang Masuk - Inventory Gudang')
@section('page_title', 'Catat Barang Masuk')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Soft UI styling overrides */
    .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        height: 40px !important;
        padding: 6px 12px !important;
        font-size: 0.75rem !important;
        font-family: 'Poppins', sans-serif !important;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        color: #495057 !important;
        padding-left: 0 !important;
        font-weight: 600 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #cb0c9f !important;
        background-image: linear-gradient(310deg, #7928ca, #b80075) !important;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        z-index: 9999 !important;
    }
    .select2-search__field {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.375rem !important;
        outline: none !important;
        padding: 5px 8px !important;
    }
</style>
@endpush

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Validation Errors -->
        @if($errors->any())
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <div class="flex items-center mb-1">
                <i class="fa fa-exclamation-circle mr-2 text-base"></i>
                <span class="font-bold">Terjadi kesalahan input:</span>
            </div>
            <ul class="list-disc list-inside pl-4">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <h6 class="font-bold text-slate-800 text-lg">Form Barang Masuk Baru (Surat Jalan)</h6>
                <a href="{{ route('barang-masuk.index') }}" class="inline-block px-5 py-2.5 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">
                <form action="{{ route('barang-masuk.store') }}" method="POST" id="barangMasukForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="no_surat_jalan" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Nomor Surat Jalan <span class="text-red-500">*</span></label>
                            <input type="text" name="no_surat_jalan" id="no_surat_jalan" value="{{ old('no_surat_jalan') }}" required placeholder="Contoh: 9908761/26" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="pengirim" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Pengirim (Dari) <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim') }}" placeholder="Contoh: PT Bintang Cakra Kencana / Supplier" class="w-full px-3 py-2 text-sm text-slate-700 placeholder-slate-400 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="tanggal_surat_jalan" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Tanggal Surat Jalan <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_surat_jalan" id="tanggal_surat_jalan" value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="tanggal_masuk" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Tanggal Terima / Masuk <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="jenis_transaksi" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Jenis Transaksi <span class="text-red-500">*</span></label>
                            <select name="jenis_transaksi" id="jenis_transaksi" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                <option value="Biasa" {{ old('jenis_transaksi') == 'Biasa' ? 'selected' : '' }}>Reguler</option>
                                <option value="Retur" {{ old('jenis_transaksi') == 'Retur' ? 'selected' : '' }}>Retur / Kembalian</option>
                                <option value="Mutasi" {{ old('jenis_transaksi') == 'Mutasi' ? 'selected' : '' }}>Mutasi Antar Gudang</option>
                            </select>
                        </div>

                        <div>
                            <label for="gudang_tujuan_kode" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Gudang Penerima (Masuk Ke) <span class="text-red-500">*</span></label>
                            <select name="gudang_tujuan_kode" id="gudang_tujuan_kode" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                @if(count($gudangTujuans) > 1)
                                    <option value="">-- Pilih Gudang Penerima --</option>
                                @endif
                                @foreach($gudangTujuans as $g)
                                    <option value="{{ $g->kode_gudang }}" {{ (old('gudang_tujuan_kode') == $g->kode_gudang || count($gudangTujuans) == 1) ? 'selected' : '' }}>
                                        {{ $g->nama_gudang }} ({{ $g->kode_gudang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Gudang Asal (Hidden by default, shown only when Mutasi) -->
                        <div class="md:col-span-2 hidden" id="gudangAsalWrapper">
                            <label for="gudang_asal_kode" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Gudang Asal (Dikurangi Dari) <span class="text-red-500">*</span></label>
                            <select name="gudang_asal_kode" id="gudang_asal_kode" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                <option value="">-- Pilih Gudang Asal --</option>
                                @foreach($gudangs as $g)
                                    <option value="{{ $g->kode_gudang }}" {{ old('gudang_asal_kode') == $g->kode_gudang ? 'selected' : '' }}>
                                        {{ $g->nama_gudang }} ({{ $g->kode_gudang }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Stok barang yang dimasukkan akan otomatis dikurangi dari gudang asal ini.</p>
                        </div>
                    </div>

                    <hr class="my-6 border-slate-200">

                    <!-- Detail Items Section -->
                    <div class="flex justify-between items-center mb-4">
                        <h6 class="font-bold text-slate-800 text-md">Daftar Barang Masuk</h6>
                        <button type="button" id="addRowBtn" class="inline-block px-4 py-2 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                            <i class="fa fa-plus mr-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500" id="itemsTable">
                            <thead class="align-bottom">
                                <tr>
                                    <th class="px-3 py-3 font-bold text-left uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[35%]">Barang</th>
                                    <th class="px-3 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[10%]">BOX</th>
                                    <th class="px-3 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[10%]">PCS</th>
                                    <th class="px-3 py-3 font-bold text-center uppercase align-middle bg-slate-50 border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70 w-[15%]">QTY</th>
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
                        <a href="{{ route('barang-masuk.index') }}" class="px-6 py-3 font-bold text-slate-500 uppercase bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-xs tracking-wider">
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
                        <input type="number" step="any" name="items[${index}][qty_box]" value="0" class="w-full text-center px-3 py-2 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </td>
                    <td class="p-3 align-middle bg-transparent border-b shadow-none text-center">
                        <input type="number" step="any" name="items[${index}][qty_pcs]" value="0" class="w-full text-center px-3 py-2 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </td>
                    <td class="p-3 align-middle bg-transparent border-b shadow-none text-center">
                        <input type="number" step="any" name="items[${index}][qty_total]" required placeholder="QTY" class="w-full text-center px-3 py-2 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    </td>
                    <td class="p-3 align-middle bg-transparent border-b shadow-none text-left">
                        <input type="hidden" name="items[${index}][satuan_id]" class="satuan-hidden-input">
                        <select name="items[${index}][satuan_select]" class="satuan-select w-full px-2.5 py-2 text-xs text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
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
                // Item already has a unit: select it, disable dropdown, and set hidden input
                $satuanSelect.val(itemData.satuan_id).prop('disabled', true).addClass('bg-gray-100 cursor-not-allowed');
                $satuanHidden.val(itemData.satuan_id);
            } else {
                // Item has NO unit: let user select a unit from dropdown, clear selection, enable it
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

        function toggleGudangAsal() {
            let selectedType = $("#jenis_transaksi").val();
            if (selectedType === "Mutasi") {
                $("#gudangAsalWrapper").removeClass("hidden").show();
                $("#gudang_asal_kode").prop("required", true);
            } else {
                $("#gudangAsalWrapper").addClass("hidden").hide();
                $("#gudang_asal_kode").prop("required", false).val("");
            }
        }

        $("#jenis_transaksi").on("change", toggleGudangAsal);
        toggleGudangAsal();

        // Check if table is empty and add a placeholder or initial row
        function checkEmptyTable() {
            if ($("#itemsTableBody tr").length === 0) {
                $("#itemsTableBody").append(`
                    <tr id="emptyPlaceholderRow">
                        <td colspan="6" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
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
        $("#barangMasukForm").on("submit", function(e) {
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

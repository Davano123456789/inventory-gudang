@extends('layouts.masterDashboard')

@section('title', 'Edit Pengguna - Inventory Gudang')
@section('page_title', 'Kelola Pengguna')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-6">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl">
                <h6 class="font-bold text-slate-800 text-lg">Edit Akun Pengguna: {{ $user->name }}</h6>
            </div>

            <!-- Card Body -->
            <div class="flex-auto p-6">

                <!-- Alert Validation Errors -->
                @if($errors->any())
                    <div class="mb-6 p-4 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200">
                        <div class="flex items-center gap-2 mb-2 font-bold">
                            <i class="fa fa-exclamation-triangle"></i> Gagal Menyimpan Akun!
                        </div>
                        <ul class="list-disc pl-5 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Nama Pengguna -->
                        <div>
                            <label for="name" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap..." required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Alamat Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" placeholder="contoh@domain.com" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        </div>

                        <!-- Password fields removed - Users must manage their own passwords now -->

                        <!-- Role Select -->
                        <div>
                            <label for="role" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Peran (Role) <span class="text-red-500">*</span></label>
                            <select name="role" id="role" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                <option value="" disabled {{ old('role', $user->role) ? '' : 'selected' }}>-- Pilih Peran --</option>
                                @foreach($allowedRoles as $roleKey => $roleLabel)
                                    <option value="{{ $roleKey }}" {{ old('role', $user->role) == $roleKey ? 'selected' : '' }}>{{ $roleLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Warehouse Selector (Toggled dynamically by JQuery) -->
                        <div id="warehouseSelectorContainer" class="{{ Auth::user()->isKepalaGudang() ? 'hidden' : '' }}">
                            <label for="kode_gudang" class="block mb-2 text-xs font-bold text-slate-700 uppercase">Gudang Penugasan <span class="text-red-500">*</span></label>
                            @if(Auth::user()->isKepalaGudang())
                                <input type="text" disabled value="{{ Auth::user()->gudang ? Auth::user()->gudang->nama_gudang : '' }}" class="w-full px-3 py-2 text-sm text-slate-700 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none cursor-not-allowed">
                            @else
                                <select name="kode_gudang" id="kode_gudang" required class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                    <option value="">-- Pilih Gudang Tugas --</option>
                                    @foreach($gudangs as $g)
                                        <option value="{{ $g->kode_gudang }}" {{ old('kode_gudang', $user->kode_gudang) == $g->kode_gudang ? 'selected' : '' }}>
                                            {{ $g->nama_gudang }} ({{ $g->kode_gudang }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start gap-3 border-t pt-4">
                        <button type="submit" class="px-6 py-3 font-bold text-white uppercase bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md transition-colors text-xs tracking-wider">
                            <i class="fa fa-save mr-1"></i> Perbarui Pengguna
                        </button>
                        <a href="{{ route('user.index') }}" class="px-6 py-3 font-bold text-slate-500 uppercase bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-xs tracking-wider">
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
    $(document).ready(function() {
        function toggleWarehouseSelector() {
            let roleVal = $("#role").val();
            let $container = $("#warehouseSelectorContainer");
            let $select = $("#kode_gudang");

            if ($select.length) {
                if (roleVal === "super_admin" || roleVal === "admin" || !roleVal) {
                    $container.hide();
                    $select.prop("required", false).val("");
                } else {
                    $container.show();
                    $select.prop("required", true);
                }
            }
        }

        // Run on load and change
        $("#role").on("change", toggleWarehouseSelector);
        toggleWarehouseSelector();
    });
</script>
@endpush
@endsection

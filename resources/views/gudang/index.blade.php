@extends('layouts.masterDashboard')

@section('title', 'Master Gudang - Inventory Gudang')
@section('page_title', 'Master Gudang')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">
        
        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex justify-between items-center">
                <h6 class="font-bold text-slate-800 text-lg">Daftar Cabang Gudang</h6>
                @if(Auth::user() && Auth::user()->isSuperAdmin())
                <a href="{{ route('gudang.create') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-colors rounded-lg cursor-pointer bg-blue-600 hover:bg-blue-700 leading-pro text-xs tracking-tight">
                    <i class="fa fa-plus mr-1"></i> Tambah Gudang
                </a>
                @endif
            </div>

            <!-- Card Body (Table) -->
            <div class="flex-auto px-0 pt-0 pb-2">
                <div class="p-0 overflow-x-auto">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Kode Gudang</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Nama Gudang</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Alamat</th>
                                @if(Auth::user() && Auth::user()->isSuperAdmin())
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gudangs as $index => $gudang)
                            <tr>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-semibold leading-normal text-slate-600">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-bold leading-normal text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">{{ $gudang->kode_gudang }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-semibold leading-normal text-slate-800">{{ $gudang->nama_gudang }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b shadow-none">
                                    <span class="text-sm leading-normal text-slate-500">{{ Str::limit($gudang->alamat, 50) ?? '-' }}</span>
                                </td>
                                @if(Auth::user() && Auth::user()->isSuperAdmin())
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <a href="{{ route('gudang.edit', $gudang->id) }}" class="text-xs font-semibold leading-normal text-slate-400 hover:text-amber-600 mr-3 transition-colors">
                                        <i class="fa fa-edit mr-1"></i> Edit
                                    </a>
                                    <button type="button" onclick="openDeleteModal('{{ route('gudang.destroy', $gudang->id) }}', '{{ $gudang->nama_gudang }}', 'gudang')" class="text-xs font-semibold leading-normal text-slate-400 hover:text-red-600 transition-colors">
                                        <i class="fa fa-trash mr-1"></i> Hapus
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa fa-warehouse text-4xl text-slate-300 mb-3"></i>
                                        <span class="text-sm text-slate-400 font-medium">Belum ada data cabang gudang.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

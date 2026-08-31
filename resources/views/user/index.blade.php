@extends('layouts.masterDashboard')

@section('title', 'Kelola Pengguna - Inventory Gudang')
@section('page_title', 'Kelola Pengguna')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">

        <!-- Card Container -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            
            <!-- Card Header -->
            <div class="p-6 pb-0 mb-0 border-b-0 border-solid border-black-125 rounded-t-2xl flex flex-wrap justify-between items-center gap-4">
                <h6 class="font-bold text-slate-800 text-lg leading-none">Daftar Akun Pengguna</h6>
                
                <a href="{{ route('user.create') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight">
                    <i class="fa fa-user-plus mr-1"></i> Tambah Pengguna
                </a>
            </div>

            <!-- Card Body -->
            <div class="flex-auto px-0 pt-0 pb-2">
                <div class="p-0 overflow-x-auto">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">No</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Nama Pengguna</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Email</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Jabatan</th>
                                <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Gudang Penugasan</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $u)
                            <tr>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-semibold leading-normal text-slate-600">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm font-bold leading-normal text-slate-700">{{ $u->name }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <span class="text-sm leading-normal text-slate-600 font-semibold">{{ $u->email }}</span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($u->isSuperAdmin())
                                        <span class="text-xs font-bold leading-normal text-white bg-gradient-to-tl from-blue-600 to-sky-400 px-2.5 py-1 rounded-lg shadow-soft-md">Super Admin</span>
                                    @else
                                        <span class="text-xs font-bold leading-normal text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">Kepala Gudang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    @if($u->isKepalaGudang() && $u->gudang)
                                        <span class="text-xs font-bold leading-normal text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg">
                                            {{ $u->gudang->nama_gudang }} ({{ $u->kode_gudang }})
                                        </span>
                                    @elseif($u->isKepalaGudang())
                                        <span class="text-xs font-bold leading-normal text-red-600 bg-red-50 px-2.5 py-1 rounded-lg">
                                            Gudang Tidak Ditemukan / Kosong
                                        </span>
                                    @else
                                        <span class="text-xs font-normal leading-normal text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-none">
                                    <a href="{{ route('user.edit', $u->id) }}" class="text-xs font-semibold leading-normal text-slate-400 hover:text-amber-600 mr-3 transition-colors">
                                        <i class="fa fa-edit mr-1"></i> Edit
                                    </a>
                                    
                                    @if(Auth::id() !== $u->id)
                                    <button type="button" onclick="openDeleteModal('{{ route('user.destroy', $u->id) }}', '{{ $u->name }}')" class="text-xs font-semibold leading-normal text-slate-400 hover:text-red-600 transition-colors">
                                        <i class="fa fa-trash mr-1"></i> Hapus
                                    </button>
                                    @else
                                    <span class="text-xs font-semibold text-slate-300 cursor-not-allowed" title="Anda sedang menggunakan akun ini">
                                        <i class="fa fa-lock mr-1"></i> Terkunci
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center align-middle bg-transparent border-b shadow-none">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa fa-users text-4xl text-slate-300 mb-3"></i>
                                        <span class="text-sm text-slate-400 font-medium">Belum ada akun pengguna terdaftar.</span>
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

<!-- Modal Delete Confirmation (Form) -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    function openDeleteModal(actionUrl, identifierName) {
        Swal.fire({
            title: 'Hapus Akun Pengguna?',
            text: `Menghapus akun "${identifierName}" akan menghilangkan hak aksesnya ke sistem selamanya!`,
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

    $(document).ready(function() {
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

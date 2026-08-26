<!-- Loopple Templates: https://www.loopple.com/templates | Copyright Loopple (https://www.loopple.com) -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventory Gudang')</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        * {
            font-family: 'Poppins', sans-serif !important;
        }
        /* Fix FontAwesome icons being overridden by Poppins */
        .fa, .fas, .far, .fab, .fa-solid, .fa-regular, .fa-brands, i {
            font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome' !important;
            font-weight: 900 !important;
        }
    </style>
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://demos.creative-tim.com/soft-ui-dashboard-tailwind/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/soft-ui-dashboard-tailwind/assets/css/nucleo-svg.css" rel="stylesheet" />

    <!-- PopperJS -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    
    <!-- Loopple Soft UI Custom styles -->
    <link rel="stylesheet" href="{{ asset('tamplate-dashboard/assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('tamplate-dashboard/assets/css/loopple/loopple.css') }}">

    <!-- FontAwesome from CDN (just in case) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />



    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>

<body class="g-sidenav-show bg-gray-100 antialiased font-sans text-slate-500">

    <!-- Sidebar Layout -->
    @include('layouts.sidebar')

    <!-- Main Panel -->
    <div class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200" id="panel">
        
        <!-- Top Navbar -->
        <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all shadow-none duration-250 ease-soft-in rounded-2xl lg:flex-nowrap lg:justify-start" id="navbarTop" navbar-scroll="true">
            <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
                <nav>
                    <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
                        <li class="text-sm leading-normal">
                            <a class="opacity-50 text-slate-700" href="javascript:;">Halaman</a>
                        </li>
                        <li class="text-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']" aria-current="page">@yield('page_title', 'Dashboard')</li>
                    </ol>
                    <h6 class="mb-0 font-bold capitalize">@yield('page_title', 'Dashboard')</h6>
                </nav>
                <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
                    <div class="flex items-center md:ml-auto md:pr-4">
                        @if(Auth::user() && Auth::user()->isSuperAdmin())
                            @php $navbarGudangs = \App\Models\Gudang::orderBy('nama_gudang', 'asc')->get(); @endphp
                            <form action="{{ route('switch-gudang') }}" method="POST" id="switchGudangForm" class="m-0">
                                @csrf
                                <select name="kode_gudang" onchange="document.getElementById('switchGudangForm').submit()" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold shadow-soft-md border-0 focus:outline-none focus:ring-0 cursor-pointer pr-8">
                                    @foreach($navbarGudangs as $g)
                                        <option value="{{ $g->kode_gudang }}" {{ Auth::user()->getActiveGudangCode() === $g->kode_gudang ? 'selected' : '' }}>
                                            Gudang: {{ $g->nama_gudang }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif(Auth::user() && Auth::user()->isKepalaGudang())
                            <div class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold shadow-soft-md">
                                <i class="fa fa-warehouse mr-1"></i> Gudang: {{ Auth::user()->gudang ? Auth::user()->gudang->nama_gudang : 'Tidak Ditugaskan' }}
                            </div>
                        @else
                            <div class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold shadow-soft-md">
                                <i class="fa fa-warehouse mr-1"></i> Sistem Inventory
                            </div>
                        @endif
                    </div>
                    <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
                        <li class="flex items-center pl-4">
                            <a href="javascript:;" class="block px-0 py-2 text-sm font-semibold transition-all ease-nav-brand text-slate-500 hover:text-blue-600">
                                <i class="fa fa-user sm:mr-1" aria-hidden="true"></i>
                                <span class="hidden sm:inline">{{ Auth::user() ? Auth::user()->name : 'User' }}</span>
                            </a>
                        </li>

                        <li class="flex items-center pl-4 xl:hidden">
                            <a href="javascript:;" class="block p-0 text-sm transition-all ease-nav-brand text-slate-500" sidenav-trigger="">
                                <div class="w-4.5 overflow-hidden">
                                    <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                    <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                    <i class="ease-soft relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="w-full px-6 py-6 mx-auto loopple-min-height-78vh">
            @yield('content')
            
            <!-- Footer -->
            @include('layouts.footer')
        </div>

    </div>

    <!-- Core Scripts -->
    <script src="https://demos.creative-tim.com/soft-ui-dashboard-tailwind/assets/js/plugins/chartjs.min.js"></script>
    <script src="https://demos.creative-tim.com/soft-ui-dashboard-tailwind/assets/js/plugins/perfect-scrollbar.min.js" async></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/Loopple/loopple-public-assets@main/soft-ui-dashboard-tailwind/js/soft-ui-dashboard-tailwind.js" async></script>

    <!-- SweetAlert2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global SweetAlert2 Script -->
    <script>
        // Global Delete Confirmation function using FontAwesome Warning icon
        function openDeleteModal(actionUrl, targetName, entityType = 'gudang') {
            Swal.fire({
                html: `
                    <div class="text-center">
                        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100">
                            <i class="fa fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <h5 class="text-slate-800 font-bold text-lg mb-2">Apakah Anda yakin?</h5>
                        <p class="text-slate-500 text-sm leading-relaxed">Anda akan menghapus ${entityType} <strong>"${targetName}"</strong>. Tindakan ini tidak dapat dibatalkan!</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl shadow-soft-2xl border-0 bg-white p-6 antialiased',
                    confirmButton: 'inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight mr-2',
                    cancelButton: 'inline-block px-6 py-3 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer bg-gray-100 hover:bg-gray-200 leading-pro text-xs ease-soft-in tracking-tight'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.action = actionUrl;
                    form.method = 'POST';
                    
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = token;
                    form.appendChild(csrfInput);
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Global Alert Notifications using FontAwesome success/error icons
        @if(session('success'))
            Swal.fire({
                html: `
                    <div class="text-center">
                        <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                            <i class="fa fa-check text-2xl"></i>
                        </div>
                        <h5 class="text-slate-800 font-bold text-lg mb-2">Berhasil!</h5>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ session('success') }}</p>
                    </div>
                `,
                showConfirmButton: false,
                timer: 3500,
                customClass: {
                    popup: 'rounded-2xl shadow-soft-2xl border-0 bg-white p-6 antialiased'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                html: `
                    <div class="text-center">
                        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100">
                            <i class="fa fa-times text-2xl"></i>
                        </div>
                        <h5 class="text-slate-800 font-bold text-lg mb-2">Gagal!</h5>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ session('error') }}</p>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-2xl shadow-soft-2xl border-0 bg-white p-6 antialiased',
                    confirmButton: 'inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-sky-400 leading-pro text-xs ease-soft-in shadow-soft-md hover:shadow-soft-2xl hover:scale-102 active:opacity-85 tracking-tight'
                },
                buttonsStyling: false
            });
        @endif
    </script>

    @stack('scripts')
</body>
</html>

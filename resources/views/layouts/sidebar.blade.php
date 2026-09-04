<aside class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 block w-full -translate-x-full flex-wrap items-center justify-between border-0 bg-[#0f172a] p-0 antialiased shadow-xl transition-transform duration-200 xl:left-0 xl:translate-x-0 text-white" id="sidenav-main">
    <!-- Sidebar Header / Logo -->
    <div class="h-20 flex items-center px-5 py-4 border-b border-slate-800">
        <i class="absolute top-0 right-0 hidden p-4 opacity-75 cursor-pointer fas fa-times text-white xl:hidden" sidenav-close="" aria-hidden="true"></i>
        <a class="flex items-center gap-3 text-white no-underline" href="{{ url('/') }}">
            <img src="{{ asset('images/logo-pt.png') }}" class="h-10 w-10 object-contain" alt="PT. Bintang Cakra Kencana Logo">
            <div class="flex flex-col leading-tight font-normal">
                <span class="text-xs text-white tracking-tight uppercase">PT. BINTANG CAKRA</span>
                <span class="text-[11px] text-white tracking-wider uppercase">KENCANA</span>
            </div>
        </a>
    </div>

    <!-- Sidebar Menu Navigation -->
    <div class="items-center block w-auto max-h-screen overflow-auto grow basis-full mt-2">
        <ul class="flex flex-col pl-0 mb-0 pb-32">
            
            <!-- Dashboard -->
            @php $isActive = Request::is('/') || Request::is('dashboard'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ url('/') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-home text-sm"></i>
                    </div>
                    <span class="ml-1">Dashboard</span>
                </a>
            </li>

            <!-- Master Gudang -->
            @php $isActive = Request::is('gudang*'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('gudang.index') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-warehouse text-sm"></i>
                    </div>
                    <span class="ml-1">Master Gudang</span>
                </a>
            </li>

            <!-- Master Satuan -->
            @php $isActive = Request::is('satuan*'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('satuan.index') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-tag text-sm"></i>
                    </div>
                    <span class="ml-1">Master Satuan</span>
                </a>
            </li>

            <!-- Master Barang -->
            @php $isActive = Request::is('barang') || Request::is('barang/*'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('barang.index') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-boxes text-sm"></i>
                    </div>
                    <span class="ml-1">Master Barang</span>
                </a>
            </li>

            <!-- Section: Transaksi -->
            <li class="w-full mt-5 mb-1">
                <h6 class="px-6 font-extrabold uppercase text-[10px] tracking-wider text-slate-400 m-0">TRANSAKSI</h6>
            </li>

            <!-- Barang Masuk -->
            @php $isActive = Request::is('barang-masuk') || (Request::is('barang-masuk/*') && !Request::is('barang-masuk/create')); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('barang-masuk.index') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-arrow-down text-sm"></i>
                    </div>
                    <span class="ml-1">Barang Masuk</span>
                </a>
            </li>

            <!-- Barang Keluar -->
            @php $isActive = Request::is('barang-keluar*'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('barang-keluar.index') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-arrow-up text-sm"></i>
                    </div>
                    <span class="ml-1">Barang Keluar</span>
                </a>
            </li>

            <!-- Stock Opname -->
            @php $isActive = Request::is('stock-opname*'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('stock-opname.index') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-clipboard-check text-sm"></i>
                    </div>
                    <span class="ml-1">Stock Opname</span>
                </a>
            </li>

            <!-- Section: Laporan -->
            <li class="w-full mt-5 mb-1">
                <h6 class="px-6 font-extrabold uppercase text-[10px] tracking-wider text-slate-400 m-0">LAPORAN</h6>
            </li>

            <!-- Rekap Stok -->
            @php $isActive = Request::is('laporan/rekap*'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('laporan.rekap') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-file-invoice text-sm"></i>
                    </div>
                    <span class="ml-1">Rekap Stok</span>
                </a>
            </li>

            <!-- Section: Sistem (If Admin) -->
            @if(Auth::user() && !Auth::user()->isStaff())
            <li class="w-full mt-5 mb-1">
                <h6 class="px-6 font-extrabold uppercase text-[10px] tracking-wider text-slate-400 m-0">SISTEM</h6>
            </li>

            <!-- Kelola Pengguna -->
            @php $isActive = Request::is('user*'); @endphp
            <li class="mt-0.5 w-full">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium {{ $isActive ? 'bg-blue-600 text-white font-bold border-l-4 border-blue-400 shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}" href="{{ route('user.index') }}">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }} text-center">
                        <i class="fa fa-users text-sm"></i>
                    </div>
                    <span class="ml-1">Kelola Pengguna</span>
                </a>
            </li>
            @endif

            <!-- Logout -->
            <li class="mt-4 w-full mb-6">
                <a class="py-3 my-0.5 flex items-center whitespace-nowrap px-5 transition-all text-sm font-medium text-slate-300 hover:bg-red-500/20 hover:text-red-400" href="javascript:;" onclick="event.preventDefault(); document.getElementById('sidebarLogoutForm').submit();" title="Keluar dari sistem">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-slate-800 text-red-400 text-center">
                        <i class="fa fa-sign-out-alt text-sm"></i>
                    </div>
                    <span class="ml-1 font-semibold">Keluar</span>
                </a>
                <form id="sidebarLogoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</aside>

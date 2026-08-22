@extends('layouts.masterDashboard')

@section('title', 'Dashboard Inventory Gudang')
@section('page_title', 'Dashboard')

@section('content')
<div class="flex flex-wrap -mx-3 removable">
    <!-- Card: Total Items -->
    <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-4">
            <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                    <div class="flex-none w-2/3 max-w-full px-3">
                        <div>
                            <p class="mb-0 font-sans font-semibold leading-normal text-sm">Total Jenis Barang</p>
                            <h5 class="mb-0 font-bold">1,024 <span class="leading-normal text-sm font-weight-bolder text-emerald-500">Item</span></h5>
                        </div>
                    </div>
                    <div class="px-3 text-right basis-1/3">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-blue-600 shadow-soft-2xl">
                            <i class="fa fa-boxes text-lg leading-none relative top-3.5 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card: Barang Masuk Hari Ini -->
    <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-4">
            <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                    <div class="flex-none w-2/3 max-w-full px-3">
                        <div>
                            <p class="mb-0 font-sans font-semibold leading-normal text-sm">Barang Masuk (Hari Ini)</p>
                            <h5 class="mb-0 font-bold">120,438 <span class="leading-normal text-sm font-weight-bolder text-emerald-500">Lbr</span></h5>
                        </div>
                    </div>
                    <div class="px-3 text-right basis-1/3">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-blue-600 shadow-soft-2xl">
                            <i class="fa fa-arrow-down text-lg leading-none relative top-3.5 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card: Barang Keluar Hari Ini -->
    <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-4">
            <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                    <div class="flex-none w-2/3 max-w-full px-3">
                        <div>
                            <p class="mb-0 font-sans font-semibold leading-normal text-sm">Barang Keluar (Hari Ini)</p>
                            <h5 class="mb-0 font-bold">1,025 <span class="leading-normal text-sm font-weight-bolder text-emerald-500">Lbr</span></h5>
                        </div>
                    </div>
                    <div class="px-3 text-right basis-1/3">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-blue-600 shadow-soft-2xl">
                            <i class="fa fa-arrow-up text-lg leading-none relative top-3.5 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card: Peringatan Stok Rendah -->
    <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border mb-4">
            <div class="flex-auto p-4">
                <div class="flex flex-row -mx-3">
                    <div class="flex-none w-2/3 max-w-full px-3">
                        <div>
                            <p class="mb-0 font-sans font-semibold leading-normal text-sm">Stok Rendah</p>
                            <h5 class="mb-0 font-bold">12 <span class="leading-normal text-rose-500 text-sm font-weight-bolder">Peringatan</span></h5>
                        </div>
                    </div>
                    <div class="px-3 text-right basis-1/3">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-blue-600 shadow-soft-2xl">
                            <i class="fa fa-exclamation-triangle text-lg leading-none relative top-3.5 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StokGudang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Render the scoped dashboard statistics.
     */
    public function index()
    {
        $user = auth()->user();
        $activeGudang = $user->getActiveGudangCode();

        // 1. Total Jenis Barang
        $totalBarang = Barang::count();

        // Today's Date
        $today = Carbon::today()->toDateString();

        // 2. Incoming goods qty today
        $incomingTodayQuery = DB::table('detail_barang_masuks')
            ->join('barang_masuks', 'detail_barang_masuks.barang_masuk_id', '=', 'barang_masuks.id')
            ->whereDate('barang_masuks.tanggal_masuk', $today);
        
        if ($activeGudang !== 'all') {
            $incomingTodayQuery->where('barang_masuks.gudang_tujuan_kode', $activeGudang);
        }
        $incomingToday = $incomingTodayQuery->sum('detail_barang_masuks.qty_total');

        // 3. Outgoing goods qty today
        $outgoingTodayQuery = DB::table('detail_barang_keluars')
            ->join('barang_keluars', 'detail_barang_keluars.barang_keluar_id', '=', 'barang_keluars.id')
            ->whereDate('barang_keluars.tanggal_keluar', $today);
        
        if ($activeGudang !== 'all') {
            $outgoingTodayQuery->where('barang_keluars.gudang_asal_kode', $activeGudang);
        }
        $outgoingToday = $outgoingTodayQuery->sum('detail_barang_keluars.qty');

        // 4. Low Stock warnings (dynamically read threshold from Setting model)
        $minStokAlert = (int) \App\Models\Setting::get('min_stok_alert', 5);

        if ($activeGudang === 'all') {
            // Group by barang_id across all warehouses
            $lowStock = StokGudang::select('barang_id', DB::raw('SUM(stok_sekarang) as total_stok'))
                ->groupBy('barang_id')
                ->having('total_stok', '<=', $minStokAlert)
                ->count();
        } else {
            // Single warehouse
            $lowStock = StokGudang::where('kode_gudang', $activeGudang)
                ->where('stok_sekarang', '<=', $minStokAlert)
                ->count();
        }

        return view('dashboard', compact('totalBarang', 'incomingToday', 'outgoingToday', 'lowStock', 'minStokAlert'));
    }
}

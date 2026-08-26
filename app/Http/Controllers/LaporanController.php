<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Display the Rekap Stok report.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $activeGudang = $user->getActiveGudangCode();
        
        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        if ($activeGudang !== 'all') {
            $gudangs = $gudangs->where('kode_gudang', $activeGudang);
        }

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Ensure default gudang if none is selected
        $selectedGudang = $request->input('gudang_kode');
        if (!$selectedGudang && $gudangs->count() > 0) {
            $selectedGudang = $gudangs->first()->kode_gudang;
        }

        $reportData = collect();

        if ($selectedGudang) {
            $reportData = \App\Models\KartuStok::with(['barang.satuan', 'barangMasuk', 'barangKeluar'])
                ->where('kode_gudang', $selectedGudang)
                ->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        }

        return view('laporan.rekap', compact('gudangs', 'selectedGudang', 'startDate', 'endDate', 'reportData'));
    }
}

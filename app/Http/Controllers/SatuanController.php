<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    /**
     * Restrict write operations to Super Admin.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $action = $request->route() ? $request->route()->getActionMethod() : '';
            if ($user && !$user->isSuperAdmin() && in_array($action, ['edit', 'update', 'destroy'])) {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satuans = Satuan::all();
        return view('satuan.index', compact('satuans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('satuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan',
        ]);

        Satuan::create($request->all());

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Satuan $satuan)
    {
        return view('satuan.show', compact('satuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Satuan $satuan)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan,' . $satuan->id,
        ]);

        $satuan->update($request->all());

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Satuan $satuan)
    {
        // Check if there are items using this unit
        if ($satuan->barangs()->count() > 0) {
            return redirect()->route('satuan.index')->with('error', 'Satuan tidak dapat dihapus karena sedang digunakan oleh beberapa barang!');
        }

        $satuan->delete();

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus!');
    }
}

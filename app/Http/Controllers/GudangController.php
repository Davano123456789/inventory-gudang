<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    /**
     * Restrict write operations to Super Admin.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $action = $request->route() ? $request->route()->getActionMethod() : '';
            if ($user && !$user->isSuperAdmin() && in_array($action, ['create', 'store', 'edit', 'update', 'destroy'])) {
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
        $gudangs = Gudang::all();
        return view('gudang.index', compact('gudangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gudang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_gudang' => 'required|string|max:50|unique:gudangs,kode_gudang',
            'nama_gudang' => 'required|string|max:150',
            'alamat' => 'nullable|string',
        ]);

        Gudang::create($request->all());

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gudang $gudang)
    {
        return view('gudang.show', compact('gudang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gudang $gudang)
    {
        return view('gudang.edit', compact('gudang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gudang $gudang)
    {
        $request->validate([
            'kode_gudang' => 'required|string|max:50|unique:gudangs,kode_gudang,' . $gudang->id,
            'nama_gudang' => 'required|string|max:150',
            'alamat' => 'nullable|string',
        ]);

        $gudang->update($request->all());

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gudang $gudang)
    {
        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gudang;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $users = User::with('gudang')->orderBy('name', 'asc')->get();
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        return view('user.create', compact('gudangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:super_admin,kepala_gudang',
            'kode_gudang' => 'required_if:role,kepala_gudang|nullable|exists:gudangs,kode_gudang',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'kode_gudang' => $request->role === 'kepala_gudang' ? $request->kode_gudang : null,
        ]);

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil didaftarkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        return view('user.edit', compact('user', 'gudangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:super_admin,kepala_gudang',
            'kode_gudang' => 'required_if:role,kepala_gudang|nullable|exists:gudangs,kode_gudang',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'kode_gudang' => $request->role === 'kepala_gudang' ? $request->kode_gudang : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->route('user.index')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil dihapus!');
    }
}

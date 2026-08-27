<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private function checkAccess()
    {
        if (!Auth::check() || Auth::user()->isStaff()) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function getAllowedRoles()
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            return [
                'super_admin' => 'Super Admin',
                'admin' => 'Admin',
                'kepala_gudang' => 'Kepala Gudang',
                'staff_gudang' => 'Staff Gudang'
            ];
        } elseif ($user->isAdmin()) {
            return [
                'kepala_gudang' => 'Kepala Gudang',
                'staff_gudang' => 'Staff Gudang'
            ];
        } elseif ($user->isKepalaGudang()) {
            return [
                'staff_gudang' => 'Staff Gudang'
            ];
        }
        return [];
    }

    public function index()
    {
        $this->checkAccess();

        $user = Auth::user();
        $query = User::with('gudang')->orderBy('name', 'asc');

        if ($user->isAdmin()) {
            $query->whereIn('role', ['kepala_gudang', 'staff_gudang']);
        } elseif ($user->isKepalaGudang()) {
            $query->where('role', 'staff_gudang')
                  ->where('kode_gudang', $user->kode_gudang);
        }

        $users = $query->get();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $this->checkAccess();

        $allowedRoles = $this->getAllowedRoles();
        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        return view('user.create', compact('gudangs', 'allowedRoles'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        $allowedRoles = array_keys($this->getAllowedRoles());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', $allowedRoles),
            'kode_gudang' => 'nullable|exists:gudangs,kode_gudang',
        ]);

        $kodeGudang = null;
        if (Auth::user()->isKepalaGudang()) {
            $kodeGudang = Auth::user()->kode_gudang;
        } else {
            if (in_array($request->role, ['kepala_gudang', 'staff_gudang'])) {
                $request->validate(['kode_gudang' => 'required|exists:gudangs,kode_gudang']);
                $kodeGudang = $request->kode_gudang;
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'kode_gudang' => $kodeGudang,
        ]);

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil didaftarkan!');
    }

    public function edit(User $user)
    {
        $this->checkAccess();
        $currentUser = Auth::user();

        // Check if current user can edit this target user
        if ($currentUser->isAdmin() && !in_array($user->role, ['kepala_gudang', 'staff_gudang'])) {
            abort(403, 'Anda tidak berhak mengedit akun ini.');
        }
        if ($currentUser->isKepalaGudang() && ($user->role !== 'staff_gudang' || $user->kode_gudang !== $currentUser->kode_gudang)) {
            abort(403, 'Anda tidak berhak mengedit akun ini.');
        }

        $allowedRoles = $this->getAllowedRoles();
        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        return view('user.edit', compact('user', 'gudangs', 'allowedRoles'));
    }

    public function update(Request $request, User $user)
    {
        $this->checkAccess();
        $currentUser = Auth::user();

        // Validate permission
        if ($currentUser->isAdmin() && !in_array($user->role, ['kepala_gudang', 'staff_gudang'])) {
            abort(403, 'Anda tidak berhak memodifikasi akun ini.');
        }
        if ($currentUser->isKepalaGudang() && ($user->role !== 'staff_gudang' || $user->kode_gudang !== $currentUser->kode_gudang)) {
            abort(403, 'Anda tidak berhak memodifikasi akun ini.');
        }

        $allowedRoles = array_keys($this->getAllowedRoles());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', $allowedRoles),
            'kode_gudang' => 'nullable|exists:gudangs,kode_gudang',
        ]);

        $kodeGudang = null;
        if ($currentUser->isKepalaGudang()) {
            $kodeGudang = $currentUser->kode_gudang;
        } else {
            if (in_array($request->role, ['kepala_gudang', 'staff_gudang'])) {
                $request->validate(['kode_gudang' => 'required|exists:gudangs,kode_gudang']);
                $kodeGudang = $request->kode_gudang;
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'kode_gudang' => $kodeGudang,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $this->checkAccess();
        $currentUser = Auth::user();

        if (auth()->id() === $user->id) {
            return redirect()->route('user.index')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        // Validate permission
        if ($currentUser->isAdmin() && !in_array($user->role, ['kepala_gudang', 'staff_gudang'])) {
            abort(403, 'Anda tidak berhak menghapus akun ini.');
        }
        if ($currentUser->isKepalaGudang() && ($user->role !== 'staff_gudang' || $user->kode_gudang !== $currentUser->kode_gudang)) {
            abort(403, 'Anda tidak berhak menghapus akun ini.');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil dihapus!');
    }
}

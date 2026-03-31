<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Hitung statistik untuk Dashboard Atas
        $totalUsers = User::count();
        $adminCount = User::whereIn('role', ['administrator', 'super_admin'])->count();
        $managerCount = User::where('role', 'manager')->count();

        // Tarik data user
        $users = User::orderBy('created_at', 'desc')->get();

        return view('users.index', compact('users', 'totalUsers', 'adminCount', 'managerCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:administrator,manager,staff',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:administrator,manager,staff',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        // Update password cuma kalau diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Data User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Cegah user hapus dirinya sendiri
       if(\Illuminate\Support\Facades\Auth::id() == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        User::findOrFail($id)->delete();
        return back()->with('success', 'User berhasil dihapus secara permanen!');
    }
}
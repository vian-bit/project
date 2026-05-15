<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperuser()) {
            $users = User::with('department')->where('role', '!=', 'superuser')->get();
        } else {
            $users = User::where('department_id', $user->department_id)
                ->where('role', 'user')->get();
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = Auth::user();
        $departments = $user->isSuperuser() ? Department::all() : Department::where('id', $user->department_id)->get();
        $userTypes = UserType::where('is_active', true)->get();

        return view('users.create', compact('departments', 'userTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,user',
            'department_id' => 'required|exists:departments,id',
            'user_type' => 'required_if:role,user|nullable|exists:user_types,code',
            'start_date' => 'required_if:role,user|date',
        ]);

        if ($user->isAdmin() && $validated['role'] === 'admin') {
            return back()->withErrors(['role' => 'Admin tidak dapat membuat akun admin lain']);
        }

        if ($user->isAdmin() && $validated['department_id'] != $user->department_id) {
            return back()->withErrors(['department_id' => 'Anda hanya dapat membuat user untuk departemen Anda']);
        }

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $authUser = Auth::user();
        $departments = $authUser->isSuperuser() ? Department::all() : Department::where('id', $authUser->department_id)->get();
        $userTypes = UserType::where('is_active', true)->get();

        return view('users.edit', compact('user', 'departments', 'userTypes'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'wa_lid' => 'nullable|string|max:50',
            'password' => 'nullable|min:8',
            'department_id' => 'required|exists:departments,id',
            'user_type' => 'required_if:role,user|nullable|exists:user_types,code',
            'start_date' => 'required_if:role,user|date',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}

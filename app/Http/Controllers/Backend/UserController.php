<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->has('role') && $request->role) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(20)->appends($request->query());
        return view('backend.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('backend.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        $role = Role::firstWhere('name', $request->role);
        $data = $request->only(['name', 'email', 'phone', 'address', 'city', 'is_active']);
        $data['role_id'] = $role?->id;
        $data['password'] = Hash::make($request->password);
        $data['is_active'] = $request->has('is_active');

        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Utilizator creat cu succes!');
    }

    public function show(User $user)
    {
        $user->load('orders');
        return view('backend.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('backend.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ];

        if ($request->has('password') && $request->password) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $request->validate($rules);

        $role = Role::firstWhere('name', $request->role);
        $data = $request->only(['name', 'email', 'phone', 'address', 'city', 'is_active']);
        $data['role_id'] = $role?->id;
        $data['is_active'] = $request->has('is_active');

        if ($request->has('password') && $request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Utilizator actualizat cu succes!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nu vă puteți șterge propriul cont!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilizator șters cu succes!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nu vă puteți dezactiva propriul cont!');
        }

        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Statusul utilizatorului a fost actualizat!');
    }
}
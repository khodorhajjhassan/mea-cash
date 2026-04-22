<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const PROTECTED_ROLES = ['super-admin', 'admin'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = $this->groupedPermissions();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($request->permissions);
        });

        return redirect()->route('admin.roles.index')
            ->with('success', __('roles.messages.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->name === 'super-admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'The super-admin role has all access and cannot be edited.');
        }

        $permissions = $this->groupedPermissions();

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super-admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'The super-admin role has all access and cannot be edited.');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        DB::transaction(function () use ($request, $role) {
            if ($role->name !== 'admin') {
                $role->update(['name' => $request->name]);
            }

            $role->syncPermissions($request->permissions);
        });

        return redirect()->route('admin.roles.index')
            ->with('success', __('roles.messages.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->with('error', __('roles.messages.cannot_delete_protected'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', __('roles.messages.deleted'));
    }

    /**
     * Role selection for users.
     */
    public function assignments(Request $request)
    {
        $query = User::query()->with('roles');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->paginate(20);
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.roles.assignments', compact('users', 'roles'));
    }

    /**
     * Update user roles.
     */
    public function updateAssignments(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $requestedRoles = collect($request->input('roles', []))->values();

        // Protection against removing last super-admin
        if ($user->hasRole('super-admin') && ! $requestedRoles->contains('super-admin')) {
            $superAdminCount = User::role('super-admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Cannot remove the last super-admin.');
            }
        }

        if ($user->hasRole('super-admin') && ! $requestedRoles->contains('super-admin')) {
            return back()->with('error', 'Super-admin assignment is protected for this user.');
        }

        $user->syncRoles($requestedRoles->all());

        return back()->with('success', __('roles.messages.assignment_updated'));
    }

    private function groupedPermissions()
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0]);
    }
}

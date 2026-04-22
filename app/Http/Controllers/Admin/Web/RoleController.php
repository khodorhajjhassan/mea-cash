<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
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
        $permissions = Permission::all()->groupBy(function($perm) {
            return explode('.', $perm->name)[0];
        });

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
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

        $permissions = Permission::all()->groupBy(function($perm) {
            return explode('.', $perm->name)[0];
        });

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
            'permissions' => 'required|array'
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
        if (in_array($role->name, ['super-admin', 'admin'])) {
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
        $query = \App\Models\User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->paginate(20);
        $roles = Role::all();

        return view('admin.roles.assignments', compact('users', 'roles'));
    }

    /**
     * Update user roles.
     */
    public function updateAssignments(Request $request, \App\Models\User $user)
    {
        // Protection against removing last super-admin
        if ($user->hasRole('super-admin') && !in_array('super-admin', $request->roles ?? [])) {
            $superAdminCount = \App\Models\User::role('super-admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Cannot remove the last super-admin.');
            }
        }

        $user->syncRoles($request->roles);

        return back()->with('success', __('roles.messages.assignment_updated'));
    }
}

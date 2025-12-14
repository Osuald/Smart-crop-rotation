<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->latest()->get();
        $permissions = Permission::all()->groupBy('category');

        return view('admin.role_management', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('category');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Attach permissions
            if ($request->has('permissions')) {
                $role->permissions()->attach($request->permissions);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role created successfully',
                    'role' => $role->load('permissions')
                ]);
            }

            return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating role: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error creating role: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'role' => $role
            ]);
        }

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('category');
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Sync permissions
            $role->permissions()->sync($request->permissions ?? []);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role updated successfully',
                    'role' => $role->load('permissions')
                ]);
            }

            return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating role: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating role: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        try {
            // Check if role has users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that has assigned users'
                ], 403);
            }

            // Detach all permissions
            $role->permissions()->detach();

            // Delete the role
            $role->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role deleted successfully'
                ]);
            }

            return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting role: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    /**
     * Manage role permissions
     */
    public function permissions(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all()->groupBy('category');

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'role' => $role,
                'permissions' => $permissions
            ]);
        }

        return view('admin.roles.permissions', compact('role', 'permissions'));
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, Role $role)
    {
        try {
            $permissionIds = $request->input('permissions', []);

            // Sync the role's permissions
            $role->permissions()->sync($permissionIds);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role permissions updated successfully'
                ]);
            }

            return redirect()->route('admin.roles.permissions', $role)->with('success', 'Role permissions updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating permissions: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }
}

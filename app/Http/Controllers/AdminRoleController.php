<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Controller as BaseController; // Add this import

class AdminRoleController extends BaseController // Make sure extending BaseController
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::query();

        // Apply search filter if provided
        if ($request->has('filter') && $request->filter) {
            $filter = $request->filter;
            $query->where(function($q) use ($filter) {
                $q->where('name', 'like', "%{$filter}%")
                  ->orWhere('description', 'like', "%{$filter}%")
                  ->orWhere('permissions', 'like', "%{$filter}%");
            });
        }

        $roles = $query->orderBy('created_at', 'desc')->get();
        $availablePermissions = Role::getAvailablePermissions();

        return view('admin.admin-roles', compact('roles', 'availablePermissions'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $availablePermissions = Role::getAvailablePermissions();
        return view('admin.admin-roles.create', compact('availablePermissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'permissions' => $request->permissions ?: [],
                'status' => 'active'
            ]);

            return redirect()->route('admin.admin-roles.index')
                ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role
     */
    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            $availablePermissions = Role::getAvailablePermissions();
            return view('admin.admin-roles.show', compact('role', 'availablePermissions'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.admin-roles.index')
                ->with('error', 'Role not found.');
        }
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit($id)
    {
        try {
            $role = Role::findOrFail($id);
            $availablePermissions = Role::getAvailablePermissions();
            return view('admin.admin-roles.edit', compact('role', 'availablePermissions'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.admin-roles.index')
                ->with('error', 'Role not found.');
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name,' . $id,
                'description' => 'nullable|string|max:1000',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please correct the errors below.');
            }

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'permissions' => $request->permissions ?: []
            ]);

            return redirect()->route('admin.admin-roles.index')
                ->with('success', 'Role updated successfully.');

        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.admin-roles.index')
                ->with('error', 'Role not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role from storage
     */
    public function destroy(Request $request, $id = null)
    {
        try {
            if ($request->has('role_ids')) {
                // Bulk delete
                $roleIds = $request->role_ids;
                $deletedCount = Role::whereIn('id', $roleIds)->delete();
                
                return redirect()->route('admin.admin-roles.index')
                    ->with('success', "Successfully deleted {$deletedCount} role(s).");
            } else {
                // Single delete
                $role = Role::findOrFail($id);
                $roleName = $role->name;
                $role->delete();
                
                return redirect()->route('admin.admin-roles.index')
                    ->with('success', "Role '{$roleName}' deleted successfully.");
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.admin-roles.index')
                ->with('error', 'Role not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.admin-roles.index')
                ->with('error', 'Failed to delete role(s): ' . $e->getMessage());
        }
    }

    /**
     * Get role data for AJAX requests
     */
    public function getRoleData($id)
    {
        try {
            $role = Role::findOrFail($id);
            return response()->json([
                'success' => true,
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'permissions' => $role->permissions,
                    'status' => $role->status
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.'
            ], 404);
        }
    }
}
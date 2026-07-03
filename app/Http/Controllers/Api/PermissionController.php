<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends BaseController
{
    /**
     * List semua permission (grouped)
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::all()->groupBy('group');
        return $this->success($permissions, 'List permission berhasil diambil');
    }

    /**
     * Buat permission baru
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'group' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'group' => $request->group ?? 'other',
            'description' => $request->description,
            'guard_name' => 'web',
        ]);

        return $this->success($permission, 'Permission berhasil dibuat', 201);
    }

    /**
     * Update permission
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id,
            'group' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update($request->only(['name', 'group', 'description']));

        return $this->success($permission, 'Permission berhasil diupdate');
    }

    /**
     * Hapus permission
     */
    public function destroy($id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return $this->success(null, 'Permission berhasil dihapus');
    }
}
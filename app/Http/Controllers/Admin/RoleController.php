<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\MenuHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected const PROTECTED_ROLE = 'Admin';

    public function index(): View
    {
        return view('pages.admin.roles.index', [
            'title' => 'Kelola Role',
            'roles' => Role::withCount('users')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.roles.create', [
            'title' => 'Tambah Role',
            'permissionGroups' => MenuHelper::permissionGroups(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'in:'.implode(',', array_keys(MenuHelper::permissionLabels()))],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role): View|RedirectResponse
    {
        if ($role->name === self::PROTECTED_ROLE) {
            return redirect()->route('roles.index')->withErrors(['role' => 'Role Admin tidak dapat diubah.']);
        }

        return view('pages.admin.roles.edit', [
            'title' => 'Ubah Role',
            'role' => $role,
            'permissionGroups' => MenuHelper::permissionGroups(),
            'currentPermissions' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === self::PROTECTED_ROLE) {
            return redirect()->route('roles.index')->withErrors(['role' => 'Role Admin tidak dapat diubah.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'in:'.implode(',', array_keys(MenuHelper::permissionLabels()))],
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === self::PROTECTED_ROLE) {
            return back()->withErrors(['role' => 'Role Admin tidak dapat dihapus.']);
        }

        if (User::role($role->name)->exists()) {
            return back()->withErrors(['role' => 'Role tidak dapat dihapus karena masih digunakan oleh user.']);
        }

        $role->delete();

        return back()->with('status', 'Role berhasil dihapus.');
    }
}

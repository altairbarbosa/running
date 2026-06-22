<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionGroupController extends Controller
{
    public function index()
    {
        $groups = PermissionGroup::withCount('users')->with('permissions')->orderByDesc('is_system')->orderBy('name')->get();

        return view('permissions.index', ['groups' => $groups, 'catalog' => config('permissions.catalog')]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $key = Str::slug($data['name']);
        $base = $key ?: 'grupo';
        for ($suffix = 2; PermissionGroup::where('key', $key)->exists(); $suffix++) {
            $key = $base.'-'.$suffix;
        }
        $group = PermissionGroup::create(['name' => $data['name'], 'key' => $key, 'description' => $data['description'] ?? null]);
        $this->sync($group, $data['permissions'] ?? []);

        return back()->with('success', 'Grupo de permissões criado.');
    }

    public function update(Request $request, PermissionGroup $permissionGroup)
    {
        $data = $this->validated($request);
        $permissionGroup->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        $this->sync($permissionGroup, $data['permissions'] ?? []);

        return back()->with('success', 'Grupo de permissões atualizado.');
    }

    public function destroy(PermissionGroup $permissionGroup)
    {
        abort_if($permissionGroup->is_system, 422, 'Grupos padrão não podem ser excluídos.');
        abort_if($permissionGroup->users()->exists(), 422, 'Reatribua os usuários antes de excluir este grupo.');
        $permissionGroup->delete();

        return back()->with('success', 'Grupo de permissões excluído.');
    }

    private function validated(Request $request): array
    {
        $allowed = collect(config('permissions.catalog'))->flatMap(fn ($items) => array_keys($items))->all();

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allowed)],
        ]);
    }

    private function sync(PermissionGroup $group, array $permissions): void
    {
        $group->permissions()->delete();
        $group->permissions()->createMany(array_map(fn ($permission) => ['permission' => $permission], array_values(array_unique($permissions))));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MuscleGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MuscleGroupController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validated($request);
        MuscleGroup::create([...$data, 'slug' => Str::slug($data['name']), 'sort_order' => ((int) MuscleGroup::max('sort_order')) + 10]);

        return redirect()->route('exercises.index')->with('success', 'Grupo muscular cadastrado.');
    }

    public function update(Request $request, MuscleGroup $muscleGroup)
    {
        $data = $this->validated($request, $muscleGroup);
        if (! $data['active'] && $muscleGroup->exercises()->exists()) {
            throw ValidationException::withMessages(['active' => 'Este grupo possui exercícios vinculados e não pode ser desativado.']);
        }

        $muscleGroup->update([...$data, 'slug' => Str::slug($data['name'])]);

        return redirect()->route('exercises.index')->with('success', 'Grupo muscular atualizado.');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'groups' => ['required', 'array'],
            'groups.*' => ['required', 'integer', 'distinct', 'exists:muscle_groups,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['groups'] as $index => $groupId) {
                MuscleGroup::whereKey($groupId)->update(['sort_order' => ($index + 1) * 10]);
            }
        });

        return response()->json(['saved' => true]);
    }

    private function validated(Request $request, ?MuscleGroup $group = null): array
    {
        $request->merge(['slug' => Str::slug($request->string('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('muscle_groups')->ignore($group)],
            'slug' => ['required', 'max:100', Rule::unique('muscle_groups')->ignore($group)],
            'active' => ['required', 'boolean'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $exercises = Exercise::query()
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->trim().'%'))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('exercises.index', compact('exercises'));
    }

    public function create()
    {
        return view('exercises.form', ['exercise' => new Exercise]);
    }

    public function store(Request $request)
    {
        Exercise::create($this->validated($request));

        return redirect()->route('exercises.index')->with('success', 'Exercício cadastrado.');
    }

    public function edit(Exercise $exercise)
    {
        return view('exercises.form', compact('exercise'));
    }

    public function update(Request $request, Exercise $exercise)
    {
        $exercise->update($this->validated($request, $exercise));

        return redirect()->route('exercises.index')->with('success', 'Exercício atualizado.');
    }

    public function destroy(Exercise $exercise)
    {
        $exercise->update(['active' => false]);

        return back()->with('success', 'Exercício desativado.');
    }

    private function validated(Request $request, ?Exercise $exercise = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('exercises')->ignore($exercise)],
            'muscle_group' => ['nullable', 'string', 'max:80'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'active' => ['required', 'boolean'],
        ]);
    }
}

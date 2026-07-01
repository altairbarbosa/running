<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\MuscleGroup;
use App\Services\ExerciseMediaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $exercises = Exercise::query()->with(['muscleGroup', 'media'])
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->trim().'%'))
            ->when($request->integer('muscle_group_id'), fn ($query, $groupId) => $query->where('muscle_group_id', $groupId))
            ->orderBy('muscle_group_id')->orderBy('name')->paginate(30)->withQueryString();

        $muscleGroups = MuscleGroup::query()->withCount('exercises')->orderBy('sort_order')->orderBy('name')->get();
        $modalExerciseId = $request->integer('exercise') ?: (int) $request->session()->getOldInput('exercise_id');
        $modalExercise = $modalExerciseId ? Exercise::with('media')->find($modalExerciseId) : null;

        return view('exercises.index', compact('exercises', 'muscleGroups', 'modalExercise'));
    }

    public function create()
    {
        return redirect()->route('exercises.index', ['modal' => 'create']);
    }

    public function store(Request $request, ExerciseMediaService $mediaService)
    {
        $data = $this->validated($request);
        $exercise = Exercise::create(collect($data)->except(['images', 'video_url', 'remove_media'])->all());
        $mediaService->update($exercise, $request->file('images', []), $data['video_url'] ?? null);

        return redirect()->route('exercises.index')->with('success', 'Exercício cadastrado.');
    }

    public function edit(Exercise $exercise)
    {
        return redirect()->route('exercises.index', ['modal' => 'edit', 'exercise' => $exercise->id]);
    }

    public function update(Request $request, Exercise $exercise, ExerciseMediaService $mediaService)
    {
        $data = $this->validated($request, $exercise);
        $exercise->update(collect($data)->except(['images', 'video_url', 'remove_media'])->all());
        $mediaService->update($exercise, $request->file('images', []), $data['video_url'] ?? null, $data['remove_media'] ?? []);

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
            'muscle_group_id' => ['required', 'integer', 'exists:muscle_groups,id'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'active' => ['required', 'boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', File::image()->max(5 * 1024)],
            'video_url' => ['nullable', 'url:http,https', 'max:2048'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['integer', 'exists:exercise_media,id'],
        ]);
    }

}

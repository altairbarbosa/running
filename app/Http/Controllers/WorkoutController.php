<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $workouts = Workout::with(['member', 'items'])
            ->when(! $user->isStaff(), fn ($query) => $query->where('member_id', $user->id))
            ->latest('starts_at')->paginate(12);

        return view('workouts.index', compact('workouts'));
    }

    public function create()
    {
        return view('workouts.create', [
            'members' => User::where('role', 'member')->where('active', true)->orderBy('name')->get(),
            'exercises' => Exercise::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'member')->where('active', true))],
            'name' => ['required', 'string', 'max:120'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.exercise_id' => ['required', 'distinct', 'exists:exercises,id'],
            'items.*.sets' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.repetitions' => ['required', 'string', 'max:30'],
            'items.*.weight' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'items.*.rest_seconds' => ['nullable', 'integer', 'min:0', 'max:3600'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $workout = DB::transaction(function () use ($data, $request) {
            $workout = Workout::create([
                ...collect($data)->except('items')->all(),
                'created_by' => $request->user()->id,
                'status' => 'active',
            ]);
            foreach (array_values($data['items']) as $index => $item) {
                $workout->items()->create([...$item, 'position' => $index + 1]);
            }

            return $workout;
        });

        return redirect()->route('workouts.show', $workout)->with('success', 'Treino elaborado com sucesso.');
    }

    public function show(Request $request, Workout $workout)
    {
        abort_unless($request->user()->isStaff() || $workout->member_id === $request->user()->id, 403);
        $workout->load(['member', 'author', 'items.exercise']);

        return view('workouts.show', compact('workout'));
    }

    public function destroy(Workout $workout)
    {
        $workout->delete();

        return redirect()->route('workouts.index')->with('success', 'Treino removido.');
    }
}

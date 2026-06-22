<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = User::query()->with(['workouts.items'])
            ->where('role', 'member')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $modalMember = $request->integer('member') ? User::with(['workouts.items'])->where('role', 'member')->find($request->integer('member')) : null;

        return view('members.index', compact('members', 'modalMember'));
    }

    public function create()
    {
        return redirect()->route('members.index', ['modal' => 'create']);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['role'] = 'member';
        $data['password'] = $data['password'] ?: str()->password(16);
        User::create($data);

        return redirect()->route('members.index')->with('success', 'Aluno cadastrado com sucesso.');
    }

    public function edit(User $member)
    {
        abort_unless($member->role === 'member', 404);

        return redirect()->route('members.index', ['modal' => 'edit', 'member' => $member->id]);
    }

    public function update(Request $request, User $member)
    {
        abort_unless($member->role === 'member', 404);
        $data = $this->validated($request, $member);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Cadastro atualizado.');
    }

    public function destroy(User $member)
    {
        abort_unless($member->role === 'member', 404);
        $member->update(['active' => false]);

        return back()->with('success', 'Aluno desativado.');
    }

    private function validated(Request $request, ?User $member = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($member)],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['required', 'boolean'],
            'password' => [$member ? 'nullable' : 'required', 'string', 'min:8'],
        ]);
    }
}

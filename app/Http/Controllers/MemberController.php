<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = User::query()
            ->where('role', 'member')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.form', ['member' => new User]);
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

        return view('members.form', compact('member'));
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

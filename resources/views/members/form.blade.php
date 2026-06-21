<x-layouts.app :title="$member->exists ? 'Editar aluno' : 'Novo aluno'">
    <form method="POST" action="{{ $member->exists ? route('members.update', $member) : route('members.store') }}" class="card mx-auto max-w-3xl p-6 lg:p-8">@csrf @if($member->exists)@method('PUT')@endif
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2"><label class="label">Nome completo</label><input class="field" name="name" value="{{ old('name', $member->name) }}" required></div>
            <div><label class="label">E-mail</label><input class="field" type="email" name="email" value="{{ old('email', $member->email) }}" required></div>
            <div><label class="label">Telefone</label><input class="field" name="phone" value="{{ old('phone', $member->phone) }}"></div>
            <div><label class="label">Data de nascimento</label><input class="field" type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}"></div>
            <div><label class="label">Senha {{ $member->exists ? '(deixe em branco para manter)' : '' }}</label><input class="field" type="password" name="password" {{ $member->exists ? '' : 'required' }}></div>
            <div class="sm:col-span-2"><label class="label">Endereço</label><input class="field" name="address" value="{{ old('address', $member->address) }}"></div>
            <div><label class="label">Status</label><select class="field" name="active"><option value="1" @selected(old('active', $member->active ?? true))>Ativo</option><option value="0" @selected(!old('active', $member->active ?? true))>Inativo</option></select></div>
        </div>
        <div class="mt-8 flex justify-end gap-3"><a class="btn-secondary" href="{{ route('members.index') }}">Cancelar</a><button class="btn-primary">Salvar aluno</button></div>
    </form>
</x-layouts.app>

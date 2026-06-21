<x-layouts.app title="Nova matrícula">
    <form method="POST" action="{{ route('memberships.store') }}" class="card mx-auto max-w-3xl p-6 lg:p-8" x-data="{ plan: '{{ old('plan_id') }}' }">@csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2"><label class="label">Aluno</label><select class="field" name="member_id" required><option value="">Selecione o aluno</option>@foreach($members as $member)<option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $member->name }} · {{ $member->email }}</option>@endforeach</select></div>
            <div class="sm:col-span-2"><label class="label">Plano</label><select class="field" name="plan_id" x-model="plan" required><option value="">Selecione o plano</option>@foreach($plans as $plan)<option value="{{ $plan->id }}">{{ $plan->name }} · R$ {{ number_format($plan->price, 2, ',', '.') }}</option>@endforeach</select></div>
            <div><label class="label">Data de início</label><input class="field" type="date" name="starts_at" value="{{ old('starts_at', today()->format('Y-m-d')) }}" required></div>
            <div><label class="label">Dia de vencimento</label><input class="field" type="number" min="1" max="28" name="billing_day" value="{{ old('billing_day', 10) }}" required><p class="mt-1 text-xs text-slate-400">Entre 1 e 28.</p></div>
            <div><label class="label">Término previsto</label><input class="field" type="date" name="ends_at" value="{{ old('ends_at') }}"></div>
            <div class="sm:col-span-2"><label class="label">Observações</label><textarea class="field" name="notes" rows="3">{{ old('notes') }}</textarea></div>
        </div>
        <div class="mt-6 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-700">Ao salvar, o sistema gera a taxa de matrícula e as próximas cobranças automaticamente.</div>
        <div class="mt-8 flex justify-end gap-3"><a class="btn-secondary" href="{{ route('memberships.index') }}">Cancelar</a><button class="btn-primary">Confirmar matrícula</button></div>
    </form>
</x-layouts.app>

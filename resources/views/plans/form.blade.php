<x-layouts.app :title="$plan->exists ? 'Editar plano' : 'Novo plano'">
    <form method="POST" action="{{ $plan->exists ? route('plans.update', $plan) : route('plans.store') }}" class="card mx-auto max-w-3xl p-6 lg:p-8">@csrf @if($plan->exists)@method('PUT')@endif
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2"><label class="label">Nome do plano</label><input class="field" name="name" value="{{ old('name', $plan->name) }}" placeholder="Ex.: Musculação mensal" required></div>
            <div><label class="label">Valor recorrente</label><input class="field" type="number" min="0.01" step="0.01" name="price" value="{{ old('price', $plan->price) }}" required></div>
            <div><label class="label">Taxa de matrícula</label><input class="field" type="number" min="0" step="0.01" name="enrollment_fee" value="{{ old('enrollment_fee', $plan->enrollment_fee ?? 0) }}" required></div>
            <div><label class="label">Periodicidade</label><select class="field" name="billing_interval_months"><option value="1" @selected(old('billing_interval_months', $plan->billing_interval_months) == 1)>Mensal</option><option value="3" @selected(old('billing_interval_months', $plan->billing_interval_months) == 3)>Trimestral</option><option value="6" @selected(old('billing_interval_months', $plan->billing_interval_months) == 6)>Semestral</option><option value="12" @selected(old('billing_interval_months', $plan->billing_interval_months) == 12)>Anual</option></select></div>
            <div><label class="label">Status</label><select class="field" name="active"><option value="1" @selected(old('active', $plan->active ?? true))>Ativo</option><option value="0" @selected(!old('active', $plan->active ?? true))>Inativo</option></select></div>
            <div class="sm:col-span-2"><label class="label">Descrição</label><textarea class="field" name="description" rows="4">{{ old('description', $plan->description) }}</textarea></div>
        </div>
        @if($plan->exists)<p class="mt-5 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">Alterar o valor afeta apenas novas matrículas.</p>@endif
        <div class="mt-8 flex justify-end gap-3"><a class="btn-secondary" href="{{ route('plans.index') }}">Cancelar</a><button class="btn-primary">Salvar plano</button></div>
    </form>
</x-layouts.app>

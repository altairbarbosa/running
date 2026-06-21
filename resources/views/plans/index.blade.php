<x-layouts.app title="Planos">
    <div class="mb-6 flex justify-end"><a class="btn-primary" href="{{ route('plans.create') }}">+ Novo plano</a></div>
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($plans as $plan)
            <article class="card p-6">
                <div class="flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-brand-600">A cada {{ $plan->billing_interval_months }} {{ $plan->billing_interval_months === 1 ? 'mês' : 'meses' }}</p><h2 class="mt-2 text-xl font-black text-ink-950">{{ $plan->name }}</h2></div><span class="size-2 rounded-full {{ $plan->active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span></div>
                <p class="mt-5 text-3xl font-black text-ink-950"><span class="text-sm font-semibold text-slate-400">R$</span> {{ number_format($plan->price, 2, ',', '.') }}</p>
                <p class="mt-2 text-sm text-slate-500">Matrícula: R$ {{ number_format($plan->enrollment_fee, 2, ',', '.') }}</p>
                <p class="mt-4 min-h-10 text-sm text-slate-500">{{ $plan->description ?: 'Sem descrição adicional.' }}</p>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4"><span class="text-xs font-semibold text-slate-500">{{ $plan->memberships_count }} matrícula(s) ativa(s)</span><a class="text-sm font-semibold text-brand-700" href="{{ route('plans.edit', $plan) }}">Editar →</a></div>
            </article>
        @empty
            <p class="col-span-full py-12 text-center text-slate-500">Nenhum plano cadastrado.</p>
        @endforelse
    </div>
</x-layouts.app>

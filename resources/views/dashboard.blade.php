<x-layouts.app title="Visão geral">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="text-sm text-slate-500">Olá, {{ str(auth()->user()->name)->before(' ') }}.</p><h2 class="mt-1 text-2xl font-black text-ink-950">O que está acontecendo hoje</h2></div>
        @if(auth()->user()->isStaff())<a href="{{ route('workouts.create') }}" class="btn-primary">+ Elaborar treino</a>@endif
    </div>
    @if(auth()->user()->isStaff())
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('memberships.index') }}" class="card p-6 transition hover:shadow-md"><p class="text-sm font-medium text-slate-500">Matrículas ativas</p><p class="mt-3 text-3xl font-black text-ink-950">{{ $membershipCount }}</p></a>
            <a href="{{ route('finance.index', ['status' => 'overdue']) }}" class="card border-red-100 p-6 transition hover:shadow-md"><p class="text-sm font-medium text-red-600">Inadimplência</p><p class="mt-3 text-3xl font-black text-red-700">R$ {{ number_format($overdueAmount, 2, ',', '.') }}</p></a>
            <a href="{{ route('finance.index', ['status' => 'paid']) }}" class="card border-emerald-100 p-6 transition hover:shadow-md"><p class="text-sm font-medium text-emerald-600">Recebido no mês</p><p class="mt-3 text-3xl font-black text-emerald-700">R$ {{ number_format($receivedThisMonth, 2, ',', '.') }}</p></a>
        </div>
    @endif
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @if(auth()->user()->isStaff())<div class="card p-6"><p class="text-sm font-medium text-slate-500">Alunos cadastrados</p><p class="mt-3 text-4xl font-black text-ink-950">{{ $memberCount }}</p></div><div class="card p-6"><p class="text-sm font-medium text-slate-500">Exercícios ativos</p><p class="mt-3 text-4xl font-black text-ink-950">{{ $exerciseCount }}</p></div>@endif
        <div class="card p-6"><p class="text-sm font-medium text-slate-500">Treinos ativos</p><p class="mt-3 text-4xl font-black text-brand-600">{{ $workoutCount }}</p></div>
    </div>
    <div class="card mt-8 overflow-hidden"><div class="border-b border-slate-100 px-6 py-5"><h3 class="font-bold text-ink-950">Treinos recentes</h3></div><div class="divide-y divide-slate-100">
        @forelse($workouts as $workout)<a href="{{ route('workouts.show', $workout) }}" class="flex items-center justify-between px-6 py-4 hover:bg-slate-50"><div><p class="font-semibold text-ink-950">{{ $workout->name }}</p><p class="text-sm text-slate-500">{{ $workout->member->name }}</p></div><div class="text-right"><p class="text-sm font-medium">{{ $workout->starts_at->format('d/m/Y') }}</p><span class="text-xs text-brand-600">Ver treino →</span></div></a>@empty<div class="px-6 py-12 text-center text-sm text-slate-500">Nenhum treino elaborado ainda.</div>@endforelse
    </div></div>
</x-layouts.app>

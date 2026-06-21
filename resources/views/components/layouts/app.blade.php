<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Running' }} · Running</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div x-data="{ menu: false }" class="min-h-screen lg:flex">
    <div x-show="menu" x-cloak class="fixed inset-0 z-30 bg-slate-950/40 lg:hidden" @click="menu = false"></div>
    <aside :class="menu ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-ink-950 text-white transition-transform lg:static lg:translate-x-0">
        <div class="flex h-20 items-center gap-3 border-b border-white/10 px-7">
            <div class="grid size-10 place-items-center rounded-xl bg-brand-500 font-black text-ink-950">R</div>
            <div><div class="font-bold tracking-wide">RUNNING</div><div class="text-xs text-slate-400">Gestão de academia</div></div>
        </div>
        <nav class="flex-1 space-y-1 p-4 text-sm">
            <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Visão geral</a>
            @if(auth()->user()->isStaff())
                <a href="{{ route('members.index') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('members.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Alunos</a>
                <a href="{{ route('exercises.index') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('exercises.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Exercícios</a>
                <p class="px-4 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[.2em] text-slate-600">Financeiro</p>
                <a href="{{ route('plans.index') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('plans.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Planos</a>
                <a href="{{ route('memberships.index') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('memberships.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Matrículas</a>
                <a href="{{ route('finance.index') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('finance.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Cobranças</a>
                @if(auth()->user()->isAdmin())<a href="{{ route('users.index') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Usuários e acessos</a>@endif
            @endif
            <a href="{{ route('workouts.index') }}" class="block rounded-xl px-4 py-3 {{ request()->routeIs('workouts.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Treinos</a>
        </nav>
        <div class="border-t border-white/10 p-5">
            <a href="{{ route('profile.edit') }}" class="mb-4 flex items-center gap-3 rounded-xl p-1 hover:bg-white/5"><x-avatar :user="auth()->user()" size="sm"/><div class="min-w-0"><div class="truncate text-sm font-semibold">{{ auth()->user()->name }}</div><div class="text-xs text-slate-500">Editar perfil</div></div></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-xs font-semibold text-slate-400 hover:text-white">Sair da conta</button></form>
        </div>
    </aside>
    <main class="min-w-0 flex-1">
        <header class="flex h-20 items-center justify-between border-b border-slate-200 bg-white px-5 lg:px-10">
            <button class="rounded-lg p-2 lg:hidden" @click="menu = true" aria-label="Abrir menu">☰</button>
            <div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-600">Running</p><h1 class="text-xl font-bold text-ink-950">{{ $title ?? 'Visão geral' }}</h1></div>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-full bg-brand-50 p-1 pr-3 text-xs font-bold text-brand-700"><x-avatar :user="auth()->user()" size="sm"/>{{ auth()->user()->role === 'member' ? 'Aluno' : (auth()->user()->role === 'trainer' ? 'Professor' : 'Administrador') }}</a>
        </header>
        <div class="p-5 lg:p-10">
            @if(session('success'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><strong>Revise os dados:</strong><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            {{ $slot }}
        </div>
    </main>
</div>
</body>
</html>

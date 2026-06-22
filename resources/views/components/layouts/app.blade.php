<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#101a1e">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>{{ $title ?? 'Running' }} · Running</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-x-hidden">
<div x-data="{ menu: false }" @keydown.escape.window="menu = false" class="min-h-screen lg:flex">
    <div x-show="menu" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-ink-950/60 backdrop-blur-sm lg:hidden" @click="menu = false"></div>
    <aside :class="menu ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 flex w-[min(19rem,88vw)] flex-col bg-ink-950 text-white shadow-2xl transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:translate-x-0 lg:shadow-none">
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">
            <x-logo />
            <button class="grid size-10 place-items-center rounded-xl text-slate-400 hover:bg-white/5 hover:text-white lg:hidden" @click="menu = false" aria-label="Fechar menu"><x-icon name="close" /></button>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto p-3 text-sm">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="dashboard" />Visão geral</a>
            @if(auth()->user()->isStaff())
                <p class="px-3.5 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[.2em] text-slate-600">Gestão</p>
                <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="members" />Alunos</a>
                <a href="{{ route('exercises.index') }}" class="nav-link {{ request()->routeIs('exercises.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="exercise" />Exercícios</a>
                <a href="{{ route('workouts.index') }}" class="nav-link {{ request()->routeIs('workouts.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="workout" />Treinos</a>
                <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="shop" />Loja</a>
                <p class="px-3.5 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[.2em] text-slate-600">Financeiro</p>
                <a href="{{ route('plans.index') }}" class="nav-link {{ request()->routeIs('plans.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="plans" />Planos</a>
                <a href="{{ route('memberships.index') }}" class="nav-link {{ request()->routeIs('memberships.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="membership" />Matrículas</a>
                <a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="finance" />Cobranças</a>
                @if(auth()->user()->isAdmin())
                    <p class="px-3.5 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[.2em] text-slate-600">Administração</p>
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="users" />Usuários e acessos</a>
                @endif
            @else
                <p class="px-3.5 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[.2em] text-slate-600">Minha área</p>
                <a href="{{ route('workouts.index') }}" class="nav-link {{ request()->routeIs('workouts.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="workout" />Treinos</a>
                <a href="{{ route('portal.billing') }}" class="nav-link {{ request()->routeIs('portal.billing') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="receipt" />Mensalidades</a>
                <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.*') ? 'nav-link-active' : 'nav-link-idle' }}"><x-icon name="shop" />Loja</a>
            @endif
        </nav>
        <div class="border-t border-white/10 p-3">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl p-2.5 hover:bg-white/5"><x-avatar :user="auth()->user()" size="sm"/><div class="min-w-0 flex-1"><div class="truncate text-sm font-semibold">{{ auth()->user()->name }}</div><div class="text-xs text-slate-500">Editar perfil</div></div><x-icon name="profile" size="size-4" class="text-slate-500" /></a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">@csrf<button class="nav-link w-full nav-link-idle"><x-icon name="logout" />Sair da conta</button></form>
        </div>
    </aside>
    <main class="min-w-0 flex-1 bg-slate-50">
        <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/95 backdrop-blur">
            <div class="page-shell flex h-16 items-center justify-between gap-3 px-4 sm:h-20 sm:px-6 lg:px-8">
                <button class="grid size-10 shrink-0 place-items-center rounded-xl border border-slate-200 text-ink-950 lg:hidden" @click="menu = true" aria-label="Abrir menu"><x-icon name="menu" /></button>
                <div class="min-w-0 flex-1"><p class="hidden text-[10px] font-bold uppercase tracking-[.2em] text-brand-600 sm:block">Painel Running</p><h1 class="truncate text-lg font-black text-ink-950 sm:text-xl">{{ $title ?? 'Visão geral' }}</h1></div>
                <a href="{{ route('profile.edit') }}" class="flex shrink-0 items-center gap-2 rounded-full bg-brand-50 p-1 sm:pr-3"><x-avatar :user="auth()->user()" size="sm"/><span class="hidden text-xs font-bold text-brand-700 sm:inline">{{ auth()->user()->role === 'member' ? 'Aluno' : (auth()->user()->role === 'trainer' ? 'Professor' : 'Administrador') }}</span></a>
            </div>
        </header>
        <div class="page-shell p-4 pb-10 sm:p-6 lg:p-8 lg:pb-12">
            @if(session('success'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><strong>Revise os dados:</strong><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            {{ $slot }}
        </div>
    </main>
</div>
</body>
</html>

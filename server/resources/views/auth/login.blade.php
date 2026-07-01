<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#101a1e"><link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml"><title>Entrar · Running</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="min-h-screen bg-ink-950">
<main class="grid min-h-screen lg:grid-cols-2">
    <section class="hidden flex-col justify-between bg-[radial-gradient(circle_at_top_left,_#08765e,_#101a1e_58%)] p-14 text-white lg:flex">
        <x-logo />
        <div class="max-w-xl"><p class="mb-4 text-sm font-bold uppercase tracking-[.25em] text-brand-500">Gestão inteligente</p><h1 class="text-5xl font-black leading-tight">Treinos bem cuidados.<br>Academia bem gerida.</h1><p class="mt-6 text-lg leading-relaxed text-slate-300">A nova geração do primeiro sistema Running, agora segura, simples e pronta para crescer.</p></div>
        <p class="text-sm text-slate-500">Laravel · Blade · Alpine.js</p>
    </section>
    <section class="flex items-center justify-center bg-slate-50 p-6"><div class="w-full max-w-md">
        <div class="mb-9 lg:hidden"><x-logo dark /></div>
        <p class="text-sm font-bold text-brand-600">BEM-VINDO DE VOLTA</p><h2 class="mt-2 text-3xl font-black text-ink-950">Acesse sua conta</h2><p class="mt-2 text-slate-500">Use suas credenciais para continuar.</p>
        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">@csrf
            <div><label class="label" for="email">E-mail</label><input class="field" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><div class="flex items-center justify-between"><label class="label" for="password">Senha</label><a href="{{ route('password.request') }}" class="text-xs font-bold text-brand-700 hover:underline">Esqueci minha senha</a></div><input class="field" id="password" name="password" type="password" required autocomplete="current-password"></div>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1"> Lembrar de mim</label>
            <button class="btn-primary w-full" type="submit">Entrar</button>
        </form>
        @if(session('status'))<div class="mt-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('status') }}</div>@endif
    </div></section>
</main></body></html>

<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#101a1e"><link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml"><title>Recuperar senha · Running</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-50">
<main class="flex min-h-screen items-center justify-center p-6"><div class="w-full max-w-md"><div class="mb-8"><x-logo dark /></div><div class="card p-6 sm:p-8"><p class="text-sm font-bold text-brand-600">RECUPERAR ACESSO</p><h1 class="mt-2 text-2xl font-black text-ink-950">Esqueceu sua senha?</h1><p class="mt-2 text-sm leading-6 text-slate-500">Informe seu e-mail. Se houver uma conta cadastrada, enviaremos um link seguro e temporário.</p>
    @if(session('status'))<div class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">@csrf<div><label class="label" for="email">E-mail</label><input class="field" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div><button class="btn-primary w-full">Enviar link de recuperação</button></form>
    <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-bold text-brand-700">Voltar para o login</a>
</div></div></main></body></html>

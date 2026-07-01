@props(['compact' => false, 'dark' => false])
<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <svg class="size-11 shrink-0" viewBox="0 0 48 48" fill="none" aria-label="Running">
        <rect width="48" height="48" rx="14" fill="#12C7A0"/>
        <circle cx="29.5" cy="11.5" r="3.5" fill="#0B1D21"/>
        <path d="m26.5 18-4.5 9.5 7 5-2 7M22 27.5l-7 7H9.5M25 21l-7.5 1.5-4-4M25 21l7 4 5-3" stroke="#0B1D21" stroke-width="3.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    @unless($compact)<div class="min-w-0"><div class="font-black tracking-[.14em] {{ $dark ? 'text-ink-950' : 'text-white' }}">RUNNING</div><div class="text-xs {{ $dark ? 'text-slate-500' : 'text-slate-400' }}">Gestão em movimento</div></div>@endunless
</div>

@props(['user', 'size' => 'md'])
@php($sizeClass = ['sm' => 'size-9 text-xs', 'md' => 'size-11 text-sm', 'lg' => 'size-24 text-2xl'][$size] ?? 'size-11 text-sm')
<span {{ $attributes->class([$sizeClass, 'inline-grid shrink-0 place-items-center overflow-hidden rounded-full bg-brand-100 font-black uppercase text-brand-700']) }}>
    @if($user->avatar_url)
        <img src="{{ $user->avatar_url }}" alt="Foto de {{ $user->name }}" class="size-full object-cover">
    @else
        {{ $user->initials }}
    @endif
</span>

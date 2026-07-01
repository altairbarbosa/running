<x-layouts.app title="Permissões">
    @php
        $groupData = fn ($group) => [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description ?? '',
            'permissions' => $group->permissions->pluck('permission')->all(),
            'action' => route('permissions.update', $group),
            'system' => $group->is_system,
        ];
    @endphp
    <div x-data="permissionManager(@js(route('permissions.store')))" @keydown.escape.window="close()">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><h2 class="text-lg font-black text-ink-950">Grupos de permissões</h2><p class="mt-1 text-sm text-slate-500">Defina conjuntos de acesso e atribua-os aos usuários.</p></div>
            <button type="button" class="btn-primary" @click="create()"><x-icon name="plus"/>Novo grupo</button>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($groups as $group)
                <article class="card p-5">
                    <div class="flex items-start justify-between gap-3"><div><div class="flex flex-wrap items-center gap-2"><h3 class="font-black text-ink-950">{{ $group->name }}</h3>@if($group->is_system)<span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700">Padrão</span>@endif</div><p class="mt-1 text-sm text-slate-500">{{ $group->description ?: 'Grupo personalizado de acesso.' }}</p></div><button class="icon-btn" title="Editar grupo" @click="edit({{ Js::from($groupData($group)) }})"><x-icon name="edit" size="size-4"/></button></div>
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4"><span class="text-xs text-slate-500">{{ $group->permissions->count() }} permissões · {{ $group->users_count }} usuários</span>@if(!$group->is_system && $group->users_count===0)<form method="POST" action="{{ route('permissions.destroy',$group) }}" onsubmit="return confirm('Excluir este grupo?')">@csrf @method('DELETE')<button class="icon-btn-danger" title="Excluir grupo"><x-icon name="trash" size="size-4"/></button></form>@endif</div>
                </article>
            @endforeach
        </div>

        <div x-show="modal" x-cloak class="fixed inset-0 z-50 overflow-hidden" role="dialog" aria-modal="true"><div class="flex h-full items-end justify-center sm:items-center sm:p-6"><div class="fixed inset-0 bg-ink-950/65 backdrop-blur-sm" @click="close()"></div><form :action="form.action" method="POST" class="relative flex h-[100dvh] w-full flex-col overflow-hidden bg-white shadow-2xl sm:h-[calc(100dvh-3rem)] sm:max-h-[760px] sm:max-w-3xl sm:rounded-3xl">@csrf<template x-if="mode==='edit'"><input type="hidden" name="_method" value="PUT"></template>
            <div class="flex shrink-0 items-start justify-between border-b border-slate-100 px-5 py-5 sm:px-7"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-600" x-text="mode==='edit'?'Atualizar grupo':'Novo grupo'"></p><h2 class="mt-1 text-xl font-black text-ink-950" x-text="mode==='edit'?'Editar permissões':'Criar grupo de permissões'"></h2></div><button type="button" class="grid size-10 place-items-center rounded-xl text-slate-400 hover:bg-slate-100" @click="close()"><x-icon name="close"/></button></div>
            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-7"><div class="grid gap-5 sm:grid-cols-2"><div><label class="label">Nome do grupo</label><input class="field" name="name" x-model="form.name" maxlength="100" required></div><div><label class="label">Descrição</label><input class="field" name="description" x-model="form.description" maxlength="255"></div></div><div class="mt-7 space-y-6">
                @foreach($catalog as $section => $permissions)
                    <section><h3 class="mb-3 text-xs font-black uppercase tracking-wider text-slate-400">{{ $section }}</h3><div class="grid gap-2 sm:grid-cols-2">@foreach($permissions as $key => $label)<label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:bg-slate-50"><input type="checkbox" name="permissions[]" value="{{ $key }}" x-model="form.permissions" class="size-4 accent-emerald-600"><span class="text-sm font-semibold text-slate-700">{{ $label }}</span></label>@endforeach</div></section>
                @endforeach
            </div></div>
            <div class="flex shrink-0 gap-3 border-t border-slate-100 px-5 py-4 sm:justify-end sm:px-7"><button type="button" class="btn-secondary" @click="close()">Cancelar</button><button class="btn-primary" x-text="mode==='edit'?'Salvar grupo':'Criar grupo'"></button></div>
        </form></div></div>
    </div>
    <script>function permissionManager(storeUrl){const empty=()=>({name:'',description:'',permissions:[],action:storeUrl});return{modal:false,mode:'create',form:empty(),create(){this.mode='create';this.form=empty();this.open()},edit(group){this.mode='edit';this.form={...group,permissions:[...group.permissions]};this.open()},open(){this.modal=true;document.body.classList.add('overflow-hidden')},close(){this.modal=false;document.body.classList.remove('overflow-hidden')}}}</script>
</x-layouts.app>

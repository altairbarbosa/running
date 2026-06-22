<x-layouts.app title="Exercícios">
    @php
        $oldModal = old('exercise_modal');
        $oldGroupModal = old('group_modal');
        $initialExercise = $modalExercise ? [
            'id' => $modalExercise->id,
            'name' => $modalExercise->name,
            'muscle_group_id' => $modalExercise->muscle_group_id,
            'instructions' => $modalExercise->instructions ?? '',
            'active' => $modalExercise->active ? '1' : '0',
            'action' => route('exercises.update', $modalExercise),
            'media' => $modalExercise->media->map(fn ($media) => ['id' => $media->id, 'type' => $media->type, 'url' => $media->public_url, 'provider' => $media->provider])->values()->all(),
        ] : null;
    @endphp
    <div
        x-data="exerciseManager({
            storeUrl: @js(route('exercises.store')),
            initialMode: @js($oldModal ?: request('modal')),
            initialExercise: @js($oldModal === 'edit' ? [
                'id' => old('exercise_id'),
                'name' => old('name'),
                'muscle_group_id' => old('muscle_group_id'),
                'instructions' => old('instructions', ''),
                'active' => old('active', '1'),
                'action' => old('exercise_action'),
                'media' => $modalExercise?->media->map(fn ($media) => ['id' => $media->id, 'type' => $media->type, 'url' => $media->public_url, 'provider' => $media->provider])->values()->all() ?? [],
            ] : $initialExercise),
            oldCreate: @js($oldModal === 'create' ? [
                'name' => old('name'),
                'muscle_group_id' => old('muscle_group_id'),
                'instructions' => old('instructions', ''),
                'active' => old('active', '1'),
            ] : null),
            groupStoreUrl: @js(route('muscle-groups.store')),
            groupInitialMode: @js($oldGroupModal),
            groupInitial: @js($oldGroupModal ? ['id' => old('group_id'), 'name' => old('name'), 'active' => old('active', '1'), 'action' => old('group_action')] : null),
            groupReorderUrl: @js(route('muscle-groups.reorder')),
            groups: @js($muscleGroups->map(fn ($group) => ['id' => $group->id, 'name' => $group->name, 'count' => $group->exercises_count, 'active' => $group->active ? '1' : '0', 'action' => route('muscle-groups.update', $group)])->values()),
        })"
        @keydown.escape.window="groupModal ? closeGroup() : close()"
    >
        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><h2 class="text-lg font-black text-ink-950">Biblioteca de exercícios</h2><p class="mt-1 text-sm text-slate-500">{{ $exercises->total() }} {{ $exercises->total() === 1 ? 'exercício encontrado' : 'exercícios encontrados' }}</p></div>
            <div class="flex flex-col gap-2 sm:flex-row"><button type="button" class="btn-secondary w-full sm:w-auto" @click="openGroups()">Gerenciar grupos</button><button type="button" class="btn-primary w-full sm:w-auto" @click="openCreate()"><x-icon name="plus" />Novo exercício</button></div>
        </div>

        <form class="card mb-6 grid gap-3 p-4 sm:grid-cols-[minmax(220px,1fr)_minmax(200px,280px)_auto] sm:items-end">
            <div><label class="label" for="exercise-search">Buscar</label><div class="relative"><x-icon name="search" size="size-4" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"/><input id="exercise-search" class="field pl-10" name="search" value="{{ request('search') }}" placeholder="Nome do exercício"></div></div>
            <div><label class="label" for="group-filter">Grupo muscular</label><select id="group-filter" class="field" name="muscle_group_id"><option value="">Todos os grupos</option>@foreach($muscleGroups as $group)<option value="{{ $group->id }}" @selected(request('muscle_group_id') == $group->id)>{{ $group->name }} · {{ $group->exercises_count }}{{ $group->active ? '' : ' · inativo' }}</option>@endforeach</select></div>
            <div class="flex gap-2"><button class="btn-primary flex-1 sm:flex-none">Aplicar</button>@if(request()->hasAny(['search', 'muscle_group_id']))<a href="{{ route('exercises.index') }}" class="btn-secondary">Limpar</a>@endif</div>
        </form>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($exercises as $exercise)
            <article class="card group flex min-h-52 flex-col p-5 transition hover:border-brand-100 hover:shadow-md">
                @if($cover = $exercise->media->firstWhere('type', 'image'))<img src="{{ $cover->public_url }}" alt="{{ $exercise->name }}" class="mb-4 h-36 w-full rounded-xl object-cover" loading="lazy">@endif
                <div class="flex items-start justify-between gap-4"><span class="inline-flex items-center gap-2 rounded-lg bg-brand-50 px-2.5 py-1.5 text-xs font-bold text-brand-700"><x-icon name="exercise" size="size-3.5" />{{ $exercise->muscleGroup?->name ?? 'Sem grupo' }}</span><span class="inline-flex items-center gap-1.5 text-[11px] font-semibold {{ $exercise->active ? 'text-emerald-700' : 'text-slate-400' }}"><span class="size-1.5 rounded-full {{ $exercise->active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>{{ $exercise->active ? 'Ativo' : 'Inativo' }}</span></div>
                <h3 class="mt-4 text-lg font-black text-ink-950">{{ $exercise->name }}</h3>
                <p class="mt-2 line-clamp-2 flex-1 text-sm leading-relaxed text-slate-500">{{ $exercise->instructions ?: 'Sem instruções adicionais.' }}</p>
                @if($exercise->media->isNotEmpty())<p class="mt-3 text-xs font-semibold text-slate-400">{{ $exercise->media->where('type', 'image')->count() }} imagem(ns) · {{ $exercise->media->where('type', 'video')->count() }} vídeo(s)</p>@endif
                <button type="button" class="icon-btn mt-5 self-end" title="Editar exercício" aria-label="Editar {{ $exercise->name }}" @click="openEdit({{ Js::from(['id' => $exercise->id, 'name' => $exercise->name, 'muscle_group_id' => $exercise->muscle_group_id, 'instructions' => $exercise->instructions ?? '', 'active' => $exercise->active ? '1' : '0', 'action' => route('exercises.update', $exercise), 'media' => $exercise->media->map(fn ($media) => ['id' => $media->id, 'type' => $media->type, 'url' => $media->public_url, 'provider' => $media->provider])->values()->all()]) }})"><x-icon name="edit" size="size-4"/></button>
            </article>
        @empty
            <div class="card col-span-full grid place-items-center px-6 py-16 text-center"><div class="grid size-12 place-items-center rounded-2xl bg-brand-50 text-brand-700"><x-icon name="exercise" /></div><p class="mt-4 font-bold text-ink-950">Nenhum exercício encontrado</p><p class="mt-1 text-sm text-slate-500">Ajuste os filtros ou cadastre um novo exercício.</p></div>
        @endforelse
        </div>
        <div class="mt-6">{{ $exercises->links() }}</div>

        <div x-show="modal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" :aria-label="mode === 'edit' ? 'Editar exercício' : 'Novo exercício'">
            <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-6">
                <div x-show="modal" x-transition.opacity class="fixed inset-0 bg-ink-950/65 backdrop-blur-sm" @click="close()"></div>
                <form :action="form.action" method="POST" enctype="multipart/form-data" x-show="modal" x-transition class="relative w-full rounded-t-3xl bg-white shadow-2xl sm:max-w-2xl sm:rounded-3xl">
                    @csrf
                    <template x-if="mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="exercise_modal" :value="mode">
                    <input type="hidden" name="exercise_id" :value="form.id">
                    <input type="hidden" name="exercise_action" :value="form.action">
                    <template x-for="mediaId in removedMedia" :key="mediaId"><input type="hidden" name="remove_media[]" :value="mediaId"></template>
                    <div class="flex items-start justify-between border-b border-slate-100 px-5 py-5 sm:px-7"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-600" x-text="mode === 'edit' ? 'Atualizar cadastro' : 'Novo cadastro'"></p><h2 class="mt-1 text-xl font-black text-ink-950" x-text="mode === 'edit' ? 'Editar exercício' : 'Novo exercício'"></h2></div><button type="button" class="grid size-10 place-items-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-ink-950" @click="close()" aria-label="Fechar"><x-icon name="close" /></button></div>
                    <div class="max-h-[65vh] space-y-5 overflow-y-auto px-5 py-6 sm:px-7">
                        @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><strong>Revise os dados:</strong><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                        <div><label class="label" for="exercise-name">Nome</label><input class="field" id="exercise-name" name="name" x-model="form.name" required x-ref="nameInput"></div>
                        <div><label class="label" for="exercise-group">Grupo muscular</label><select class="field" id="exercise-group" name="muscle_group_id" x-model="form.muscle_group_id" required><option value="">Selecione o grupo</option>@foreach($muscleGroups as $group)<option value="{{ $group->id }}" @disabled(!$group->active)>{{ $group->name }}{{ $group->active ? '' : ' (inativo)' }}</option>@endforeach</select><p class="mt-1.5 text-xs text-slate-400">O grupo organiza exercícios e facilita a elaboração dos treinos.</p></div>
                        <div><label class="label" for="exercise-instructions">Instruções</label><textarea class="field" id="exercise-instructions" name="instructions" rows="4" x-model="form.instructions"></textarea></div>
                        <div class="rounded-2xl border border-slate-200 p-4"><div><h3 class="text-sm font-bold text-ink-950">Imagens e vídeo</h3><p class="mt-1 text-xs text-slate-500">Até 5 imagens por envio e um novo link de vídeo.</p></div>
                            <div x-show="form.media.length" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3"><template x-for="item in form.media" :key="item.id"><div class="relative overflow-hidden rounded-xl border border-slate-200" :class="removedMedia.includes(item.id) && 'opacity-40'"><img x-show="item.type === 'image'" :src="item.url" class="h-24 w-full object-cover"><a x-show="item.type === 'video'" :href="item.url" target="_blank" class="grid h-24 place-items-center bg-slate-50 px-2 text-center text-xs font-bold text-brand-700" x-text="item.provider === 'other' ? 'Abrir vídeo' : item.provider"></a><button type="button" class="absolute right-1.5 top-1.5 rounded-lg bg-white/95 px-2 py-1 text-[10px] font-bold text-red-600 shadow" @click="toggleMedia(item.id)" x-text="removedMedia.includes(item.id) ? 'Manter' : 'Remover'"></button></div></template></div>
                            <div x-show="previews.length" class="mt-4 grid grid-cols-3 gap-3"><template x-for="preview in previews" :key="preview"><img :src="preview" class="h-24 w-full rounded-xl object-cover"></template></div>
                            <label class="btn-secondary mt-4 cursor-pointer"><x-icon name="plus" size="size-4"/>Selecionar imagens<input type="file" name="images[]" accept="image/png,image/jpeg,image/webp" multiple class="hidden" @change="previewImages($event)"></label>
                            <div class="mt-4"><label class="label" for="exercise-video">URL de um novo vídeo</label><input class="field" id="exercise-video" type="url" name="video_url" placeholder="https://youtube.com/... ou https://vimeo.com/..."><p class="mt-1 text-xs text-slate-400">YouTube, Vimeo ou outro endereço público.</p></div>
                        </div>
                        <div><label class="label" for="exercise-status">Status</label><select class="field" id="exercise-status" name="active" x-model="form.active"><option value="1">Ativo</option><option value="0">Inativo</option></select></div>
                    </div>
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:justify-end sm:px-7"><button type="button" class="btn-secondary" @click="close()">Cancelar</button><button class="btn-primary" x-text="mode === 'edit' ? 'Salvar alterações' : 'Salvar exercício'"></button></div>
                </form>
            </div>
        </div>

        <div x-show="groupModal" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true" aria-label="Gerenciar grupos musculares">
            <div class="fixed inset-0 bg-ink-950/65 backdrop-blur-sm" @click="closeGroup()"></div>
            <div class="relative flex h-[100dvh] w-full flex-col bg-white sm:absolute sm:left-1/2 sm:top-1/2 sm:h-auto sm:max-h-[88vh] sm:max-w-4xl sm:-translate-x-1/2 sm:-translate-y-1/2 sm:rounded-3xl sm:shadow-2xl">
                <div class="flex shrink-0 items-center gap-3 border-b border-slate-100 px-4 py-4 sm:px-7 sm:py-5">
                    <button x-show="groupView === 'form'" type="button" class="grid size-10 shrink-0 place-items-center rounded-xl border border-slate-200 lg:hidden" @click="groupView = 'list'" aria-label="Voltar"><span class="text-xl">←</span></button>
                    <div class="min-w-0 flex-1"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-brand-600">Configurações</p><h2 class="truncate text-lg font-black text-ink-950 sm:text-xl" x-text="groupView === 'form' && groupMode === 'edit' ? 'Editar grupo' : (groupView === 'form' ? 'Novo grupo' : 'Grupos musculares')"></h2></div>
                    <button type="button" class="grid size-10 shrink-0 place-items-center rounded-xl text-slate-400 hover:bg-slate-100" @click="closeGroup()"><x-icon name="close"/></button>
                </div>
                <div class="grid min-h-0 flex-1 lg:grid-cols-[1fr_340px]">
                    <section :class="groupView === 'list' ? 'flex' : 'hidden lg:flex'" class="min-h-0 flex-col border-slate-100 lg:border-r">
                        <div class="flex shrink-0 items-center justify-between px-4 py-4 sm:px-7"><div><h3 class="font-bold text-ink-950">Grupos cadastrados</h3><p class="text-xs text-slate-500">{{ $muscleGroups->count() }} grupos</p></div><button type="button" class="btn-primary min-h-10 px-3 py-2" @click="newGroup()"><x-icon name="plus" size="size-4"/>Adicionar</button></div>
                        <div x-ref="groupList" class="flex-1 space-y-2 overflow-y-auto px-4 pb-6 sm:px-7" @pointermove.window="moveGroup($event)" @pointerup.window="endGroupDrag()" @pointercancel.window="endGroupDrag()"><template x-for="(group, index) in groups" :key="group.id"><div :data-group-index="index" class="flex items-center gap-2 rounded-xl border bg-white p-2 transition" :class="dragIndex === index ? 'border-brand-500 shadow-md' : 'border-slate-200'"><button type="button" class="grid size-10 shrink-0 touch-none place-items-center rounded-lg text-xl font-bold text-slate-400 hover:bg-slate-100" @pointerdown="startGroupDrag(index, $event)" aria-label="Arrastar para reordenar">⠿</button><button type="button" class="flex min-w-0 flex-1 items-center justify-between gap-3 px-1 py-1.5 text-left" @click="editGroup(group)"><div class="min-w-0"><p class="truncate text-sm font-bold text-ink-950" x-text="group.name"></p><p class="text-xs text-slate-500" x-text="`${group.count} exercício(s)`"></p></div><span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-bold" :class="group.active === '1' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'" x-text="group.active === '1' ? 'Ativo' : 'Inativo'"></span></button></div></template><p x-show="orderSaving" class="py-2 text-center text-xs font-semibold text-brand-700">Salvando nova ordem…</p></div>
                    </section>
                    <form :action="groupForm.action" method="POST" :class="groupView === 'form' ? 'flex' : 'hidden lg:flex'" class="min-h-0 flex-col">@csrf<template x-if="groupMode==='edit'"><input type="hidden" name="_method" value="PUT"></template><input type="hidden" name="group_modal" :value="groupMode"><input type="hidden" name="group_id" :value="groupForm.id"><input type="hidden" name="group_action" :value="groupForm.action">
                        <div class="flex-1 overflow-y-auto p-5 sm:p-7"><h3 class="hidden font-bold text-ink-950 lg:block" x-text="groupMode==='edit'?'Editar grupo':'Novo grupo'"></h3>@if($oldGroupModal && $errors->any())<div class="mb-4 rounded-xl bg-red-50 px-3 py-2 text-xs text-red-700 lg:mt-4">{{ $errors->first() }}</div>@endif<div class="space-y-4 lg:mt-5"><div><label class="label">Nome</label><input class="field" name="name" x-model="groupForm.name" required x-ref="groupName"></div><div><label class="label">Status</label><select class="field" name="active" x-model="groupForm.active"><option value="1">Ativo</option><option value="0">Inativo</option></select><p class="mt-1.5 text-xs text-slate-400">Grupos com exercícios não podem ser desativados.</p></div></div></div>
                        <div class="shrink-0 border-t border-slate-100 bg-white p-4 sm:px-7"><button class="btn-primary w-full" x-text="groupMode==='edit'?'Salvar grupo':'Adicionar grupo'"></button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exerciseManager(config) {
            const empty = () => ({ id: '', name: '', muscle_group_id: '', instructions: '', active: '1', action: config.storeUrl, media: [] });
            return {
                modal: false,
                mode: 'create',
                form: empty(),
                previews: [],
                removedMedia: [],
                groupModal: false,
                groupView: 'list',
                groupMode: 'create',
                groupForm: { id: '', name: '', active: '1', action: config.groupStoreUrl },
                groups: config.groups,
                dragIndex: null,
                dragSnapshot: [],
                orderSaving: false,
                init() {
                    if (config.initialMode === 'edit' && config.initialExercise) this.openEdit(config.initialExercise);
                    else if (config.initialMode === 'create') this.openCreate(config.oldCreate);
                    if (config.groupInitialMode) { this.openGroups(); config.groupInitialMode === 'edit' ? this.editGroup(config.groupInitial) : this.newGroup(config.groupInitial); }
                },
                openCreate(values = null) { this.mode = 'create'; this.form = { ...empty(), ...(values || {}) }; this.previews = []; this.removedMedia = []; this.show(); },
                openEdit(exercise) { this.mode = 'edit'; this.form = { ...empty(), ...exercise, muscle_group_id: String(exercise.muscle_group_id) }; this.previews = []; this.removedMedia = []; this.show(); },
                previewImages(event) { this.previews = [...event.target.files].slice(0, 5).map(file => URL.createObjectURL(file)); },
                toggleMedia(id) { this.removedMedia = this.removedMedia.includes(id) ? this.removedMedia.filter(item => item !== id) : [...this.removedMedia, id]; },
                show() { this.modal = true; document.body.classList.add('overflow-hidden'); this.$nextTick(() => this.$refs.nameInput?.focus()); },
                close() { this.modal = false; document.body.classList.remove('overflow-hidden'); },
                openGroups() { this.groupView = 'list'; this.groupModal = true; document.body.classList.add('overflow-hidden'); },
                closeGroup() { this.groupModal = false; document.body.classList.remove('overflow-hidden'); },
                newGroup(values = null) { this.groupView = 'form'; this.groupMode = 'create'; this.groupForm = { id: '', name: '', active: '1', action: config.groupStoreUrl, ...(values || {}) }; this.$nextTick(() => this.$refs.groupName?.focus()); },
                editGroup(group) { this.groupView = 'form'; this.groupMode = 'edit'; this.groupForm = { ...group, active: String(group.active) }; this.$nextTick(() => this.$refs.groupName?.focus()); },
                startGroupDrag(index, event) { this.dragIndex = index; this.dragSnapshot = [...this.groups]; event.currentTarget.setPointerCapture?.(event.pointerId); document.body.classList.add('select-none'); },
                moveGroup(event) { if (this.dragIndex === null) return; event.preventDefault(); const list = this.$refs.groupList; const rect = list.getBoundingClientRect(); if (event.clientY < rect.top + 55) list.scrollBy(0, -12); else if (event.clientY > rect.bottom - 55) list.scrollBy(0, 12); const item = document.elementFromPoint(event.clientX, event.clientY)?.closest('[data-group-index]'); if (!item) return; const target = Number(item.dataset.groupIndex); if (target === this.dragIndex) return; const [moved] = this.groups.splice(this.dragIndex, 1); this.groups.splice(target, 0, moved); this.dragIndex = target; },
                async endGroupDrag() { if (this.dragIndex === null) return; this.dragIndex = null; document.body.classList.remove('select-none'); this.orderSaving = true; try { const response = await fetch(config.groupReorderUrl, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ groups: this.groups.map(group => group.id) }) }); if (!response.ok) throw new Error(); } catch { this.groups = this.dragSnapshot; window.alert('Não foi possível salvar a nova ordem.'); } finally { this.orderSaving = false; } },
            };
        }
    </script>
</x-layouts.app>

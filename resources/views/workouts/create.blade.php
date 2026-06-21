<x-layouts.app title="Elaborar treino">
    <form method="POST" action="{{ route('workouts.store') }}" x-data="{ items: [{ exercise_id: '', sets: 3, repetitions: '8-12', weight: '', rest_seconds: 60, notes: '' }] }">@csrf
        <div class="card p-6 lg:p-8"><h2 class="text-lg font-black text-ink-950">Informações do treino</h2><div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="xl:col-span-2"><label class="label">Aluno</label><select class="field" name="member_id" required><option value="">Selecione</option>@foreach($members as $member)<option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $member->name }}</option>@endforeach</select></div>
            <div class="xl:col-span-2"><label class="label">Nome do treino</label><input class="field" name="name" value="{{ old('name') }}" placeholder="Ex.: A — Peitoral e tríceps" required></div>
            <div><label class="label">Início</label><input class="field" type="date" name="starts_at" value="{{ old('starts_at', now()->format('Y-m-d')) }}" required></div><div><label class="label">Término</label><input class="field" type="date" name="ends_at" value="{{ old('ends_at') }}"></div>
            <div class="md:col-span-2"><label class="label">Observações gerais</label><input class="field" name="notes" value="{{ old('notes') }}"></div>
        </div></div>
        <div class="card mt-6 p-6 lg:p-8">
            <div class="flex items-center justify-between"><div><h2 class="text-lg font-black text-ink-950">Exercícios</h2><p class="text-sm text-slate-500">A ordem abaixo será a ordem da ficha.</p></div><button type="button" class="btn-secondary" @click="items.push({ exercise_id: '', sets: 3, repetitions: '8-12', weight: '', rest_seconds: 60, notes: '' })">+ Adicionar</button></div>
            <div class="mt-6 space-y-4"><template x-for="(item, index) in items" :key="index"><div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-4 flex items-center justify-between"><span class="grid size-8 place-items-center rounded-full bg-ink-950 text-xs font-bold text-white" x-text="index + 1"></span><button type="button" class="text-sm font-semibold text-red-600 disabled:opacity-30" :disabled="items.length === 1" @click="items.splice(index, 1)">Remover</button></div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <div class="xl:col-span-2"><label class="label">Exercício</label><select class="field" :name="`items[${index}][exercise_id]`" x-model="item.exercise_id" required><option value="">Selecione</option>@foreach($exercises as $exercise)<option value="{{ $exercise->id }}">{{ $exercise->name }}{{ $exercise->muscle_group ? ' · '.$exercise->muscle_group : '' }}</option>@endforeach</select></div>
                    <div><label class="label">Séries</label><input class="field" type="number" min="1" max="20" :name="`items[${index}][sets]`" x-model="item.sets" required></div><div><label class="label">Repetições</label><input class="field" :name="`items[${index}][repetitions]`" x-model="item.repetitions" required></div><div><label class="label">Carga (kg)</label><input class="field" type="number" min="0" step="0.01" :name="`items[${index}][weight]`" x-model="item.weight"></div><div><label class="label">Descanso (s)</label><input class="field" type="number" min="0" :name="`items[${index}][rest_seconds]`" x-model="item.rest_seconds"></div>
                    <div class="md:col-span-2 xl:col-span-6"><label class="label">Observação</label><input class="field" :name="`items[${index}][notes]`" x-model="item.notes" placeholder="Cadência, técnica, ajuste..."></div>
                </div>
            </div></template></div>
        </div><div class="mt-6 flex justify-end gap-3"><a class="btn-secondary" href="{{ route('workouts.index') }}">Cancelar</a><button class="btn-primary">Salvar treino</button></div>
    </form>
</x-layouts.app>

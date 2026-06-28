<script setup lang="ts">
import RelativeTime from '@/components/RelativeTime.vue';
import type { EnumOption, Need, NeedCommitment } from '@/types/models';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{ need: Need }>();

const updating = ref(false);
const showPeople = ref(false);

const priorityBorder: Record<string, string> = {
    critica: 'border-l-red-500',
    alta: 'border-l-orange-500',
    media: 'border-l-amber-400',
    baja: 'border-l-slate-300',
};

const statusPill: Record<string, string> = {
    solicitada: 'bg-slate-100 text-slate-600',
    comprometida: 'bg-blue-100 text-blue-700',
    en_camino: 'bg-indigo-100 text-indigo-700',
    entregada: 'bg-teal-100 text-teal-700',
    confirmada: 'bg-green-100 text-green-700',
    cancelada: 'bg-slate-100 text-slate-400',
};

// Friendly labels for each lifecycle step a person can take on their commitment.
const actionMeta: Record<string, { label: string; primary: boolean }> = {
    en_camino: { label: '🚚 Voy en camino', primary: true },
    entregada: { label: '📦 Entregado', primary: true },
    confirmada: { label: '✅ Confirmar recibido', primary: true },
    comprometida: { label: '↩ Reactivar', primary: false },
    cancelada: { label: '✕ Cancelar', primary: false },
};

const statusCounts = computed(() => props.need.statusCounts ?? []);
const commitments = computed<NeedCommitment[]>(() => props.need.commitments ?? []);
const commitCount = computed(() => props.need.commitmentsCount ?? 0);

const peopleToggleLabel = computed(() =>
    commitCount.value > 0
        ? `${commitCount.value} ${commitCount.value === 1 ? 'persona' : 'personas'}`
        : 'Nadie se encarga aún',
);

function actionLabel(transition: EnumOption): string {
    return actionMeta[transition.value]?.label ?? transition.label;
}

function isPrimary(transition: EnumOption): boolean {
    return actionMeta[transition.value]?.primary ?? false;
}

function advanceCommitment(commitmentId: number, status: string): void {
    router.patch(
        `/commitments/${commitmentId}/estado`,
        { status },
        {
            preserveScroll: true,
            onStart: () => (updating.value = true),
            onFinish: () => (updating.value = false),
        },
    );
}

const commitForm = useForm({ name: '' });

function commit(): void {
    commitForm.post(`/necesidades/${props.need.id}/comprometerse`, {
        preserveScroll: true,
        onSuccess: () => {
            commitForm.reset();
            showPeople.value = true;
        },
    });
}
</script>

<template>
    <li
        class="rounded-lg border border-l-4 border-slate-200 bg-white p-3"
        :class="[priorityBorder[need.priority.value] ?? 'border-l-slate-300', { 'opacity-60': updating }]"
    >
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="text-sm font-medium text-slate-900">
                    <span v-if="need.category?.icon">{{ need.category.icon }} </span>{{ need.name }}
                    <span v-if="need.quantity" class="font-normal text-slate-500">
                        · {{ need.quantity }} {{ need.unit }}
                    </span>
                </p>
                <p class="text-xs text-slate-400">
                    Prioridad {{ need.priority.label }} · pedida
                    <RelativeTime v-if="need.createdAt" :value="need.createdAt" />{{ ' ' }}
                    <span v-if="need.createdBy">por {{ need.createdBy }}</span>
                </p>
                <p v-if="need.notes" class="mt-0.5 text-xs text-slate-500">{{ need.notes }}</p>
            </div>
            <span
                class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold"
                :class="statusPill[need.status.value] ?? 'bg-slate-100 text-slate-600'"
            >
                {{ need.status.label }}
            </span>
        </div>

        <!-- Per-status counters: how many people sit in each lifecycle state. -->
        <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
            <span
                v-for="count in statusCounts"
                :key="count.value"
                class="rounded-full px-2 py-0.5 text-[11px] font-semibold"
                :class="statusPill[count.value] ?? 'bg-slate-100 text-slate-600'"
            >
                {{ count.label }} · {{ count.count }}
            </span>

            <button
                type="button"
                class="ml-auto rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                :aria-expanded="showPeople"
                @click="showPeople = !showPeople"
            >
                👥 {{ peopleToggleLabel }}
                <span class="ml-0.5 inline-block transition" :class="{ 'rotate-180': showPeople }">▾</span>
            </button>
        </div>

        <!-- Toggleable per-person list: each person owns their status + buttons. -->
        <div v-if="showPeople" class="mt-2 space-y-2 rounded-lg bg-slate-50 p-2.5">
            <ul v-if="commitments.length" class="space-y-2">
                <li
                    v-for="person in commitments"
                    :key="person.id"
                    class="rounded-lg border border-slate-200 bg-white p-2"
                >
                    <div class="flex items-center justify-between gap-2">
                        <span class="min-w-0 truncate text-sm font-medium text-slate-800">🙋 {{ person.name }}</span>
                        <span
                            class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                            :class="statusPill[person.status.value] ?? 'bg-slate-100 text-slate-600'"
                        >
                            {{ person.status.label }}
                        </span>
                    </div>
                    <div v-if="person.allowedTransitions.length" class="mt-2 flex flex-wrap items-center gap-2">
                        <template v-for="transition in person.allowedTransitions" :key="transition.value">
                            <button
                                v-if="isPrimary(transition)"
                                type="button"
                                :disabled="updating"
                                class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-slate-800 disabled:opacity-50"
                                @click="advanceCommitment(person.id, transition.value)"
                            >
                                {{ actionLabel(transition) }}
                            </button>
                            <button
                                v-else
                                type="button"
                                :disabled="updating"
                                class="text-xs text-slate-400 transition hover:text-slate-700 disabled:opacity-50"
                                @click="advanceCommitment(person.id, transition.value)"
                            >
                                {{ actionLabel(transition) }}
                            </button>
                        </template>
                    </div>
                </li>
            </ul>
            <p v-else class="text-center text-xs text-slate-500">
                Nadie se ha encargado todavía. ¡Sé el primero!
            </p>

            <form class="flex items-end gap-2 border-t border-slate-200 pt-2" @submit.prevent="commit">
                <div class="min-w-0 flex-1">
                    <label class="text-[11px] text-slate-500" :for="`commit-${need.id}`">Tu nombre (opcional)</label>
                    <input
                        :id="`commit-${need.id}`"
                        v-model="commitForm.name"
                        type="text"
                        placeholder="Ej. Carlos / Brigada El Valle"
                        class="mt-0.5 w-full rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm focus:border-slate-400 focus:outline-none"
                    />
                </div>
                <button
                    type="submit"
                    :disabled="commitForm.processing"
                    class="shrink-0 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-700 disabled:opacity-50"
                >
                    🙋 Me encargo
                </button>
            </form>
        </div>
    </li>
</template>

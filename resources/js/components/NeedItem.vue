<script setup lang="ts">
import CommitmentsSheet from '@/components/CommitmentsSheet.vue';
import RelativeTime from '@/components/RelativeTime.vue';
import type { EnumOption, Need } from '@/types/models';
import { router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{ need: Need }>();

const updating = ref(false);

// reka-ui (the Sheet) generates ids that differ between SSR and the browser,
// so only mount the interactive panel on the client to avoid hydration noise.
const mounted = ref(false);
onMounted(() => {
    mounted.value = true;
});

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

// Friendly labels for the forward lifecycle steps (committing is handled apart).
const actionMeta: Record<string, { label: string; primary: boolean }> = {
    en_camino: { label: '🚚 Voy en camino', primary: true },
    entregada: { label: '📦 Entregado', primary: true },
    confirmada: { label: '✅ Confirmar recibido', primary: true },
    solicitada: { label: '↩ Reabrir', primary: false },
    cancelada: { label: '✕ Cancelar', primary: false },
};

const forwardActions = computed(() =>
    props.need.allowedTransitions.filter((t) => actionMeta[t.value]?.primary),
);
const secondaryActions = computed(() =>
    props.need.allowedTransitions.filter((t) => actionMeta[t.value] && !actionMeta[t.value].primary),
);

const commitCount = computed(() => props.need.commitmentsCount ?? 0);

const commitLabel = computed(() =>
    commitCount.value > 0
        ? `${commitCount.value} se ${commitCount.value === 1 ? 'encarga' : 'encargan'}`
        : 'Me encargo',
);
const commitClass = computed(() =>
    commitCount.value > 0
        ? 'bg-blue-50 text-blue-700 hover:bg-blue-100'
        : 'bg-slate-900 text-white hover:bg-slate-800',
);

function label(transition: EnumOption): string {
    return actionMeta[transition.value]?.label ?? transition.label;
}

function advance(status: string): void {
    router.patch(
        `/necesidades/${props.need.id}/estado`,
        { status },
        {
            preserveScroll: true,
            onStart: () => (updating.value = true),
            onFinish: () => (updating.value = false),
        },
    );
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

        <div class="mt-2.5 flex flex-wrap items-center gap-2">
            <template v-if="need.isOpen">
                <CommitmentsSheet v-if="mounted" :need="need">
                    <template #trigger>
                        <button
                            type="button"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="commitClass"
                        >
                            🙋 {{ commitLabel }}
                        </button>
                    </template>
                </CommitmentsSheet>
                <button
                    v-else
                    type="button"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                    :class="commitClass"
                >
                    🙋 {{ commitLabel }}
                </button>
            </template>

            <button
                v-for="transition in forwardActions"
                :key="transition.value"
                type="button"
                :disabled="updating"
                class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800 disabled:opacity-50"
                @click="advance(transition.value)"
            >
                {{ label(transition) }}
            </button>
            <button
                v-for="transition in secondaryActions"
                :key="transition.value"
                type="button"
                :disabled="updating"
                class="text-xs text-slate-400 hover:text-slate-700 disabled:opacity-50"
                @click="advance(transition.value)"
            >
                {{ label(transition) }}
            </button>
        </div>
    </li>
</template>

<script setup lang="ts">
import CommitForm from '@/components/CommitForm.vue';
import CommitmentRow from '@/components/CommitmentRow.vue';
import NeedStatusCounts from '@/components/NeedStatusCounts.vue';
import RelativeTime from '@/components/RelativeTime.vue';
import { priorityBorder, statusPill } from '@/lib/needStatus';
import type { Need, NeedCommitment } from '@/types/models';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{ need: Need }>();

const updating = ref(false);
const showPeople = ref(false);

const commitments = computed<NeedCommitment[]>(() => props.need.commitments ?? []);
const commitCount = computed(() => props.need.commitmentsCount ?? 0);
const isCancelled = computed(() => props.need.status.value === 'cancelada');

const peopleToggleLabel = computed(() =>
    commitCount.value > 0
        ? `${commitCount.value} ${commitCount.value === 1 ? 'persona' : 'personas'}`
        : 'Nadie se encarga aún',
);

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

function cancelNeed(): void {
    if (confirm('¿Cancelar toda esta necesidad? Se cancelarán también todos los compromisos.')) {
        router.post(`/necesidades/${props.need.id}/cancelar`, {}, { preserveScroll: true });
    }
}

function reopenNeed(): void {
    router.post(`/necesidades/${props.need.id}/reabrir`, {}, { preserveScroll: true });
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
                :class="statusPill(need.status.value)"
            >
                {{ need.status.label }}
            </span>
        </div>

        <!-- Per-status counters: how many people sit in each lifecycle state. -->
        <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
            <NeedStatusCounts :counts="need.statusCounts ?? []" />

            <div class="ml-auto flex items-center gap-1.5">
                <button
                    type="button"
                    class="rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                    :aria-expanded="showPeople"
                    @click="showPeople = !showPeople"
                >
                    👥 {{ peopleToggleLabel }}
                    <span class="ml-0.5 inline-block transition" :class="{ 'rotate-180': showPeople }">▾</span>
                </button>
                <button
                    v-if="need.isOpen"
                    type="button"
                    class="rounded-lg px-2 py-1 text-xs font-medium text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                    @click="cancelNeed"
                >
                    ✕ Cancelar
                </button>
                <button
                    v-else-if="isCancelled"
                    type="button"
                    class="rounded-lg px-2 py-1 text-xs font-medium text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    @click="reopenNeed"
                >
                    ↩ Reabrir
                </button>
            </div>
        </div>

        <!-- Toggleable per-person list: each helper owns their status + buttons. -->
        <div v-if="showPeople" class="mt-2 space-y-2 rounded-lg bg-slate-50 p-2.5">
            <ul v-if="commitments.length" class="space-y-2">
                <CommitmentRow
                    v-for="person in commitments"
                    :key="person.id"
                    :commitment="person"
                    :disabled="updating"
                    @advance="advanceCommitment"
                />
            </ul>
            <p v-else class="text-center text-xs text-slate-500">
                Nadie se ha encargado todavía. ¡Sé el primero!
            </p>

            <CommitForm :need-id="need.id" @committed="showPeople = true" />
        </div>
    </li>
</template>

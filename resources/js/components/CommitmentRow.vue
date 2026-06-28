<script setup lang="ts">
import { actionMeta, statusPill } from '@/lib/needStatus';
import type { NeedCommitment } from '@/types/models';

defineProps<{ commitment: NeedCommitment; disabled?: boolean }>();

const emit = defineEmits<{ advance: [commitmentId: number, status: string] }>();
</script>

<template>
    <li class="rounded-lg border border-slate-200 bg-white p-2">
        <div class="flex items-center justify-between gap-2">
            <span class="min-w-0 truncate text-sm font-medium text-slate-800">🙋 {{ commitment.name }}</span>
            <span
                class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                :class="statusPill(commitment.status.value)"
            >
                {{ commitment.status.label }}
            </span>
        </div>
        <div v-if="commitment.allowedTransitions.length" class="mt-2 flex flex-wrap items-center gap-2">
            <template v-for="transition in commitment.allowedTransitions" :key="transition.value">
                <button
                    v-if="actionMeta(transition).primary"
                    type="button"
                    :disabled="disabled"
                    class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-slate-800 disabled:opacity-50"
                    @click="emit('advance', commitment.id, transition.value)"
                >
                    {{ actionMeta(transition).label }}
                </button>
                <button
                    v-else
                    type="button"
                    :disabled="disabled"
                    class="text-xs text-slate-400 transition hover:text-slate-700 disabled:opacity-50"
                    @click="emit('advance', commitment.id, transition.value)"
                >
                    {{ actionMeta(transition).label }}
                </button>
            </template>
        </div>
    </li>
</template>

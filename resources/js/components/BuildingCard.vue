<script setup lang="ts">
import RelativeTime from '@/components/RelativeTime.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import type { BuildingSummary } from '@/types/models';
import { Link } from '@inertiajs/vue3';

defineProps<{ building: BuildingSummary }>();

const priorityDot: Record<string, string> = {
    critica: 'bg-red-500',
    alta: 'bg-orange-500',
    media: 'bg-amber-500',
    baja: 'bg-slate-400',
};
</script>

<template>
    <Link
        :href="`/edificios/${building.slug}`"
        class="block rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300 hover:shadow-md"
    >
        <div class="flex items-start justify-between gap-3">
            <div class="flex min-w-0 items-start gap-2">
                <span class="text-xl leading-none">{{ building.type.icon }}</span>
                <div class="min-w-0">
                    <h3 class="truncate font-semibold text-slate-900">{{ building.name }}</h3>
                    <p class="truncate text-sm text-slate-500">
                        {{ building.community?.name ?? building.address ?? 'Ubicación por confirmar' }}
                    </p>
                </div>
            </div>
            <StatusBadge :status="building.status" size="sm" />
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span
                v-if="building.mode.value === 'rescate'"
                class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20"
            >
                {{ building.mode.icon }} Rescate
            </span>
            <span class="text-xs text-slate-500">
                {{ building.openNeedsCount }}
                {{ building.openNeedsCount === 1 ? 'necesidad abierta' : 'necesidades abiertas' }}
            </span>
            <span v-if="building.lastReportedAt" class="text-xs text-slate-400">
                · <RelativeTime :value="building.lastReportedAt" />
            </span>
        </div>

        <ul v-if="building.topNeeds.length" class="mt-3 flex flex-wrap gap-1.5">
            <li
                v-for="need in building.topNeeds"
                :key="need.name"
                class="inline-flex items-center gap-1.5 rounded-md bg-slate-50 px-2 py-1 text-xs text-slate-700 ring-1 ring-inset ring-slate-200"
            >
                <span class="h-1.5 w-1.5 rounded-full" :class="priorityDot[need.priority] ?? 'bg-slate-400'" />
                {{ need.name }}
            </li>
        </ul>
    </Link>
</template>

<script setup lang="ts">
import type { NeedsSummary } from '@/types/models';

defineProps<{
    summary: NeedsSummary;
    activeNeedStatus: string | null;
    activeCategory: string | null;
}>();

const emit = defineEmits<{
    toggleStatus: [value: string];
    toggleCategory: [id: number];
}>();

const statusColor: Record<string, string> = {
    solicitada: 'text-slate-700',
    comprometida: 'text-blue-600',
    en_camino: 'text-indigo-600',
    entregada: 'text-teal-600',
    confirmada: 'text-green-600',
};
</script>

<template>
    <section class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-slate-900">
                Necesidades <span class="text-sm font-normal text-slate-400">· {{ summary.open }} abiertas</span>
            </h2>
            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                🕐 {{ summary.lastHour }} en la última hora
            </span>
        </div>

        <p class="mt-2 text-xs text-slate-400">Toca un estado o categoría para filtrar los lugares.</p>

        <div class="mt-2 grid grid-cols-5 gap-1.5 text-center">
            <button
                v-for="status in summary.byStatus"
                :key="status.value"
                type="button"
                class="rounded-lg border px-1 py-2 transition"
                :class="
                    activeNeedStatus === status.value
                        ? 'border-slate-900 bg-slate-900 text-white'
                        : 'border-transparent bg-slate-50 hover:border-slate-300'
                "
                @click="emit('toggleStatus', status.value)"
            >
                <span
                    class="block text-lg leading-none font-bold"
                    :class="activeNeedStatus === status.value ? 'text-white' : (statusColor[status.value] ?? 'text-slate-700')"
                >
                    {{ status.count }}
                </span>
                <span
                    class="mt-1 block text-[10px] leading-tight"
                    :class="activeNeedStatus === status.value ? 'text-slate-200' : 'text-slate-500'"
                >
                    {{ status.label }}
                </span>
            </button>
        </div>

        <div v-if="summary.topCategories.length" class="mt-3">
            <p class="mb-1.5 text-xs font-medium text-slate-500">Más pedido (abiertas)</p>
            <div class="flex flex-wrap gap-1.5">
                <button
                    v-for="category in summary.topCategories"
                    :key="category.id"
                    type="button"
                    class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs transition"
                    :class="
                        activeCategory === String(category.id)
                            ? 'border-slate-900 bg-slate-900 text-white'
                            : 'border-slate-200 bg-slate-100 text-slate-700 hover:border-slate-300'
                    "
                    @click="emit('toggleCategory', category.id)"
                >
                    {{ category.icon }} {{ category.name }}
                    <span class="font-bold">{{ category.count }}</span>
                </button>
            </div>
        </div>
    </section>
</template>

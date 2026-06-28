<script setup lang="ts">
import type { BuildingStats } from '@/types/models';
import { computed } from 'vue';

const props = defineProps<{ stats: BuildingStats }>();

interface Counter {
    label: string;
    value: number;
    icon: string;
    accent: string;
}

const counters = computed<Counter[]>(() => [
    { label: 'Edificios', value: props.stats.total, icon: '🏢', accent: 'bg-white border-slate-200 text-slate-900' },
    { label: 'Críticos', value: props.stats.criticos, icon: '🔴', accent: 'bg-red-50 border-red-200 text-red-700' },
    { label: 'En rescate', value: props.stats.rescate, icon: '🆘', accent: 'bg-orange-50 border-orange-200 text-orange-700' },
    { label: 'Personas atrapadas', value: props.stats.personasAtrapadas, icon: '⚠️', accent: 'bg-red-600 border-red-700 text-white' },
    { label: 'Necesidades abiertas', value: props.stats.necesidadesAbiertas, icon: '📦', accent: 'bg-white border-slate-200 text-slate-900' },
    { label: 'Necesidades críticas', value: props.stats.necesidadesCriticas, icon: '🚨', accent: 'bg-amber-50 border-amber-200 text-amber-700' },
    { label: 'Hospitales', value: props.stats.hospitales, icon: '🏥', accent: 'bg-white border-slate-200 text-slate-900' },
    { label: 'Sectores', value: props.stats.sectores, icon: '📍', accent: 'bg-white border-slate-200 text-slate-900' },
]);
</script>

<template>
    <section class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
        <div
            v-for="counter in counters"
            :key="counter.label"
            class="flex items-center gap-3 rounded-xl border p-3"
            :class="counter.accent"
        >
            <span class="text-2xl leading-none">{{ counter.icon }}</span>
            <div class="min-w-0">
                <p class="text-xl font-bold leading-tight">{{ counter.value }}</p>
                <p class="text-xs leading-tight opacity-80">{{ counter.label }}</p>
            </div>
        </div>
    </section>
</template>

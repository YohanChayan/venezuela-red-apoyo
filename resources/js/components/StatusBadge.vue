<script setup lang="ts">
import type { EnumOption } from '@/types/models';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{ status: EnumOption; size?: 'sm' | 'md' }>(),
    { size: 'md' },
);

const palette: Record<string, string> = {
    green: 'bg-green-100 text-green-800 ring-green-600/20',
    amber: 'bg-amber-100 text-amber-800 ring-amber-600/20',
    red: 'bg-red-100 text-red-800 ring-red-600/20',
    slate: 'bg-slate-100 text-slate-600 ring-slate-500/20',
};

const classes = computed(
    () => palette[props.status.color ?? ''] ?? 'bg-slate-100 text-slate-700 ring-slate-500/20',
);
</script>

<template>
    <span
        class="inline-flex items-center gap-1 rounded-full font-medium ring-1 ring-inset"
        :class="[classes, size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-1 text-sm']"
    >
        <span v-if="status.emoji">{{ status.emoji }}</span>
        {{ status.label }}
    </span>
</template>

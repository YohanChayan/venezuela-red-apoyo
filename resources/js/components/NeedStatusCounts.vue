<script setup lang="ts">
import { statusPill } from '@/lib/needStatus';
import type { NeedStatusCount } from '@/types/models';

defineProps<{ counts: NeedStatusCount[] }>();
</script>

<template>
    <span
        v-for="count in counts"
        :key="count.value"
        class="rounded-full px-2 py-0.5 text-[11px] font-semibold"
        :class="statusPill(count.value)"
    >
        {{ count.label }} · {{ count.count }}
    </span>
</template>

<script setup lang="ts">
import { fullDate, humanDate } from '@/lib/date';
import { onMounted, ref } from 'vue';

const props = defineProps<{ value: string | null | undefined }>();

// Date formatting depends on the local timezone/locale, which differs between
// the Node SSR render and the browser. To avoid hydration mismatches we render
// nothing on the server and first client paint, then fill in after mount.
const mounted = ref(false);
onMounted(() => {
    mounted.value = true;
});
</script>

<template>
    <span v-if="mounted && value" :title="fullDate(value)">{{ humanDate(value) }}</span>
</template>

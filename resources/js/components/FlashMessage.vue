<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const flash = computed(() => page.props.flash as { success?: string | null; error?: string | null } | undefined);
</script>

<template>
    <div v-if="flash?.success || flash?.error" class="mx-auto max-w-6xl px-4 pt-4">
        <div
            v-if="flash?.success"
            class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800"
        >
            {{ flash.success }}
        </div>
        <div
            v-if="flash?.error"
            class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800"
        >
            {{ flash.error }}
        </div>
    </div>
</template>

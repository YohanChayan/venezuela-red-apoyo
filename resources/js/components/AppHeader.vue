<script setup lang="ts">
import BuildingFormModal from '@/components/BuildingFormModal.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-3 px-4 py-3">
            <Link href="/" class="flex items-center gap-2.5">
                <img src="/favicon.svg" alt="VRA" class="h-9 w-9 shrink-0 rounded-md" />
                <span class="leading-tight">
                    <span class="block font-bold tracking-tight text-slate-900">VRA</span>
                    <span class="text-xs text-slate-500">Venezuela Red de Apoyo</span>
                </span>
            </Link>
            <BuildingFormModal mode="create">
                <template #trigger>
                    <Button class="shrink-0 bg-red-600 text-white hover:bg-red-700">+ Registrar lugar</Button>
                </template>
            </BuildingFormModal>
        </div>
    </header>
</template>

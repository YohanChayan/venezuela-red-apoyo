<script setup lang="ts">
import BuildingFormModal from '@/components/BuildingFormModal.vue';
import FeedbackModal from '@/components/FeedbackModal.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <header
            class="sticky top-0 z-10 border-b border-slate-200 bg-white/90 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3"
            >
                <Link href="/" class="flex items-center gap-2.5">
                    <img
                        src="/favicon.svg"
                        alt="VEI"
                        class="h-9 w-9 shrink-0 rounded-md"
                    />
                    <span class="leading-tight">
                        <span
                            class="block font-bold tracking-tight text-slate-900"
                            >VEI</span
                        >
                        <span class="text-xs text-slate-500"
                            >Venezuela Entrega Insumos</span
                        >
                    </span>
                </Link>
                <BuildingFormModal mode="create">
                    <template #trigger>
                        <Button
                            class="shrink-0 gap-1.5 rounded-xl bg-slate-900 font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.99]"
                        >
                            <span
                                class="text-base leading-none"
                                aria-hidden="true"
                                >➕</span
                            >
                            Agregar un lugar
                        </Button>
                    </template>
                </BuildingFormModal>
            </div>
        </header>

        <FeedbackModal />
    </div>
</template>

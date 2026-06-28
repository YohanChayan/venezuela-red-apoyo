<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{ needId: number }>();
const emit = defineEmits<{ committed: [] }>();

const form = useForm({ name: '' });

function submit(): void {
    form.post(`/necesidades/${props.needId}/comprometerse`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('committed');
        },
    });
}
</script>

<template>
    <form class="flex items-end gap-2 border-t border-slate-200 pt-2" @submit.prevent="submit">
        <div class="min-w-0 flex-1">
            <label class="text-[11px] text-slate-500" :for="`commit-${needId}`">Tu nombre (opcional)</label>
            <input
                :id="`commit-${needId}`"
                v-model="form.name"
                type="text"
                placeholder="Ej. Carlos / Brigada El Valle"
                class="mt-0.5 w-full rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm focus:border-slate-400 focus:outline-none"
            />
        </div>
        <button
            type="submit"
            :disabled="form.processing"
            class="shrink-0 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-700 disabled:opacity-50"
        >
            🙋 Me encargo
        </button>
    </form>
</template>

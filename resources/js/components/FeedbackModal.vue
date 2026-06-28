<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface TypeOption {
    value: 'sugerencia' | 'problema' | 'comentario';
    label: string;
    emoji: string;
}

const types: TypeOption[] = [
    { value: 'sugerencia', label: 'Sugerencia', emoji: '💡' },
    { value: 'problema', label: 'Problema o error', emoji: '🐞' },
    { value: 'comentario', label: 'Comentario', emoji: '💬' },
];

const open = ref(false);

const form = useForm({
    type: 'sugerencia' as TypeOption['value'],
    message: '',
    contact: '',
    url: '',
});

function submit(): void {
    form.url = window.location.pathname;
    form.post('/feedback', {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
            form.reset();
        },
    });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <button
                type="button"
                class="fixed right-4 bottom-4 z-20 flex items-center gap-1.5 rounded-full border border-slate-300 bg-white/95 px-3.5 py-2 text-sm font-medium text-slate-600 shadow-md backdrop-blur transition hover:bg-slate-50 hover:text-slate-900"
                aria-label="Enviar sugerencia o reportar un problema"
            >
                <span aria-hidden="true">💬</span>
                <span class="hidden sm:inline">Sugerencias</span>
            </button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Ayúdanos a mejorar</DialogTitle>
                <DialogDescription>
                    Cuéntanos qué te gustaría cambiar, qué falló o qué echas de menos. Lo leemos todo. Sin registro.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label>¿De qué se trata?</Label>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            v-for="option in types"
                            :key="option.value"
                            type="button"
                            class="flex flex-col items-center gap-1 rounded-lg border p-2 text-center transition"
                            :class="
                                form.type === option.value
                                    ? 'border-slate-900 bg-slate-900 text-white'
                                    : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'
                            "
                            @click="form.type = option.value"
                        >
                            <span class="text-xl" aria-hidden="true">{{ option.emoji }}</span>
                            <span class="text-[10px] leading-tight">{{ option.label }}</span>
                        </button>
                    </div>
                    <p v-if="form.errors.type" class="text-xs text-red-600">{{ form.errors.type }}</p>
                </div>

                <div class="space-y-1.5">
                    <Label for="feedback-message">Tu mensaje <span class="text-red-600">*</span></Label>
                    <Textarea
                        id="feedback-message"
                        v-model="form.message"
                        rows="4"
                        placeholder="Escribe aquí tu sugerencia, el problema que viste o tu comentario…"
                    />
                    <p v-if="form.errors.message" class="text-xs text-red-600">{{ form.errors.message }}</p>
                </div>

                <div class="space-y-1.5">
                    <Label for="feedback-contact">Contacto <span class="text-slate-400">(opcional)</span></Label>
                    <Input
                        id="feedback-contact"
                        v-model="form.contact"
                        placeholder="Correo o teléfono, si quieres que te respondamos"
                    />
                    <p v-if="form.errors.contact" class="text-xs text-red-600">{{ form.errors.contact }}</p>
                </div>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    Enviar
                </Button>
            </form>
        </DialogContent>
    </Dialog>
</template>

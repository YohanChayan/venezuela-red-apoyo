<script setup lang="ts">
import RelativeTime from '@/components/RelativeTime.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import type { Need } from '@/types/models';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{ need: Need }>();

const open = ref(false);
const form = useForm({ name: '' });

function commit(): void {
    form.post(`/necesidades/${props.need.id}/comprometerse`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Sheet v-model:open="open">
        <SheetTrigger as-child>
            <slot name="trigger" />
        </SheetTrigger>
        <SheetContent side="right" class="w-full overflow-y-auto sm:max-w-md">
            <SheetHeader>
                <SheetTitle>¿Quién se encarga?</SheetTitle>
                <SheetDescription>
                    {{ need.name }} ·
                    {{ need.commitmentsCount ?? 0 }}
                    {{ (need.commitmentsCount ?? 0) === 1 ? 'persona' : 'personas' }}
                </SheetDescription>
            </SheetHeader>

            <div class="space-y-4 px-4 pb-4">
                <ul v-if="need.commitments && need.commitments.length" class="space-y-2">
                    <li
                        v-for="(person, index) in need.commitments"
                        :key="index"
                        class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-sm"
                    >
                        <span class="font-medium text-slate-800">🙋 {{ person.name }}</span>
                        <RelativeTime :value="person.at" />
                    </li>
                </ul>
                <p v-else class="rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500">
                    Nadie se ha encargado todavía. ¡Sé el primero!
                </p>

                <form class="space-y-2 rounded-lg bg-slate-50 p-3" @submit.prevent="commit">
                    <Label for="commit-name">Tu nombre (opcional)</Label>
                    <Input id="commit-name" v-model="form.name" placeholder="Ej. Carlos / Brigada El Valle" />
                    <Button
                        type="submit"
                        class="w-full bg-red-600 text-white hover:bg-red-700"
                        :disabled="form.processing"
                    >
                        🙋 Yo me encargo
                    </Button>
                    <p class="text-center text-[11px] text-slate-400">
                        Varias personas pueden encargarse del mismo insumo.
                    </p>
                </form>
            </div>
        </SheetContent>
    </Sheet>
</template>

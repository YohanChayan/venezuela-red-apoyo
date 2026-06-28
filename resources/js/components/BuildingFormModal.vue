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
import type { Building, EnumOption } from '@/types/models';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{ mode?: 'create' | 'edit'; building?: Building }>(),
    { mode: 'create' },
);

const page = usePage();
const options = computed(
    () =>
        page.props.buildingOptions as {
            types: EnumOption[];
            statuses: EnumOption[];
            structuralStatuses: EnumOption[];
            sectors: string[];
        },
);

const isEdit = computed(() => props.mode === 'edit' && !!props.building);
const open = ref(false);

// Manual urgency choices (the "sin asignar" gray state is only ever auto).
const manualStatuses = computed(() =>
    options.value.statuses.filter((status) => status.value !== 'sin_asignar'),
);

interface DuplicateMatch {
    name: string;
    slug: string;
    sector: string | null;
    status: string;
}

const form = useForm({
    version: props.building?.version ?? 0,
    name: props.building?.name ?? '',
    type: props.building?.type.value ?? '',
    status: props.building?.statusIsManual ? props.building.status.value : 'auto',
    structural_status: props.building?.structuralStatus.value ?? 'sin_evaluar',
    community_name: props.building?.community?.name ?? '',
    municipality: props.building?.community?.municipality ?? '',
    state: props.building?.community?.state ?? 'La Guaira',
    address: props.building?.address ?? '',
    residents_estimate: (props.building?.residents ?? undefined) as number | undefined,
    contact_name: props.building?.contactName ?? '',
    contact_phone: props.building?.contactPhone ?? '',
    notes: props.building?.notes ?? '',
});

function submit(): void {
    const onSuccess = () => {
        open.value = false;
        if (!isEdit.value) {
            form.reset();
        }
    };

    if (isEdit.value && props.building) {
        form.put(`/edificios/${props.building.slug}`, { preserveScroll: true, onSuccess });
    } else {
        form.post('/edificios', { onSuccess });
    }
}

// Non-blocking near-duplicate warning while typing the name (create only).
const duplicates = ref<DuplicateMatch[]>([]);
let duplicateTimer: ReturnType<typeof setTimeout> | undefined;

watch(
    () => form.name,
    (name) => {
        if (isEdit.value) {
            return;
        }
        clearTimeout(duplicateTimer);
        if (name.trim().length < 3) {
            duplicates.value = [];
            return;
        }
        duplicateTimer = setTimeout(async () => {
            const params = new URLSearchParams({ name, community: form.community_name ?? '' });
            try {
                const response = await fetch(`/api/edificios-similares?${params.toString()}`, {
                    headers: { Accept: 'application/json' },
                });
                duplicates.value = response.ok ? await response.json() : [];
            } catch {
                duplicates.value = [];
            }
        }, 400);
    },
);
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <slot name="trigger" />
        </DialogTrigger>
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ isEdit ? 'Editar edificio' : 'Registrar un lugar' }}</DialogTitle>
                <DialogDescription>
                    {{
                        isEdit
                            ? 'Actualiza los datos. Si alguien más editó mientras tanto, te avisaremos.'
                            : 'Sin cuenta ni registro. Llena lo que sepas; otros pueden completar el resto.'
                    }}
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="name">Nombre del lugar <span class="text-red-600">*</span></Label>
                    <Input id="name" v-model="form.name" placeholder="Ej. Edificio Petunia, Hospital X…" />
                    <p class="text-xs text-slate-400">Con el nombre basta. Lo demás es opcional; otros pueden completarlo.</p>
                    <p v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</p>
                </div>

                <div
                    v-if="!isEdit && duplicates.length"
                    class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm"
                >
                    <p class="font-medium text-amber-800">⚠️ ¿Ya existe este lugar? Verifica antes de crear:</p>
                    <ul class="mt-1.5 space-y-1">
                        <li v-for="match in duplicates" :key="match.slug">
                            <a
                                :href="`/edificios/${match.slug}`"
                                target="_blank"
                                class="font-medium text-amber-900 underline"
                            >
                                {{ match.name }}
                            </a>
                            <span class="text-amber-700"> — {{ match.sector ?? 'sin sector' }} · {{ match.status }}</span>
                        </li>
                    </ul>
                </div>

                <div class="space-y-1.5">
                    <Label>¿Qué tipo de lugar es? <span class="text-slate-400">(opcional)</span></Label>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <button
                            v-for="type in options.types"
                            :key="type.value"
                            type="button"
                            class="flex flex-col items-center gap-1 rounded-lg border p-2 text-center transition"
                            :class="
                                form.type === type.value
                                    ? 'border-slate-900 bg-slate-900 text-white'
                                    : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'
                            "
                            @click="form.type = type.value"
                        >
                            <span class="text-xl">{{ type.icon }}</span>
                            <span class="text-[10px] leading-tight">{{ type.label }}</span>
                        </button>
                    </div>
                    <p v-if="form.errors.type" class="text-xs text-red-600">{{ form.errors.type }}</p>
                </div>

                <div class="space-y-1.5">
                    <Label>Urgencia</Label>
                    <div class="flex flex-wrap gap-1.5">
                        <button
                            type="button"
                            class="rounded-lg border px-3 py-2 text-sm font-medium transition"
                            :class="
                                form.status === 'auto'
                                    ? 'border-slate-900 bg-slate-900 text-white'
                                    : 'border-slate-200 hover:border-slate-300'
                            "
                            @click="form.status = 'auto'"
                        >
                            ⚙️ Automático
                        </button>
                        <button
                            v-for="s in manualStatuses"
                            :key="s.value"
                            type="button"
                            class="rounded-lg border px-3 py-2 text-sm transition"
                            :class="
                                form.status === s.value
                                    ? 'border-slate-900 bg-slate-100'
                                    : 'border-slate-200 hover:border-slate-300'
                            "
                            :title="s.label"
                            @click="form.status = s.value"
                        >
                            {{ s.emoji }} {{ s.label }}
                        </button>
                    </div>
                    <p v-if="form.status === 'auto'" class="text-xs text-slate-500">
                        El sistema asigna el color automáticamente según las necesidades y el estado del lugar.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1.5">
                        <Label for="sector">Sector / zona</Label>
                        <Input
                            id="sector"
                            v-model="form.community_name"
                            list="sectores-existentes"
                            placeholder="San Bernardino"
                        />
                        <datalist id="sectores-existentes">
                            <option v-for="sector in options.sectors" :key="sector" :value="sector" />
                        </datalist>
                    </div>
                    <div class="space-y-1.5">
                        <Label for="muni">Municipio</Label>
                        <Input id="muni" v-model="form.municipality" />
                    </div>
                    <div class="space-y-1.5">
                        <Label for="state">Estado</Label>
                        <Input id="state" v-model="form.state" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label for="address">Dirección / referencia</Label>
                    <Input id="address" v-model="form.address" placeholder="Cómo llegar / punto de referencia" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <Label for="cname">Contacto responsable</Label>
                        <Input id="cname" v-model="form.contact_name" />
                    </div>
                    <div class="space-y-1.5">
                        <Label for="cphone">Teléfono</Label>
                        <Input id="cphone" v-model="form.contact_phone" type="tel" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label for="notes">Notas</Label>
                    <Textarea id="notes" v-model="form.notes" rows="2" />
                </div>

                <p v-if="form.errors.version" class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ form.errors.version }}
                </p>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    {{ isEdit ? 'Guardar cambios' : 'Registrar lugar' }}
                </Button>
            </form>
        </DialogContent>
    </Dialog>
</template>

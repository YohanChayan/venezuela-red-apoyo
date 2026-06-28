<script setup lang="ts">
import AppHeader from '@/components/AppHeader.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import type { EnumOption } from '@/types/models';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    options: {
        types: EnumOption[];
        statuses: EnumOption[];
        modes: EnumOption[];
        structuralStatuses: EnumOption[];
    };
}>();

const form = useForm({
    name: '',
    type: '',
    mode: 'abastecimiento',
    status: 'necesita_apoyo',
    structural_status: 'sin_evaluar',
    community_name: '',
    municipality: '',
    state: '',
    address: '',
    people_trapped_estimate: null as number | null,
    residents_estimate: null as number | null,
    contact_name: '',
    contact_phone: '',
    notes: '',
});

function submit(): void {
    form.post('/edificios', { preserveScroll: true });
}
</script>

<template>
    <Head title="Registrar edificio" />

    <div class="min-h-screen bg-slate-50 text-slate-900">
        <AppHeader />
        <FlashMessage />

        <main class="mx-auto max-w-2xl px-4 py-6">
            <Link href="/" class="text-sm text-slate-500 hover:text-slate-700">← Volver al mapa</Link>

            <h1 class="mt-3 text-2xl font-bold">Registrar un edificio</h1>
            <p class="mt-1 text-sm text-slate-500">
                Sin registro ni cuenta. Llena lo que sepas — luego cualquiera puede completar el resto.
            </p>

            <form class="mt-5 space-y-5" @submit.prevent="submit">
                <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Nombre del edificio *</span>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Ej. Edificio Petunia, Hospital X…"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        />
                        <span v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</span>
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Tipo *</span>
                            <select
                                v-model="form.type"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            >
                                <option value="" disabled>Elige…</option>
                                <option v-for="type in options.types" :key="type.value" :value="type.value">
                                    {{ type.icon }} {{ type.label }}
                                </option>
                            </select>
                            <span v-if="form.errors.type" class="text-xs text-red-600">{{ form.errors.type }}</span>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Situación</span>
                            <select
                                v-model="form.mode"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            >
                                <option v-for="mode in options.modes" :key="mode.value" :value="mode.value">
                                    {{ mode.icon }} {{ mode.label }}
                                </option>
                            </select>
                        </label>
                    </div>

                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">
                        <strong>🆘 Rescate:</strong> edificio con escombros, personas atrapadas, hace falta sacar gente.
                        <strong>📦 Abastecimiento:</strong> en pie pero necesita insumos (agua, medicinas, comida…).
                    </p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Urgencia</span>
                            <select
                                v-model="form.status"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            >
                                <option v-for="status in options.statuses" :key="status.value" :value="status.value">
                                    {{ status.emoji }} {{ status.label }}
                                </option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Estado estructural</span>
                            <select
                                v-model="form.structural_status"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            >
                                <option
                                    v-for="structural in options.structuralStatuses"
                                    :key="structural.value"
                                    :value="structural.value"
                                >
                                    {{ structural.label }}
                                </option>
                            </select>
                        </label>
                    </div>

                    <label v-if="form.mode === 'rescate'" class="block">
                        <span class="text-sm font-medium text-slate-700">Personas atrapadas (estimado)</span>
                        <input
                            v-model.number="form.people_trapped_estimate"
                            type="number"
                            min="0"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        />
                    </label>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-4">
                    <h2 class="text-sm font-semibold text-slate-700">Ubicación</h2>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Comunidad / sector</span>
                            <input
                                v-model="form.community_name"
                                type="text"
                                placeholder="Ej. San Bernardino"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Municipio</span>
                            <input
                                v-model="form.municipality"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Estado</span>
                            <input
                                v-model="form.state"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            />
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Dirección / referencia</span>
                        <input
                            v-model="form.address"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        />
                    </label>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-4">
                    <h2 class="text-sm font-semibold text-slate-700">Contacto responsable (opcional)</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Nombre</span>
                            <input
                                v-model="form.contact_name"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Teléfono</span>
                            <input
                                v-model="form.contact_phone"
                                type="tel"
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            />
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Notas</span>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        />
                    </label>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                >
                    Registrar edificio
                </button>
            </form>
        </main>
    </div>
</template>

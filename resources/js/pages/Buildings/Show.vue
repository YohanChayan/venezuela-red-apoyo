<script setup lang="ts">
import AddNeedModal from '@/components/AddNeedModal.vue';
import AppHeader from '@/components/AppHeader.vue';
import BuildingFormModal from '@/components/BuildingFormModal.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import NeedItem from '@/components/NeedItem.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import RelativeTime from '@/components/RelativeTime.vue';
import { Button } from '@/components/ui/button';
import type {
    Building,
    BuildingHistoryEntry,
    EnumOption,
    SupplyCategory,
} from '@/types/models';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    building: Building;
    supplyCategories: SupplyCategory[];
    priorities: EnumOption[];
    history: BuildingHistoryEntry[];
}>();

const openNeeds = computed(() => props.building.needs.filter((need) => need.isOpen));
const resolvedNeeds = computed(() => props.building.needs.filter((need) => !need.isOpen));

const fieldLabels: Record<string, string> = {
    name: 'Nombre',
    type: 'Tipo',
    status: 'Urgencia',
    mode: 'Situación',
    structural_status: 'Estado estructural',
    address: 'Dirección',
    contact_name: 'Contacto',
    contact_phone: 'Teléfono',
    notes: 'Notas',
    residents_estimate: 'Residentes',
    community_id: 'Sector',
};

function eventLabel(event: string): string {
    return { created: 'creó', updated: 'actualizó', deleted: 'eliminó' }[event] ?? event;
}

function formatDate(value: string | null): string {
    if (!value) {
        return '';
    }
    return new Date(value).toLocaleString('es-VE', { dateStyle: 'short', timeStyle: 'short' });
}
</script>

<template>
    <Head :title="building.name" />

    <div class="min-h-screen bg-slate-50 text-slate-900">
        <AppHeader />
        <FlashMessage />

        <main class="mx-auto max-w-3xl px-4 py-6">
            <Link href="/" class="text-sm text-slate-500 hover:text-slate-700">← Volver al mapa</Link>

            <header class="mt-3 rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="flex items-center gap-2 text-xl font-bold">
                            <span>{{ building.type.icon }}</span>{{ building.name }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ building.type.label }}
                            <span v-if="building.community"> · Sector {{ building.community.name }}</span>
                            <span v-if="building.community?.state"> ({{ building.community.state }})</span>
                        </p>
                        <p v-if="building.address" class="text-sm text-slate-500">{{ building.address }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <StatusBadge :status="building.status" />
                        <BuildingFormModal mode="edit" :building="building">
                            <template #trigger>
                                <Button variant="outline" size="sm">✏️ Editar</Button>
                            </template>
                        </BuildingFormModal>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2 text-sm">
                    <span class="rounded-lg bg-slate-100 px-2.5 py-1">
                        {{ building.mode.icon }} {{ building.mode.label }}
                    </span>
                    <span class="rounded-lg bg-slate-100 px-2.5 py-1">🏚️ {{ building.structuralStatus.label }}</span>
                    <span v-if="building.residents" class="rounded-lg bg-slate-100 px-2.5 py-1">
                        👥 {{ building.residents }} residentes
                    </span>
                </div>

                <div
                    v-if="building.contactName || building.contactPhone"
                    class="mt-4 rounded-lg bg-slate-50 px-3 py-2 text-sm"
                >
                    <span class="font-medium">Contacto:</span>
                    {{ building.contactName ?? 'Responsable' }}
                    <a
                        v-if="building.contactPhone"
                        :href="`tel:${building.contactPhone}`"
                        class="ml-1 font-medium text-red-600"
                    >
                        ☎ {{ building.contactPhone }}
                    </a>
                </div>

                <div v-if="building.notes" class="mt-3 rounded-lg bg-slate-50 px-3 py-2">
                    <p class="text-sm text-slate-700">{{ building.notes }}</p>
                    <p v-if="building.notesUpdatedAt" class="mt-1 text-xs text-slate-400">
                        📝 Nota actualizada <RelativeTime :value="building.notesUpdatedAt" />
                    </p>
                </div>

                <p class="mt-3 text-xs text-slate-400">
                    <span v-if="building.createdAt">Registrado <RelativeTime :value="building.createdAt" /></span>
                    <span v-if="building.lastReportedAt">
                        · última actualización <RelativeTime :value="building.lastReportedAt" />
                        <span v-if="building.lastReportedBy">por {{ building.lastReportedBy }}</span>
                    </span>
                </p>

                <p v-if="building.sourceUrl" class="mt-3 text-xs text-slate-400">
                    Dato inicial (confianza {{ building.confidence }}) ·
                    <a :href="building.sourceUrl" target="_blank" rel="noopener" class="underline">fuente</a>
                    · verificar en terreno
                </p>
            </header>

            <section class="mt-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">
                        Necesidades
                        <span class="text-sm font-normal text-slate-500">({{ openNeeds.length }} abiertas)</span>
                    </h2>
                    <AddNeedModal
                        :building-slug="building.slug"
                        :supply-categories="supplyCategories"
                        :priorities="priorities"
                    >
                        <template #trigger>
                            <Button size="sm" class="bg-red-600 text-white hover:bg-red-700">
                                + Agregar necesidad
                            </Button>
                        </template>
                    </AddNeedModal>
                </div>

                <ul v-if="openNeeds.length" class="mt-3 space-y-1.5">
                    <NeedItem v-for="need in openNeeds" :key="need.id" :need="need" />
                </ul>
                <p
                    v-else
                    class="mt-3 rounded-lg border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500"
                >
                    No hay necesidades abiertas registradas.
                </p>

                <details v-if="resolvedNeeds.length" class="mt-3">
                    <summary class="cursor-pointer text-sm text-slate-500">
                        {{ resolvedNeeds.length }} necesidades resueltas/cerradas
                    </summary>
                    <ul class="mt-2 space-y-1.5">
                        <NeedItem v-for="need in resolvedNeeds" :key="need.id" :need="need" />
                    </ul>
                </details>
            </section>

            <section class="mt-6">
                <h2 class="text-lg font-semibold">Historial de cambios</h2>
                <ol v-if="history.length" class="mt-3 space-y-2">
                    <li
                        v-for="entry in history"
                        :key="entry.id"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                    >
                        <p class="text-slate-700">
                            <span class="font-medium">{{ entry.user }}</span> {{ eventLabel(entry.event) }} el registro
                            <span class="text-slate-400">· {{ formatDate(entry.at) }}</span>
                        </p>
                        <ul v-if="entry.changes.length" class="mt-1 space-y-0.5 text-xs text-slate-500">
                            <li v-for="change in entry.changes" :key="change.field">
                                <span class="font-medium">{{ fieldLabels[change.field] ?? change.field }}:</span>
                                <span class="line-through">{{ change.old ?? '—' }}</span> →
                                <span class="text-slate-700">{{ change.new ?? '—' }}</span>
                            </li>
                        </ul>
                    </li>
                </ol>
                <p
                    v-else
                    class="mt-3 rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500"
                >
                    Aún no hay cambios registrados para este lugar.
                </p>
            </section>
        </main>
    </div>
</template>

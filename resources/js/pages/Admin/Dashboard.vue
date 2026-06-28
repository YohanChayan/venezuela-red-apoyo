<script setup lang="ts">
import FlashMessage from '@/components/FlashMessage.vue';
import type { Paginator } from '@/types/models';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface AuditChange {
    field: string;
    old: string | null;
    new: string | null;
}

interface AuditEntry {
    id: number;
    event: string;
    user: string;
    at: string | null;
    target: { label: string; slug: string | null; kind: string };
    changes: AuditChange[];
}

const props = defineProps<{
    audits: Paginator<AuditEntry>;
    filters: { building: string | null };
    editsLocked: boolean;
    buildings: { slug: string; name: string }[];
}>();

const isMaster = computed(
    () => (usePage().props.auth as { user?: { isMaster?: boolean } } | undefined)?.user?.isMaster ?? false,
);

function filterBuilding(slug: string): void {
    router.get('/admin', { building: slug || undefined }, { preserveState: true, preserveScroll: true, replace: true });
}

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
    priority: 'Prioridad',
    quantity: 'Cantidad',
    custom_supply_name: 'Insumo',
    community_id: 'Sector',
};

function eventLabel(event: string): string {
    return { created: 'creó', updated: 'actualizó', deleted: 'eliminó', restored: 'restauró' }[event] ?? event;
}

function formatDate(value: string | null): string {
    return value ? new Date(value).toLocaleString('es-VE', { dateStyle: 'short', timeStyle: 'short' }) : '';
}
</script>

<template>
    <Head title="Panel de administración" />

    <div class="min-h-screen bg-slate-100 text-slate-900">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-3 px-4 py-3">
                <h1 class="flex items-center gap-2 font-bold"><span>🛡️</span> Panel de administración</h1>
                <div class="flex items-center gap-3">
                    <Link v-if="isMaster" href="/admin/usuarios" class="text-sm text-slate-600 hover:text-slate-900">
                        👥 Usuarios
                    </Link>
                    <Link href="/" class="text-sm text-slate-500 hover:text-slate-700">Ver sitio</Link>
                    <Link href="/logout" method="post" as="button" class="text-sm text-red-600 hover:underline">
                        Salir
                    </Link>
                </div>
            </div>
        </header>

        <FlashMessage />

        <main class="mx-auto max-w-5xl space-y-6 px-4 py-6">
            <section
                class="rounded-xl border p-4"
                :class="editsLocked ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-white'"
            >
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold">
                            {{ editsLocked ? '🔒 Ediciones BLOQUEADAS' : '🔓 Ediciones abiertas' }}
                        </p>
                        <p class="text-sm text-slate-500">
                            {{
                                editsLocked
                                    ? 'Nadie puede crear ni editar (solo gestores). Úsalo durante un ataque.'
                                    : 'Cualquiera puede registrar y editar lugares.'
                            }}
                        </p>
                    </div>
                    <Link
                        href="/admin/lock"
                        method="post"
                        as="button"
                        preserve-scroll
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                        :class="editsLocked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                    >
                        {{ editsLocked ? 'Reabrir ediciones' : 'Bloquear ediciones' }}
                    </Link>
                </div>
            </section>

            <section>
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-semibold">Historial de cambios ({{ audits.total }})</h2>
                    <select
                        :value="filters.building ?? ''"
                        class="h-9 rounded-lg border border-slate-300 bg-white px-2 text-sm"
                        @change="filterBuilding(($event.target as HTMLSelectElement).value)"
                    >
                        <option value="">Todos los edificios</option>
                        <option v-for="building in buildings" :key="building.slug" :value="building.slug">
                            {{ building.name }}
                        </option>
                    </select>
                </div>

                <ol v-if="audits.data.length" class="space-y-2">
                    <li
                        v-for="entry in audits.data"
                        :key="entry.id"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-slate-700">
                                <span class="font-medium">{{ entry.user }}</span>
                                {{ eventLabel(entry.event) }}
                                <span class="text-slate-400">{{ entry.target.kind }}:</span>
                                <Link
                                    v-if="entry.target.slug"
                                    :href="`/edificios/${entry.target.slug}`"
                                    class="font-medium text-slate-900 underline"
                                >
                                    {{ entry.target.label }}
                                </Link>
                                <span v-else class="font-medium">{{ entry.target.label }}</span>
                            </p>
                            <span class="text-xs text-slate-400">{{ formatDate(entry.at) }}</span>
                        </div>
                        <ul v-if="entry.changes.length" class="mt-1 space-y-0.5 text-xs text-slate-500">
                            <li v-for="change in entry.changes" :key="change.field">
                                <span class="font-medium">{{ fieldLabels[change.field] ?? change.field }}:</span>
                                <span class="line-through">{{ change.old ?? '—' }}</span> →
                                <span class="text-slate-700">{{ change.new ?? '—' }}</span>
                            </li>
                        </ul>
                    </li>
                </ol>
                <p v-else class="rounded-lg border border-dashed border-slate-300 p-8 text-center text-slate-500">
                    No hay cambios registrados con estos filtros.
                </p>

                <nav v-if="audits.last_page > 1" class="mt-6 flex items-center justify-between">
                    <Link
                        v-if="audits.prev_page_url"
                        :href="audits.prev_page_url"
                        preserve-scroll
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50"
                    >
                        ← Anterior
                    </Link>
                    <span v-else />
                    <span class="text-sm text-slate-500">{{ audits.current_page }} / {{ audits.last_page }}</span>
                    <Link
                        v-if="audits.next_page_url"
                        :href="audits.next_page_url"
                        preserve-scroll
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50"
                    >
                        Siguiente →
                    </Link>
                    <span v-else />
                </nav>
            </section>
        </main>
    </div>
</template>

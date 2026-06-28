<script setup lang="ts">
import AppHeader from '@/components/AppHeader.vue';
import BuildingCard from '@/components/BuildingCard.vue';
import BuildingFormModal from '@/components/BuildingFormModal.vue';
import DashboardStats from '@/components/DashboardStats.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import NeedsBreakdown from '@/components/NeedsBreakdown.vue';
import type {
    BuildingStats,
    BuildingSummary,
    EnumOption,
    NeedsSummary,
    Paginator,
} from '@/types/models';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    buildings: Paginator<BuildingSummary>;
    stats: BuildingStats;
    needsSummary: NeedsSummary;
    filters: {
        q: string;
        type: string | null;
        status: string | null;
        needStatus: string | null;
        category: string | null;
    };
    options: { types: EnumOption[]; statuses: EnumOption[] };
}>();

const search = ref(props.filters.q ?? '');
let searchTimer: ReturnType<typeof setTimeout> | undefined;
// Set when we clear the search box programmatically, so the debounced watcher
// below skips the redundant navigation it would otherwise fire.
let suppressSearchWatch = false;

const hasActiveFilters = computed<boolean>(() =>
    Boolean(
        props.filters.q ||
        props.filters.type ||
        props.filters.status ||
        props.filters.needStatus ||
        props.filters.category,
    ),
);

/**
 * Reset every filter (type, status, need status, category and search) and show
 * all places. The "Todos" chip uses this so it never leaves a stale category or
 * status stuck in the URL.
 */
function clearAllFilters(): void {
    clearTimeout(searchTimer);
    if (search.value !== '') {
        suppressSearchWatch = true;
        search.value = '';
    }
    router.get(
        '/',
        {},
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function applyFilters(overrides: {
    type?: string | null;
    status?: string | null;
    needStatus?: string | null;
    category?: string | number | null;
}): void {
    router.get(
        '/',
        {
            q: search.value || undefined,
            type:
                ('type' in overrides ? overrides.type : props.filters.type) ||
                undefined,
            status:
                ('status' in overrides
                    ? overrides.status
                    : props.filters.status) || undefined,
            needStatus:
                ('needStatus' in overrides
                    ? overrides.needStatus
                    : props.filters.needStatus) || undefined,
            category:
                ('category' in overrides
                    ? overrides.category
                    : props.filters.category) || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function toggleNeedStatus(value: string): void {
    applyFilters({
        needStatus: props.filters.needStatus === value ? null : value,
    });
}

function toggleCategory(id: number): void {
    applyFilters({
        category: props.filters.category === String(id) ? null : id,
    });
}

watch(search, () => {
    if (suppressSearchWatch) {
        suppressSearchWatch = false;
        return;
    }
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => applyFilters({}), 350);
});

function toggleType(value: string): void {
    applyFilters({ type: props.filters.type === value ? null : value });
}

function toggleStatus(value: string): void {
    applyFilters({ status: props.filters.status === value ? null : value });
}

interface SectorGroup {
    name: string;
    buildings: BuildingSummary[];
}

const grouped = computed<SectorGroup[]>(() => {
    const groups = new Map<string, SectorGroup>();
    for (const building of props.buildings.data) {
        const key = building.community?.name ?? 'Sin sector asignado';
        if (!groups.has(key)) {
            groups.set(key, { name: key, buildings: [] });
        }
        groups.get(key)!.buildings.push(building);
    }
    // Sectors with the most buildings first; alphabetical as a tie-breaker.
    return Array.from(groups.values()).sort(
        (a, b) =>
            b.buildings.length - a.buildings.length ||
            a.name.localeCompare(b.name, 'es'),
    );
});
</script>

<template>
    <Head title="VEI" />

    <div class="min-h-screen bg-slate-50 text-slate-900">
        <AppHeader />
        <FlashMessage />

        <main class="mx-auto max-w-6xl px-4 py-6">
            <section
                class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="h-1.5 w-full bg-[linear-gradient(to_right,#FFCC00_0_33.3%,#00247D_33.3%_66.6%,#CF142B_66.6%)]"
                />
                <div class="px-5 py-5 sm:px-6 sm:py-6">
                    <div class="flex items-center gap-3">
                        <img
                            src="/favicon.svg"
                            alt="VEI"
                            class="h-11 w-11 shrink-0 rounded-lg"
                        />
                        <div class="min-w-0">
                            <h1
                                class="text-lg font-bold text-slate-900 sm:text-xl"
                            >
                                VEI — Venezuela Entrega Insumos
                            </h1>
                            <p class="text-sm text-slate-500">
                                Mapa vivo de ayuda tras el terremoto
                            </p>
                        </div>
                    </div>

                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Aquí vemos en tiempo real qué necesita cada edificio,
                        hospital y refugio: rescate, insumos y recursos.
                        Cualquiera puede reportar una necesidad o encargarse de
                        cubrirla,
                        <span class="font-medium text-slate-700"
                            >sin cuenta ni registro</span
                        >.
                    </p>

                    <p
                        class="mt-2 flex items-center gap-1.5 text-sm font-medium text-red-700"
                    >
                        <span aria-hidden="true">❤️</span>
                        Juntos llegamos más rápido: cada reporte y cada mano
                        cuentan.
                    </p>

                    <BuildingFormModal mode="create">
                        <template #trigger>
                            <button
                                type="button"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3.5 text-base font-bold text-white shadow-sm transition hover:bg-red-700 active:scale-[0.99] sm:w-auto"
                            >
                                <span
                                    class="text-xl leading-none"
                                    aria-hidden="true"
                                    >➕</span
                                >
                                Agregar un edificio aquí
                            </button>
                        </template>
                    </BuildingFormModal>
                    <p class="mt-1.5 text-xs text-slate-500">
                        ¿Conoces un lugar afectado? Edificios, centros de salud,
                        refugios, escuelas… súmalo al mapa.
                    </p>
                </div>
            </section>

            <DashboardStats :stats="stats" />

            <NeedsBreakdown
                :summary="needsSummary"
                :active-need-status="filters.needStatus"
                :active-category="filters.category"
                class="mt-3"
                @toggle-status="toggleNeedStatus"
                @toggle-category="toggleCategory"
            />

            <section class="mt-5 space-y-3">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Buscar por nombre, sector o dirección…"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/30 focus:outline-none"
                />

                <div class="-mx-1 flex gap-1.5 overflow-x-auto px-1 pb-1">
                    <button
                        type="button"
                        class="shrink-0 rounded-full border px-3 py-1 text-sm font-medium transition"
                        :class="
                            !hasActiveFilters
                                ? 'border-slate-900 bg-slate-900 text-white'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'
                        "
                        @click="clearAllFilters()"
                    >
                        Todos
                    </button>
                    <button
                        v-for="type in options.types"
                        :key="type.value"
                        type="button"
                        class="shrink-0 rounded-full border px-3 py-1 text-sm font-medium transition"
                        :class="
                            filters.type === type.value
                                ? 'border-slate-900 bg-slate-900 text-white'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'
                        "
                        @click="toggleType(type.value)"
                    >
                        {{ type.icon }} {{ type.label }}
                    </button>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <button
                        v-for="status in options.statuses"
                        :key="status.value"
                        type="button"
                        class="rounded-full border px-3 py-1 text-sm font-medium transition"
                        :class="
                            filters.status === status.value
                                ? 'border-slate-900 bg-slate-100'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'
                        "
                        @click="toggleStatus(status.value)"
                    >
                        {{ status.emoji }} {{ status.label }}
                    </button>
                </div>
            </section>

            <p class="mt-4 text-sm text-slate-500">
                {{ buildings.total }}
                {{ buildings.total === 1 ? 'lugar' : 'lugares' }}
                <span v-if="buildings.last_page > 1"
                    >· página {{ buildings.current_page }} de
                    {{ buildings.last_page }}</span
                >
            </p>

            <section v-if="grouped.length" class="mt-2 space-y-8">
                <div v-for="group in grouped" :key="group.name">
                    <h2
                        class="mb-3 flex items-center gap-2 text-sm font-semibold tracking-wide text-slate-500 uppercase"
                    >
                        📍 {{ group.name }}
                        <span
                            class="rounded-full bg-slate-200 px-2 py-0.5 text-xs text-slate-600"
                        >
                            {{ group.buildings.length }}
                        </span>
                    </h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <BuildingCard
                            v-for="building in group.buildings"
                            :key="building.id"
                            :building="building"
                        />
                    </div>
                </div>
            </section>

            <div
                v-else
                class="mt-10 rounded-xl border border-dashed border-slate-300 p-10 text-center"
            >
                <p class="text-slate-500">
                    No hay lugares que coincidan con la búsqueda.
                </p>
            </div>

            <nav
                v-if="buildings.last_page > 1"
                class="mt-8 flex items-center justify-between"
            >
                <Link
                    v-if="buildings.prev_page_url"
                    :href="buildings.prev_page_url"
                    preserve-scroll
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50"
                >
                    ← Anterior
                </Link>
                <span v-else />
                <span class="text-sm text-slate-500"
                    >{{ buildings.current_page }} /
                    {{ buildings.last_page }}</span
                >
                <Link
                    v-if="buildings.next_page_url"
                    :href="buildings.next_page_url"
                    preserve-scroll
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50"
                >
                    Siguiente →
                </Link>
                <span v-else />
            </nav>
        </main>
    </div>
</template>

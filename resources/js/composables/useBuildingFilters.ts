import type { BuildingSummary } from '@/types/models';
import { computed, reactive, type ComputedRef } from 'vue';

export interface BuildingFilterState {
    search: string;
    status: string | null;
    mode: string | null;
    type: string | null;
}

export interface CommunityGroup {
    name: string;
    buildings: BuildingSummary[];
}

const statusRank: Record<string, number> = {
    critico: 0,
    necesita_apoyo: 1,
    normal: 2,
};

/**
 * Client-side filtering and community grouping for the public list. Keeps the
 * UI instant while the dataset is small; swap to server-side pagination later.
 */
export function useBuildingFilters(source: () => BuildingSummary[]) {
    const filters = reactive<BuildingFilterState>({
        search: '',
        status: null,
        mode: null,
        type: null,
    });

    const filtered: ComputedRef<BuildingSummary[]> = computed(() => {
        const term = filters.search.trim().toLowerCase();

        return source().filter((building) => {
            if (filters.status && building.status.value !== filters.status) {
                return false;
            }
            if (filters.mode && building.mode.value !== filters.mode) {
                return false;
            }
            if (filters.type && building.type.value !== filters.type) {
                return false;
            }
            if (term) {
                const haystack = [
                    building.name,
                    building.address ?? '',
                    building.community?.name ?? '',
                    ...building.topNeeds.map((need) => need.name),
                ]
                    .join(' ')
                    .toLowerCase();

                if (!haystack.includes(term)) {
                    return false;
                }
            }

            return true;
        });
    });

    const grouped: ComputedRef<CommunityGroup[]> = computed(() => {
        const groups = new Map<string, CommunityGroup>();

        for (const building of filtered.value) {
            const key = building.community?.name ?? 'Sin sector asignado';
            if (!groups.has(key)) {
                groups.set(key, { name: key, buildings: [] });
            }
            groups.get(key)!.buildings.push(building);
        }

        for (const group of groups.values()) {
            group.buildings.sort(
                (a, b) => (statusRank[a.status.value] ?? 9) - (statusRank[b.status.value] ?? 9),
            );
        }

        return Array.from(groups.values()).sort((a, b) => a.name.localeCompare(b.name, 'es'));
    });

    const hasActiveFilters = computed(
        () => Boolean(filters.search || filters.status || filters.mode || filters.type),
    );

    function reset(): void {
        filters.search = '';
        filters.status = null;
        filters.mode = null;
        filters.type = null;
    }

    return { filters, filtered, grouped, hasActiveFilters, reset };
}

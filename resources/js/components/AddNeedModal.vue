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
import type { EnumOption, Supply, SupplyCategory } from '@/types/models';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    buildingSlug: string;
    supplyCategories: SupplyCategory[];
    priorities: EnumOption[];
}>();

const open = ref(false);
const selectedCategoryId = ref<number | null>(null);
const search = ref('');
const processing = ref(false);
const error = ref('');

interface SupplyWithCategory extends Supply {
    category: SupplyCategory;
}

interface StagedNeed {
    key: number;
    supply_id: number | null;
    custom_supply_name: string;
    supply_category_id: number | null;
    categoryLabel: string;
    name: string;
    unit: string;
    quantity: number | undefined;
    priority: string;
    editable: boolean;
}

const items = ref<StagedNeed[]>([]);
let keySeq = 1;

const allSupplies = computed<SupplyWithCategory[]>(() =>
    props.supplyCategories.flatMap((category) =>
        category.supplies.map((supply) => ({ ...supply, category })),
    ),
);

const stagedSupplyIds = computed(() => new Set(items.value.map((item) => item.supply_id)));

const searchResults = computed<SupplyWithCategory[]>(() => {
    const term = search.value.trim().toLowerCase();
    if (!term) {
        return [];
    }
    return allSupplies.value
        .filter((supply) => supply.name.toLowerCase().includes(term) && !stagedSupplyIds.value.has(supply.id))
        .slice(0, 8);
});

const selectedCategory = computed(
    () => props.supplyCategories.find((category) => category.id === selectedCategoryId.value) ?? null,
);

const canSubmit = computed(
    () => items.value.length > 0 && items.value.every((item) => !item.editable || item.custom_supply_name.trim() !== ''),
);

function stageSupply(supply: Supply, category: SupplyCategory): void {
    if (stagedSupplyIds.value.has(supply.id)) {
        return;
    }
    items.value.push({
        key: keySeq++,
        supply_id: supply.id,
        custom_supply_name: '',
        supply_category_id: category.id,
        categoryLabel: category.name,
        name: supply.name,
        unit: supply.unit ?? '',
        quantity: undefined,
        priority: 'media',
        editable: false,
    });
}

function stageFromSearch(item: SupplyWithCategory): void {
    stageSupply(item, item.category);
    search.value = '';
}

function stageCustom(): void {
    const category = selectedCategory.value;
    items.value.push({
        key: keySeq++,
        supply_id: null,
        custom_supply_name: '',
        supply_category_id: category?.id ?? null,
        categoryLabel: category?.name ?? 'Otro',
        name: '',
        unit: '',
        quantity: undefined,
        priority: 'media',
        editable: true,
    });
}

function remove(key: number): void {
    items.value = items.value.filter((item) => item.key !== key);
}

function reset(): void {
    items.value = [];
    selectedCategoryId.value = null;
    search.value = '';
    error.value = '';
}

function submit(): void {
    const needs = items.value.map((item) => ({
        supply_id: item.supply_id,
        custom_supply_name: item.editable ? item.custom_supply_name : null,
        supply_category_id: item.supply_category_id,
        quantity: item.quantity ?? null,
        unit: item.unit || null,
        priority: item.priority,
    }));

    router.post(
        `/edificios/${props.buildingSlug}/necesidades/lote`,
        { needs },
        {
            preserveScroll: true,
            onStart: () => {
                processing.value = true;
                error.value = '';
            },
            onSuccess: () => {
                open.value = false;
                reset();
            },
            onError: () => {
                error.value = 'Revisa los insumos: cada uno escrito a mano necesita nombre y categoría.';
            },
            onFinish: () => {
                processing.value = false;
            },
        },
    );
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <slot name="trigger" />
        </DialogTrigger>
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Agregar necesidades</DialogTitle>
                <DialogDescription>
                    Agrega todas las que hagan falta de una vez. Búscalas o elígelas por categoría.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <Label for="need-search">Buscar insumo</Label>
                    <Input
                        id="need-search"
                        v-model="search"
                        placeholder="agua, palas, insulina, combustible…"
                    />
                    <ul v-if="searchResults.length" class="divide-y divide-slate-100 rounded-lg border border-slate-200">
                        <li v-for="item in searchResults" :key="item.id">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm hover:bg-slate-50"
                                @click="stageFromSearch(item)"
                            >
                                <span class="font-medium text-slate-800">+ {{ item.name }}</span>
                                <span class="shrink-0 text-xs text-slate-400">
                                    {{ item.category.icon }} {{ item.category.name }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="relative text-center">
                    <span class="bg-background relative z-10 px-2 text-xs text-slate-400">o elige por categoría</span>
                    <span class="absolute inset-x-0 top-1/2 h-px bg-slate-200" />
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <button
                        v-for="category in supplyCategories"
                        :key="category.id"
                        type="button"
                        class="flex flex-col items-center gap-1 rounded-lg border p-2 text-center transition"
                        :class="
                            selectedCategoryId === category.id
                                ? 'border-slate-900 bg-slate-900 text-white'
                                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'
                        "
                        @click="selectedCategoryId = category.id"
                    >
                        <span class="text-lg">{{ category.icon }}</span>
                        <span class="text-[10px] leading-tight">{{ category.name }}</span>
                    </button>
                </div>

                <div v-if="selectedCategory" class="flex flex-wrap gap-1.5">
                    <button
                        v-for="supply in selectedCategory.supplies"
                        :key="supply.id"
                        type="button"
                        :disabled="stagedSupplyIds.has(supply.id)"
                        class="rounded-full border px-3 py-1 text-sm transition"
                        :class="
                            stagedSupplyIds.has(supply.id)
                                ? 'border-green-300 bg-green-50 text-green-700'
                                : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'
                        "
                        @click="stageSupply(supply, selectedCategory)"
                    >
                        {{ stagedSupplyIds.has(supply.id) ? '✓' : '+' }} {{ supply.name }}
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-dashed border-slate-400 bg-white px-3 py-1 text-sm text-slate-600 hover:bg-slate-50"
                        @click="stageCustom"
                    >
                        ✏️ Otro
                    </button>
                </div>

                <div v-if="items.length" class="space-y-2">
                    <Label>Necesidades a agregar ({{ items.length }})</Label>
                    <ul class="space-y-2">
                        <li
                            v-for="item in items"
                            :key="item.key"
                            class="flex flex-wrap items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 p-2"
                        >
                            <div class="min-w-[40%] flex-1">
                                <Input
                                    v-if="item.editable"
                                    v-model="item.custom_supply_name"
                                    placeholder="Escribe el insumo…"
                                    class="bg-white"
                                />
                                <span v-else class="text-sm font-medium text-slate-800">{{ item.name }}</span>
                                <span class="block text-[11px] text-slate-400">{{ item.categoryLabel }}</span>
                            </div>
                            <input
                                v-model.number="item.quantity"
                                type="number"
                                min="0"
                                placeholder="Cant."
                                class="h-8 w-16 rounded-md border border-slate-300 bg-white px-2 text-sm"
                            />
                            <input
                                v-model="item.unit"
                                placeholder="unidad"
                                class="h-8 w-20 rounded-md border border-slate-300 bg-white px-2 text-sm"
                            />
                            <select
                                v-model="item.priority"
                                class="h-8 w-24 rounded-md border border-slate-300 bg-white px-1 text-sm"
                            >
                                <option v-for="priority in priorities" :key="priority.value" :value="priority.value">
                                    {{ priority.label }}
                                </option>
                            </select>
                            <button
                                type="button"
                                class="text-slate-400 hover:text-red-600"
                                title="Quitar"
                                @click="remove(item.key)"
                            >
                                ✕
                            </button>
                        </li>
                    </ul>
                </div>

                <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

                <Button class="w-full" :disabled="!canSubmit || processing" @click="submit">
                    {{ items.length ? `Guardar ${items.length} necesidad${items.length === 1 ? '' : 'es'}` : 'Elige al menos un insumo' }}
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>

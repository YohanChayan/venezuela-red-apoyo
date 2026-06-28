<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Supply;
use App\Models\SupplyCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SupplyCatalogSeeder extends Seeder
{
    /**
     * Prefabricated, categorised catalog of supplies. People pick from these
     * for speed; a free-text field on the need covers anything missing.
     * Idempotent: safe to re-run as the catalog grows.
     */
    public function run(): void
    {
        foreach ($this->catalog() as $order => $group) {
            $category = SupplyCategory::updateOrCreate(
                ['slug' => Str::slug($group['name'])],
                [
                    'name' => $group['name'],
                    'icon' => $group['icon'],
                    'color' => $group['color'],
                    'sort_order' => $order,
                ],
            );

            foreach ($group['supplies'] as $supplyOrder => $supply) {
                Supply::updateOrCreate(
                    ['slug' => Str::slug($category->slug.' '.$supply['name'])],
                    [
                        'supply_category_id' => $category->id,
                        'name' => $supply['name'],
                        'unit' => $supply['unit'],
                        'is_predefined' => true,
                        'sort_order' => $supplyOrder,
                    ],
                );
            }
        }
    }

    /**
     * @return array<int, array{name: string, icon: string, color: string, supplies: array<int, array{name: string, unit: string}>}>
     */
    private function catalog(): array
    {
        return [
            [
                'name' => 'Agua y saneamiento',
                'icon' => '💧',
                'color' => 'blue',
                'supplies' => [
                    ['name' => 'Agua potable', 'unit' => 'litros'],
                    ['name' => 'Botellones de agua', 'unit' => 'unidades'],
                    ['name' => 'Pastillas potabilizadoras', 'unit' => 'unidades'],
                    ['name' => 'Tanques de agua', 'unit' => 'unidades'],
                    ['name' => 'Filtros de agua', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Alimentos',
                'icon' => '🍲',
                'color' => 'orange',
                'supplies' => [
                    ['name' => 'Comida no perecedera', 'unit' => 'cajas'],
                    ['name' => 'Comida enlatada', 'unit' => 'unidades'],
                    ['name' => 'Alimentos preparados', 'unit' => 'raciones'],
                    ['name' => 'Jugos y bebidas', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Medicinas e insumos médicos',
                'icon' => '💊',
                'color' => 'red',
                'supplies' => [
                    ['name' => 'Analgésicos', 'unit' => 'unidades'],
                    ['name' => 'Antibióticos', 'unit' => 'unidades'],
                    ['name' => 'Suero / solución salina', 'unit' => 'unidades'],
                    ['name' => 'Insulina', 'unit' => 'unidades'],
                    ['name' => 'Medicamentos crónicos', 'unit' => 'unidades'],
                    ['name' => 'Material de curación y vendas', 'unit' => 'unidades'],
                    ['name' => 'Guantes quirúrgicos', 'unit' => 'cajas'],
                    ['name' => 'Jeringas', 'unit' => 'unidades'],
                    ['name' => 'Oxígeno medicinal', 'unit' => 'bombonas'],
                    ['name' => 'Bolsas de sangre', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Rescate y herramientas',
                'icon' => '🛠️',
                'color' => 'slate',
                'supplies' => [
                    ['name' => 'Palas', 'unit' => 'unidades'],
                    ['name' => 'Picos', 'unit' => 'unidades'],
                    ['name' => 'Guantes de trabajo', 'unit' => 'pares'],
                    ['name' => 'Cascos de seguridad', 'unit' => 'unidades'],
                    ['name' => 'Botas de seguridad', 'unit' => 'pares'],
                    ['name' => 'Mascarillas antipolvo', 'unit' => 'unidades'],
                    ['name' => 'Linternas', 'unit' => 'unidades'],
                    ['name' => 'Cuerdas', 'unit' => 'metros'],
                    ['name' => 'Motosierras', 'unit' => 'unidades'],
                    ['name' => 'Gatos hidráulicos', 'unit' => 'unidades'],
                    ['name' => 'Mandarrias y barras', 'unit' => 'unidades'],
                    ['name' => 'Carretillas', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Maquinaria pesada',
                'icon' => '🚜',
                'color' => 'yellow',
                'supplies' => [
                    ['name' => 'Retroexcavadora', 'unit' => 'unidades'],
                    ['name' => 'Grúa', 'unit' => 'unidades'],
                    ['name' => 'Camión de carga', 'unit' => 'unidades'],
                    ['name' => 'Montacargas', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Energía y combustible',
                'icon' => '⚡',
                'color' => 'amber',
                'supplies' => [
                    ['name' => 'Plantas eléctricas / generadores', 'unit' => 'unidades'],
                    ['name' => 'Gasolina', 'unit' => 'litros'],
                    ['name' => 'Diésel', 'unit' => 'litros'],
                    ['name' => 'Gas doméstico', 'unit' => 'bombonas'],
                    ['name' => 'Baterías', 'unit' => 'unidades'],
                    ['name' => 'Power banks', 'unit' => 'unidades'],
                    ['name' => 'Velas', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Refugio y abrigo',
                'icon' => '🛏️',
                'color' => 'purple',
                'supplies' => [
                    ['name' => 'Carpas', 'unit' => 'unidades'],
                    ['name' => 'Colchonetas', 'unit' => 'unidades'],
                    ['name' => 'Cobijas / mantas', 'unit' => 'unidades'],
                    ['name' => 'Toldos y lonas', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Higiene',
                'icon' => '🧼',
                'color' => 'teal',
                'supplies' => [
                    ['name' => 'Kits de higiene', 'unit' => 'unidades'],
                    ['name' => 'Jabón', 'unit' => 'unidades'],
                    ['name' => 'Toallas sanitarias', 'unit' => 'paquetes'],
                    ['name' => 'Papel higiénico', 'unit' => 'unidades'],
                    ['name' => 'Desinfectante', 'unit' => 'litros'],
                ],
            ],
            [
                'name' => 'Bebés y cuidado infantil',
                'icon' => '🍼',
                'color' => 'pink',
                'supplies' => [
                    ['name' => 'Fórmula / leche infantil', 'unit' => 'unidades'],
                    ['name' => 'Pañales', 'unit' => 'paquetes'],
                    ['name' => 'Teteros', 'unit' => 'unidades'],
                    ['name' => 'Ropa de bebé', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Comunicación',
                'icon' => '📻',
                'color' => 'indigo',
                'supplies' => [
                    ['name' => 'Radios', 'unit' => 'unidades'],
                    ['name' => 'Megáfonos', 'unit' => 'unidades'],
                    ['name' => 'Cargadores', 'unit' => 'unidades'],
                    ['name' => 'Saldo / recargas', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Transporte',
                'icon' => '🚚',
                'color' => 'cyan',
                'supplies' => [
                    ['name' => 'Ambulancia', 'unit' => 'unidades'],
                    ['name' => 'Camión', 'unit' => 'unidades'],
                    ['name' => 'Vehículo particular', 'unit' => 'unidades'],
                    ['name' => 'Motos', 'unit' => 'unidades'],
                ],
            ],
            [
                'name' => 'Personal y voluntarios',
                'icon' => '🧑‍🚒',
                'color' => 'green',
                'supplies' => [
                    ['name' => 'Médicos', 'unit' => 'personas'],
                    ['name' => 'Enfermeros', 'unit' => 'personas'],
                    ['name' => 'Paramédicos', 'unit' => 'personas'],
                    ['name' => 'Rescatistas', 'unit' => 'personas'],
                    ['name' => 'Ingenieros estructurales', 'unit' => 'personas'],
                    ['name' => 'Conductores', 'unit' => 'personas'],
                    ['name' => 'Voluntarios generales', 'unit' => 'personas'],
                    ['name' => 'Apoyo psicológico', 'unit' => 'personas'],
                ],
            ],
            [
                'name' => 'Otro',
                'icon' => '🧩',
                'color' => 'slate',
                'supplies' => [],
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BuildingMode;
use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\StructuralStatus;
use App\Models\Building;
use App\Models\Community;
use App\Services\StatusDeriver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BuildingSeeder extends Seeder
{
    /**
     * Seeds buildings and hospitals from the parallel research agent's output.
     * Source data lives in database/seeders/data/*.json with provenance and
     * confidence flags. Idempotent by slug.
     */
    public function run(): void
    {
        foreach (['hospitals_seed.json', 'buildings_seed.json'] as $file) {
            $path = database_path("seeders/data/{$file}");

            if (! File::exists($path)) {
                $this->command?->warn("Seed file no encontrado: {$file}");

                continue;
            }

            /** @var array<int, array<string, mixed>> $entries */
            $entries = json_decode(File::get($path), true) ?? [];

            foreach ($entries as $entry) {
                $this->seedBuilding($entry);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function seedBuilding(array $entry): void
    {
        $community = $this->resolveCommunity($entry);
        [$structural, , $mode] = $this->mapStatus(
            (string) ($entry['reported_status'] ?? ''),
            BuildingType::tryFrom((string) ($entry['type'] ?? 'otro')) ?? BuildingType::Otro,
        );

        $building = Building::updateOrCreate(
            ['slug' => Str::slug((string) $entry['name'])],
            [
                'community_id' => $community?->id,
                'name' => $entry['name'],
                'type' => (BuildingType::tryFrom((string) ($entry['type'] ?? 'otro')) ?? BuildingType::Otro)->value,
                'address' => $entry['address'] ?? null,
                'lat' => $entry['lat'] ?? null,
                'lng' => $entry['lng'] ?? null,
                'structural_status' => $structural->value,
                'mode' => $mode->value,
                'status_is_manual' => false,
                'notes' => $entry['notes'] ?? null,
                'source_url' => $entry['source_url'] ?? null,
                'confidence' => $entry['confidence'] ?? null,
                'last_reported_by' => 'Investigación inicial (verificar en terreno)',
            ],
        );

        app(StatusDeriver::class)->apply($building);
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function resolveCommunity(array $entry): ?Community
    {
        $name = $entry['community'] ?? null;

        if (! $name) {
            return null;
        }

        return Community::updateOrCreate(
            ['slug' => Str::slug((string) $name)],
            [
                'name' => $name,
                'municipality' => $entry['municipality'] ?? null,
                'state' => $entry['state'] ?? null,
            ],
        );
    }

    /**
     * Maps the free-text reported status from news into our structured enums.
     *
     * @return array{0: StructuralStatus, 1: BuildingStatus, 2: BuildingMode}
     */
    private function mapStatus(string $reported, BuildingType $type): array
    {
        $text = Str::lower($reported);

        $structural = match (true) {
            Str::contains($text, ['colaps', 'destruido', 'desplome total']) => StructuralStatus::Colapsado,
            Str::contains($text, ['dañad', 'desplome', 'inoperativo', 'grave']) => StructuralStatus::Danado,
            Str::contains($text, ['operativo', 'habilitado']) => StructuralStatus::SinEvaluar,
            default => StructuralStatus::SinEvaluar,
        };

        $status = match ($structural) {
            StructuralStatus::Colapsado => BuildingStatus::Critico,
            StructuralStatus::Danado => BuildingStatus::NecesitaApoyo,
            default => BuildingStatus::Normal,
        };

        $needsRescue = in_array($structural, [StructuralStatus::Colapsado, StructuralStatus::Danado], true)
            && $type !== BuildingType::Hospital;

        $mode = $needsRescue ? BuildingMode::Rescate : BuildingMode::Abastecimiento;

        return [$structural, $status, $mode];
    }
}

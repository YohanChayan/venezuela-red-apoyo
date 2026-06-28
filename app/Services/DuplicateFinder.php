<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Building;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Deterministic near-duplicate detection for building names. Normalizes names
 * (accents, case, spacing, filler words) and ranks candidates with PHP's
 * similar_text / levenshtein. No external search engine; results only warn
 * the user, they never block creation (this is a public wiki).
 */
class DuplicateFinder
{
    private const THRESHOLD = 72.0;

    /**
     * @return Collection<int, Building>
     */
    public function findSimilar(string $name, ?string $community = null, int $limit = 5): Collection
    {
        $target = $this->normalize($name);

        if (mb_strlen($target) < 3) {
            return collect();
        }

        $targetCommunity = $community ? $this->normalize($community) : null;

        return Building::query()
            ->select(['id', 'name', 'slug', 'status', 'community_id'])
            ->with('community:id,name')
            ->get()
            ->map(function (Building $building) use ($target, $targetCommunity): array {
                $score = $this->similarity($target, $this->normalize($building->name));

                if (
                    $targetCommunity !== null
                    && $building->community
                    && $this->normalize($building->community->name) === $targetCommunity
                ) {
                    $score += 8;
                }

                return ['building' => $building, 'score' => min(100.0, $score)];
            })
            ->filter(fn (array $row): bool => $row['score'] >= self::THRESHOLD)
            ->sortByDesc('score')
            ->take($limit)
            ->map(fn (array $row): Building => $row['building'])
            ->values();
    }

    private function normalize(string $value): string
    {
        $value = Str::lower(Str::ascii($value));
        $value = preg_replace('/[^a-z0-9 ]+/', ' ', $value) ?? '';
        // Drop generic filler words that don't help identify a specific place.
        $value = preg_replace(
            '/\b(edificio|edif|residencias?|residencial|torre|conjunto|apto|apartamento|el|la|los|las|de|del)\b/',
            ' ',
            $value,
        ) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    private function similarity(string $a, string $b): float
    {
        if ($a === '' || $b === '') {
            return 0.0;
        }

        if ($a === $b) {
            return 100.0;
        }

        similar_text($a, $b, $percent);

        $maxLen = max(mb_strlen($a), mb_strlen($b));
        $levRatio = $maxLen > 0 ? (1 - levenshtein($a, $b) / $maxLen) * 100 : 0.0;

        return max($percent, $levRatio);
    }
}

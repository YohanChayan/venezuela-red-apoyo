<?php

declare(strict_types=1);

namespace App\Support;

use BackedEnum;

/**
 * Serializes backed enums into option arrays for the frontend, including any
 * presentation accessors (label/icon/color/emoji) the enum happens to expose.
 */
final class EnumOptions
{
    /**
     * @param  array<int, BackedEnum>  $cases
     * @return array<int, array<string, mixed>>
     */
    public static function from(array $cases): array
    {
        return array_map(static function (BackedEnum $case): array {
            $option = [
                'value' => $case->value,
                'label' => method_exists($case, 'label') ? $case->label() : $case->name,
            ];

            foreach (['icon', 'color', 'emoji'] as $accessor) {
                if (method_exists($case, $accessor)) {
                    $option[$accessor] = $case->{$accessor}();
                }
            }

            return $option;
        }, $cases);
    }

    /**
     * Serialize a single enum instance the same way.
     *
     * @return array<string, mixed>|null
     */
    public static function one(?BackedEnum $case): ?array
    {
        return $case === null ? null : self::from([$case])[0];
    }
}

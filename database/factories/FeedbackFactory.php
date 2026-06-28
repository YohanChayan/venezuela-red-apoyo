<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FeedbackType;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(FeedbackType::cases()),
            'message' => fake()->sentence(),
            'contact' => fake()->boolean(40) ? fake()->safeEmail() : null,
            'url' => '/',
            'contributor_id' => null,
            'user_agent' => fake()->userAgent(),
        ];
    }
}

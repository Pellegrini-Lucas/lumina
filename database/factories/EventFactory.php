<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('now', '+1 month');
        $endTime = fake()->dateTimeBetween($startTime, $startTime->format('Y-m-d H:i:s').' +4 hours');

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_all_day' => fake()->boolean(20),
        ];
    }

    /**
     * Indicate that the event is all day.
     */
    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_all_day' => true,
        ]);
    }
}

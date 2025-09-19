<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company' => fake()->optional()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'vat_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{8}'),
            'address' => fake()->optional()->address(),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'CAD']),
            'hourly_rate' => fake()->optional()->randomFloat(2, 50, 200),
            'settings' => [
                'preferred_payment_terms' => fake()->randomElement([15, 30, 45, 60]),
                'tax_rate' => fake()->randomFloat(2, 0, 25),
            ],
        ];
    }
}

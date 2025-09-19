<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => \App\Models\Invoice::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'method' => fake()->randomElement(['cash', 'bank_transfer', 'credit_card', 'paypal', 'stripe', 'other']),
            'reference' => fake()->optional()->regexify('[A-Z0-9]{10}'),
            'notes' => fake()->optional()->sentence(),
            'paid_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}

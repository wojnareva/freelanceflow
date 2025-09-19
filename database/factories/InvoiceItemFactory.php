<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 100);
        $rate = fake()->randomFloat(2, 50, 200);
        $amount = $quantity * $rate;
        
        return [
            'invoice_id' => \App\Models\Invoice::factory(),
            'type' => fake()->randomElement(['time', 'fixed', 'expense']),
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'rate' => $rate,
            'amount' => $amount,
        ];
    }
}

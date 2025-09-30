<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * The Faker instance for a specific locale.
     *
     * @var \Faker\Generator|null
     */
    protected $faker;

    /**
     * Create a new factory instance for a specific locale.
     *
     * @param string $locale
     * @return $this
     */
    public function withLocale(string $locale)
    {
        $this->faker = Faker::create($locale);
        return $this;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->faker ?? fake();

        return [
            'name' => $faker->name(),
            'company' => $faker->optional()->company(),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->optional()->phoneNumber(),
            'vat_number' => $faker->optional()->regexify('[A-Z]{2}[0-9]{8}'),
            'address' => $faker->optional()->address(),
            'currency' => $faker->randomElement(['USD', 'EUR', 'GBP', 'CAD']),
            'hourly_rate' => $faker->optional()->randomFloat(2, 50, 200),
            'settings' => [
                'preferred_payment_terms' => $faker->randomElement([15, 30, 45, 60]),
                'tax_rate' => $faker->randomFloat(2, 0, 25),
            ],
        ];
    }
}

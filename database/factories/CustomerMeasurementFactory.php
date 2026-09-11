<?php

namespace Database\Factories;

use App\Models\CustomerMeasurement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerMeasurement>
 */
class CustomerMeasurementFactory extends Factory
{
    protected $model = CustomerMeasurement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_code' => CustomerMeasurement::CODE_PREFIX.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'unit' => 'in',
            'chest' => fake()->randomFloat(1, 30, 50),
            'bust' => fake()->randomFloat(1, 30, 50),
            'waist' => fake()->randomFloat(1, 20, 45),
            'shoulder' => fake()->randomFloat(1, 12, 22),
            'arm_length' => fake()->randomFloat(1, 18, 28),
            'top_length' => fake()->randomFloat(1, 20, 32),
            'half_bust' => fake()->randomFloat(1, 14, 24),
            'half_length' => fake()->randomFloat(1, 16, 28),
            'round_sleeve' => fake()->randomFloat(1, 10, 18),
            'length_sleeve' => fake()->randomFloat(1, 20, 26),
            'hip' => fake()->randomFloat(1, 30, 50),
            'inseam' => fake()->randomFloat(1, 24, 36),
            'thigh' => fake()->randomFloat(1, 18, 28),
            'ankle' => fake()->randomFloat(1, 8, 12),
            'trouser_skirt_length' => fake()->randomFloat(1, 24, 42),
            'height' => fake()->randomFloat(1, 55, 75),
            'weight' => fake()->randomFloat(1, 50, 120),
            'dress_size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL']),
            'gown_length' => fake()->randomFloat(1, 40, 62),
            'notes' => null,
        ];
    }
}

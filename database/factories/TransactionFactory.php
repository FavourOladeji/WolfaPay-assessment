<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $providerIds = Provider::factory(10)->create()->pluck('id');
        return [
            'provider_id' => $this->faker->randomElement($providerIds),
            'provider_transaction_id' => 'TXN-' . strtoupper(fake()->bothify('??####')),
            'amount' => $this->faker->numberBetween(1, 2000000),
            'reference_number' => Transaction::generateReferenceNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

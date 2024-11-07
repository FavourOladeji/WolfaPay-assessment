<?php

namespace Tests\Feature\V1;

use App\Http\Middleware\ForceJsonRequestHeader;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTransactionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_transaction_gets_created(): void
    {
        $user = User::factory()->create();
        $provider = Provider::factory()->create();
        $payload = [
            'provider_id' => $provider->id,
            'provider_transaction_id' => 'TXN-' . strtoupper(fake()->bothify('??####')),
            'amount' => 20000
        ];

        Sanctum::actingAs($user);
        $response = $this->post('/api/v1/transactions', $payload);
        $createdTransaction = array_merge($payload, ['reference_number' => $response->json('data')['reference_number']]);
        $this->assertDatabaseHas('transactions', $createdTransaction);
        $response->assertStatus(201);
    }

    public function test_transaction_cannot_be_created_with_invalid_provider_id()
    {
        $user = User::factory()->create();
        $payload = [
            'provider_transaction_id' => 'TXN-' . strtoupper(fake()->bothify('??####')),
            'amount' => 20000
        ];
        Sanctum::actingAs($user);
        $response = $this->post('/api/v1/transactions', $payload);
        $response->assertInvalid('provider_id');

        $payload = [
            'provider_id' => 1,
            'provider_transaction_id' => 'TXN-' . strtoupper(fake()->bothify('??####')),
            'amount' => 20000
        ];
        $response2 = $this->post('/api/v1/transactions', $payload);
        $response2->assertInvalid('provider_id');

    }

    public function test_only_authenticated_users_can_create_transactions()
    {
        $provider = Provider::factory()->create();
        $payload = [
            'provider_id' => $provider->id,
            'provider_transaction_id' => 'TXN-' . strtoupper(fake()->bothify('??####')),
            'amount' => 20000
        ];

        $response = $this->post('/api/v1/transactions', $payload, ['Accept' => 'application/json']);
        $response->assertUnauthorized();
    }
}

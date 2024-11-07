<?php

namespace Tests\Feature\V1;

use App\Models\Provider;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetTransactionsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_all_created_transactions_are_returned_in_paginated_format(): void
    {
        $user = User::factory()->create();
        $countOfTransactions = 20;
        $transactions = Transaction::factory($countOfTransactions)->create();
        Sanctum::actingAs($user);
        $response = $this->get('/api/v1/transactions');
        dump($response->json());
        $response->assertJsonStructure(['data', 'links', 'meta']);
        $response->assertJsonPath('meta.total', $countOfTransactions)->assertJsonPath('meta.per_page', 10);
        $this->assertDatabaseCount('transactions', $countOfTransactions);
        $response->assertStatus(200);
    }

    public function test_transactions_are_filterd_by_providers(): void
    {
        $user = User::factory()->create();
        $countOfTransactions = 20;
        $transactions = Transaction::factory($countOfTransactions)->create();
        $provider = $transactions->first()->provider;
        $payload = [
            'provider_id' => $provider->id
        ];
        $payload = http_build_query($payload);
        $countOfTransactionsByProvider = Transaction::where('provider_id', $provider->id)->count();
        Sanctum::actingAs($user);
        $response = $this->get("/api/v1/transactions?{$payload}");
        $response
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->has('data', $countOfTransactionsByProvider)
                    ->has(
                        'data.0',
                        fn(AssertableJson $json) =>
                        $json->where('provider', $provider->name)->etc()
                    )
                    ->etc()
            )
            ->assertJsonPath('meta.total', $countOfTransactionsByProvider);


        $this->assertDatabaseCount('transactions', $countOfTransactions);
        $response->assertStatus(200);
    }

    public function test_transactions_are_sorted_by_amount()
    {
        $user = User::factory()->create();
        $countOfTransactions = 20;
        Transaction::factory($countOfTransactions)->create();
        $sortDirection = "desc";
        $transactionWithHighestAmount = Transaction::orderBy('amount', $sortDirection)->first();
        $payload = http_build_query([
            'sort_column' => 'amount',
            'sort_direction' => $sortDirection
        ]);
        Sanctum::actingAs($user);
        $response = $this->get("/api/v1/transactions?{$payload}");
        $response
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->has(
                    'data.0',
                    fn(AssertableJson $json) =>
                    $json->where('amount', $transactionWithHighestAmount->amount)
                        ->where('reference_number', $transactionWithHighestAmount->reference_number)
                        ->where('reference_number', $transactionWithHighestAmount->reference_number)
                        ->etc()
                )
                    ->etc()
            )
            ->assertJsonPath('data.0.amount', $transactionWithHighestAmount->amount);
    }

    public function test_transactions_are_sorted_by_created_at()
    {
        $user = User::factory()->create();
        $countOfTransactions = 20;
        Transaction::factory($countOfTransactions)->create();
        $sortDirection = "desc";
        $latestTransaction = Transaction::orderBy('created_at', $sortDirection)->first();
        $payload = http_build_query([
            'sort_column' => 'created_at',
            'sort_direction' => $sortDirection
        ]);
        Sanctum::actingAs($user);
        $response = $this->get("/api/v1/transactions?{$payload}");
        $response
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->has(
                    'data.0',
                    fn(AssertableJson $json) =>
                    $json->where('created_at', Carbon::parse($latestTransaction->created_at)->toISOString())
                        ->where('reference_number', $latestTransaction->reference_number)
                        ->where('amount', $latestTransaction->amount)
                        ->where('provider_transaction_id', $latestTransaction->provider_transaction_id)
                        ->etc()
                )
                    ->etc()
            );
    }

    public function test_that_invalid_request_query_parameters_return_an_error()
    {
        $user = User::factory()->create();
        $countOfTransactions = 20;
        Transaction::factory($countOfTransactions)->create();
        $sortDirection = "desc";
        $payload = http_build_query([
            'sort_column' => 'provider_transaction_id',
            'sort_direction' => $sortDirection
        ]);
        Sanctum::actingAs($user);
        $response = $this->get("/api/v1/transactions?{$payload}");
        $response->assertInvalid('sort_column');
    }
}

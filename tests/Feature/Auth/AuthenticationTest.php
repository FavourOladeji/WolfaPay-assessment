<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;

use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

test('users can generate token using the login route', function () {

    $user = User::factory()->create();

    /**
     * @var TestResponse
     */
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'mobile'
    ]);

    assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'mobile'
    ]);
    $response->assertStatus(200)->assertJsonStructure([
        'success',
        'token'
    ]);

});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('tokens get deleted when user logs out', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'mobile'
    ]);

    assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'mobile'
    ]);

    Sanctum::actingAs($user);

    /**
     * @var TestResponse
     */
    $this->post('/logout');

    assertDatabaseEmpty('personal_access_tokens');
});

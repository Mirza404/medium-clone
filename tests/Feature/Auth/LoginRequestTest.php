<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

it('authenticates valid credentials via the login request', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
    ]);

    $response = $this->post('/login', [
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('throttles login after five failed attempts from the same ip', function () {
    $user = User::factory()->create([
        'email' => 'ratelimit@example.com',
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->from('/login')
            ->post('/login', [
                'email' => 'ratelimit@example.com',
                'password' => 'invalid-password',
            ]);
    }

    $response = $this->from('/login')
        ->post('/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'invalid-password',
        ]);

    $response->assertSessionHasErrors('email');

    $key = Str::transliterate(Str::lower('ratelimit@example.com').'|127.0.0.1');
    $this->assertTrue(RateLimiter::tooManyAttempts($key, 5));
});

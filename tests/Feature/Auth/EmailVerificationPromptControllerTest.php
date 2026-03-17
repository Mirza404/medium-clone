<?php

use App\Models\User;

it('redirects verified users away from the verification notice', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertRedirect(route('dashboard'));
});

it('shows the verification view to unverified users', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertViewIs('auth.verify-email');
});

<?php

use App\Models\User;

it('toggles the follower relationship and returns the updated count', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $followResponse = $this->actingAs($user)
        ->post(route('follow', $target));

    $followResponse
        ->assertOk()
        ->assertJson(['followersCount' => 1]);

    $this->assertDatabaseHas('followers', [
        'user_id' => $target->id,
        'follower_id' => $user->id,
    ]);

    $unfollowResponse = $this->actingAs($user)
        ->post(route('follow', $target));

    $unfollowResponse
        ->assertOk()
        ->assertJson(['followersCount' => 0]);

    $this->assertDatabaseMissing('followers', [
        'user_id' => $target->id,
        'follower_id' => $user->id,
    ]);
});

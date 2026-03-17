<?php

use App\Models\Follower;
use App\Models\User;

it('tracks both the owner and the follower relationship', function () {
    $user = User::factory()->create();
    $follower = User::factory()->create();

    $record = Follower::create([
        'user_id' => $user->id,
        'follower_id' => $follower->id,
    ]);

    expect($record->user->is($user))->toBeTrue()
        ->and($record->follower->is($follower))->toBeTrue();
});

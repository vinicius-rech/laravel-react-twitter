<?php
if (!function_exists('it')) { return; }

use App\Models\User;
use App\Models\Tweet;

it('has a user relationship', function () {
    $tweet = Tweet::factory()->create();
    $this->assertInstanceOf(User::class, $tweet->user);
});

it('scopes visible tweets for guests to public only', function () {
    Tweet::factory()->count(2)->create(['visibility' => 'public']);
    Tweet::factory()->count(3)->create(['visibility' => 'private']);

    $visible = Tweet::visibleTo(null)->get();
    $this->assertCount(2, $visible);
    $this->assertSame(['public'], $visible->pluck('visibility')->unique()->values()->all());
});

it('scopes visible tweets for a user to include their private tweets', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();

    $minePrivate = Tweet::factory()->create([
        'user_id' => $me->id,
        'visibility' => 'private',
    ]);
    $public = Tweet::factory()->create(['visibility' => 'public']);
    $otherPrivate = Tweet::factory()->create([
        'user_id' => $other->id,
        'visibility' => 'private',
    ]);

    $visible = Tweet::visibleTo($me)->get();
    $ids = $visible->pluck('id')->all();
    $this->assertContains($minePrivate->id, $ids);
    $this->assertContains($public->id, $ids);
    $this->assertNotContains($otherPrivate->id, $ids);
});

it('supports soft deleting tweets', function () {
    $tweet = Tweet::factory()->create();
    $tweet->delete();

    $this->assertNotNull(Tweet::withTrashed()->find($tweet->id));
    $this->assertNull(Tweet::find($tweet->id));
});

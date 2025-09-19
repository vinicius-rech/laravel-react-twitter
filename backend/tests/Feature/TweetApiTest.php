<?php

use App\Models\User;
use App\Models\Tweet;

it('lists visible tweets for guests (only public)', function () {
    Tweet::factory()->count(2)->create(['visibility' => 'public']);
    Tweet::factory()->count(3)->create(['visibility' => 'private']);

    $res = $this->getJson(route('tweets.index'));
    $res->assertUnauthorized(); // index is protected by sanctum
});

it('lists tweets visible to authenticated user', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();
    $minePrivate = Tweet::factory()->create(['user_id' => $me->id, 'visibility' => 'private']);
    $public = Tweet::factory()->create(['visibility' => 'public']);
    $otherPrivate = Tweet::factory()->create(['user_id' => $other->id, 'visibility' => 'private']);

    $this->actingAs($me);
    $res = $this->getJson(route('tweets.index'));
    $res->assertOk();
    $ids = collect($res->json('data'))->pluck('id');
    expect($ids)->toContain($public->id, $minePrivate->id)->not->toContain($otherPrivate->id);
});

it('creates a tweet for authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $payload = ['content' => 'Hello World', 'visibility' => 'public'];
    $res = $this->postJson(route('tweets.store'), $payload);
    $res->assertCreated();
    $this->assertDatabaseHas('tweets', ['content' => 'Hello World', 'user_id' => $user->id]);
});

it('validates tweet creation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $res = $this->postJson(route('tweets.store'), []);
    $res->assertUnprocessable();
    $res->assertJsonStructure(['message', 'errors' => ['content', 'visibility']]);
});

it('updates only own tweet', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();
    $mine = Tweet::factory()->create(['user_id' => $me->id, 'visibility' => 'public']);
    $others = Tweet::factory()->create(['user_id' => $other->id, 'visibility' => 'public']);

    $this->actingAs($me);
    $this->putJson(route('tweets.update', $mine), ['content' => 'Updated', 'visibility' => 'private'])
        ->assertOk();
    $this->assertDatabaseHas('tweets', ['id' => $mine->id, 'content' => 'Updated', 'visibility' => 'private']);

    $this->putJson(route('tweets.update', $others), ['content' => 'Hack', 'visibility' => 'public'])
        ->assertForbidden();
});

it('deletes only own tweet (soft delete)', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();
    $mine = Tweet::factory()->create(['user_id' => $me->id]);
    $others = Tweet::factory()->create(['user_id' => $other->id]);

    $this->actingAs($me);
    $this->deleteJson(route('tweets.destroy', $mine))->assertOk();
    $this->assertSoftDeleted('tweets', ['id' => $mine->id]);

    $this->deleteJson(route('tweets.destroy', $others))->assertForbidden();
    $this->assertDatabaseHas('tweets', ['id' => $others->id, 'deleted_at' => null]);
});

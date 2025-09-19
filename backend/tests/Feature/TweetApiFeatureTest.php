<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TweetApiFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        Tweet::factory()->count(2)->create(['visibility' => 'public']);
        Tweet::factory()->count(3)->create(['visibility' => 'private']);

        $res = $this->getJson(route('tweets.index'));
        $res->assertUnauthorized();
    }

    public function test_lists_tweets_visible_to_authenticated_user(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $minePrivate = Tweet::factory()->create(['user_id' => $me->id, 'visibility' => 'private']);
        $public = Tweet::factory()->create(['visibility' => 'public']);
        $otherPrivate = Tweet::factory()->create(['user_id' => $other->id, 'visibility' => 'private']);

        $this->actingAs($me);
        $res = $this->getJson(route('tweets.index'));
        $res->assertOk();
        $ids = collect($res->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($public->id));
        $this->assertTrue($ids->contains($minePrivate->id));
        $this->assertFalse($ids->contains($otherPrivate->id));
    }

    public function test_creates_tweet_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = ['content' => 'Hello World', 'visibility' => 'public'];
        $res = $this->postJson(route('tweets.store'), $payload);
        $res->assertCreated();
        $this->assertDatabaseHas('tweets', ['content' => 'Hello World', 'user_id' => $user->id]);
    }

    public function test_validates_tweet_creation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $res = $this->postJson(route('tweets.store'), []);
        $res->assertUnprocessable();
        $res->assertJsonStructure(['message', 'errors' => ['content', 'visibility']]);
    }

    public function test_updates_only_own_tweet(): void
    {
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
    }

    public function test_deletes_only_own_tweet_soft_delete(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $mine = Tweet::factory()->create(['user_id' => $me->id]);
        $others = Tweet::factory()->create(['user_id' => $other->id]);

        $this->actingAs($me);
        $this->deleteJson(route('tweets.destroy', $mine))->assertOk();
        $this->assertSoftDeleted('tweets', ['id' => $mine->id]);

        $this->deleteJson(route('tweets.destroy', $others))->assertForbidden();
        $this->assertDatabaseHas('tweets', ['id' => $others->id, 'deleted_at' => null]);
    }
}

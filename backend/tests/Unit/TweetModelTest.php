<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TweetModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_user_relationship(): void
    {
        $tweet = Tweet::factory()->create();
        $this->assertInstanceOf(User::class, $tweet->user);
    }

    public function test_scope_visible_to_guests_only_public(): void
    {
        Tweet::factory()->count(2)->create(['visibility' => 'public']);
        Tweet::factory()->count(3)->create(['visibility' => 'private']);

        $visible = Tweet::visibleTo(null)->get();
        $this->assertCount(2, $visible);
        $this->assertSame(['public'], $visible->pluck('visibility')->unique()->values()->all());
    }

    public function test_scope_visible_to_user_includes_own_private(): void
    {
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
    }

    public function test_soft_deletes(): void
    {
        $tweet = Tweet::factory()->create();
        $tweet->delete();

        $this->assertNotNull(Tweet::withTrashed()->find($tweet->id));
        $this->assertNull(Tweet::find($tweet->id));
    }
}

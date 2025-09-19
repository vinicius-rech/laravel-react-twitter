<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTweetRequest;
use Symfony\Component\HttpFoundation\Response;

class TweetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $tweets = Tweet::visibleTo($user)
            ->with('user')
            ->latest()
            ->paginate(9);

        return response()->json($tweets, Response::HTTP_OK);
    }

    /**
     * Store a newly created tweet in storage.
     *
     * @param  StoreTweetRequest  $request
     * @return JsonResponse
     */
    public function store(StoreTweetRequest $request): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'message' => 'User not authenticated.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $tweet = Tweet::create([
            'user_id' => $userId,
            'content' => $request->content,
            'visibility' => $request->visibility
        ]);

        return response()->json($tweet->load('user'), Response::HTTP_CREATED);
    }

    /**
     * Update the specified tweet in storage.
     *
     * @param  StoreTweetRequest  $request
     * @param  Tweet  $tweet
     * @return JsonResponse
     */
    public function update(StoreTweetRequest $request, Tweet $tweet): JsonResponse
    {
        if (!$this->isOwner($tweet)) {
            return response()->json(
                ['message' => 'Unauthorized'],
                Response::HTTP_FORBIDDEN
            );
        }

        $tweet->update($request->only(['content', 'visibility']));

        return response()->json($tweet->load('user'), Response::HTTP_OK);
    }

    /**
     * Check if the authenticated user is the owner of the tweet.
     *
     * @param  Tweet  $tweet
     * @return bool
     */
    private function isOwner(Tweet $tweet): bool
    {
        return $tweet->user_id === auth()->id();
    }

    /**
     * Remove the specified tweet from storage.
     *
     * @param  Tweet  $tweet
     * @return JsonResponse
     */
    public function destroy(Tweet $tweet): JsonResponse
    {
        if (!$this->isOwner($tweet)) {
            return response()->json(
                ['message' => 'Unauthorized'],
                Response::HTTP_FORBIDDEN
            );
        }

        $tweet->delete();

        return response()->json(
            ['message' => 'Tweet deleted successfully.'],
            Response::HTTP_OK
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTweetRequest;
use Symfony\Component\HttpFoundation\Response;

class TweetController extends Controller
{
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
}

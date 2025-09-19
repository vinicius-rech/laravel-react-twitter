<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /*
     * Response for invalid credentials
     */
    private const INVALID_CREDENTIALS_RESPONSE = [
        'message' => 'Invalid credentials'
    ];

    /*
     * Response for unauthorized access attempts
     */
    private const UNAUTHORIZED_RESPONSE = [
        'message' => 'Unauthorized'
    ];

    /*
     * Generate a new authentication token for the user
     */
    private function generateToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Build the authentication response data
     */
    private function buildAuthResponseData(User $user, string $auth_token): array
    {
        return [
            'user' => $user,
            'token_type' => 'Bearer',
            'auth_token' => $auth_token
        ];
    }

    /**
     * Handle user login and token generation.
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse(
                self::INVALID_CREDENTIALS_RESPONSE,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $passwordMatches = Hash::check($request->password, $user->password);

        if (!$passwordMatches) {
            return $this->errorResponse(
                self::INVALID_CREDENTIALS_RESPONSE,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $auth_token = $this->generateToken($user);

        if ($auth_token) {
            $data = $this->buildAuthResponseData($user, $auth_token);

            return $this->successResponse($data, Response::HTTP_CREATED);
        }

        return $this->errorResponse(
            self::UNAUTHORIZED_RESPONSE,
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $newUser = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'uuid' => Str::uuid(),
        ];

        $createdUser = User::create($newUser);

        $auth_token = $this->generateToken($createdUser);
        $data = $this->buildAuthResponseData($createdUser, $auth_token);

        return $this->successResponse($data, Response::HTTP_CREATED);
    }

    /**
     * Handle user logout and token revocation.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(
            ['message' => 'Logged out successfully'],
            Response::HTTP_OK
        );
    }

    /**
     * Handle successful responses.
     */
    private function successResponse(
        $data,
        $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json(['data' => $data], $statusCode);
    }

    /**
     * Handle error responses.
     */
    private function errorResponse(
        array $data,
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json($data, $statusCode);
    }
}

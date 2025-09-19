<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Hash;
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
     * Handle user login and token generation.
     */
    public function login(AuthRequest $request)
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

        $token = $this->generateToken($user);

        if ($token) {
            $data = [
                'user' => $user,
                'token_type' => 'Bearer',
                'token' => $token
            ];

            return $this->successResponse($data, Response::HTTP_CREATED);
        }

        return $this->errorResponse(
            self::UNAUTHORIZED_RESPONSE,
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Handle successful responses.
     */
    private function successResponse($data, $statusCode = Response::HTTP_OK)
    {
        return response()->json(['data' => $data], $statusCode);
    }

    /**
     * Handle error responses.
     */
    private function errorResponse(array $data, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json($data, $statusCode);
    }
}

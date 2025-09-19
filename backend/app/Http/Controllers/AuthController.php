<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private const INVALID_CREDENTIALS_RESPONSE = [
        'message' => 'Invalid credentials'
    ];

    private const UNAUTHORIZED_RESPONSE = [
        'message' => 'Unauthorized'
    ];

    public function login(AuthRequest $request)
    {
        dd('asdasd');
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

        $token = $user->createToken('auth_token')->plainTextToken;

        if ($token) {
            return $this->successResponse(
                ['token' => $token],
                Response::HTTP_CREATED
            );
        }

        return $this->errorResponse(
            self::UNAUTHORIZED_RESPONSE,
            Response::HTTP_UNAUTHORIZED
        );
    }

    private function successResponse($data, $statusCode = Response::HTTP_OK)
    {
        return response()->json(['data' => $data], $statusCode);
    }

    private function errorResponse(array $data, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json($data, $statusCode);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user = $request->user();

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account is deactivated.',
            ], 403);
        }

  //      $user->tokens()->delete();

        $token = $user->createToken('api-auth-token', ['*'], now()->addDays(14));

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'you are logged out',
        ]);
    }

    /**
     * Revoke all other tokens except the current one (logout from other devices).
     */
    public function logoutFromOtherDevices(Request $request)
    {
        $user = $request->user();
        $currentTokenId = $user->currentAccessToken()->id;

        // Delete all tokens except the current one
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return response()->json([
            'message' => 'Logged out from other devices.',
        ]);
    }
}

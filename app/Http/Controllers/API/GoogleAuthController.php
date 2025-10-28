<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function callback(Request $request)
{
    $idToken = $request->input('id_token');

    $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
        'id_token' => $idToken
    ]);

    if (!$response->ok()) {
        return response()->json(['success' => false, 'message' => 'Token không hợp lệ'], 401);
    }

    $googleUser = $response->json();

    $user = User::updateOrCreate(
        ['email' => $googleUser['email']],
        [
            'name' => $googleUser['name'] ?? $googleUser['email'],
            'google_id' => $googleUser['sub'],
            'avatar' => $googleUser['picture'] ?? null,
        ]
    );

    $token = $user->createToken('google-login')->plainTextToken;

    return response()->json([
        'success' => true,
        'access_token' => $token,
        'user' => $user,
    ]);
}

}

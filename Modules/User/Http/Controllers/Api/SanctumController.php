<?php

namespace Modules\User\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\User\Models\User;
use Hash;
use Carbon\Carbon;

class SanctumController extends Controller
{
    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
       
        $user = User::where('user_email', $request->email)->first();
  
        if (!$user || !Hash::check($request->password, $user->user_pass)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $expiresAt = Carbon::now()->addMinutes(config('sanctum.expiration', 60));
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);
    }

    public function refreshToken(Request $request){
        return 'refreshToken';
    }
    public function listToken(Request $request){
        return 'listToken';
    }
    public function deleteTokens(Request $request){
        return 'deleteTokens';
    }
    public function deleteTokenById(Request $request){
        return 'deleteTokenById';
    }
}

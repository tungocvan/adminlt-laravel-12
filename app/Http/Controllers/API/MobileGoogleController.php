<?php
  
namespace App\Http\Controllers\API;
  
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Google_Client;
use Illuminate\Support\Facades\Log;
  
class MobileGoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
     public function verify(Request $request)
     {
         \Log::info('Google verify request', $request->all());
     
         $idToken = $request->input('id_token');
         if (!$idToken) {
             return response()->json(['error' => 'Missing id_token'], 400);
         }
         try {
            $idToken = $request->input('id_token');
        
            $client = new \Google_Client(['client_id' => '323384860483-tn2g6j1h9g55bl6gn6q7hrj1cf03ebog.apps.googleusercontent.com']);
            $payload = $client->verifyIdToken($idToken);
        
            if (!$payload) {
                return response()->json(['error' => 'Invalid ID token'], 401);
            }
        
            $email = $payload['email'];
            $name = $payload['name'];
            $googleId = $payload['sub'];
        
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'google_id' => $googleId,
                    'password' => Hash::make(uniqid()),
                    'email_verified_at' => now(),
                ]
            );
        
            $token = $user->createToken('mobile')->plainTextToken;
        
            return response()->json([
                'token' => $token,
                'user' => $user->only(['id', 'name', 'email']),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Google Verify Error: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
         
     }
     




}   

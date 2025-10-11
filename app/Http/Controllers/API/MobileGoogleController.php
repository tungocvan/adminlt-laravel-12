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
             $client = new \Google_Client(['client_id' => '323384860483-tn2g6j1h9g55bl6gn6q7hrj1cf03ebog.apps.googleusercontent.com']);
             $payload = $client->verifyIdToken($idToken);
     
             if (!$payload) {
                 return response()->json(['error' => 'Invalid ID token'], 401);
             }
     
             $user = User::firstOrCreate(
                 ['email' => $payload['email']],
                 [
                     'name' => $payload['name'],
                     'google_id' => $payload['sub'],
                     'password' => Hash::make(uniqid()),
                     'email_verified_at' => now(),
                 ]
             );
     
             $token = $user->createToken('mobile')->plainTextToken;
     
             return response()->json(['token' => $token, 'user' => $user]);
     
         } catch (\Throwable $e) {
             return response()->json(['error' => $e->getMessage()], 500);
         }
     }
     




}   
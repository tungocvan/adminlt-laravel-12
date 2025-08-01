<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
  
class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();


    }
          
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
        
            $user = Socialite::driver('google')->user();
            $finduser = User::where('google_id', $user->id)->first();
            //dd($user);
            if($finduser){
         
                Auth::login($finduser);
                return redirect()->intended('admin');
         
            }else{
                   $newUser = User::updateOrCreate(['email' => $user->email],[
                        'name' => $user->name,
                        'google_id'=> $user->id, 
                        'username' => $user->email,                        
                        'password' => Hash::make('123456'),                        
                    ]);
                    $newUser->assignRole('user');
         
                Auth::login($newUser);
        
                return redirect()->intended('admin');
            }
        
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

}   
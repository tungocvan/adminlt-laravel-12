<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Hash;
class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return view('profile.account',compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('admin.profile')->with('status','Profile updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updatePassword(Request $request): RedirectResponse
    {


        $passwordOld = $request['password-old'];
        $password = $request['password'];
        $confirmPassword = $request['confirm-password'];

        if($password == $confirmPassword ){
            $credentials = [
                'email' => $request->user()->email,
                'password' => $passwordOld,
            ];

            if (Auth::attempt($credentials)) {
                $request->user()->password = bcrypt($password);
                $request->user()->save();
                return redirect()->route('admin.profile')->with('status','password-updated');
            }

            return redirect()->route('admin.profile')->with('status','password-ko-dung');

        }
        return redirect()->route('admin.profile')->with('status','password-ko-khop');
    }
    public function destroy(Request $request): RedirectResponse
    {
        // $request->validateWithBag('userDeletion', [
        //     'password' => ['required', 'current_password'],
        // ]);


        $user = $request->user();
        //dd($user);
        Auth::logout();

        $user->delete();



        $request->session()->invalidate();
        $request->session()->regenerateToken();

        //return Redirect::to('/')->with('status','password-updated');
        return redirect()->route('login')->with('status','account-delete');
    }
}

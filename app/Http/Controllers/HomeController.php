<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $roleName = $request->user()->getRoleNames()[0];
        switch ($roleName) {
            case 'Admin':
                return redirect()->route('admin.index');
            case 'User':
                return redirect()->route('admin.index');

            default:
                 return view('home');
        }


    }
    public function dashboard()
    {
        return view('dashboard');
    }
}

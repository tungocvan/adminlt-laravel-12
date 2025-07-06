<?php

namespace Modules\Mail\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\BirthdayWish;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:mail-list|mail-create|mail-edit|mail-delete', ['only' => ['index','show']]);
         $this->middleware('permission:mail-create', ['only' => ['create','store']]);
         $this->middleware('permission:mail-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:mail-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        
        return view('Mail::mail'); 
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function notification(Request $request)
    {
        $user = User::find(1);
  
        $messages["hi"] = "Hey, Happy Birthday {$user->name}";
        $messages["wish"] = "On behalf of the entire company I wish you a very happy birthday and send you my best wishes for much happiness in your life.";
          
        $user->notify(new BirthdayWish($messages));
        return view('Mail::notifi'); 
    }
}

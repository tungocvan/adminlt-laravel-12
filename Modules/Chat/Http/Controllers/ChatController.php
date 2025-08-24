<?php

namespace Modules\Chat\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:chat-list|chat-create|chat-edit|chat-delete|admin-list', ['only' => ['index','show']]);
         $this->middleware('permission:chat-create', ['only' => ['create','store']]);
         $this->middleware('permission:chat-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:chat-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Chat::chat');
    }


    public function history($userId)
    {
        $authId = Auth::id();

        $messages = Message::with('fromUser:id,name')
            ->where(function($q) use ($authId, $userId) {
                $q->where('from_id', $authId)->where('to_id', $userId);
            })
            ->orWhere(function($q) use ($authId, $userId) {
                $q->where('from_id', $userId)->where('to_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Lưu tin nhắn khi gửi đi
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Chưa đăng nhập'], 403);
        }
        $authId = Auth::id();

        $message = Message::create([
            'from_id' => $authId,
            'to_id'   => $request->to_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
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
}

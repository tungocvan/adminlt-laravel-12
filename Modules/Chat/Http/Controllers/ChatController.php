<?php

namespace Modules\Chat\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\CommunityMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
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

    // lịch sử chat riêng
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

    // lưu tin nhắn riêng
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

    // lưu tin nhắn cộng đồng
    public function storeCommunity(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = CommunityMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json($message);
    }

    // lấy lịch sử chat cộng đồng
    public function historyCommunity()
    {
        $messages = CommunityMessage::with('user:id,name')
            ->orderBy('created_at','asc')
            ->get();

        return response()->json($messages);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Google\Auth\OAuth2;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationController extends Controller
{
    /**
     * Trang test gửi thông báo
     */
    public function index()
    {
        return view('pushNotification');
    }

    /**
     * Lưu Firebase Device Token vào DB
     */
    public function saveToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user) {
            $user->device_token = $request->device_token;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Token saved successfully',
                'token'   => $request->device_token,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not authenticated',
        ], 401);
    }

    /**
     * Gửi thông báo tới tất cả user có device_token
     */
    public function sendNotification(Request $request)
    {
        $tokens = User::whereNotNull('device_token')->pluck('device_token')->all();

        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
        $messaging = $factory->createMessaging();

        $message = CloudMessage::new()
            ->withNotification([
                'title' => $request->title,
                'body' => $request->body,
            ]);

        $messaging->sendMulticast($message, $tokens);

        return back()->with('success', 'Notification sent successfully!');
    }
}

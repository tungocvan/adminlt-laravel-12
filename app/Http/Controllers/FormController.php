<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FormController extends Controller
{
    public function store(Request $request)
    {
     
        // Validate dữ liệu
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'file' => 'nullable|image|max:2048', // 2MB max
            // các field khác...
        ]);

        // Xử lý file nếu có
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            $validated['file_path'] = $path;
        }

        // Lưu vào DB hoặc xử lý logic
        // Model::create($validated); // ví dụ

        return response()->json([
            'status' => 'success',
            'message' => 'Dữ liệu đã được gửi thành công!',
            'data' => $validated
        ]);
    }

    public function createUser(): View
        {
            return view('Settings::create-user');
        }

     public function storeUser(Request $request): RedirectResponse
        {
            $validatedData = $request->validate([
                    'name' => 'required',
                    'username' => 'required|unique:users',
                    'password' => 'required|min:5',
                    'email' => 'required|email|unique:users'
                ], [
                    'name.required' => 'Name field is required.',
                    'username.required' => 'username field is required.',
                    'password.required' => 'Password field is required.',
                    'email.required' => 'Email field is required.',
                    'email.email' => 'Email field must be email address.'
                ]);
            
            $validatedData['password'] = bcrypt($validatedData['password']);
            $user = User::create($validatedData);
                  
            return back()->with('success', 'User created successfully.');
        }      

}

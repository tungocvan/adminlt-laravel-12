<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = tnv_getUsers($request->all());
        return response()->json($users);
    }
     
     public function show($id)
     {
      
        $user = User::find($id);
 
         if (!$user) {
             return response()->json([
                 'message' => 'User not found'
             ], 404);
         }
 
         return response()->json($user);
     }
 
     // Xóa user theo id
     public function destroy($id)
     {
         $user = User::find($id);
 
         if (!$user) {
             return response()->json([
                 'message' => 'User not found'
             ], 404);
         }
 
         $user->delete();
 
         return response()->json([
             'message' => 'User deleted successfully'
         ]);
     }
    
     public function update(Request $request, $id)
    {
        $result = tnv_update_user($id, $request->all());
        $status = $result['status'] ? 200 : 400;
        return response()->json($result, $status);
    }

}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\TnvUserHelper;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = TnvUserHelper::getUsers($request->all());
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
 
        
    
    public function update(Request $request, $id)
    {
       
        $result = TnvUserHelper::updateUser($id, $request->all());
        $status = $result['status'] ? 200 : 400;
        return response()->json($result, $status);
    }

    public function destroy($id)
    {
        $result = TnvUserHelper::deleteUsers($id);
        return response()->json($result);
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []); // ví dụ: [1, 2, 3]
        
        $result = TnvUserHelper::deleteUsers($ids);
        return response()->json($result);
    }

}

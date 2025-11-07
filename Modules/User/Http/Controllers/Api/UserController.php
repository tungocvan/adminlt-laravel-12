<?php

namespace Modules\User\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\TnvUserHelper;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $perPage = $request->input('per_page', 20);
    
        $result = User::filter($params, $perPage);
    
        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'meta' => $result['meta'],
            'params' => $params
        ]);
    }
    

    public function show(Request $request, $identifier)
    {
        $params = [];

        // Nếu là số => coi là ID
        if (is_numeric($identifier)) {
            $params['id'] = (int) $identifier;
        } else {
            // Ngược lại coi là email
            $params['email'] = $identifier;
        }

        $user = TnvUserHelper::getUsers($params);

        if ($user->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
        $data = $user->first();
        $data['shipping_info']=$data->getOption('shipping_info', []);
        return response()->json([
            'success' => true,
            'data' => $data,

        ]);
    }
 
        
    
    public function update(Request $request, $id)
    {
       
        $data = $request->all();
        $result = TnvUserHelper::updateUser((int)$id, $data);
        return response()->json($result);
        // $status = $result['status'] ? 200 : 400;
        // return response()->json($result, $status);
       
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

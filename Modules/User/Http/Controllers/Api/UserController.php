<?php

namespace Modules\User\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
            'params' => $params,
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
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User not found',
                ],
                404,
            );
        }
        $data = $user->first();
        $data['shipping_info'] = $data->getOption('shipping_info', []);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $result = TnvUserHelper::updateUser((int) $id, $data);
        return response()->json($result);
    }
    public function updateApp(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'profile' => 'nullable|array',
            'shipping_info' => 'nullable|array',
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Người dùng không tồn tại.',
                ],
                404,
            );
        }

        // Cập nhật thông tin cơ bản
        if (!is_null($request->name)) {
            $user->name = $request->name;
        }

        if (!is_null($request->password)) {
            $user->password = Hash::make($request->password);
        }

        // Chỉ save nếu có thay đổi core fields
        if ($user->isDirty(['name', 'password'])) {
            $user->save();
        }

        // Cập nhật option
        if (!is_null($request->profile)) {
            $user->setOption('profile', $request->profile);
        }

        if (!is_null($request->shipping_info)) {
            $user->setOption('shipping_info', $request->shipping_info);
        }

        return [
            'status' => 'success',
            'data' => $user,
            'message' => 'Cập nhật người dùng thành công!',
        ];
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

<?php
namespace App\Helpers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class TnvUserHelper
{
    
    public static function getUsers(array $params = [])
    {
        $query = User::query();

        // Lọc theo ID
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $query->whereIn('id', $params['id']);
            } else {
                $query->where('id', $params['id']);
            }
        }

        // Lọc theo is_admin
        if (isset($params['is_admin'])) {
            $query->where('is_admin', $params['is_admin']);
        }

        // Tìm kiếm theo từ khóa
        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('username', 'like', "%{$keyword}%");
            });
        }

        // Sắp xếp
        $sortBy = $params['sort_by'] ?? 'id';
        $sortOrder = $params['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang mặc định
        $perPage = $params['per_page'] ?? 20;

        return $query->paginate($perPage);
    }

    public static function updateUser(int $id, array $data)
    {
        //return $id;
       
        $user = User::find($id);
        
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found'
            ];
        }

        // Xác định các trường có thể cập nhật
        $allowedFields = [
            'name',
            'email',
            'username',
            'is_admin',
            'birthdate',
            'password'
        ];
        
        $data = array_intersect_key($data, array_flip($allowedFields));
        
        // Tạo validator để kiểm tra trùng email/username
        $validator = Validator::make($data, [
            'email' => [
                'sometimes', 'email',
                Rule::unique('users', 'email')->ignore($id)
            ],
            'username' => [
                'sometimes', 'string',
                Rule::unique('users', 'username')->ignore($id)
            ],
            'password' => ['sometimes', 'string', 'min:6'],
        ]);

        
        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }
        
        // Mã hóa password nếu có
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        
        $user->update($data);

        return [
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ];
    }

    public static function deleteUsers(array|string|int $params = [])
    {
        // Nếu truyền vào là 1 ID đơn (int hoặc string)
        if (!is_array($params)) {
            $user = User::find($params);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => "User với ID {$params} không tồn tại."
                ];
            }

            if($user['is_admin'] == -1){
                return [
                    'success' => false,
                    'message' => "User với ID {$params} là Admin."
                ];
            }

            $user->delete();

            return [
                'success' => true,
                'message' => "Đã xóa user ID {$params} thành công."
            ];
        }

        // Nếu là mảng chứa danh sách ID
        if (empty($params)) {
            return [
                'success' => false,
                'message' => 'Không có ID nào được truyền vào để xóa.'
            ];
        }


        // Chỉ giữ lại các ID tồn tại trong DB
        $existingIds = User::whereIn('id', $params)->pluck('id')->toArray();

        if (empty($existingIds)) {
            return [
                'success' => false,
                'message' => 'Không có user hợp lệ để xóa.'
            ];
        }
        
        // Xóa hàng loạt
        $warning = ""; $userAdmin=""; $count=0;
        $message = "";
        foreach ($existingIds as $id) {
            $user = User::find($id);
            if ($user && $user['is_admin'] !=-1) {
                $user->forceDelete();
                $count = $count +1;
            }else{          
                $userAdmin = $userAdmin." - ".$user['email'];      
                $warning = "Không thể xóa User Admin $userAdmin";
            }
        }
        
        if($count > 0){
            $message = "Đã xóa thành công $count user.";
        }


        return [
            'success' => true,
            'message' => $message,
            'warning' => $warning,
            'deleted_ids' => $existingIds
        ];
    }


    public static function register(array $user)
    {
        try {
            // --- VALIDATION ---
            $validator = Validator::make($user, [
                'name'                  => 'required|string|max:100',
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required|string|min:6',
                'c_password' => 'nullable|string|min:6|same:password',
                'is_admin'              => 'nullable|integer',
                'verified'              => 'nullable|string',
                'role_name'              => 'nullable|string',
            ], [
                'email.unique' => 'Email đã được sử dụng.',
                'c_password.same' => 'Mật khẩu nhập lại không khớp.',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            ]);
            

            if ($validator->fails()) {
                return [
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ];
            }

            DB::beginTransaction();

            // --- Xử lý username ---
            if (empty($user['username'])) {
                $baseUsername = Str::before($user['email'], '@');
                $username = $baseUsername;
                $count = 0;
                while (User::where('username', $username)->exists()) {
                    $count++;
                    $username = $baseUsername . rand(100, 999);
                    if ($count > 5) break; // tránh vòng lặp vô hạn
                }
                $user['username'] = $username;
            }

            
            // --- Xử lý is_admin ---
            if (empty($user['is_admin']) && empty($user['role_name'])) {
                $roleName = 'User';
                $user['is_admin'] = 0;
            }else{
                if (!empty($user['role_name'])) {
                    // nếu có is_admin -> kiểm tra Role theo id
                    $role = \Spatie\Permission\Models\Role::where('name', $user['role_name'])->first();
                    if ($role) {
                        $roleName = $role->name; // nếu tìm thấy thì gán theo name
                        $user['is_admin'] = $role->id;
                    } else {
                        $roleName = 'User'; // fallback
                        $user['is_admin'] = 0;
                    }
                }else{
                    $roleName = 'User';
                    $user['is_admin'] = 0;
                }

            }

            
            // --- Xử lý email_verified_at ---
            $emailVerifiedAt = !empty($user['verified']) ? now():null;

            // --- Tạo Role (nếu chưa có) ---
            $role = Role::firstOrCreate(['name' => $roleName]);

            // --- Tạo User ---
            $newUser = User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'username' => $user['username'],
                'password' => Hash::make($user['password']),
                'email_verified_at' => $emailVerifiedAt,
                'is_admin' => $user['is_admin'],
            ]);

            // --- Gán Role ---
            $newUser->assignRole($role);

            // --- Tạo Token (nếu có Sanctum hoặc Passport) ---
            $token = method_exists($newUser, 'createToken')
                ? $newUser->createToken('api_token')->plainTextToken
                : null;

            DB::commit();

            // --- Trả về kết quả ---
            return [
                'status' => 'success',
                'message' => 'Đăng ký thành công!',
                'token' => $token,
                'data' => [
                    'id' => $newUser->id,
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'username' => $newUser->username,
                    'is_admin' => $newUser->is_admin,
                    'roles' => $newUser->getRoleNames(),
                ],
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Đăng ký thất bại: ' . $e->getMessage(),
            ];
        }
    }

    public static function login(array $user)
    {
        // --- 1️⃣ Validate dữ liệu đầu vào ---
        $validator = Validator::make($user, [
            'email'    => 'required', // có thể là email hoặc username
            'password' => 'required|string|min:6',
        ], [
            'email.required'    => 'Vui lòng nhập email hoặc tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return [
                'status'  => 'error',
                'message' => 'Dữ liệu không hợp lệ.',
                'errors'  => $validator->errors(),
            ];
        }

        // --- 2️⃣ Xác định kiểu đăng nhập: email hay username ---
        $loginField = filter_var($user['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $user['email'],
            'password'  => $user['password'],
        ];

        // --- 3️⃣ Thực hiện đăng nhập ---
        if (!Auth::attempt($credentials)) {
            return [
                'status'  => 'error',
                'message' => 'Thông tin đăng nhập không chính xác.',
            ];
        }

        // --- 4️⃣ Lấy thông tin user ---
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        // --- 5️⃣ Tạo token (cho API sử dụng Sanctum hoặc Passport) ---
        $token = $authUser->createToken('api_token')->plainTextToken;

        return [
            'status' => 'success',
            'message' => 'Đăng nhập thành công!',
            'data' => [
                'user'  => $authUser,
                'token' => $token,
            ],
        ];
    }

}

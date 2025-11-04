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
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TnvHelper;
use Illuminate\Support\Carbon;

class TnvUserHelper
{
    
    /**
     * Hàm lấy danh sách user có thể tái sử dụng ở bất kỳ đâu
     *
     * Hỗ trợ:
     *  - Lọc theo id, is_admin, referral_code, email
     *  - Tìm kiếm theo keyword (name, email, username)
     *  - Sort, paginate, get, first, count
     *  - Load quan hệ, chọn cột cụ thể
     */
    public static function getUsers(array $params = [])
    {
        $query = User::query();

        // 🔹 Làm sạch params: bỏ null hoặc rỗng
        $params = array_filter($params, fn($v) => $v !== null && $v !== '');

        /**
         * =====================
         * Hàm parse date chuẩn hóa
         * =====================
         */
        $parseDate = function($date) {
            // dd/mm/yyyy -> yyyy-mm-dd
            if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $date, $m)) {
                return "{$m[3]}-{$m[2]}-{$m[1]}";
            }
            // yyyy-mm-dd
            if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $date)) {
                return $date;
            }
            return null;
        };

        /**
         * =====================
         * FILTER THÔNG THƯỜNG
         * =====================
         */
        if (!empty($params['id'])) {
            is_array($params['id'])
                ? $query->whereIn('id', $params['id'])
                : $query->where('id', $params['id']);
        }

        if (!empty($params['email'])) {
            is_array($params['email'])
                ? $query->whereIn('email', $params['email'])
                : $query->where('email', $params['email']);
        }

        if (isset($params['is_admin'])) {
            $query->where('is_admin', $params['is_admin']);
        }

        if (isset($params['referral_code'])) {
            $query->where('referral_code', $params['referral_code']);
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        /**
         * =====================
         * FILTER KEYWORD SEARCH
         * =====================
         */
        if (!empty($params['search'])) {
            $query->where(function($q) use ($params) {
                $q->where('name', 'like', '%' . $params['search'] . '%')
                ->orWhere('email', 'like', '%' . $params['search'] . '%');
            });
        }

        /**
         * =====================
         * FILTER DATE FIELD (DATE/DATETIME)
         * =====================
         */
        $dateFields = ['birthdate', 'created_at', 'updated_at'];

        foreach ($dateFields as $field) {
            // exact match
            if (!empty($params[$field])) {
                $date = $parseDate($params[$field]);
                if ($date) {
                    $query->whereDate($field, $date);
                }
            }

            // from
            if (!empty($params[$field . '_from'])) {
                $date = $parseDate($params[$field . '_from']);
                if ($date) {
                    $query->whereDate($field, '>=', $date);
                }
            }

            // to
            if (!empty($params[$field . '_to'])) {
                $date = $parseDate($params[$field . '_to']);
                if ($date) {
                    $query->whereDate($field, '<=', $date);
                }
            }
        }

        /**
         * =====================
         * GỌI QUERY CHUNG
         * =====================
         */
        return TnvHelper::BaseQueryService($query, $params);
    }

    
    public static function updateUser(int $userId, array $data)
    {
        try {
            // --- TÌM USER ---
            $user = User::find($userId);
            if (!$user) {
                return [
                    'status'  => 'error',
                    'message' => 'Không tìm thấy người dùng.',
                ];
            }
            if (!empty($data['birthdate'])) {
                $parsed = TnvHelper::parseDate($data['birthdate']);
                if (!$parsed) {
                    return [
                        'status'  => 'error',
                        'message' => "Ngày sinh '{$data['birthdate']}' không hợp lệ. Dùng dd/mm/yyyy hoặc yyyy-mm-dd",
                    ];
                }
                $data['birthdate'] = $parsed; // sẵn sàng save vào DB
            }
           
            // --- VALIDATION ---
            $validator = Validator::make($data, [
                'email'         => 'nullable|email|unique:users,email,' . $user->id,
                'username'      => 'nullable|string|max:100|unique:users,username,' . $user->id,
                'password'      => 'nullable|string|min:6',
                'c_password'    => 'nullable|string|same:password',
                'name'          => 'nullable|string|max:100',
                'role_name'     => 'nullable|string',
                'verified'      => 'nullable|boolean',
                'is_admin'      => 'nullable|integer',
                'referral_code' => 'nullable|string|max:50',
                'birthdate'     => 'nullable|date',
            ], [
                'email.unique'    => 'Email đã được sử dụng.',
                'username.unique' => 'Username đã tồn tại.',
                'c_password.same' => 'Mật khẩu nhập lại không khớp.',
            ]);

            if ($validator->fails()) {
                return [
                    'status'  => 'error',
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ];
            }

            DB::beginTransaction();

            // --- UPDATE FIELDS ---
            // Chỉ cần lặp qua những field có trong $data
            $updatableFields = [
                'name', 'email', 'username', 'birthdate',
                'referral_code', 'is_admin',
            ];

            foreach ($updatableFields as $field) {
                if (array_key_exists($field, $data)) {
                    $user->$field = $data[$field]; // trait AutoParseDates tự parse Y-m-d
                }
            }

            // --- PASSWORD ---
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            // --- VERIFIED FLAG ---
            if (array_key_exists('verified', $data)) {
                $user->email_verified_at = $data['verified'] ? now() : null;
            }

            // --- ROLE HANDLING ---
            if (!empty($data['role_name'])) {
                $roleName = $data['role_name'];
                $role = Role::firstOrCreate(['name' => $roleName]);
                $user->syncRoles([$role]);
            }

            // --- LƯU ---
            $user->save();
            DB::commit();

            return [
                'status'  => 'success',
                'message' => 'Cập nhật người dùng thành công!',
                'data'    => [
                    'id'            => $user->id,
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'username'      => $user->username,
                    'birthdate'     => $user->birthdate,
                    'referral_code' => $user->referral_code,
                    'is_admin'      => $user->is_admin,
                    'verified'      => !empty($user->email_verified_at),
                    'roles'         => $user->getRoleNames(),
                    'updated_at'    => $user->updated_at,
                ],
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'status'  => 'error',
                'message' => 'Cập nhật thất bại: ' . $e->getMessage(),
            ];
        }
    }


    

    public static function updateAllUser(array $userIds, array $data): array
    {
        if (empty($userIds)) {
            return [
                'status' => 'error',
                'message' => 'Không có user nào được chọn.',
            ];
        }

        try {
            DB::beginTransaction();

            // 🔧 Chuẩn hóa dữ liệu
            if (isset($data['birthdate']) && !empty($data['birthdate'])) {
                try {
                    $data['birthdate'] = Carbon::parse($data['birthdate'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    return [
                        'status' => 'error',
                        'message' => "Ngày sinh '{$data['birthdate']}' không hợp lệ.",
                    ];
                }
            }

            if (isset($data['password']) && !empty($data['password'])) {
                if (!Hash::needsRehash($data['password'])) {
                    $data['password'] = bcrypt($data['password']);
                }
            }

            if (isset($data['verified'])) {
                $data['verified'] = (bool) $data['verified'];
            }

            if (isset($data['is_admin'])) {
                $data['is_admin'] = (int) $data['is_admin'];
            }

            // Tách role_name ra xử lý riêng
            $roleName = $data['role_name'] ?? null;
            unset($data['role_name']);

            // 🔁 Thực hiện cập nhật
            $updated = User::whereIn('id', $userIds)->update($data);

            // Nếu có role_name, sync roles cho từng user
            if ($roleName) {
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    $user->syncRoles([$roleName]);
                }
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => "Đã cập nhật {$updated} người dùng thành công.",
                'count' => $updated,
                'data' => array_merge($data, ['role_name' => $roleName]),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Lỗi khi cập nhật hàng loạt: ' . $e->getMessage(),
            ];
        }
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
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|string|min:6',
                'c_password'    => 'nullable|string|min:6|same:password',
                'name'          => 'nullable|string|max:100',
                'username'      => 'nullable|string|max:100',
                'birthdate'     => 'nullable|date',
                'role_name'     => 'nullable|string',
                'verified'      => 'nullable|boolean',
                'is_admin'      => 'nullable|integer',
                'referral_code' => 'nullable|string|max:50',
            ], [
                'email.unique'   => 'Email đã được sử dụng.',
                'c_password.same'=> 'Mật khẩu nhập lại không khớp.',
                'password.min'   => 'Mật khẩu phải có ít nhất 6 ký tự.',
            ]);
    
            if ($validator->fails()) {
                return [
                    'status'  => 'error',
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ];
            }
    
            DB::beginTransaction();
    
            // --- AUTO-GENERATE USERNAME ---
            if (empty($user['username'])) {
                $baseUsername = Str::before($user['email'], '@');
                $username = $baseUsername;
                $count = 0;
    
                while (User::where('username', $username)->exists()) {
                    $count++;
                    $username = $baseUsername . rand(100, 999);
                    if ($count > 5) break;
                }
    
                $user['username'] = $username;
            }
    
            // --- DEFAULT NAME = USERNAME (nếu chưa có hoặc rỗng) ---
            if (empty($user['name'])) {
                $user['name'] = $user['username'];
            }
    
            // --- ROLE HANDLING ---
            $roleName = $user['role_name'] ?? 'User';
            $role = Role::where('name', $roleName)->first();
    
            // Nếu role_name không tồn tại -> fallback "User"
            if (!$role) {
                $roleName = 'User';
                $role = Role::where('name', $roleName)->first();
            }
    
            // Nếu vẫn chưa có role "User", tự tạo
            if (!$role) {
                $role = Role::firstOrCreate(['name' => 'User']);
            }
    
            // --- IS_ADMIN DEFAULT ---
            $user['is_admin'] = $user['is_admin'] ?? 0;
    
            // --- VERIFIED FLAG ---
            $emailVerifiedAt = !empty($user['verified']) ? now() : null;
    
            // --- CREATE USER ---
            $newUser = User::create([
                'name'              => $user['name'],
                'email'             => $user['email'],
                'username'          => $user['username'],
                'password'          => Hash::make($user['password']),
                'email_verified_at' => $emailVerifiedAt,
                'is_admin'          => $user['is_admin'],
                'birthdate'         => $user['birthdate'] ?? null,
                'referral_code'     => $user['referral_code'] ?? null,
            ]);
    
            // --- ASSIGN ROLE ---
            $newUser->assignRole($role);
    
            // --- CREATE API TOKEN (Sanctum/Passport optional) ---
            $token = method_exists($newUser, 'createToken')
                ? $newUser->createToken('api_token')->plainTextToken
                : null;
    
            DB::commit();
    
            // --- RESPONSE ---
            return [
                'status'  => 'success',
                'message' => 'Đăng ký thành công!',
                'token'   => $token,
                'data'    => [
                    'id'        => $newUser->id,
                    'name'      => $newUser->name,
                    'email'     => $newUser->email,
                    'username'  => $newUser->username,
                    'is_admin'  => $newUser->is_admin,
                    'roles'     => $newUser->getRoleNames(),
                ],
            ];
    
        } catch (\Throwable $e) {
            DB::rollBack();
    
            return [
                'status'  => 'error',
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

    public static function importUsersFromExcel($file)
    {
        try {
            // --- Kiểm tra file tồn tại ---
            if (!$file || !$file->isValid()) {
                return [
                    'status' => false,
                    'message' => 'File tải lên không hợp lệ.',
                ];
            }

            // --- Đọc nội dung Excel ---
            $rows = Excel::toArray([], $file)[0]; // sheet đầu tiên

            if (count($rows) <= 1) {
                return [
                    'status' => false,
                    'message' => 'File Excel trống hoặc không có dữ liệu.',
                ];
            }

            // --- Giả sử dòng đầu là header ---
            $header = array_map('trim', $rows[0]);
            $imported = [];
            $errors = [];
            $count = 0;

            DB::beginTransaction();

            foreach (array_slice($rows, 1) as $index => $row) {
                if (count(array_filter($row)) === 0) continue; // bỏ dòng trống

                // Tạo mảng dữ liệu theo header
                $data = array_combine($header, $row);

                // Chuẩn hóa key
                $data = array_change_key_case($data, CASE_LOWER);

                // --- VALIDATE ---
                
                foreach ($data as $key => $value) {
                    if (is_numeric($value)) {
                        $data[$key] = (string) $value;
                    }
                }
                
                $validator = Validator::make($data, [
                    'name' => 'required|string|max:100',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'nullable|string|min:6',
                    'role_name' => 'nullable|string',
                    'is_admin' => 'nullable|integer',
                    'verified' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $index + 2,
                        'error' => $validator->errors()->first(),
                        'data' => $data,
                    ];
                    continue;
                }

                // --- Xử lý username ---
                if (empty($data['username'])) {
                    $baseUsername = Str::before($data['email'], '@');
                    $username = $baseUsername;
                    $countTry = 0;
                    while (User::where('username', $username)->exists()) {
                        $username = $baseUsername . rand(100, 999);
                        $countTry++;
                        if ($countTry > 5) break;
                    }
                    $data['username'] = $username;
                }

                // --- Role ---
                $roleName = !empty($data['role_name']) ? $data['role_name'] : 'User';
                $role = Role::firstOrCreate(['name' => $roleName]);

                // --- Mã hóa mật khẩu ---
                $password = !empty($data['password']) ? Hash::make($data['password']) : Hash::make('123456');

                // --- Email verified ---
                $emailVerifiedAt = !empty($data['verified']) ? now() : null;

                // --- Tạo user ---
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'password' => $password,
                    'is_admin' => $data['is_admin'] ?? 0,
                    'email_verified_at' => $emailVerifiedAt,
                ]);

                $user->assignRole($role);
                $imported[] = $user;
                $count++;
            }

            DB::commit();

            return [
                'status' => true,
                'message' => "Đã import thành công {$count} user.",
                'imported_count' => $count,
                'errors' => $errors,
                'data' => $imported,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Import Users Failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Lỗi khi import: ' . $e->getMessage(),
            ];
        }
    }

    public static function exportUsersToExcel(
        array $userIds = [],
        array $fields = [],
        string $title = 'BÁO CÁO DANH SÁCH NGƯỜI DÙNG',
        string $footer = 'NGƯỜI LẬP BẢNG'
    ) {
        try {
            $query = User::query()->with('roles');
    
            if (!empty($userIds)) {
                $query->whereIn('id', $userIds);
            }
    
            $users = $query->get();
    
            if ($users->isEmpty()) {
                return [
                    'status' => false,
                    'message' => 'Không có user nào để xuất.',
                ];
            }
    
            // ====== Cấu hình field mặc định ======
            $defaultFields = [
                'id',
                'name',
                'email',
                'username',
                'is_admin',
                'roles',
                'email_verified_at',
                'created_at',
            ];
    
            // ====== Mapping sang tên tiếng Việt ======
            $fieldLabels = [
                'id' => 'ID',
                'name' => 'Họ và tên',
                'email' => 'Email',
                'username' => 'Tên đăng nhập',
                'is_admin' => 'Phân quyền',
                'roles' => 'Vai trò',
                'email_verified_at' => 'Xác minh Email',
                'created_at' => 'Ngày tạo',
                'birthdate' => 'Ngày sinh',
            ];
    
            $exportFields = !empty($fields) ? $fields : $defaultFields;
    
            // ====== Tạo file Excel ======
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Users Export');
    
            // ====== 1️⃣ Tiêu đề ======
            $titleText = $title . ' - ' . now()->format('d/m/Y');
            $sheet->mergeCells('A1:' . chr(64 + count($exportFields)) . '1');
            $sheet->setCellValue('A1', $titleText);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
            // ====== 2️⃣ Header (bắt đầu dòng 3) ======
            $headerRow = 3;
            $header = [];
            foreach ($exportFields as $field) {
                $header[] = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            }
            $sheet->fromArray([$header], null, 'A' . $headerRow);
    
            $headerRange = 'A' . $headerRow . ':' . chr(64 + count($exportFields)) . $headerRow;
            $sheet->getStyle($headerRange)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEFEFEF'],
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
    
            // ====== 3️⃣ Ghi dữ liệu (bắt đầu từ dòng 4) ======
            $row = $headerRow + 1;
            foreach ($users as $user) {
                $col = 1;
                foreach ($exportFields as $field) {
                    $value = match ($field) {
                        'roles' => $user->getRoleNames()->implode(', '),
                        'is_admin' => $user->is_admin == -1 ? 'Super Admin' : ($user->is_admin == 1 ? 'Admin' : 'User'),
                        'email_verified_at', 'created_at' => $user->$field ? $user->$field->format('d/m/Y H:i') : '',
                        default => $user->$field ?? '',
                    };
                    $sheet->setCellValueByColumnAndRow($col, $row, $value);
                    $col++;
                }
                $row++;
            }
    
            // ====== 4️⃣ Căn giữa & viền bảng ======
            $dataRange = 'A' . $headerRow . ':' . chr(64 + count($exportFields)) . ($row - 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            foreach (range(1, count($exportFields)) as $colIndex) {
                $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
            }
    
            // ====== 5️⃣ Footer chuyên nghiệp ======
            $totalCols = count($exportFields);
            $mergeCols = min(3, $totalCols);
            $startColIndex = $totalCols - $mergeCols + 1;
            $endColIndex = $totalCols;
    
            // Helper chuyển số sang chữ cột
            $colLetter = function ($index) {
                $letter = '';
                while ($index > 0) {
                    $mod = ($index - 1) % 26;
                    $letter = chr(65 + $mod) . $letter;
                    $index = intdiv($index - 1, 26);
                }
                return $letter;
            };
    
            $startCol = $colLetter($startColIndex);
            $endCol = $colLetter($endColIndex);
            $footerRow = $sheet->getHighestRow() + 2;
    
            $sheet->mergeCells("{$startCol}{$footerRow}:{$endCol}{$footerRow}");
            $sheet->setCellValue("{$startCol}{$footerRow}", $footer);
    
            $footerStyle = $sheet->getStyle("{$startCol}{$footerRow}");
            $footerStyle->getFont()->setBold(true);
            $footerStyle->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            // $footerStyle->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($footerRow)->setRowHeight(25);
    
            // ====== 6️⃣ Lưu file ======
            $timestamp = now()->format('Ymd_His');
            $fileName = "users_export_{$timestamp}.xlsx";
            $exportPath = storage_path("app/exports/{$fileName}");
    
            if (!is_dir(dirname($exportPath))) {
                mkdir(dirname($exportPath), 0777, true);
            }
    
            $writer = new Xlsx($spreadsheet);
            $writer->save($exportPath);
    
            return [
                'status' => true,
                'message' => 'Xuất file thành công.',
                'path' => $exportPath,
                'count' => $users->count(),
                'fields' => $exportFields,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'message' => 'Lỗi khi xuất file: ' . $e->getMessage(),
            ];
        }
    }

}

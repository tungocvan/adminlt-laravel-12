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

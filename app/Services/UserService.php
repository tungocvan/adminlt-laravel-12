<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\UsersExport;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class UserService
{
    // -----------------------------
    // 1️⃣ CRUD CƠ BẢN
    // -----------------------------
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
        return self::BaseQueryService($query, $params);
    }

    public static function list(array $filters = [], int $perPage = 10)
    {
        $query = User::query();

        if (!empty($filters['keyword'])) {
            $query->keyword($filters['keyword']);
        }

        if (!empty($filters['sortField'])) {
            $query->orderBy($filters['sortField'], $filters['sortDirection'] ?? 'asc');
        }

        $users = $query->paginate($perPage);

        return [
            'status' => 'success',
            'data'   => $users,
        ];
    }

    public static function create(array $data, array $profile = [], array $shipping = [])
    {
        try {
            $data['password'] = Hash::make($data['password']);

            DB::beginTransaction();
            $user = User::create($data);

            // Gán role mặc định
            $roleName = $data['role_name'] ?? 'User';
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->assignRole($role->name);
            }

            // Lưu profile & shipping
            $user->setOption('profile', $profile);
            $user->setOption('shipping_info', $shipping);

            DB::commit();

            return [
                'status'  => 'success',
                'message' => 'Tạo người dùng thành công!',
                'data'    => $user,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status'  => 'error',
                'message' => 'Lỗi tạo người dùng: ' . $e->getMessage(),
            ];
        }
    }

    public static function update(int $id, array $data, ?array $profile = null, ?array $shipping = null)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return ['status' => 'error', 'message' => 'Không tìm thấy người dùng!'];
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            // Cập nhật role nếu có
            if (!empty($data['role_id'])) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $user->syncRoles([$role->name]);
                }
            }

            // Cập nhật option
            if ($profile !== null) {
                $user->setOption('profile', $profile);
            }

            if ($shipping !== null) {
                $user->setOption('shipping_info', $shipping);
            }

            return [
                'status'  => 'success',
                'message' => 'Cập nhật người dùng thành công!',
                'data'    => $user,
            ];
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Lỗi cập nhật người dùng: ' . $e->getMessage(),
            ];
        }
    }

    public static function delete(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return ['status' => 'error', 'message' => 'Không tìm thấy người dùng.'];
        }

        if ($user->is_admin == -1) {
            return ['status' => 'error', 'message' => 'Không thể xóa tài khoản admin.'];
        }

        try {
            DB::beginTransaction();

            // Xóa options kèm theo
            $user->options()->delete();

            $user->delete();

            DB::commit();
            return ['status' => 'success', 'message' => 'Đã xóa người dùng thành công!'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => 'Lỗi xóa người dùng: ' . $e->getMessage()];
        }
    }

    public static function deleteMany(array $ids)
    {
        try {
            DB::beginTransaction();

            $users = User::whereIn('id', $ids)->get();
            foreach ($users as $user) {
                if ($user->is_admin != -1) {
                    $user->options()->delete();
                    $user->delete();
                }
            }

            DB::commit();

            return ['status' => 'success', 'message' => 'Đã xóa các người dùng được chọn!'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => 'Lỗi xóa hàng loạt: ' . $e->getMessage()];
        }
    }

    // -----------------------------
    // 2️⃣ ROLE & QUYỀN HẠN
    // -----------------------------

    public static function assignRole(int $userId, string|int $role)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Không tìm thấy người dùng.'];
        }

        $roleModel = is_numeric($role) ? Role::find($role) : Role::where('name', $role)->first();
        if (!$roleModel) {
            return ['status' => 'error', 'message' => 'Vai trò không tồn tại.'];
        }

        $user->syncRoles([$roleModel->name]);

        return ['status' => 'success', 'message' => 'Cập nhật vai trò thành công.'];
    }

    public static function assignRoleMany(array $userIds, string|int $role)
    {
        $roleModel = is_numeric($role) ? Role::find($role) : Role::where('name', $role)->first();
        if (!$roleModel) {
            return ['status' => 'error', 'message' => 'Vai trò không tồn tại.'];
        }

        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $user) {
            $user->syncRoles([$roleModel->name]);
        }

        return ['status' => 'success', 'message' => 'Cập nhật vai trò cho nhiều người dùng thành công!'];
    }

    public static function updateUserRoleAndReferral(array $userIds, ?int $roleId = null, ?string $referral = null)
    {
        $role = $roleId ? Role::find($roleId) : null;
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            if ($role) {
                $user->syncRoles([$role->name]);
            }

            if (!empty($referral)) {
                $user->referral_code = $referral;
                $user->save();
            }
        }

        return ['status' => 'success', 'message' => 'Cập nhật vai trò và referral thành công!'];
    }

    public static function getRolesList()
    {
        return Role::orderBy('name')->pluck('name', 'id')->toArray();
    }

    // -----------------------------
    // 3️⃣ XÁC THỰC EMAIL
    // -----------------------------

    public static function toggleApproval(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Không tìm thấy người dùng.'];
        }

        $user->email_verified_at = $user->email_verified_at ? null : now();
        $user->save();

        $msg = $user->email_verified_at
            ? 'Người dùng đã được duyệt.'
            : 'Đã gỡ xác thực người dùng.';

        return ['status' => 'success', 'message' => $msg];
    }

    // -----------------------------
    // 4️⃣ EXPORT / PRINT
    // -----------------------------

    public static function exportExcel(array $userIds)
    {
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.xlsx";

        return Excel::download(new UsersExport($userIds), $fileName);
    }

    public static function exportPdf(array $userIds)
    {
        $users = User::whereIn('id', $userIds)->get();
        $pdf = Pdf::loadView('exports.users-pdf', compact('users'));
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.pdf";

        return response()->streamDownload(fn() => print $pdf->output(), $fileName);
    }

    public static function generatePrintHtml(array $userIds)
    {
        $users = User::whereIn('id', $userIds)->get();
        $html = View::make('exports.print-users', compact('users'))->render();

        return [
            'status' => 'success',
            'data'   => [
                'html' => $html,
                'base64' => base64_encode(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8')),
            ],
        ];
    }

    // -----------------------------
    // 5️⃣ PROFILE & SHIPPING
    // -----------------------------
    public static function updateGmail(int $userId, array $info_gmail)
    {
        $user = User::find($userId);

        if (!$user) {
            return [
                'status' => 'error',
                'message' => '❌ Không tìm thấy người dùng.'
            ];
        }

        // Email đã được xác thực từ user
        $email = $user->email;
        $name =  $user->name;
        // Chuẩn bị thông tin Gmail
        $gmailInfo = [
            'email' => $email,
            'password' => $info_gmail['password'] ?? null,
            'name' => $name,
            'updated_at' => now()->toDateTimeString(),
        ];

        // Kiểm tra có password không
        if (empty($gmailInfo['password'])) {
            return [
                'status' => 'error',
                'message' => '⚠️ Bạn cần cung cấp mật khẩu ứng dụng Gmail (App Password).'
            ];
        }

        // Lưu qua setOption (mã hoá tự động)
        $user->setOption('gmail', $gmailInfo);

        return [
            'status' => 'success',
            'message' => '✅ Đã cập nhật thông tin Gmail cho người dùng: ' . $email,
            'data' => $gmailInfo,
        ];
    }

    public static function updateProfile(int $userId, array $profile)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Không tìm thấy người dùng.'];
        }

        $user->setOption('profile', $profile);

        return ['status' => 'success', 'message' => 'Đã cập nhật hồ sơ cá nhân.'];
    }

    public static function updateShippingInfo(int $userId, array $shipping)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Không tìm thấy người dùng.'];
        }

        $user->setOption('shipping_info', $shipping);

        return ['status' => 'success', 'message' => 'Đã cập nhật thông tin giao hàng.'];
    }

    public static function getProfile(int $userId)
    {
        return User::find($userId)?->getOption('profile', []);
    }

    public static function getShippingInfo(int $userId)
    {
        return User::find($userId)?->getOption('shipping_info', []);
    }

    // -----------------------------
    // 6️⃣ HÀM TIỆN ÍCH
    // -----------------------------

    public static function getById(int $id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) return null;

        $user->profile = $user->getOption('profile', []);
        $user->shipping_info = $user->getOption('shipping_info', []);

        return $user;
    }

    public static function existsEmail(string $email, ?int $ignoreId = null)
    {
        return User::where('email', $email)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public static function count()
    {
        return User::count();
    }

    public static function search(string $keyword, int $limit = 10)
    {
        return User::keyword($keyword)->limit($limit)->get();
    }

    public static function BaseQueryService(Builder $query, array $params = [])
    {
        // 🔹 Làm sạch params
        $params = array_filter($params, fn($v) => $v !== null && $v !== '' && $v !== []);

        // 🔹 Select cột
        if (!empty($params['select'])) {
            $query->select($params['select']);
        }

        // 🔹 Eager load quan hệ
        if (!empty($params['with'])) {
            $query->with($params['with']);
        }

        // 🔹 Eager load đếm quan hệ
        if (!empty($params['with_count'])) {
            $query->withCount($params['with_count']);
        }

        // 🔹 Filters
        if (!empty($params['filters']) && is_array($params['filters'])) {
            foreach ($params['filters'] as $column => $value) {
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->where($column, $value);
                }
            }
        }

        // 🔹 Keyword search
        if (!empty($params['keyword']) && is_callable([$query->getModel(), 'scopeKeyword'])) {
            $query->keyword($params['keyword']);
        }

        // 🔹 Sort
        if (!empty($params['sort_by'])) {
            if (is_array($params['sort_by'])) {
                foreach ($params['sort_by'] as $col => $order) {
                    $query->orderBy($col, $order);
                }
            } else {
                $query->orderBy($params['sort_by'], $params['sort_order'] ?? 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // 🔹 Loại kết quả
        $type = $params['type'] ?? 'paginate';
        $perPage = $params['per_page'] ?? 20;

        return match ($type) {
            'first'    => $query->first(),
            'count'    => $query->count(),
            'exists'   => $query->exists(),
            'get'      => $query->get(),            
            default    => $query->paginate($perPage),
        };
    }

}

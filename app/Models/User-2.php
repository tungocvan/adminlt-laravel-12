<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_admin',
        'birthdate',
        'google_id',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date', // thêm birthdate
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            Mail::to($user->email)
                ->queue(new WelcomeUserMail($user));
        });

        static::deleting(function ($user) {
            $user->roles()->detach();
            $user->permissions()->detach();
        });
    }

    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function scopeKeyword($query, $keyword)
    {
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%")
              ->orWhere('username', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope filter toàn diện, tích hợp phân trang
     * Hỗ trợ exact + range cho field date/datetime
     * Hỗ trợ DB kiểu DATE/DATETIME hoặc string "dd/mm/yyyy"
     */
    /**
 * Scope filter tối ưu, trả về data + meta
 */
    /**
 * Scope filter toàn diện, tự động nhận biết kiểu cột (date/string), trả về data + meta
 */
public function scopeFilter(Builder $query, array $params, int $perPage = 20): array
{
    $params = array_filter($params, fn($v) => $v !== null && $v !== '');

    // Hàm parse date chuẩn hóa dd/mm/yyyy -> yyyy-mm-dd
    $parseDate = function($date) {
        if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $date, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }
        if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $date)) {
            return $date;
        }
        return null;
    };

    $table = (new self())->getTable();

    // 1️⃣ Filter bình thường
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

    if (isset($params['status'])) {
        $query->where('status', $params['status']);
    }

    if (isset($params['referral_code'])) {
        $query->where('referral_code', $params['referral_code']);
    }

    // 2️⃣ Keyword search
    if (!empty($params['search'])) {
        $query->keyword($params['search']);
    }

    // 3️⃣ Filter tự động các field date/datetime
    $dateFields = array_filter((new self())->getCasts(), fn($type) => in_array($type, ['date', 'datetime']));

    foreach ($dateFields as $field => $type) {
        // Kiểm tra kiểu cột DB
        $columnType = Schema::getColumnType($table, $field);
        $isStringColumn = in_array($columnType, ['string', 'varchar', 'text']); // nếu DB kiểu string, cần STR_TO_DATE

        $applyDateFilter = function($q, $col, $date, $op = '=') use ($isStringColumn) {
            if ($isStringColumn) {
                $q->whereRaw("STR_TO_DATE($col, '%d/%m/%Y') $op ?", [$date]);
            } else {
                $q->whereDate($col, $op, $date);
            }
        };

        // exact match
        if (!empty($params[$field])) {
            $date = $parseDate($params[$field]);
            if ($date) {
                $query->where(function($q) use ($field, $date, $applyDateFilter) {
                    $applyDateFilter($q, $field, $date);
                });
            }
        }

        // from
        if (!empty($params[$field . '_from'])) {
            $date = $parseDate($params[$field . '_from']);
            if ($date) {
                $query->where(function($q) use ($field, $date, $applyDateFilter) {
                    $applyDateFilter($q, $field, $date, '>=');
                });
            }
        }

        // to
        if (!empty($params[$field . '_to'])) {
            $date = $parseDate($params[$field . '_to']);
            if ($date) {
                $query->where(function($q) use ($field, $date, $applyDateFilter) {
                    $applyDateFilter($q, $field, $date, '<=');
                });
            }
        }
    }

    // 4️⃣ Sắp xếp mặc định
    $query->orderBy('id', 'desc');

    // 5️⃣ Phân trang
    $paginated = $query->paginate($perPage);

    // 6️⃣ Trả về data + meta
    return [
        'data' => $paginated->items(),
        'meta' => [
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
        ],
    ];
}

}

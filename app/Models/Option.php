<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $table = 'wp_options';
    protected $primaryKey = 'option_id';
    public $timestamps = false;

    protected $fillable = ['option_name', 'option_value', 'autoload'];

    public function getRouteKeyName()
    {
        return 'option_id'; // 👈 quan trọng cho edit/update/destroy
    }
    /**
     * Lấy option theo tên
     */
    public static function get_option($name, $default = null)
    {
        $option = static::where('option_name', $name)->first();

        if ($option) {
            $unserialized = @unserialize($option->option_value);
            return $unserialized !== false || $option->option_value === 'b:0;'
                ? $unserialized
                : $option->option_value;
        }

        return $default;
    }

    /**
     * Tạo hoặc cập nhật option
     */
    public static function set_option($name, $value, $autoload = 'yes')
    {
        $option = static::firstOrNew(['option_name' => $name]);

        // Nếu mảng thì serialize
        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
        }

        $option->option_value = $value;
        $option->autoload = $autoload;
        $option->save();

        return $option;
    }

    /**
     * Xóa option
     */
    public static function delete_option($name): bool
    {
        return (bool) static::where('option_name', $name)->delete();
    }

    /**
     * Cập nhật option (nếu đã tồn tại)
     */
    public static function update_option($name, $value, $autoload = null)
    {
        $option = static::where('option_name', $name)->first();
        if (!$option) {
            return static::set_option($name, $value, $autoload ?? 'yes');
        }

        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
        }

        $option->option_value = $value;
        if ($autoload !== null) {
            $option->autoload = $autoload;
        }
        $option->save();

        return $option;
    }
}

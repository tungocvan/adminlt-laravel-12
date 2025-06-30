<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $table = 'wp_options';
    
    // Sử dụng 'option_id' làm khóa chính
    protected $primaryKey = 'option_id';
    
    // Cho phép các cột được fill
    protected $fillable = ['option_name', 'option_value', 'autoload'];

    public $timestamps = false;

    /**
     * Giả lập hàm get_option giống như WordPress
     */
    public static function get_option($name, $default = null)
    {
        $option = static::where('option_name', $name)->first();
        if ($option) {
            // Nếu giá trị là chuỗi đã được serialize, unserialize để lấy giá trị gốc
            return @unserialize($option->option_value) !== false ? unserialize($option->option_value) : $option->option_value;
        }

        return $default;
    }

    /**
     * Giả lập hàm set_option giống như WordPress
     * Nếu giá trị là mảng, sẽ serialize trước khi lưu
     */
    public static function set_option($name, $value, $autoload = 'yes')
    {
        $option = static::firstOrNew(['option_name' => $name]);
        
        // Kiểm tra nếu giá trị là mảng, serialize thành chuỗi
        if (is_array($value)) {
            $value = serialize($value);
        }
        
        $option->option_value = $value;
        $option->autoload = $autoload;
        $option->save();
    }
}

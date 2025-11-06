<?php

namespace App\Traits;

use App\Models\Option;

trait HasOptions
{
    /**
     * Quan hệ morph: model có nhiều option
     */
    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }

    /**
     * Kiểm tra option tồn tại
     */
    public function hasOption(string $name): bool
    {
        return $this->options()
            ->where('option_name', $name)
            ->exists();
    }

    /**
     * Lấy option theo tên
     */
    public function getOption(string $name, $default = null)
    {
        $option = $this->options()
            ->firstWhere('option_name', $name);

        return $option ? $option->option_value : $default;
    }

    /**
     * Lưu option (tự động update or create)
     * Có hỗ trợ merge JSON nếu $merge = true
     */
    public function setOption(string $name, $value, string $autoload = 'no', bool $merge = false)
    {
        $current = $this->getOption($name);

        // Merge array nếu cần
        if ($merge && is_array($current) && is_array($value)) {
            $value = array_merge($current, $value);
        }

        return $this->options()->updateOrCreate(
            ['option_name' => $name],
            [
                'option_value' => $value,
                'autoload' => $autoload
            ]
        );
    }

    /**
     * Xóa option
     */
    public function deleteOption(string $name): bool
    {
        return (bool) $this->options()
            ->where('option_name', $name)
            ->delete();
    }

    /**
     * Lấy toàn bộ option dưới dạng:
     * [ 'shipping' => [...], 'profile' => [...], ... ]
     */
    public function getAllOptions(): array
    {
        return $this->options()
            ->pluck('option_value', 'option_name')
            ->toArray();
    }
}

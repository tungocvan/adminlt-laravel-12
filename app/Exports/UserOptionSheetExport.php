<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserOptionSheetExport implements FromArray, WithTitle
{
    protected string $optionName;

    public function __construct(string $optionName)
    {
        $this->optionName = $optionName;
    }

    public function array(): array
    {
        $rows = [];

        // Lấy toàn bộ user
        $users = User::all();

        // Gom tất cả keys của option để tạo header
        $allKeys = [];

        foreach ($users as $user) {
            $options = method_exists($user, 'getAllOptions')
                ? $user->getAllOptions()
                : ($user->options ?? []);

            if (isset($options[$this->optionName]) && is_array($options[$this->optionName])) {
                $allKeys = array_merge($allKeys, array_keys($options[$this->optionName]));
            }
        }

        $allKeys = array_unique($allKeys);

        // Header
        $header = array_merge(['user_id'], $allKeys);
        $rows[] = $header;

        // Dữ liệu
        foreach ($users as $user) {
            $options = method_exists($user, 'getAllOptions')
                ? $user->getAllOptions()
                : ($user->options ?? []);

            if (!isset($options[$this->optionName])) {
                continue;
            }

            $row = [$user->id];

            foreach ($allKeys as $key) {
                $row[] = $options[$this->optionName][$key] ?? null;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function title(): string
    {
        return $this->optionName;
    }
}

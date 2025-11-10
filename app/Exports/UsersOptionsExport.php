<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsersOptionsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        // Sheet users
        $sheets[] = new UsersSheetExport();

        // Lấy toàn bộ option keys từ tất cả user
        $optionKeys = $this->collectAllOptionKeys();

        // Tạo sheet cho từng option
        foreach ($optionKeys as $optionName) {
            $sheets[] = new UserOptionSheetExport($optionName);
        }

        return $sheets;
    }

    private function collectAllOptionKeys(): array
    {
        $optionNames = [];

        $users = User::all();

        foreach ($users as $user) {
            $options = method_exists($user, 'getAllOptions')
                ? $user->getAllOptions()
                : ($user->options ?? []);

            foreach ($options as $key => $value) {
                $optionNames[$key] = true;
            }
        }

        return array_keys($optionNames);
    }
}

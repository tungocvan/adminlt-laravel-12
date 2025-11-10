<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class UsersSheetExport implements FromArray, WithTitle
{
    public function array(): array
    {
        $users = User::all();

        $rows = [];
        $rows[] = [
            'id', 'name', 'email', 'username', 'is_admin',
            'birthdate', 'google_id', 'referral_code',
            'created_at', 'updated_at'
        ];

        foreach ($users as $u) {
            $rows[] = [
                $u->id,
                $u->name,
                $u->email,
                $u->username,
                $u->is_admin,
                $u->birthdate,
                $u->google_id,
                $u->referral_code,
                $u->created_at,
                $u->updated_at
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'users';
    }
}

<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $selectedUsers;

    public function __construct($selectedUsers)
    {
        $this->selectedUsers = $selectedUsers;
    }

    public function collection()
    {
        return User::whereIn('id', $this->selectedUsers)->select('id', 'name', 'email')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email'];
    }
}

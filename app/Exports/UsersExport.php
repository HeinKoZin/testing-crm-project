<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select("id", "name", "email", "phone", "gender")->get();
    }

    public function headings(): array
    {
        return ["ID", "Name", "Email", "Phone", "Gender"];
    }
}

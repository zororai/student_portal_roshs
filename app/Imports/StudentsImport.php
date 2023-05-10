<?php

namespace App\Imports;

use App\User;
use App\Student;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = User::create([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']),
        ]);

        return new Student([
            'user_id'           => $user->id,
            'parent_id'         => $row['parent_id'],
            'class_id'          => $row['class_id'],
            'roll_number'       => $row['roll_number'],
            'gender'            => $row['gender'],
            'phone'             => $row['phone'],
            'dateofbirth'       => $row['dateofbirth'],
            'current_address'   => $row['current_address'],
            'permanent_address' => $row['permanent_address'],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserSeeder extends Seeder
{

    public function run(): void
    {
    $users = [
        [
            'first_name' => 'admin',
            'last_name'  => 'main',
            'email'      => 'admin@gmail.com',
            'password'   => Hash::make('password'),
            'is_admin'   => 1,
        ],
        [
            'first_name' => 'user',
            'last_name'  => 'user',
            'email'      => 'user@gmail.com',
            'password'   => Hash::make('password'),
            'is_admin'   => 0,
        ],
    ];
    foreach ($users as $user) {
        User::updateOrCreate(
            ['email' => $user['email']],
            [
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'password'   => $user['password'],
                'is_admin'   => $user['is_admin'],
            ]
        );
    }
}
}
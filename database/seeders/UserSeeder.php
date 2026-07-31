<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'test',
                'email' => 'test@gmail.com',
                'password' => bcrypt('12345678'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}

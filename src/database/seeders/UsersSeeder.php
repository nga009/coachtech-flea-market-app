<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'test1',
                'email' => 'test1@test.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'test2',
                'email' => 'test2@test.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }

    }
}

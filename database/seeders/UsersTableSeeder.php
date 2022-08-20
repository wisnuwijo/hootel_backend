<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                "role_id" => 1,
                "name" => "admin",
                "email" => "admin@mail.com",
                "email_verified_at" => now(),
                "password" => bcrypt("123123"),
                "api_token" => md5(Str::random(10))
            ],
            [
                "role_id" => 2,
                "name" => "customer1",
                "email" => "customer1@mail.com",
                "email_verified_at" => now(),
                "password" => bcrypt("123123"),
                "api_token" => md5(Str::random(10))
            ]
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use DateTime;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "test",
            'email' => 'test@gmail.com',
            'password' => Hash::make('test2024'),
            'created_at' => now(), // Carbon（Datetimeクラスを継承）インスタンス
            'updated_at' => new DateTime(), // DateTimeインスタンス
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tests')->insert([
            'title' => 'title01',
            'body' => 'Body01.',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ]);
        DB::table('tests')->insert([
            'title' => 'title02',
            'body' => 'Body02.',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ]);
    }
}

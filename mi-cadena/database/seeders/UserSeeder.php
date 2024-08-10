<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::create([
        'name' => "Alvaro Eduardo Ocampo Paz",
        'email' => "aeopaz@gmail.com",
        'mobile'=>3207236182,
        'password' => '12345678', // password
       ]);
    }
}

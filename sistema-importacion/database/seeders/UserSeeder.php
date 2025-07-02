<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123')
        ]);

        User::create([
            'name' => 'Usuario Demo',
            'email' => 'demo@test.com',
            'password' => Hash::make('password123')
        ]);
    }
}

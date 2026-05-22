<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@magazinonline.ro',
            'password' => Hash::make('password'),
            'phone' => '0712345678',
            'address' => 'Strada Admin 1',
            'city' => 'București',
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'phone' => '0712345679',
            'address' => 'Strada User 1',
            'city' => 'București',
            'role_id' => $userRole->id,
            'is_active' => true,
        ]);
    }
}
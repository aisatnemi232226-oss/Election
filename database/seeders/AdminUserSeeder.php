<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'Administrator',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => env('ADMIN_PASSWORD', 'Admin123!'),
                'is_admin' => true,
                'admin_role' => 'Administrator',
                'idnumber' => null,
            ],
        );
    }
}

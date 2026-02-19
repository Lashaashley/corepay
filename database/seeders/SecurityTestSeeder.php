<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SecurityTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'approvelvl' => 'YES',
        ]);

        $regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'approvelvl' => 'NO',
        ]);

        $restrictedUser = User::create([
            'name' => 'Restricted User',
            'email' => 'restricted@test.com',
            'password' => Hash::make('password'),
            'approvelvl' => 'NO',
        ]);

        // Grant payroll access
        DB::table('user_payroll_access')->insert([
            // Admin has access to all
            ['user_id' => $admin->id, 'payroll_type' => 'PREM', 'is_active' => 1],
            ['user_id' => $admin->id, 'payroll_type' => 'RWLS', 'is_active' => 1],
            ['user_id' => $admin->id, 'payroll_type' => 'BONUS', 'is_active' => 1],
            
            // Regular user has limited access
            ['user_id' => $regularUser->id, 'payroll_type' => 'PREM', 'is_active' => 1],
            ['user_id' => $regularUser->id, 'payroll_type' => 'RWLS', 'is_active' => 1],
            
            // Restricted user has only one
            ['user_id' => $restrictedUser->id, 'payroll_type' => 'PREM', 'is_active' => 1],
        ]);

        $this->command->info('Security test data seeded successfully!');
    }
}
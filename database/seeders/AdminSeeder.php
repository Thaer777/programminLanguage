<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $admin = User::create([
            'phone' => '0999999999',
            'password' => bcrypt('admin123'),
            'firstName' => 'Admin',
            'lastName' => 'System',
            'dateOfBirth' => '1990-01-01',
            'personalPhoto' => 'admin.jpg',
            'IDPhoto' => 'admin_id.jpg',
            'role' => 'Admin',
            'status' => 'approved'
        ]);
        $token = $admin->createToken('auth_sanctum')->plainTextToken;
echo "\n Admin token : ".$token."\n";
    }
}

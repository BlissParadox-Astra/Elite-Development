<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            [
                'user_type_id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male',
                'age' => 30,
                'address' => '123 Main St',
                'contact_number' => '09503803215',
            ],
            [
                'user_type_id' => 2,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'gender' => 'female',
                'age' => 28,
                'address' => '456 Elm St',
                'contact_number' => '09876890643',
            ],
            // Add more user data as needed
        ];

        // Insert the data into the users table
        foreach ($usersData as $userData) {
            User::create($userData);
        }
    }
}

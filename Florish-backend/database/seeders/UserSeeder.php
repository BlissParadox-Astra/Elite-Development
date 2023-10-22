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
        // Define the data for the users
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

        ];

        for ($i = 0; $i < 20; $i++) {
            $user = [
                'user_type_id' => rand(1, 2), // Random user type (1, 2, or 3)
                'first_name' => 'First' . ($i + 1),
                'last_name' => 'Last' . ($i + 1),
                'gender' => $i % 2 === 0 ? 'male' : 'female', // Alternating gender
                'age' => rand(18, 60), // Random age between 18 and 60
                'address' => 'Address' . ($i + 1),
                'contact_number' => '09' . rand(10000000, 99999999), // Random contact number
            ];

            User::create($user);
        }

        // Insert the data into the users table
        foreach ($usersData as $userData) {
            User::create($userData);
        }
    }
}

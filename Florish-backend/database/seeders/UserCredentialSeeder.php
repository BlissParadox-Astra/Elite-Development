<?php

namespace Database\Seeders;

use App\Models\UserCredential;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the data for user credentials
        $credentialsData = [
            [
                'user_id' => 1,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 2,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 3,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 4,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 5,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 6,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 7,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 8,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 9,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 10,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 11,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 12,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 3,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 14,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 15,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 16,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 17,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 18,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 19,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 20,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 21,
                'username' => 'admin',
                'password' => bcrypt('adminpassword'),
            ],
            [
                'user_id' => 22,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 23,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            [
                'user_id' => 24,
                'username' => 'cashier',
                'password' => bcrypt('cashierpassword'),
            ],
            // Add more credential data as needed
        ];
    }
}

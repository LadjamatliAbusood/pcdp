<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\user_infos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nickname' => 'Aj superadmin',
                'first_name' => 'Abusood',
                'middle_name' => 'Jumat',
                'last_name' => 'Ladjamatli',
                'ext_name' => '',
                'contact_number' => '09624501604',
                'position' => 'CP III',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 1,

            ],
            [
                'nickname' => 'Aj admin',
                'first_name' => 'Abusood',
                'middle_name' => 'Jumat',
                'last_name' => 'Ladjamatli',
                'ext_name' => '',
                'contact_number' => '09624501604',
                'position' => 'CP III',
                'email' => 'admin@gmail.com',
                'password'=> Hash::make('password'),
                'role'=> 2,
            ],

            [
            'nickname' => 'Aj client',
            'first_name' => 'Abusood',
            'middle_name' => 'Jumat',
            'last_name' => 'Ladjamatli',
            'ext_name' => '',
            'contact_number' => '09624501604',
            'position' => 'CP III',
            'email' => 'user@gmail.com',
            'password'=> Hash::make('password'),
            'role'=> 3,
            ],
           
            
            ];

           foreach ($users as $userData) {
    // Create user (only fields in users table)
    $user = User::updateOrCreate(
        ['email' => $userData['email']],
        [
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => $userData['role']
        ]
    );

    // Create user_infos entry
    user_infos::updateOrCreate(
        ['user_id' => $user->id],
        [
            'nickname' => $userData['nickname'],
            'first_name' => $userData['first_name'],
            'middle_name' => $userData['middle_name'],
            'last_name' => $userData['last_name'],
            'ext_name' => $userData['ext_name'],
            'contact_number' => $userData['contact_number'],
            'position' => $userData['position'],
        ]
    );
}

    }
}

<?php

namespace Database\Seeders;

use App\Models\foto_profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminDaniUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admindani@gmail.com'],
            [
                'name' => 'admindani',
                'username' => 'admindani',
                'password' => Hash::make('admindani'),
                'role_id' => 1,
            ]
        );

        foto_profile::updateOrCreate(
            ['user_id' => $user->id],
            ['foto' => 'foto/user.jpg']
        );
    }
}

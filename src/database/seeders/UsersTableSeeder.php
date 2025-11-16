<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'user1@a.com'],
                [
                    'name' => 'りんご',
                    'password' => Hash::make('12345678'),
                    'email_verified_at'=>'2025-11-11 19:32:06',
                    'profile_image' => 'default-profile.png',
                    'default_postal_code' => '123-4567',
                    'default_address_line' => '東京都渋谷区渋谷1-1-1',
                    'default_building' => null,
                ],
        );

        User::updateOrCreate(
            ['email' => 'user2@a.com'],
                [
                    'name' => 'トマト',
                    'password' => Hash::make('12345678'),
                    'email_verified_at'=>'2025-11-11 19:32:06',
                    'profile_image' => 'default-profile.png',
                    'default_postal_code' => '234-5678',
                    'default_address_line' => '東京都渋谷区渋谷2-2-2',
                    'default_building' => 'テストビル202',

                ],
        );
        User::updateOrCreate(
            ['email' => 'user3@a.com'],
                [
                    'name' => 'いちご',
                    'password' => Hash::make('12345678'),
                    'email_verified_at'=>'2025-11-11 19:32:06',
                    'profile_image' => 'default-profile.png',
                    'default_postal_code' => '345-6789',
                    'default_address_line' => '東京都渋谷区渋谷1-3-3',
                    'default_building' => 'テストビル303',
                ],
        );
    }
}

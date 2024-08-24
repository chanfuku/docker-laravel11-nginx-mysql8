<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 10人のユーザーを生成し、それぞれに関連するプロフィールを作成
        User::factory(10)->create()->each(function ($user) {
            // 各ユーザーに関連するプロフィールを作成
            UserProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}

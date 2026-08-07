<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0600000000',
            'role' => 'student',
            'preferred_locale' => 'ar',
            'password' => 'Test12345',
        ]);
        $testUser->forceFill(['email_verified_at' => now()])->save();

        $adminUser = User::updateOrCreate([
            'email' => 'admin@easypsi.test',
        ], [
            'name' => 'EasyPsi Admin',
            'email' => 'admin@easypsi.test',
            'phone' => '212645532991',
            'role' => 'admin',
            'preferred_locale' => 'ar',
            'password' => 'Admin12345!',
        ]);
        $adminUser->forceFill(['email_verified_at' => now()])->save();

        $teacherUser = User::updateOrCreate([
            'email' => 'teacher@easypsi.test',
        ], [
            'name' => 'Pr. Nbigui',
            'email' => 'teacher@easypsi.test',
            'phone' => '0601234567',
            'role' => 'teacher',
            'preferred_locale' => 'fr',
            'password' => 'Teacher12345!',
        ]);
        $teacherUser->forceFill(['email_verified_at' => now()])->save();
    }
}

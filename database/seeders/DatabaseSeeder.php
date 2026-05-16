<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kart;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Створюємо 10 картингів у базі через KartFactory
        $karts = Kart::factory(10)->create();

        // 2. Створюємо 10 випадкових користувачів.
        // Оскільки в міграції є 'phone', ми передаємо його явно через фабрику
        $users = User::factory(10)->create([
            'phone' => '+38099' . rand(1000000, 9999999)
        ]);

        // 3. Створюємо одного конкретного тестового користувача для твого входу
        User::factory()->create([
            'name' => 'Laited Driver',
            'email' => 'driver@example.com',
            'phone' => '+380123456789',
            'password' => bcrypt('password'), // Пароль: password
        ]);
    }
}
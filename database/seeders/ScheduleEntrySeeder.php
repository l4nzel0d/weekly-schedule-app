<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ScheduleEntry;
use App\Models\Tag;

class ScheduleEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем или находим тестового пользователя
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Тестовый Пользователь',
                'password' => bcrypt('password'), // Устанавливаем пароль
            ]
        );

        // Очищаем старые записи и теги для этого пользователя, чтобы избежать дубликатов
        $user->scheduleEntries()->delete();
        $user->tags()->delete();

        // --- Создание тегов ---
        $tagEducation = $user->tags()->create(['name' => 'Обучение', 'color' => 'blue']);
        $tagSport = $user->tags()->create(['name' => 'Спорт', 'color' => 'green']);
        $tagArt = $user->tags()->create(['name' => 'Творчество', 'color' => 'yellow']);

        // --- Создание записей в расписании ---

        // Школа (Пн-Пт, 8:00 - 13:30)
        for ($day = 1; $day <= 5; $day++) {
            ScheduleEntry::create([
                'user_id' => $user->id,
                'title' => 'Школа',
                'day_of_week' => $day,
                'start_time' => '08:00',
                'end_time' => '13:30',
            ]);
        }

        // Рисование (Вт, Пт, 15:00 - 16:15)
        foreach ([2, 5] as $day) {
            ScheduleEntry::create([
                'user_id' => $user->id,
                'title' => 'Рисование',
                'day_of_week' => $day,
                'start_time' => '15:00',
                'end_time' => '16:15',
            ]);
        }

        // Плавание
        // Среда, 18:00 - 19:30
        ScheduleEntry::create([
            'user_id' => $user->id,
            'title' => 'Плавание',
            'day_of_week' => 3, // Среда
            'start_time' => '18:00',
            'end_time' => '19:30',
        ]);

        // Суббота, 10:15 - 11:45
        ScheduleEntry::create([
            'user_id' => $user->id,
            'title' => 'Плавание',
            'day_of_week' => 6, // Суббота
            'start_time' => '10:15',
            'end_time' => '11:45',
        ]);

        // --- Привязка тегов к записям ---
        ScheduleEntry::where('title', 'Школа')->each(function ($entry) use ($tagEducation) {
            $entry->tags()->attach($tagEducation);
        });

        ScheduleEntry::where('title', 'Плавание')->each(function ($entry) use ($tagSport) {
            $entry->tags()->attach($tagSport);
        });

        ScheduleEntry::where('title', 'Рисование')->each(function ($entry) use ($tagArt) {
            $entry->tags()->attach($tagArt);
        });
    }
}

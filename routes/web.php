<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;

// Главная страница перенаправляет на страницу расписания
Route::get('/', function () {
    return redirect('/schedule');
});

// Маршруты для аутентификации (например, /login, /register)
Auth::routes();

// Группа маршрутов, требующих аутентификации
Route::middleware(['auth'])->group(function () {
    // Определяем только необходимые маршруты для ресурса Schedule
    Route::resource('schedule', ScheduleController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
});

// Стандартный маршрут /home, который создается при установке auth, можно оставить или удалить
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

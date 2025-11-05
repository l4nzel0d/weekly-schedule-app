<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ScheduleEntryController;
use App\Http\Controllers\TagController;

// Главная страница перенаправляет на страницу расписания
Route::get('/', function () {
    return redirect('/schedule-entries');
});

// Маршруты для аутентификации
Route::group([], function() {
    // Маршруты входа
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Маршрут выхода
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Маршруты регистрации
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});


// Группа маршрутов работы с бизнес-сущностями
// Требует аутентификации
Route::middleware(['auth'])->group(function () {
    // Маршруты для работы с ScheduleEntry
    Route::resource('schedule-entries', ScheduleEntryController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    // Маршруты для работы с Tag
    Route::resource('tags', TagController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
});

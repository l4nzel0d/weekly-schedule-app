<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Импортируем модель User

class ScheduleEntry extends Model
{
    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    /**
     * Получить пользователя, которому принадлежит запись расписания.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
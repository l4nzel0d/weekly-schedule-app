<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'user_id', 'color'];

    /**
     * Получить пользователя, которому принадлежит тег.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить все записи расписания, связанные с этим тегом.
     */
    public function scheduleEntries()
    {
        return $this->belongsToMany(ScheduleEntry::class);
    }
}

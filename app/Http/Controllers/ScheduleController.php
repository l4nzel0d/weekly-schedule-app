<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Отображает страницу с расписанием, опционально отфильтрованным по дню недели.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->scheduleEntries();

        if ($request->has('day') && $request->query('day') !== 'all') {
            $query->where('day_of_week', $request->query('day'));
        }

        $entries = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        $groupedEntries = $entries->groupBy('day_of_week');

        return view('schedule.index', ['groupedEntries' => $groupedEntries]);
    }

    /**
     * Сохраняет новые записи в расписании.
     */
    public function store(StoreScheduleRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['days_of_week'] as $day) {
                    ScheduleEntry::create([
                        'user_id' => auth()->id(),
                        'title' => $validated['title'],
                        'description' => $validated['description'],
                        'day_of_week' => $day,
                        'start_time' => $validated['start_time'],
                        'end_time' => $validated['end_time'],
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Произошла ошибка при создании записей.'], 500);
        }

        return response()->json(['message' => 'Записи успешно созданы.']);
    }

    /**
     * Обновляет существующую запись в расписании.
     */
    public function update(UpdateScheduleRequest $request, ScheduleEntry $scheduleEntry)
    {
        $validated = $request->validated();
        $scheduleEntry->update($validated);

        return response()->json(['message' => 'Запись успешно обновлена.']);
    }

    /**
     * Удаляет запись из расписания.
     */
    public function destroy(ScheduleEntry $scheduleEntry)
    {
        // Проверяем, что пользователь является владельцем записи
        if ($scheduleEntry->user_id !== auth()->id()) {
            return response()->json(['message' => 'У вас нет прав на удаление этой записи.'], 403);
        }

        $scheduleEntry->delete();

        return response()->json(['message' => 'Запись успешно удалена.']);
    }
}

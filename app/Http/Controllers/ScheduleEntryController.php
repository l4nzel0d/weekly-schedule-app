<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleEntryRequest;
use App\Http\Requests\UpdateScheduleEntryRequest;
use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleEntryController extends Controller
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

        // Добавляем логику поиска
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->query('search'));
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->whereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"])
                         ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        $entries = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        $groupedEntries = $entries->groupBy('day_of_week');

        return view('schedule.index', ['groupedEntries' => $groupedEntries]);
    }

    /**
     * Сохраняет новые записи в расписании.
     */
    public function store(StoreScheduleEntryRequest $request)
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

        $redirectUrl = $request->headers->get('referer', route('schedule-entries.index'));

        return response()->json([
            'message' => 'Записи успешно созданы.',
            'redirectUrl' => $redirectUrl
        ]);
    }

    /**
     * Обновляет существующую запись в расписании.
     */
    public function update(UpdateScheduleEntryRequest $request, ScheduleEntry $schedule_entry)
    {
        $validated = $request->validated();
        $schedule_entry->update($validated);

        return response()->json(['message' => 'Запись успешно обновлена.']);
    }

    /**
     * Удаляет запись из расписания.
     */
    public function destroy(ScheduleEntry $schedule_entry)
    {
        // Проверяем, что пользователь является владельцем записи
        if ($schedule_entry->user_id !== auth()->id()) {
            return response()->json(['message' => 'У вас нет прав на удаление этой записи.'], 403);
        }

        $schedule_entry->delete();

        return response()->json([
            'message' => 'Запись успешно удалена.',
            'redirectUrl' => request()->headers->get('referer', route('schedule-entries.index'))
        ]);
    }
}

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
        $query = auth()->user()->scheduleEntries()->with('tags');

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

        // Фильтрация по тегам
        if ($request->filled('tags')) {
            $tagIds = $request->query('tags');
            if (is_array($tagIds) && !empty($tagIds)) {
                $validatedTagIds = array_filter($tagIds, 'is_numeric');
                if (!empty($validatedTagIds)) {
                    $query->whereHas('tags', function ($q) use ($validatedTagIds) {
                        $q->whereIn('tags.id', $validatedTagIds);
                    });
                }
            }
        }

        $entries = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        $groupedEntries = $entries->groupBy('day_of_week');
        $tags = auth()->user()->tags()->get();

        return view('schedule-entries.index', [
            'groupedEntries' => $groupedEntries,
            'tags' => $tags
        ]);
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
                    $scheduleEntry = ScheduleEntry::create([
                        'user_id' => auth()->id(),
                        'title' => $validated['title'],
                        'description' => $validated['description'],
                        'day_of_week' => $day,
                        'start_time' => $validated['start_time'],
                        'end_time' => $validated['end_time'],
                    ]);

                    // Прикрепляем теги, если они были переданы
                    if (isset($validated['tags'])) {
                        $scheduleEntry->tags()->sync($validated['tags']);
                    }
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

        // Прикрепляем теги, если они были переданы
        if (isset($validated['tags'])) {
            $schedule_entry->tags()->sync($validated['tags']);
        } else {
            // Если теги не переданы (например, все были сняты), отсоединяем все теги
            $schedule_entry->tags()->detach();
        }

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

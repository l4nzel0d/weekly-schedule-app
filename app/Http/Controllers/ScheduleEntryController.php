<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleEntryRequest;
use App\Http\Requests\UpdateScheduleEntryRequest;
use App\Http\Requests\DestroyScheduleEntryRequest;
use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\ProvidesBackUrl; // Подключаем трейт для URL
use App\Support\JsonResponseBuilder; // Подключаем наш билдер ответов

class ScheduleEntryController extends Controller
{
    use ProvidesBackUrl; // Используем трейт

    protected string $fallbackRoute = 'schedule-entries.index'; // Запасной маршрут для расписания
    /**
     * Отображает страницу с расписанием, опционально отфильтрованным по дню недели.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->scheduleEntries()->with('tags');

        if ($request->has('day') && $request->query('day') !== 'all') {
            $query->where('day_of_week', $request->query('day'));
        }

        // Логика поиска по названию и описанию
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'ILIKE', "%{$searchTerm}%")
                         ->orWhere('description', 'ILIKE', "%{$searchTerm}%");
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

        // Формируем успешный ответ с URL для редиректа.
        $redirectUrl = $this->getBackUrl($this->fallbackRoute);
        return JsonResponseBuilder::success('Записи успешно созданы.', ['redirectUrl' => $redirectUrl]);
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

        // Формируем успешный ответ с URL для редиректа.
        $redirectUrl = $this->getBackUrl($this->fallbackRoute);
        return JsonResponseBuilder::success('Запись успешно обновлена.', ['redirectUrl' => $redirectUrl]);
    }

    /**
     * Удаляет запись из расписания.
     */
    public function destroy(DestroyScheduleEntryRequest $request, ScheduleEntry $schedule_entry)
    {
        // Логика авторизации теперь находится в DestroyScheduleEntryRequest.

        $schedule_entry->delete();

        // Формируем успешный ответ с URL для редиректа.
        $redirectUrl = $this->getBackUrl($this->fallbackRoute);
        return JsonResponseBuilder::success('Запись успешно удалена.', ['redirectUrl' => $redirectUrl]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Requests\DestroyTagRequest;
use App\Models\Tag;
use App\Http\Controllers\Traits\ProvidesBackUrl; // Подключаем трейт для URL
use App\Support\JsonResponseBuilder; // Подключаем наш билдер ответов

class TagController extends Controller
{
    use ProvidesBackUrl; // Используем трейт

    protected string $fallbackRoute = 'tags.index'; // Запасной маршрут для тегов
    /**
     * Отображает страницу управления тегами.
     */
    public function index()
    {
        $tags = auth()->user()->tags()->orderBy('name')->get();
        return view('tags.index', ['tags' => $tags]);
    }

    /**
     * Сохраняет новый тег.
     */
    public function store(StoreTagRequest $request)
    {
        auth()->user()->tags()->create($request->validated());

        // Формируем успешный ответ с URL для редиректа.
        $redirectUrl = $this->getBackUrl($this->fallbackRoute);
        return JsonResponseBuilder::success('Тег успешно создан.', ['redirectUrl' => $redirectUrl]);
    }

    /**
     * Обновляет существующий тег.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        // Формируем успешный ответ с URL для редиректа.
        $redirectUrl = $this->getBackUrl($this->fallbackRoute);
        return JsonResponseBuilder::success('Тег успешно обновлен.', ['redirectUrl' => $redirectUrl]);
    }

    /**
     * Удаляет тег.
     */
    public function destroy(DestroyTagRequest $request, Tag $tag)
    {
        // Логика авторизации теперь находится в DestroyTagRequest.

        $tag->delete();

        // Формируем успешный ответ с URL для редиректа.
        $redirectUrl = $this->getBackUrl($this->fallbackRoute);
        return JsonResponseBuilder::success('Тег успешно удален.', ['redirectUrl' => $redirectUrl]);
    }
}

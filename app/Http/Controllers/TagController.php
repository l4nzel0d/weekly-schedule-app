<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
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

        return response()->json([
            'message' => 'Тег успешно создан.',
            'redirectUrl' => route('tags.index')
        ]);
    }

    /**
     * Обновляет существующий тег.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return response()->json([
            'message' => 'Тег успешно обновлен.',
            'redirectUrl' => route('tags.index')
        ]);
    }

    /**
     * Удаляет тег.
     */
    public function destroy(Tag $tag)
    {
        // Авторизация
        if ($tag->user_id !== auth()->id()) {
            return response()->json(['message' => 'Это действие не авторизовано.'], 403);
        }

        $tag->delete();

        return response()->json([
            'message' => 'Тег успешно удален.',
            'redirectUrl' => route('tags.index')
        ]);
    }
}

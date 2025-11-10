<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyScheduleEntryRequest extends FormRequest
{
    /**
     * Определяет, авторизован ли пользователь для выполнения этого запроса.
     */
    public function authorize(): bool
    {
        // Пользователь может удалить запись только если он является ее владельцем.
        return $this->route('schedule_entry')->user_id === $this->user()->id;
    }

    /**
     * Возвращает правила валидации для запроса.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Для DELETE-запроса правила валидации не требуются.
        ];
    }
}

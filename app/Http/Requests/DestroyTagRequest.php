<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyTagRequest extends FormRequest
{
    /**
     * Определяет, авторизован ли пользователь для выполнения этого запроса.
     */
    public function authorize(): bool
    {
        // Пользователь может удалить тег только если он является его владельцем.
        return $this->route('tag')->user_id === $this->user()->id;
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

<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Стандартизированный конструктор JSON-ответов для AJAX-запросов.
 * Обеспечивает единую структуру ответов во всем приложении.
 */
final class JsonResponseBuilder
{
    /**
     * Класс является утилитарным и не должен создаваться как экземпляр.
     */
    private function __construct()
    {
    }

    /**
     * Формирует стандартный успешный JSON-ответ.
     *
     * @param string $message Сообщение для пользователя.
     * @param array $payload Ассоциативный массив с полезной нагрузкой.
     * @param int $status HTTP-код состояния (по умолчанию 200).
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success(string $message, array $payload = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            // Приведение к (object) для консистентности (пустой массив станет {}).
            'payload' => (object) $payload
        ], $status);
    }

    /**
     * Формирует стандартный ошибочный JSON-ответ.
     *
     * @param string $message Сообщение об ошибке.
     * @param int $status HTTP-код состояния.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message, int $status): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }

    /**
     * Ответ для ошибок авторизации (нет прав).
     */
    public static function unauthorized(string $message = 'Это действие не авторизовано.'): JsonResponse
    {
        return self::error($message, 403);
    }

    /**
     * Ответ, когда запрашиваемый ресурс не найден.
     */
    public static function notFound(string $message = 'Запрашиваемый ресурс не найден.'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * Ответ для непредвиденных ошибок сервера.
     * Логирует полную ошибку для разработчика, но отдает пользователю общее сообщение.
     *
     * @param \Throwable $e Перехваченное исключение.
     * @param string $message Общее сообщение для пользователя.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function generalError(Throwable $e, string $message = 'Произошла внутренняя ошибка сервера.'): JsonResponse
    {
        // Логируем полную информацию об ошибке для анализа разработчиком.
        Log::error($e);

        return self::error($message, 500);
    }
}

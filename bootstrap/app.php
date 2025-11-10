<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Support\JsonResponseBuilder; // Наш билдер
use Illuminate\Http\Request; // Для типа запроса
use Illuminate\Database\Eloquent\ModelNotFoundException; // Для обработки 404
use Illuminate\Auth\Access\AuthorizationException; // Для обработки 403
use Illuminate\Validation\ValidationException; // Для обработки 422
use Throwable; // Для общего типа исключений

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            // Если запрос ожидает JSON-ответ (AJAX-запрос).
            if ($request->expectsJson()) {
                // Определяем тип исключения и возвращаем соответствующий ответ.
                if ($e instanceof ValidationException) {
                    return JsonResponseBuilder::validationError($e);
                }

                if ($e instanceof ModelNotFoundException) {
                    return JsonResponseBuilder::notFound('Запрашиваемый ресурс не найден.');
                }

                if ($e instanceof AuthorizationException) {
                    return JsonResponseBuilder::unauthorized();
                }

                // Для всех остальных непредвиденных исключений.
                return JsonResponseBuilder::generalError($e);
            }
            // Если запрос не AJAX, Laravel продолжит стандартную обработку (HTML-страница ошибки).
        });
    })->create();

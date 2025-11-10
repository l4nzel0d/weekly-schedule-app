<?php

namespace App\Http\Controllers\Traits;

/**
 * Предоставляет возможность определять URL для "возврата" на предыдущую страницу.
 */
trait ProvidesBackUrl
{
    /**
     * Получает URL для редиректа, предпочитая заголовок 'Referer'.
     *
     * @param string $fallbackRoute Имя маршрута для использования, если 'Referer' недоступен.
     * @return string Готовый URL для редиректа.
     */
    protected function getBackUrl(string $fallbackRoute): string
    {
        // Возвращаем заголовок Referer, если он есть, иначе — запасной маршрут.
        return request()->headers->get('referer') ?? route($fallbackRoute);
    }
}

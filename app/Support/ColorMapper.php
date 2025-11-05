<?php

namespace App\Support;

/**
 * Утилитарный класс для сопоставления внутренних названий цветов
 * с классами Bootstrap и обратно.
 */
class ColorMapper
{
    /**
     * Карта сопоставления: "внутреннее имя" => "класс Bootstrap".
     *
     * @var array<string, string>
     */
    private static array $colorToBsClassMap = [
        'blue' => 'primary',
        'grey' => 'secondary',
        'green' => 'success',
        'red' => 'danger',
        'yellow' => 'warning',
        'cyan' => 'info',
        'black' => 'dark',
    ];

    /**
     * Преобразует внутреннее имя цвета в CSS-класс Bootstrap.
     *
     * @param string $color Внутреннее имя цвета (например, 'blue').
     * @return string CSS-класс Bootstrap (например, 'primary').
     */
    public static function colorToBsClass(string $color): string
    {
        return self::$colorToBsClassMap[$color] ?? 'secondary';
    }

    /**
     * Преобразует CSS-класс Bootstrap во внутреннее имя цвета.
     *
     * @param string $bsClass CSS-класс Bootstrap (например, 'primary').
     * @return string|null Внутреннее имя цвета (например, 'blue') или null, если класс не найден.
     */
    public static function bsClassToColor(string $bsClass): ?string
    {
        $map = array_flip(self::$colorToBsClassMap);
        return $map[$bsClass] ?? null;
    }

    /**
     * Возвращает список всех доступных внутренних имен цветов.
     *
     * @return string[]
     */
    public static function getColors(): array
    {
        return array_keys(self::$colorToBsClassMap);
    }

    /**
     * Возвращает полную карту сопоставления "внутреннее имя" => "класс Bootstrap".
     * Используется для передачи в JS.
     *
     * @return array<string, string>
     */
    public static function getColorToBsClassMap(): array
    {
        return self::$colorToBsClassMap;
    }

    /**
     * Возвращает полную карту сопоставления "класс Bootstrap" => "внутреннее имя".
     * Используется для передачи в JS.
     *
     * @return array<string, string>
     */
    public static function getBsClassToColorMap(): array
    {
        return array_flip(self::$colorToBsClassMap);
    }
}

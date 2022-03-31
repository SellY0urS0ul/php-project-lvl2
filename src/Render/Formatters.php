<?php

namespace Php\Project\Lvl2\Render\Formatters;

use function Php\Project\Lvl2\Render\Stylish\stylishFormatter;
use function Php\Project\Lvl2\Render\Plain\plainFormatter;
use function Php\Project\Lvl2\Render\Json\jsonFormatter;

//Функция, форматирующая полученные значения

function render(array $diff, string $format)
{
    switch ($format) {
        case 'stylish':
            $finalFormat = stylishFormatter($diff);
            break;
        case 'plain':
            $finalFormat = plainFormatter($diff);
            break;
        case 'json':
            $finalFormat = jsonFormatter($diff);
            break;
    }
    return $finalFormat;
}

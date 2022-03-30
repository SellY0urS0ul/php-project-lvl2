<?php

namespace Php\Project\Lvl2\Render\Formatters;

use function Php\Project\Lvl2\Render\Stylish\stylishFormatter;
use function Php\Project\Lvl2\Render\Plain\plainFormatter;
use function Php\Project\Lvl2\Render\Json\jsonFormatter;

//Функция, форматирующая полученные значения

function render($diff, $format)
{
    switch ($format) {
        case 'stylish':
            return stylishFormatter($diff);
            break;
        case 'plain':
            return plainFormatter($diff);
            break;
        case 'json':
            return jsonFormatter($diff);
            break;
    }
}

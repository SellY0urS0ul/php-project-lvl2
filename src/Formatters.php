<?php

namespace Php\Project\Lvl2\Formatters;

use Exception;

use function Php\Project\Lvl2\Render\Stylish\renderStylish;
use function Php\Project\Lvl2\Render\Plain\renderPlain;
use function Php\Project\Lvl2\Render\Json\renderJson;

//Функция, форматирующая полученные значения

function render(array $diff, string $format)
{
    switch ($format) {
        case 'stylish':
            return renderStylish($diff);
            break;
        case 'plain':
            return renderPlain($diff);
            break;
        case 'json':
            return renderJson($diff);
            break;
        default:
            throw new Exception('Нет такого формата');
    }
}

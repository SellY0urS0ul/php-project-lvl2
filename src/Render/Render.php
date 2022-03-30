<?php

namespace Php\Project\Lvl2\Render\Render;

// Функция, форматирующая полученные значения

function render($diff, $format)
{
    if (true) {
        return array_reduce($diff, function ($acc, $element) {
            $key = array_key_first($element);
            $name = $element[$key]["name"];
            $type = $element[$key]["type"];

            if ($name === true) {
                $name = 'true';
            }
            if ($name === false) {
                $name = 'false';
            }

            $acc = $acc . "{$type} {$key}: {$name}\n";
            return $acc;
        });
    }
    return $diff;
}
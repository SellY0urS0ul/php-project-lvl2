<?php

namespace Php\Project\Lvl2\Render\Stylish;

const TAB = 4;
const SYMBOLS_SPACE = 2;

function stylishFormatter(array $diff)
{
    $formatedDiff = makeStylishFormat($diff);
    return "{\n{$formatedDiff}}";
}

function makeStylishFormat(array $diff, int $depth = 1)
{
    $formatedDiff = array_map(function ($element) use ($depth) {

        if (!array_key_exists("Changed", $element)) {
            //Получение информации об узле
            $key = array_key_first($element);
            $value = $element[$key]["value"];
            $action = normalizeAction($element[$key]["action"]);
            $children = $element[$key]["children"];

            //Рассчет отступов в зависимости от глубины узла
            $currentTab = str_repeat(' ', ($depth * TAB - SYMBOLS_SPACE));

            //Рекурсивная обработка директорий
            $temp = '';
            if ($children !== []) {
                $temp = makeStylishFormat($children, $depth + 1);
                $temp = "{\n{$temp}{$currentTab}  }";
            }

            //Формирование финальной строки
            return "{$currentTab}{$action} {$key}: {$value}{$temp}\n";
        } else {
            $temp2 = makeStylishFormat($element, $depth);
            return $temp2;
        }
    }, $diff);
    return implode($formatedDiff);
    var_dump($formatedDiff);
}

function normalizeAction($action)
{
    switch ($action) {
        case 'Changed':
            $action = '-';
            break;
        case 'Unchanged':
            $action = ' ';
            break;
        case 'Added':
            $action = '+';
            break;
    }

    return $action;
}

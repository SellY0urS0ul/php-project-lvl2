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
    $formatedDiff = array_reduce($diff, function ($acc, $element) use ($depth) {

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
        $acc = $acc . "{$currentTab}{$action} {$key}: {$value}{$temp}\n";

        return $acc;
    });
    return $formatedDiff;
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

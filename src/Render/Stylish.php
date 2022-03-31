<?php

namespace Php\Project\Lvl2\Render\Stylish;

use Exception;

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

            if ($children !== []) {
                $childrenData = makeStylishFormat($children, $depth + 1);
                $childrens = "{\n{$childrenData}{$currentTab} }";
            } else {
                $childrens = '';
            }

            //Формирование финальной строки
            return "{$currentTab}{$action} {$key}: {$value}{$childrens}\n";
        } else {
            $temp2 = makeStylishFormat($element, $depth);
            return $temp2;
        }
    }, $diff);
    return implode($formatedDiff);
    var_dump($formatedDiff);
}

function normalizeAction(string $action): string
{
    switch ($action) {
        case 'Changed':
            $normalizedAction = '-';
            break;
        case 'Unchanged':
            $normalizedAction = ' ';
            break;
        case 'Added':
            $normalizedAction = '+';
            break;
        default:
            throw new Exception('Undefined action');
    }
    return $normalizedAction;
}

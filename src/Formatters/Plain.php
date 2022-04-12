<?php

namespace Php\Project\Lvl2\Render\Plain;

function renderPlain(array $diff)
{
    $formattedString = makePlainFormat($diff);
    return trim($formattedString);
}

function makePlainFormat(array $diff, string $path = '')
{
    $formatedDiff = array_map(function ($element) use ($path) {
        if (!array_key_exists("Changed", $element)) {
            //Получение информации об узле
            $key = array_key_first($element);
            $children = $element[$key]["children"];
            $value = ($children === []) ? formattingValue($element[$key]["value"]) : '[complex value]';
            $action = $element[$key]["action"];

            //Добавление нового элемента
            if ($action === "Added") {
                $finalStirng = "Property '{$path}{$key}' was added with value: {$value}\n";
            } elseif ($action === "Changed") {
                //Удаление элемента
                $finalStirng = "Property '{$path}{$key}' was removed\n";
            } else {
                //Рекурсивная обработка директорий
                $finalPath = "{$path}{$key}.";
                $finalStirng = makePlainFormat($children, $finalPath);
            }
        } else {
            //Обновление существующего элемента
            $key = array_key_first($element['Changed']);
            $oldChildren = $element['Changed'][$key]['children'];
            $newChildren = $element['Added'][$key]['children'];
            $newValue = ($newChildren === []) ? formattingValue($element['Added'][$key]['value']) : '[complex value]';
            $oldValue = ($oldChildren === []) ? formattingValue($element['Changed'][$key]['value']) : '[complex value]';
            $finalStirng = "Property '{$path}{$key}' was updated. From {$oldValue} to {$newValue}\n";
        }
        return $finalStirng;
    }, $diff);
    return implode($formatedDiff);
}

function formattingValue(mixed $value)
{
    $normalValue = stringifyValue($value);
    if ($normalValue !== 'false' && $normalValue !== 'true' && $normalValue !== 'null' && !is_numeric($normalValue)) {
        return "'{$normalValue}'";
    }
    return $normalValue;
}

//Функция, обрабатывающие значения bool и null
function stringifyValue(mixed $value)
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    } elseif ($value === null) {
        return 'null';
    } else {
        return $value;
    };
}

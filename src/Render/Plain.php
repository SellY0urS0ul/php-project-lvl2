<?php

namespace Php\Project\Lvl2\Render\Plain;

function plainFormatter(array $diff)
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
            $value = ($children === []) ? valueFormatter($element[$key]["value"]) : '[complex value]';
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
            $newValue = ($newChildren === []) ? valueFormatter($element['Added'][$key]['value']) : '[complex value]';
            $oldValue = ($oldChildren === []) ? valueFormatter($element['Changed'][$key]['value']) : '[complex value]';
            $finalStirng = "Property '{$path}{$key}' was updated. From {$oldValue} to {$newValue}\n";
        }
        return $finalStirng;
    }, $diff);
    return implode($formatedDiff);
}

function valueFormatter(mixed $value)
{
    if ($value !== 'false' && $value !== 'true' && $value !== 'null' && !is_numeric($value)) {
        $value = "'{$value}'";
    }
    return $value;
}

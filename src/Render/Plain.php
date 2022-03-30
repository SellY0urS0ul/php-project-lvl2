<?php

namespace Php\Project\Lvl2\Render\Plain;

function plainFormatter(array $diff)
{
    $formattedString = makePlainFormat($diff);
    return trim($formattedString);
}

function makePlainFormat(array $diff, $path = '')
{
    $formatedDiff = array_map(function ($element) use ($path, $diff) {
        if (!array_key_exists("Changed", $element)) {
            //Получение информации об узле
            $key = array_key_first($element);
            $children = $element[$key]["children"];
            $value = ($children === []) ? valueFormatter($element[$key]["value"]) : '[complex value]';
            $action = $element[$key]["action"];


            //Добавление нового элемента
            if ($action === "Added") {
                return "Property '{$path}{$key}' was added with value: {$value}\n";
            }

            //Удаление элемента
            if ($action === "Changed") {
                return "Property '{$path}{$key}' was removed\n";
            }

            //Рекурсивная обработка директорий
            if ($children !== []) {
                $path = "{$path}{$key}.";
                return makePlainFormat($children, $path);
            }
        } else {
            //Обновление существующего элемента
            $key = array_key_first($element['Changed']);
            $oldChildren = $element['Changed'][$key]['children'];
            $newChildren = $element['Added'][$key]['children'];
            $newValue = ($newChildren === []) ? valueFormatter($element['Added'][$key]['value']) : '[complex value]';
            $oldValue = ($oldChildren === []) ? valueFormatter($element['Changed'][$key]['value']) : '[complex value]';
            return "Property '{$path}{$key}' was updated. From {$oldValue} to {$newValue}\n";
        }
    }, $diff);
    return implode($formatedDiff);
}

function valueFormatter($value)
{
    if ($value !== 'false' && $value !== 'true' && $value !== 'null' && !is_numeric($value)) {
        $value = "'{$value}'";
    }
    return $value;
}

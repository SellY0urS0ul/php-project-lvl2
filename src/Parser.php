<?php

namespace Php\Project\Lvl2\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

// Функция, принимающая на вход путь к файлу и возвращающая массив с его содержимым

function parse(string $path): array
{
    if (!file_exists($path)) {
        throw new Exception("Invalid file path: {$path}");
    }

    $fileContent = file_get_contents($path);
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    if ($fileContent === false) {
        throw new \Exception("Can't read file: {$path}");
    }

    switch ($extension) {
        case "json":
            return json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
        case "yaml":
            return Yaml::parse($fileContent);
        default:
            throw new Exception("Format {$extension} not supported.");
    }
}

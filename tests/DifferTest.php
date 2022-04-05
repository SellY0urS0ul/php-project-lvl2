<?php

namespace Php\Project\Lvl2\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testDiff(): void
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expectedPlain.txt");
        $firstFile = __DIR__ . "/fixtures/file1.yaml";
        $secondFile = __DIR__ . "/fixtures/file2.yaml";
        $result = genDiff($firstFile, $secondFile, 'plain');
        $this->assertEquals($expected, $result);

        $expected = file_get_contents(__DIR__ . "/fixtures/expectedStylish.txt");
        $firstFile = __DIR__ . "/fixtures/file1.yaml";
        $secondFile = __DIR__ . "/fixtures/file2.yaml";
        $result = genDiff($firstFile, $secondFile);
        $this->assertEquals($expected, $result);

        $expected = file_get_contents(__DIR__ . "/fixtures/expectedJson.txt");
        $firstFile = __DIR__ . "/fixtures/file1.yaml";
        $secondFile = __DIR__ . "/fixtures/file2.yaml";
        $result = genDiff($firstFile, $secondFile, 'json');
        $this->assertEquals($expected, $result);

        $expected = file_get_contents(__DIR__ . "/fixtures/expectedJson2.txt");
        $firstFile = __DIR__ . "/fixtures/file1.json";
        $secondFile = __DIR__ . "/fixtures/file2.json";
        $result = genDiff($firstFile, $secondFile, 'json');
        $this->assertEquals($expected, $result);
    }
}

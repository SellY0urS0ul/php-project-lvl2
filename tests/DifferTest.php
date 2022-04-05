<?php

namespace Php\Project\Lvl2\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\makeDiff;

class DifferTest extends TestCase
{
    public function testDiff(): void
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expectedPlain.txt");
        $firstFile = __DIR__ . "/fixtures/file1.yaml";
        $secondFile = __DIR__ . "/fixtures/file2.yaml";
        $result = makeDiff($firstFile, $secondFile, 'plain');
        $this->assertEquals($expected, $result);

        $expected = file_get_contents(__DIR__ . "/fixtures/expectedStylish.txt");
        $firstFile = __DIR__ . "/fixtures/file1.yaml";
        $secondFile = __DIR__ . "/fixtures/file2.yaml";
        $result = makeDiff($firstFile, $secondFile);
        $this->assertEquals($expected, $result);

        $expected = file_get_contents(__DIR__ . "/fixtures/expectedJson.txt");
        $firstFile = __DIR__ . "/fixtures/file1.yaml";
        $secondFile = __DIR__ . "/fixtures/file2.yaml";
        $result = makeDiff($firstFile, $secondFile, 'json');
        $this->assertEquals($expected, $result);
    }
}

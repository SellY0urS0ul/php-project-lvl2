<?php

namespace Differ\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider diffProvider
     */
    public function testDiff($expected, $options): void
    {
        $result = genDiff(...$options);
        $this->assertEquals($expected, $result);
    }

    public function diffProvider(): array
    {
        $expected1 = file_get_contents(__DIR__ . "/fixtures/expectedPlain.txt");
        $expected2 = file_get_contents(__DIR__ . "/fixtures/expectedStylish.txt");
        $expected3 = file_get_contents(__DIR__ . "/fixtures/expectedJson.txt");
        $expected4 = file_get_contents(__DIR__ . "/fixtures/expectedJson2.txt");

        $firstYaml = __DIR__ . "/fixtures/file1.yaml";
        $secondYaml = __DIR__ . "/fixtures/file2.yaml";
        $firstJson = __DIR__ . "/fixtures/file1.json";
        $secondJson = __DIR__ . "/fixtures/file2.json";

        $result1 = [$firstYaml, $secondYaml, 'plain'];
        $result2 = [$firstYaml, $secondYaml];
        $result3 = [$firstYaml, $secondYaml, 'json'];
        $result4 = [$firstJson, $secondJson, 'json'];

        return  [
            [$expected1, $result1], [$expected2, $result2], [$expected3, $result3], [$expected4, $result4]
        ];
    }
}

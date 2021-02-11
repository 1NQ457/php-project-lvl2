<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Parser\getData;
use function Differ\Differ\gendiff;

class DifferTest extends TestCase
{
    public function additionProvider()
    {
        return [
            ['./tests/fixtures/before.json', './tests/fixtures/after.json', './tests/fixtures/diffStylish', 'stylish'],
            ['./tests/fixtures/before.yml', './tests/fixtures/after.yml', './tests/fixtures/diffPlain', 'plain'],
            ['./tests/fixtures/before.json', './tests/fixtures/after.yml', './tests/fixtures/diffjson', 'json']
        ];
    }

    /**
     * @dataProvider additionProvider
     */

    public function testGendiff($before, $after, $pathToExpected, $format)
    {
        $expected = file_get_contents($pathToExpected);
        $actual = genDiff($before, $after, $format);
        $this->assertEquals($expected, $actual);
    }
}

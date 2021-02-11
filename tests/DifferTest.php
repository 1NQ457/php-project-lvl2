<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Parser\getData;
use function GenDiff\Differ\gendiff;

class DifferTest extends TestCase
{
    public function additionProvider()
    {
        return [
            ['./tests/fixtures/before.json', './tests/fixtures/after.json', './tests/fixtures/diffStylish', 'stylish'],
            ['./tests/fixtures/before.yml', './tests/fixtures/after.yml', './tests/fixtures/diffPlain', 'plain'],
            ['./tests/fixtures/before.json', './tests/fixtures/after.yml', './tests/fixtures/diffPlain', 'plain']
        ];
    }

    /**
     * @dataProvider additionProvider
     */

    public function testGendiff($before, $after, $pathToExpected, $format)
    {
        $expected = file_get_contents($pathToExpected);
        $actual = gendiff($before, $after, $format);
        $this->assertEquals($expected, $actual);
    }
}

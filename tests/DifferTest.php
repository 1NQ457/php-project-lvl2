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
            ['./tests/fixtures/before.json', './tests/fixtures/after.json'],
            ['./tests/fixtures/before.yml', './tests/fixtures/after.yml'],
            ['./tests/fixtures/before.json', './tests/fixtures/after.yml'],
        ];
    }

    /**
     * @dataProvider additionProvider
     */

    public function testGendiff($before, $after)
    {
        $expected = file_get_contents('./tests/fixtures/diffStylish');
        $actual = gendiff($before, $after);
        $this->assertEquals($expected, $actual);
    }
}

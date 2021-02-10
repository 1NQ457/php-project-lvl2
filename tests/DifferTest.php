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

    public function testGetData($before, $after)
    {
        $expected = [
            [
                "follow" => false,
                "host" => "hexlet.io",
                "proxy" => "123.234.53.22",
                "timeout" => 50],
            [
                "host" => "hexlet.io",
                "timeout" => 20,
                "verbose" => true]
        ];

        $actual = getData($before, $after);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider additionProvider
     */

    public function testGendiff($before, $after)
    {
        $expected = "{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}
";
        $actual = gendiff($before, $after);
        $this->assertEquals($expected, $actual);
    }
}

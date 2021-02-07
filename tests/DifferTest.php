<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Differ\parser;
use function GenDiff\Differ\differ;
use function GenDiff\Differ\gendiff;

class DifferTest extends TestCase
{
    public function testParser()
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

        $actual = parser('./tests/fixtures/before1.json', './tests/fixtures/after1.json');

        $this->assertEquals($expected, $actual);
    }

    public function testDiffer()
    {
        $expected = [
            "- follow" => false,
            "  host" => "hexlet.io",
            "- proxy" => "123.234.53.22",
            "- timeout" => 50,
            "+ timeout" => 20,
            "+ verbose" => true
        ];

        [$before, $after] = parser('./tests/fixtures/before1.json', './tests/fixtures/after1.json');
        $actual = differ($before, $after);

        $this->assertEquals($expected, $actual);
    }

    public function testGendiff()
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

        $actual = gendiff('./tests/fixtures/before1.json', './tests/fixtures/after1.json');

        $this->assertEquals($expected, $actual);
    }
}

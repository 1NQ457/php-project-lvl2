<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Differ\parser;
use function GenDiff\Differ\getAdded;
use function GenDiff\Differ\getRemoved;
use function GenDiff\Differ\getStill;
use function GenDiff\Differ\getKeys;
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

    public function testGetAdded()
    {
        $expected = [
            'timeout' => 20,
            'verbose' => true
        ];

        [$before, $after] = parser('./tests/fixtures/before1.json', './tests/fixtures/after1.json');
        $actual = getAdded($before, $after);

        $this->assertEquals($expected, $actual);
    }

    public function testGetRemoved()
    {
        $expected = [
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false
        ];

        [$before, $after] = parser('./tests/fixtures/before1.json', './tests/fixtures/after1.json');
        $actual = getRemoved($before, $after);

        $this->assertEquals($expected, $actual);
    }

    public function testGetStill()
    {
        $expected = ['host' => 'hexlet.io'];

        [$before, $after] = parser('./tests/fixtures/before1.json', './tests/fixtures/after1.json');
        $actual = getStill($before, $after);

        $this->assertEquals($expected, $actual);
    }

    public function testGetKeys()
    {
        $expected = ['follow', 'host', 'proxy', 'timeout', 'verbose'];

        [$before, $after] = parser('./tests/fixtures/before1.json', './tests/fixtures/after1.json');
        $actual = getKeys($before, $after);

        $this->assertEquals($expected, $actual);
    }

    public function testGendiff()
    {
        $expected = "{
    - follow: 
      host: hexlet.io
    - proxy: 123.234.53.22
    - timeout: 50
    + timeout: 20
    + verbose: 1
}
";
        gendiff('./tests/fixtures/before1.json', './tests/fixtures/after1.json');
        $actual = $this->getActualOutput();
        $this->assertEquals($expected, $actual);
    }
}

<?php

namespace Gendiff\Cli;

use Docopt;

const DOC = <<<'DOCOPT'
Generate diff

Usage:
gendiff (-h|--help)
gendiff (-v|--version)
gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
-h --help                     Show this screen
-v --version                  Show version
--format <fmt>                Report format [default: stylish]
DOCOPT;

function run()
{
    $result = Docopt::handle(DOC, ['version' => '0.1.0']);
    foreach ($result as $k => $v) {
        echo $k . ': ' . json_encode($v) . PHP_EOL;
    }
}

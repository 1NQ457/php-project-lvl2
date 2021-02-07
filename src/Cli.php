<?php

namespace Gendiff\Cli;

use Docopt;

use function Gendiff\Differ\genDiff;

function run($doc)
{
    $args = \Docopt::handle($doc, ['version' => 'GenDiff. Version 0.6.0']);

    $diff = genDiff($args['<firstFile>'], $args['<secondFile>']);
    echo $diff;
}

<?php

namespace GenDiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parse($pathToFile)
{
    $rawData = file_get_contents($pathToFile);
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $parsers = [
        'json' =>
            fn ($rawData) => json_decode($rawData, true),
        'yml' =>
            fn ($rawData) => Yaml::parse($rawData)
    ];
    return $parsers[$extension]($rawData);
}

function getData($pathToBefore, $pathToAfter)
{
    return [parse($pathToBefore), parse($pathToAfter)];
}

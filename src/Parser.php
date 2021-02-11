<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse($pathToFile): object
{
    $rawData = file_get_contents($pathToFile);
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $parsers = [
        'json' =>
            fn ($rawData) => json_decode($rawData),
        'yml' =>
            fn ($rawData) => Yaml::parse($rawData, Yaml::PARSE_OBJECT_FOR_MAP),
        'yaml' =>
            fn ($rawData) => Yaml::parse($rawData, Yaml::PARSE_OBJECT_FOR_MAP)
    ];
    return $parsers[$extension]($rawData);
}

function getData($pathToBefore, $pathToAfter): array
{
    return [parse($pathToBefore), parse($pathToAfter)];
}

<?php

namespace Gendiff\Differ;

function genDiff($pathToBefore, $pathToAfter)
{
    $rawBefore = file_get_contents($pathToBefore);
    $rawAfter = file_get_contents($pathToAfter);

    $result = [];

    $before = json_decode($rawBefore, true);
    $after = json_decode($rawAfter, true);
    ksort($before);
    ksort($after);

    foreach ($before as $key => $value) {
        if (isset($after[$key])) {
            if ($after[$key] == $value) {
                $result["  {$key}"] = $value;
            } else {
                $result["- {$key}"] = $value;
                $result["+ {$key}"] = $after[$key];
            }
        } else {
            $result["- {$key}"] = $value;
        }
    }
    foreach ($after as $key => $value) {
        if (!(isset($before[$key]))) {
            $result["+ {$key}"] = $value;
        }
    }

    $output = "{\n";

    foreach ($result as $key => $value) {
        if ($value === true) {
            $output .= "    {$key}: true\n";
        } elseif ($value === false) {
            $output .= "    {$key}: false\n";
        } else {
            $output .= "    {$key}: {$value}\n";
        }
    }

    $output .= "}\n";

    echo $output;
}

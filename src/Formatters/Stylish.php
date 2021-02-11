<?php

namespace Gendiff\Formatters\Stylish;

use function Funct\Collection\flattenAll;
use function Gendiff\Tree\getName;
use function Gendiff\Tree\getType;
use function Gendiff\Tree\getOldValue;
use function Gendiff\Tree\getNewValue;
use function Gendiff\Tree\getChildren;

function boolToStr($value)
{
    if (is_bool($value)) {
        if ($value === true) {
            return 'true';
        }
        return 'false';
    }
    return $value;
}

function strFormat($value, $tab = '')
{
    if (!is_object($value)) {
        return boolToStr($value);
    }
    $arr = (array) ($value);
    $result = implode('', array_map(function ($key, $value) use ($tab) {
        return "\n" . $tab . "    {$key}: " . strFormat($value, $tab . '    ');
    }, array_keys($arr), $arr));
    return '{' . $result . "\n" . $tab . '}';
}

function makeOutput($tree, $tab = '')
{
    $addedSpace = "    ";
    return array_reduce($tree, function ($result, $node) use ($tab, $addedSpace) {
        $name = getName($node);
        $type = getType($node);

        switch ($type) {
            case 'added':
                $newValue = getNewValue($node);
                $result[] = $tab . "  + {$name}: " . strFormat($newValue, $tab . $addedSpace);
                break;
            case 'removed':
                $oldValue = getOldValue($node);
                $result[] = $tab . "  - {$name}: " . strFormat($oldValue, $tab . $addedSpace);
                break;
            case 'updated':
                $oldValue = getOldValue($node);
                $newValue = getNewValue($node);
                $result[] = $tab . "  - {$name}: " . strFormat($oldValue, $tab . $addedSpace);
                $result[] = $tab . "  + {$name}: " . strFormat($newValue, $tab . $addedSpace);
                break;
            case 'notChanged':
                $value = getOldValue($node);
                $result[] = $tab . "    {$name}: " . strFormat($value, $tab . $addedSpace);
                break;
            case 'nested':
                $children = getChildren($node);
                $result[] = $tab . "    {$name}: {";
                $result[] = makeOutput($children, $tab . $addedSpace);
                $result[] = $tab . '    }';
                break;
        };
        return flattenAll($result);
    }, []);
}

function stylishOutput($tree)
{
    $output = makeOutput($tree);
    $result = implode("\n", $output);
    return "{\n" . $result . "\n}\n";
}

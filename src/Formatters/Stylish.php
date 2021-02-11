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
    return array_reduce($tree, function ($result, $node) use ($tab) {
        $name = getName($node);
        $type = getType($node);
        switch ($type) {
            case 'added':
                $result[] = $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ");
                break;
            case 'removed':
                $result[] = $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                break;
            case 'updated':
                $result[] = $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                $result[] = $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ");
                break;
            case 'notChanged':
                $result[] = $tab . "    {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                break;
            case 'nested':
                $result[] = $tab . "    {$name}: {";
                $result[] = makeOutput(getChildren($node), $tab . "    ");
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

<?php

namespace Differ\Tree;

function makeLeaf($name, $type, $oldValue, $newValue): array
{
    return [
        "name" => $name,
        "type" => $type,
        "oldValue" => $oldValue,
        "newValue" => $newValue
    ];
}

function makeNode(string $name, string $type, $children): array
{
    return [
        "name" => $name,
        "type" => $type,
        "children" => $children
    ];
}

function getName($node): string
{
    return $node['name'];
}

function getType($node): string
{
    return $node['type'];
}

function getValue($node): array
{
    return [
        'old' => $node['oldValue'],
        'new' => $node['newValue']
    ];
}

function getChildren($node): array
{
    return $node['children'];
}

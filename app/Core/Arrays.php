<?php

namespace Solmer\Storage\Core;

class Arrays
{
    public static function arrayFind(
        string $key,
        mixed $value,
        array $array,
    ): mixed {
        foreach ($array as $arr) {
            if ($arr[$key] == $value) {
                return $arr;
            }
        }
        return false;
    }
}

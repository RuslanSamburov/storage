<?php

namespace Solmer\Storage\Core;

class Math
{
    public static function getProcent(int $min, int $max): int
    {
        return ($min / $max) * 100;
    }
}

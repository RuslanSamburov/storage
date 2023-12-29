<?php

namespace Storage\Storage\Core;

class Math
{
    public static function getProcent(int $min, int $max): int // Не создано Chat-GPT :)
    {
        return ($min / $max) * 100;
    }
}

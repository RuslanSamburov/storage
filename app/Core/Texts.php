<?php

namespace Solmer\Storage\Core;

class Texts
{
    public static function getFragmentPath(string $fragment): string
    {
        global $basePath;
        return path_app('/Application/Views/' . $fragment . '.inc.php');
    }

    public static function generateSymbols(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public static function getTxt(string $txt): string|bool
    {
        global $basePath;
        $src = $basePath . 'txts/' . $txt . '.txt';
        return file_get_contents($src) ?? false;
    }

    public static function renderText(string $template, array $values): string
    {
        $patterns = [];
        $vals = [];
        foreach ($values as $key => $value) {
            $patterns[] = '/%' . $key . '%/iu';
            $vals[] = $value;
        }
        return preg_replace($patterns, $vals, $template);
    }
}

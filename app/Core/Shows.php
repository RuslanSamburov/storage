<?php

namespace Solmer\Storage\Core;

class Shows
{
    public static function showErrors(string $fld_name, array $form_data): void
    {
        if (isset($form_data['__errors'][$fld_name])) {
            echo '<p class="my-2 result-box result-box-error">' . $form_data['__errors'][$fld_name] . '</p>';
        }
    }

    public static function showResults(string $fld_name, array $form_data): void
    {
        if (isset($form_data['__results'][$fld_name])) {
            echo '<p class="my-2 result-box result-box-result">' . $form_data['__results'][$fld_name] . '</p>';
        }
    }
}

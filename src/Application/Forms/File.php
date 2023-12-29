<?php

namespace Storage\Storage\Application\Forms;

use Storage\Storage\Core\Storage;
use Storage\Storage\Core\Form;

class File extends Form
{
    protected static function after_normalize_data(array &$data, array &$errors, &$results): void
    {
        $file = $_FILES['file'];
        $error = $file['error'];
        if ($error == UPLOAD_ERR_NO_FILE) {
            $errors['file'] = 'Загрузите файл';
        } else {
            if (Storage::getMyFreeSize() < $file['size']) {
                $errors['file'] = 'Недостаточно места';
            }
        }
    }
}

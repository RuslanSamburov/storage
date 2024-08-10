<?php

namespace Solmer\Storage\Application\Forms;

use Solmer\Storage\Core\{Storage, Form};

class File extends Form
{
    protected static function afterNormalizeData(
        array &$data,
        array &$errors,
        &$results,
    ): void {
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

<?php

namespace Solmer\Storage\Application\Forms;

use Solmer\Storage\Application\Models\Storages;
use Solmer\Storage\Core\{Storage, Form};

class DeleteFile extends Form
{
    protected const FIELDS = [
        'filename' => [
            'type' => 'string',
        ],
    ];

    protected static function afterNormalizeData(
        array &$data,
        array &$errors,
        &$results,
    ): void {
        $storage = Storage::getStorage($data['filename'], 'name');
        $file = Storage::fileExists($storage['file']);
        if (!$file) {
            $errors['filename'] = 'Файл не найден';
            if ($storage) {
                $storages = new Storages();
                $storages->delete($storage['id']);
            }
        }
    }
}

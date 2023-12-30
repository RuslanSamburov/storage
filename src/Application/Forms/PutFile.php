<?php

namespace Storage\Storage\Application\Forms;

use Storage\Storage\Application\Models\Storages;
use Storage\Storage\Core\{Storage, Form};

class PutFile extends Form
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

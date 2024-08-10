<?php

namespace Solmer\Storage\Application\Models;

use Solmer\Storage\Core\{
    Account,
    Arrays,
    Model,
    Storage,
};

class Storages extends Model
{
    protected const TABLE_NAME = "storages";

    protected const RELATIONS = [
        'users' => [
            'primary' => 'id',
            'external' => 'user_id',
        ],
        'type_storage' => [
            'primary' => 'id',
            'external' => 'type',
        ],
    ];

    protected function beforeInsert(array &$fields): void
    {
        $file = $_FILES['file'];
        $name = Storage::generateName();
        $filename = Storage::getFilename($file['name']);
        $fields['user_id'] = Account::getCurrentUser();
        $fields['name'] = $name;
        $fields['file'] = $filename;
        Storage::upload($file, $filename);
    }

    protected function beforeDelete(string $value, string $key_field = 'id'): void
    {
        $storage = Storage::getStorage($value, 'name');
        if (!$storage) {
            return;
        }
        Storage::delete($storage['file']);
    }

    protected function beforeUpdate(
        array &$fields,
        string $value,
        string $key_field = 'id'
    ): void {
        $storage = Storage::getStorage($value, 'name');
        $typesStorage = new TypesStorage();
        $types = $typesStorage->getAll('id');
        $type = Arrays::arrayFind('id', ++$storage['type'], $types);
        if (!$type) {
            $type = $types[0]['id'];
        } else {
            $type = $type['id']++;
        }
        $fields['type'] = $type;
    }
}

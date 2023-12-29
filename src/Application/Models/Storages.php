<?php

namespace Storage\Storage\Application\Models;

use Storage\Storage\Core\Helpers;
use Storage\Storage\Core\Account;
use Storage\Storage\Core\Model;
use Storage\Storage\Core\Storage;

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
        ]
    ];

    protected function before_insert(array &$fields): void
    {
        $file = $_FILES['file'];
        $name = Storage::generateName();
        $filename = Storage::get_filename($file['name']);
        $fields['user_id'] = Account::getCurrentUser();
        $fields['name'] = $name;
        $fields['file'] = $filename;
        Storage::upload($file, $filename);
    }

    protected function before_delete(string $value, string $key_field = 'id'): void
    {
        $storage = Storage::getStorage($value, 'name');
        if (!$storage) {
            return;
        }
        Storage::delete($storage['file']);
    }

    protected function before_update(
        array &$fields,
        string $value,
        string $key_field = 'id'
    ): void {
        $storage = Storage::getStorage($value, 'name');
        $typesStorage = new TypesStorage();
        $types = $typesStorage->get_all('id');
        $type = Helpers::array_find('id', ++$storage['type'], $types);
        print_r($types);
        if (!$type) {
            $type = $types[0]['id'];
        } else {
            $type = $type['id']++;
        }
        $fields['type'] = $type;
    }
}

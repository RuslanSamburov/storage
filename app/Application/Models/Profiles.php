<?php

namespace Solmer\Storage\Application\Models;

use Solmer\Storage\Core\Model;

class Profiles extends Model
{
    protected const TABLE_NAME = "profiles";

    protected const RELATIONS = [
        'users' => [
            'primary' => 'id',
            'external' => 'user_id',
        ],
        'bytes' => [
            'primary' => 'id',
            'external' => 'byte',
        ],
    ];
}

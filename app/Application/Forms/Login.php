<?php

namespace Solmer\Storage\Application\Forms;

use Solmer\Storage\Core\{Form, Response};
use Solmer\Storage\Application\Models\Users;

class Login extends Form
{
    protected const FIELDS = [
        'email' => [
            'type' => 'string',
        ],
        'password' => [
            'type' => 'string',
        ],
    ];

    public static function verifyUser(array &$data): int|false
    {
        $errors = [];
        $users = new Users();
        $user = $users->get($data['email'], 'email');
        if (!$user) {
            $errors['email'] = 'Пользователь не найден';
        } else {
            if (!password_verify($data['password'], $user['password'])) {
                $errors['password'] = 'Неверный пароль';
            } else {
                if (!$user['is_active']) {
                    Response::redirect('/activation');
                }
                return $user['id'];
            }
        }
        $data['__errors'] = $errors;
        return false;
    }
}

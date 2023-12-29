<?php

namespace Storage\Storage\Application\Forms;

use Storage\Storage\Core\Form;

use Storage\Storage\Application\Models\Users;
use Storage\Storage\Core\Response;

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

    public static function verify_user(array &$data): int|false
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

<?php

namespace Solmer\Storage\Application\Forms;

use Solmer\Storage\Core\{
    Form,
    Password,
    Texts,
};
use Solmer\Storage\Application\Models\Users;

class Register extends Form
{
    protected const FIELDS = [
        'email' => [
            'type' => 'string',
        ],
        'password' => [
            'type' => 'string',
        ],
        'password2' => [
            'type' => 'string',
            'nosave' => true,
        ],
    ];

    protected static function afterNormalizeData(
        array &$data,
        array &$errors,
        array &$results,
    ): void {
        $users = new Users();
        $user = $users->get($data['email'], 'email', 'id');
        if (!preg_match('/[@]/', $data['email'])) {
            $errors['email'] = 'Введите адрес почты';
        } else {
            if (!Password::checkLength($data['password'])) {
                $errors['password'] = 'Длина пароля должна быть более 8-и символов';
            } else {
                if ($user) {
                    $errors['email'] = 'Пользователь с такой почтой уже существует';
                } else {
                    if (!Password::checkPasswords($data['password'], $data['password2'])) {
                        $errors['password2'] = 'Пароли отличаются';
                    }
                }
            }
        }
    }

    protected static function afterPrepareData(array &$data, array &$norm_data): void
    {
        $data['password'] = Password::passwordHash($norm_data['password2']);
        $data['token'] = Texts::generateSymbols();
    }
}

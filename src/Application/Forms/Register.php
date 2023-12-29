<?php

namespace Storage\Storage\Application\Forms;

use Storage\Storage\Core\Form;

use Storage\Storage\Core\Helpers;

use Storage\Storage\Application\Models\Users;
use Storage\Storage\Core\Password;

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

    protected static function after_normalize_data(array &$data, array &$errors, array &$results): void
    {
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

    protected static function after_prepare_data(array &$data, array &$norm_data): void
    {
        $data['password'] = Password::passwordHash($norm_data['password2']);
        $data['token'] = Helpers::generateSymbols();
    }
}

<?php

namespace Solmer\Storage\Application\Forms;

use Solmer\Storage\Core\{
    Form,
    Account,
    Password,
};

class ResetPassword extends Form
{
    protected const FIELDS = [
        'old_password' => [
            'type' => 'string',
            'nosave' => true,
        ],
        'password' => [
            'type' => 'string',
        ],
        'password2' => [
            'type' => 'string',
            'nosave' => true,
        ],
    ];

    public static function afterNormalizeData(
        array &$data,
        array &$errors,
        array &$results,
    ): void {
        $user = Account::getUser(Account::getCurrentUser(), 'id', 'password');
        if (!Password::checkLength($data['password'])) {
            $errors['password'] = 'Длина пароля должна быть более 8-и символов';
        } else {
            if (Password::checkPasswords($data['old_password'], $data['password'])) {
                $errors['password'] = 'Пароль должен отличаться от предыдущего';
            } else {
                if (!Password::checkPasswords($data['password'], $data['password2'])) {
                    $errors['password2'] = 'Пароли отличаются';
                } else {
                    if (!password_verify($data['old_password'], $user['password'])) {
                        $errors['old_password'] = 'Неверный пароль';
                    } else {
                        $results['message'] = 'Пароль изменен';
                    }
                }
            }
        }
    }

    protected static function afterPrepareData(array &$data, array &$norm_data): void
    {
        $data['password'] = Password::passwordHash($norm_data['password2']);
    }
}

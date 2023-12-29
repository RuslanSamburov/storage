<?php

namespace Storage\Storage\Application\Controllers;

use Storage\Storage\Core\BaseController;
use Storage\Storage\Core\Account;
use Storage\Storage\Core\Request;

use Storage\Storage\Application\Forms\Register;
use Storage\Storage\Application\Forms\Login as LoginForm;

use Storage\Storage\Application\Models\Users;
use Storage\Storage\Core\Auth;
use Storage\Storage\Core\Response;

class Login extends BaseController
{

    public function login(): void
    {
        if (Auth::auth()) {
            Response::redirect('/');
            return;
        }

        if (Request::is_post()) {
            $formLogin = LoginForm::get_normalized_data($_POST);
            if (!isset($formLogin['__errors'])) {
                $formLogin = LoginForm::get_prepared_data($formLogin);
                $user_id = LoginForm::verify_user($formLogin);
                if ($user_id) {
                    Account::setUser($user_id);
                    if (Auth::is_user_active()) {
                        Response::redirect('/');
                    }
                }
            }
        } else {
            $formLogin = LoginForm::get_initial_data([]);
        }
        $ctx = [
            'form' => $formLogin,
        ];
        $this->render('login', $ctx);
    }

    public function register(): void
    {
        if (Auth::auth()) {
            Response::redirect('/');
            return;
        }

        if (Request::is_post()) {
            $formRegister = Register::get_normalized_data($_POST);
            if (!isset($formRegister['__errors'])) {
                $formRegister = Register::get_prepared_data($formRegister);
                $users = new Users();
                $id = $users->insert($formRegister);
                Account::setUser($id);
                Account::activationSend($formRegister['email'], $id, $formRegister['token']);
                Response::redirect('/activation');
                return;
            }
        } else {
            $formRegister = Register::get_initial_data([]);
        }
        $ctx = [
            'form' => $formRegister,
        ];
        $this->render('register', $ctx);
    }

    public function logout(): void
    {
        if (!Auth::auth()) {
            Response::redirect('/login');
            return;
        }

        Account::logout();
    }

    public function activation(): void
    {
        if (!Auth::auth_no_active()) {
            Response::redirect('/login');
            return;
        }

        if (Request::is_put()) {
            $user = Account::getUser(Account::getCurrentUser());
            Account::activationSend($user['email'], $user['id'], $user['token']);
            Response::redirect('/activation');
            return;
        }
        $this->render('activation');
    }

    public function activationUser(int $id, string $token): void
    {
        if (!Auth::auth_no_active()) {
            Response::redirect('/login');
            return;
        }

        $user = Account::getUser($id);

        if (!$user || $user['token'] != $token) {
            Response::redirect('/login');
            return;
        }

        $users = new Users();
        $users->update(['is_active' => 1], $id);

        Response::redirect('/');
    }
}

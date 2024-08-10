<?php

namespace Solmer\Storage\Application\Controllers;

use Solmer\Storage\Core\{
    BaseController,
    Account,
    Request,
    Auth,
    Response,
};
use Solmer\Storage\Application\Forms\{Register, Login as LoginForm};
use Solmer\Storage\Application\Models\Users;

class Login extends BaseController
{

    public function login(): void
    {
        if (Auth::auth()) {
            Response::redirect('/');
            return;
        }

        if (Request::isPost()) {
            $formLogin = LoginForm::getNormalizedData($_POST);
            if (!isset($formLogin['__errors'])) {
                $formLogin = LoginForm::getPreparedData($formLogin);
                $user_id = LoginForm::verifyUser($formLogin);
                if ($user_id) {
                    Account::setUser($user_id);
                    if (Auth::isUserActive()) {
                        Response::redirect('/');
                    }
                }
            }
        } else {
            $formLogin = LoginForm::getInitialData([]);
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

        if (Request::isPost()) {
            $formRegister = Register::getNormalizedData($_POST);
            if (!isset($formRegister['__errors'])) {
                $formRegister = Register::getPreparedData($formRegister);
                $users = new Users();
                $id = $users->insert($formRegister);
                Account::setUser($id);
                Account::activationSend($formRegister['email'], $id, $formRegister['token']);
                Response::redirect('/activation');
                return;
            }
        } else {
            $formRegister = Register::getInitialData([]);
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
        if (!Auth::authNoActive()) {
            Response::redirect('/login');
            return;
        }

        if (Request::isPut()) {
            $user = Account::getUser(Account::getCurrentUser());
            Account::activationSend($user['email'], $user['id'], $user['token']);
            Response::redirect('/activation');
            return;
        }
        $this->render('activation');
    }

    public function activationUser(int $id, string $token): void
    {
        if (!Auth::authNoActive()) {
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

<?php

namespace Storage\Storage\Application\Controllers;

use Storage\Storage\Application\Forms\DeleteFile;
use Storage\Storage\Application\Forms\File;
use Storage\Storage\Application\Forms\PutFile;
use Storage\Storage\Application\Forms\ResetPassword;
use Storage\Storage\Application\Models\Storages;
use Storage\Storage\Application\Models\Users;
use Storage\Storage\Application\Settings;
use Storage\Storage\Core\BaseController;
use Storage\Storage\Core\Account;
use Storage\Storage\Core\Auth;
use Storage\Storage\Core\Request;
use Storage\Storage\Core\Response;
use Storage\Storage\Core\Storage as CoreStorage;

class Storage extends BaseController
{
    public function index(): void
    {
        if (!Auth::auth()) {
            Response::redirect('/login');
            return;
        }

        $file = File::get_initial_data();
        if (Request::is_post()) {
            $file = File::get_normalized_data();
            if (!isset($file['__errors'])) {
                $storages = new Storages();
                $storages->insert();
                Response::redirect('/');
                return;
            }
        }

        if (Request::is_delete()) {
            $file = DeleteFile::get_normalized_data($_POST);
            if (!isset($file['__errors'])) {
                $file = DeleteFile::get_prepared_data($file);
                $storages = new Storages();
                $storages->delete(Request::post('filename'), 'name');
                Response::redirect('/');
                return;
            }
        }

        if (Request::is_put()) {
            $file = PutFile::get_normalized_data($_POST);
            if (!isset($file['__errors'])) {
                $file = DeleteFile::get_prepared_data($file);
                $storages = new Storages();
                $storages->update([], $file['filename'], 'name');
                Response::redirect('/');
                return;
            }
        }

        $ctx = [
            'file' => $file,
        ];

        $this->render('index', $ctx);
    }

    public function password(): void
    {
        if (!Auth::auth()) {
            Response::redirect('/login');
            return;
        }

        if (Request::is_put()) {
            $formResetPassword = ResetPassword::get_normalized_data($_POST);
            if (!isset($formResetPassword['__errors'])) {
                $update = ResetPassword::get_prepared_data($formResetPassword);
                $users = new Users();
                $users->update($update, Account::getCurrentUser());
            }
        } else {
            $formResetPassword = ResetPassword::get_initial_data();
        }

        $ctx = [
            'form' => $formResetPassword,
        ];

        $this->render('password', $ctx);
    }

    public function download(string $name): void
    {
        $filestorage = CoreStorage::getStorage($name, 'name');
        $user_id = $filestorage['user_id'] ?? null;
        if (
            !$user_id
            || ($filestorage['type'] == Settings::TYPES_STORAGE_PRIVATE && $user_id != Account::getCurrentUser())
            || $filestorage['type'] != Settings::TYPES_STORAGE_PUBLIC
        ) {
            Response::redirect('/');
            return;
        }
        CoreStorage::download($user_id, $filestorage['file']);
    }
}

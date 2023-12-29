<?php

namespace Storage\Storage\Application\Controllers;

use Storage\Storage\Application\Forms\{
    DeleteFile,
    File,
    PutFile,
    ResetPassword,
};
use Storage\Storage\Application\Models\{Storages, Users};
use Storage\Storage\Application\Settings;
use Storage\Storage\Core\{
    BaseController,
    Account,
    Auth,
    Request,
    Response,
    Storage as CoreStorage,
};

class Storage extends BaseController
{
    public function index(): void
    {
        if (!Auth::auth()) {
            Response::redirect('/login');
            return;
        }

        $file = File::getInitialData();
        if (Request::isPost()) {
            $file = File::getNormalizedData();
            if (!isset($file['__errors'])) {
                $storages = new Storages();
                $storages->insert();
                Response::redirect('/');
                return;
            }
        }

        if (Request::isDelete()) {
            $file = DeleteFile::getNormalizedData($_POST);
            if (!isset($file['__errors'])) {
                $file = DeleteFile::getPreparedData($file);
                $storages = new Storages();
                $storages->delete(Request::post('filename'), 'name');
                Response::redirect('/');
                return;
            }
        }

        if (Request::isPut()) {
            $file = PutFile::getNormalizedData($_POST);
            if (!isset($file['__errors'])) {
                $file = DeleteFile::getPreparedData($file);
                $storages = new Storages();
                $storages->update([], $file['filename'], 'name');
                Response::redirect('/');
                return;
            }
        }

        $files = CoreStorage::getStorageFiles();

        $ctx = [
            'file' => $file,
            'files' => $files,
        ];

        $this->render('index', $ctx);
    }

    public function password(): void
    {
        if (!Auth::auth()) {
            Response::redirect('/login');
            return;
        }

        if (Request::isPut()) {
            $formResetPassword = ResetPassword::getNormalizedData($_POST);
            if (!isset($formResetPassword['__errors'])) {
                $update = ResetPassword::getPreparedData($formResetPassword);
                $users = new Users();
                $users->update($update, Account::getCurrentUser());
            }
        } else {
            $formResetPassword = ResetPassword::getInitialData();
        }

        $ctx = [
            'form' => $formResetPassword,
        ];

        $this->render('password', $ctx);
    }

    public function download(string $name): void
    {
        $filestorage = CoreStorage::getStorage($name, 'name');
        $userId = $filestorage['user_id'] ?? null;
        if (
            !$userId
            || $filestorage['type'] == Settings::TYPES_STORAGE_CLOSE
            || ($filestorage['type'] == Settings::TYPES_STORAGE_PRIVATE && Account::getCurrentUser() != $userId)
        ) {
            Response::redirect('/');
            return;
        }
        CoreStorage::download($userId, $filestorage['file']);
    }
}
